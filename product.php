<?php
require_once "dbconn.php";

// 1) Fetch product
if (empty($_GET['product_id'])) {
    die("Product not specified.");
}
$id = intval($_GET['product_id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    die("Product not found.");
}

// 2) Build images array
if (!empty($product['default_image'])) {
    $imgs = array_filter(array_map('trim', explode(',', $product['default_image'])));
} else {
    $imgs = [ $product['image'] ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family:'Inter',sans-serif;
      background:#f5f5f5;
      margin:0;
      padding:40px;
    }
    .back-container {
  margin-bottom: 25px;
}

.back-btn {
  display: inline-block;
  background-color: #e0e0e0;
  color: #111;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 30px;
  transition: all 0.25s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.back-btn:hover {
  background-color: #333;
  color: #fff;
}

    
    .back-link {
      display: inline-block;
      font-size: 16px;
      color: #007acc;
      text-decoration: none;
      transition: color .2s;
    }
    .back-link:hover {
      color: #005fa3;
    }
    @media (max-width: 600px) {
      .back-link { font-size: 14px; width:100%; text-align:center; }
      .back-container { text-align:center; margin-bottom:15px; }
    }
    .detail-container {
      display:flex;
      gap:40px;
      max-width:1000px;
      margin:auto;
      background:#fff;
      padding:30px;
      border-radius:12px;
      box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    .detail-image {
      flex:1;
      position:relative;
    }
    .detail-image img {
      width:100%;
      border-radius:10px;
    }
    .arrow {
      position:absolute;
      top:50%;
      transform:translateY(-50%);
      background:rgba(255,255,255,0.8);
      border:none;
      font-size:18px;
      padding:8px 12px;
      cursor:pointer;
      border-radius:4px;
    }
    .prev { left:10px; }
    .next { right:10px; }
    .thumbnails {
      display:flex;
      gap:10px;
      margin-top:12px;
      justify-content:center;
    }
    .thumbnail {
      width:40px; height:40px;
      object-fit:cover;
      border:2px solid transparent;
      border-radius:6px;
      cursor:pointer;
    }
    .thumbnail.active { border-color:#007aff; }

    .detail-info {
      flex:1;
    }
    .detail-info h1 {
      margin-top:0;
      font-size:28px;
    }
    .price {
      font-size:24px;
      font-weight:600;
      margin:10px 0 20px;
    }
    .description {
      line-height:1.5;
      margin-bottom:30px;
    }

    .quantity-wrapper {
      display:flex;
      align-items:center;
      gap:8px;
      margin-bottom:20px;
    }
    .qty-btn,
    .quantity-wrapper input {
      width:30px; height:30px;
      border:1px solid #ccc;
      background:#fff;
      color:#333;
      font-size:20px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:6px;
      cursor:pointer;
    }
    .quantity-wrapper input {
      width:60px;
      text-align:center;
      font-size:16px;
      -moz-appearance:textfield;
    }
    .quantity-wrapper input::-webkit-outer-spin-button,
    .quantity-wrapper input::-webkit-inner-spin-button {
      -webkit-appearance:none;
      margin:0;
    }

    .addtocart-btn {
      padding:12px 30px;
      background:#000;
      color:#fff;
      border:none;
      border-radius:6px;
      font-size:16px;
      cursor:pointer;
    }
  </style>
</head>
<body>

  <div class="back-container">
  <a class="back-btn" href="category.php?category_id=<?= $product['category_id'] ?>">
     Back to Collection
  </a>
</div>


  <div class="detail-container">
    <div class="detail-image">
      <button class="arrow prev">&#8592;</button>
      <img id="main-img" src="<?= htmlspecialchars($imgs[0]) ?>" alt="">
      <button class="arrow next">&#8594;</button>
      <div class="thumbnails">
        <?php foreach($imgs as $i => $u): ?>
          <img
            src="<?= htmlspecialchars($u) ?>"
            class="thumbnail<?= $i===0 ? ' active' : '' ?>"
            data-index="<?= $i ?>"
          >
        <?php endforeach; ?>
      </div>
    </div>

    <div class="detail-info">
      <h1><?= htmlspecialchars($product['name']) ?></h1>
      <div class="price">$<?= number_format($product['price'],2) ?></div>
      <div class="description">
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </div>

      <!-- 3) Add-to-cart form -->
      <form method="POST" action="add_to_cart.php">
        <input 
          type="hidden" 
          name="product_id" 
          value="<?= (int)$product['product_id'] ?>"
        >

        <div class="quantity-wrapper">
          <button type="button" class="qty-btn" id="decrease">−</button>
          <input 
            type="number" 
            name="quantity" 
            id="quantity" 
            value="1" 
            min="1"
          >
          <button type="button" class="qty-btn" id="increase">+</button>
        </div>

        <button type="submit" class="addtocart-btn">
          Add to Cart
        </button>
      </form>
    </div>
  </div>

  <script>
    
    const imgs      = <?= json_encode($imgs, JSON_UNESCAPED_SLASHES) ?>;
    let idx         = 0;
    const mainImg   = document.getElementById('main-img');
    const thumbs    = document.querySelectorAll('.thumbnail');
    const prevBtn   = document.querySelector('.prev');
    const nextBtn   = document.querySelector('.next');

    function showImage(i) {
      idx = (i + imgs.length) % imgs.length;
      mainImg.src = imgs[idx];
      thumbs.forEach(t => 
        t.classList.toggle('active', +t.dataset.index === idx)
      );
    }
    thumbs.forEach(t => t.onclick = ()=> showImage(+t.dataset.index));
    prevBtn.onclick = ()=> showImage(idx-1);
    nextBtn.onclick = ()=> showImage(idx+1);
    const dec      = document.getElementById('decrease');
    const inc      = document.getElementById('increase');
    const qtyInput = document.getElementById('quantity');
    dec.onclick = () => {
      let v = parseInt(qtyInput.value,10) || 1;
      if (v>1) qtyInput.value = v-1;
    };
    inc.onclick = () => {
      let v = parseInt(qtyInput.value,10) || 1;
      qtyInput.value = v+1;
    };
  </script>
</body>
</html>
