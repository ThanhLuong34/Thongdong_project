<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Chi tiết sản phẩm - Thong Dong";

// Lấy id từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = findProductById($id, $PRODUCTS);

// Xử lý thêm vào giỏ (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  $pid = (int)($_POST['product_id'] ?? 0);
  $qty = (int)($_POST['qty'] ?? 1);
  if ($qty < 1) $qty = 1;

  $p = findProductById($pid, $PRODUCTS);
  if ($p) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (!isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
    $_SESSION['cart'][$pid] += $qty;

    header('Location: /thongdong/customer/cart.php');
    exit;
  }
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <?php if (!$product): ?>
    <section class="card">
      <h1 style="margin:0 0 8px;">Không tìm thấy sản phẩm</h1>
      <p class="muted">Sản phẩm không tồn tại hoặc đường dẫn không đúng.</p>
      <a class="btn" href="/thongdong/customer/shop.php">Quay lại cửa hàng</a>
    </section>
  <?php else: ?>

    <nav class="muted" style="margin-bottom:12px;">
      <a href="/thongdong/customer/shop.php" style="text-decoration:underline;">Cửa hàng</a>
      <span> / </span>
      <span><?php echo htmlspecialchars($product['name']); ?></span>
    </nav>

    <section class="product-detail card">
<div class="pd-img">
  <img
    src="<?php echo htmlspecialchars($product['image'] ?? '/thongdong/assets/img/products/placeholder.jpg'); ?>"
    alt="<?php echo htmlspecialchars($product['name'] ?? 'Sản phẩm'); ?>"
    loading="lazy"
  >
</div>

      </div>

      <div class="pd-right">
        <h1 class="pd-title"><?php echo htmlspecialchars($product['name']); ?></h1>

        <div class="pd-meta">
          <span class="price big"><?php echo formatVND($product['price']); ?></span>
          <span class="tag"><?php echo htmlspecialchars($product['tag']); ?></span>
        </div>

        <p class="pd-desc"><?php echo htmlspecialchars($product['desc']); ?></p>

        <div class="pd-spec">
          <div class="spec-item">
            <div class="muted">Khối lượng</div>
            <div><b><?php echo htmlspecialchars($product['size']); ?></b></div>
          </div>
          <div class="spec-item">
            <div class="muted">Thời gian cháy</div>
            <div><b><?php echo htmlspecialchars($product['burn']); ?></b></div>
          </div>
        </div>

        <form method="post" class="pd-actions">
  <input type="hidden" name="product_id" value="<?php echo (int)($product['id'] ?? 0); ?>">

  <div class="qty">
    <button type="button" class="qty-btn" onclick="qtyDown()">-</button>
    <input id="qtyInput" class="qty-input" type="number" name="qty" value="1" min="1">
    <button type="button" class="qty-btn" onclick="qtyUp()">+</button>
  </div>

  <button class="btn" type="submit" name="add_to_cart">Thêm vào giỏ</button>
  <a class="btn outline" href="/thongdong/customer/cart.php">Xem giỏ hàng</a>
</form>

<!-- ✅ ĐƯA NÚT REVIEW RA NGOÀI FORM để khỏi bị submit / JS chặn -->
<div class="pd-actions" style="margin-top:10px;">
  <button
    type="button"
    class="btn outline"
    onclick="window.location.href='/thongdong/customer/review-create.php?id=<?php echo (int)($product['id'] ?? 0); ?>'">
    Viết review
  </button>
</div>



        </form>
      </div>
    </section>

    <section class="card" style="margin-top:16px;">
      <h3 style="margin:0 0 10px;">Có thể bạn cũng thích</h3>
      <div class="rel-grid">
        <?php
          // gợi ý 4 sp khác (đơn giản)
          $count = 0;
          foreach ($PRODUCTS as $p) {
            if ($p['id'] == $product['id']) continue;
            $count++;
            if ($count > 4) break;
        ?>
          <a class="rel-card" href="/thongdong/customer/product-detail.php?id=<?php echo (int)$p['id']; ?>">
            <div class="rel-img">
  <img
    src="<?php echo htmlspecialchars($p['image'] ?? '/thongdong/assets/img/products/placeholder.jpg'); ?>"
    alt="<?php echo htmlspecialchars($p['name'] ?? 'Sản phẩm'); ?>"
    loading="lazy"
  >
</div>

            <div class="rel-body">
              <div class="rel-name"><?php echo htmlspecialchars($p['name']); ?></div>
              <div class="price"><?php echo formatVND($p['price']); ?></div>
            </div>
          </a>
        <?php } ?>
      </div>
    </section>

    <script>
      function qtyDown(){
        const el = document.getElementById('qtyInput');
        const v = parseInt(el.value || '1', 10);
        el.value = Math.max(1, v - 1);
      }
      function qtyUp(){
        const el = document.getElementById('qtyInput');
        const v = parseInt(el.value || '1', 10);
        el.value = Math.max(1, v + 1);
      }
    </script>

  <?php endif; ?>
</main>

