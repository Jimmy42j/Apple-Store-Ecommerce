<?php
// productforaccessories.php
require_once "dbconn.php";

// 1) Accessory lookup
if (empty($_GET['item_id'])) {
    die("Accessory not specified.");
}
$id = intval($_GET['item_id']);

$stmt = $conn->prepare("SELECT * FROM accessories WHERE item_id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) {
    die("Accessory not found.");
}

// 2) Build image gallery array
if (!empty($item['default_image'])) {
    $imgs = array_filter(array_map('trim', explode(',', $item['default_image'])));
} else {
    $imgs = [ $item['image_path'] ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($item['item_name']) ?> | mDrive</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { margin:40px; font-family:'Inter',sans-serif; background:#f5f5f5; }
    .back { margin-bottom:20px; display:inline-block; color:#007acc; text-decoration:none; }
    .back:hover { text-decoration:underline; }
    .container {
      display:flex; flex-wrap:wrap; gap:40px;
      max-width:900px; margin:auto;
      background:#fff; padding:30px; border-radius:12px;
      box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    .image { flex:0 0 400px; position:relative; }
    .image img { width:100%; border-radius:10px; }
    .arrow {
      position:absolute; top:50%; transform:translateY(-50%);
      background:rgba(255,255,255,0.8); border:none; padding:8px 12px;
      cursor:pointer; border-radius:4px;
    }
    .prev { left:10px; } .next { right:10px; }
    .thumbnails { display:flex; gap:10px; margin-top:12px; overflow-x:auto; }
    .thumbnail {
      flex:0 0 auto; width:60px; height:60px;
      object-fit:cover; border:2px solid transparent;
      border-radius:6px; cursor:pointer;
    }
    .thumbnail.active { border-color:#007aff; }

    .info { flex:1 1 300px; }
    .info h1 { margin-top:0; font-size:28px; }
    .price { font-size:20px; font-weight:600; margin:10px 0 20px; }
    .desc { margin-bottom:30px; line-height:1.5; }

    .quantity-wrapper {
      display:flex; align-items:center; gap:8px; margin-bottom:20px;
    }
    .quantity-wrapper button,
    .quantity-wrapper input {
      width:40px; height:40px; border:1px solid #ccc; border-radius:6px;
      background:#fff; font-size:20px; display:flex;
      align-items:center; justify-content:center; cursor:pointer;
    }
    .quantity-wrapper input {
      width:60px; text-align:center;
      -moz-appearance:textfield;
    }
    .quantity-wrapper input::-webkit-inner-spin-button,
    .quantity-wrapper input::-webkit-outer-spin-button {
      -webkit-appearance:none; margin:0;
    }

    /* Add to cart button */
    #addCart {
      padding:12px 30px; background:#000; color:#fff;
      border:none; border-radius:6px; cursor:pointer;
      font-size:16px;
    }
  </style>
</head>
<body>

  <a href="categoryforaccessories.php" class="back">&larr; Back to Accessories</a>

  <div class="container">
    <div class="image">
      <button class="arrow prev">&#8592;</button>
      <img id="main-img" src="<?= htmlspecialchars($imgs[0]) ?>" alt="">
      <button class="arrow next">&#8594;</button>
      <div class="thumbnails">
        <?php foreach($imgs as $i => $u): ?>
          <img
            src="<?= htmlspecialchars($u) ?>"
            class="thumbnail<?= $i===0 ? ' active' : '' ?>"
            data-index="<?= $i ?>">
        <?php endforeach; ?>
      </div>
    </div>

    <div class="info">
      <h1><?= htmlspecialchars($item['item_name']) ?></h1>
      <div class="price"><?= number_format($item['price'],0) ?> USD</div>
      <div class="desc"><?= nl2br(htmlspecialchars($item['description'] ?? '')) ?></div>

      <!-- ────── FIXED Add‐to‐Cart Form ────── -->
      <form method="POST" action="add_to_cart.php">
        <input type="hidden"
               name="accessory_id"
               value="<?= (int)$item['item_id'] ?>">

        <div class="quantity-wrapper">
          <button type="button" id="decrease">−</button>
          <input type="number"
                 name="quantity"
                 id="quantity"
                 value="1"
                 min="1">
          <button type="button" id="increase">+</button>
        </div>

        <button type="submit" id="addCart">Add to Cart</button>
      </form>
      <!-- ────────────────────────────────── -->
    </div>
  </div>

  <script>
    const imgs = <?= json_encode($imgs, JSON_UNESCAPED_SLASHES) ?>,
          mainImg = document.getElementById('main-img'),
          thumbs  = document.querySelectorAll('.thumbnail'),
          prev    = document.querySelector('.prev'),
          next    = document.querySelector('.next');
    let idx = 0;
    function show(i) {
      idx = (i + imgs.length) % imgs.length;
      mainImg.src = imgs[idx];
      thumbs.forEach(t => t.classList.toggle('active', +t.dataset.index === idx));
    }
    thumbs.forEach(t => t.onclick = ()=> show(+t.dataset.index));
    prev.onclick = ()=> show(idx-1);
    next.onclick = ()=> show(idx+1);

    const dec = document.getElementById('decrease'),
          inc = document.getElementById('increase'),
          qty = document.getElementById('quantity');
    dec.onclick = ()=> { if (+qty.value > 1) qty.value--; };
    inc.onclick = ()=> { qty.value++; };
  </script>
</body>
</html>
