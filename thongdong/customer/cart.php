<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Giỏ hàng - Thong Dong";

// Khởi tạo giỏ
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// ADD từ shop (?add=id)
if (isset($_GET['add'])) {
  $pid = (int)$_GET['add'];
  $p = findProductById($pid, $PRODUCTS);
  if ($p) {
    if (!isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
    $_SESSION['cart'][$pid] += 1;
  }
  header('Location: /thongdong/customer/cart.php');
  exit;
}

// UPDATE / REMOVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // cập nhật số lượng
  if (isset($_POST['update'])) {
    foreach ($_POST['qty'] ?? [] as $pid => $qty) {
      $pid = (int)$pid;
      $qty = (int)$qty;
      if ($qty <= 0) unset($_SESSION['cart'][$pid]);
      else $_SESSION['cart'][$pid] = $qty;
    }
  }

  // xoá 1 sản phẩm
  if (isset($_POST['remove'])) {
    $pid = (int)$_POST['remove'];
    unset($_SESSION['cart'][$pid]);
  }

  header('Location: /thongdong/customer/cart.php');
  exit;
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <section class="card">
    <h1 style="margin:0 0 14px;">Giỏ hàng</h1>

    <?php if (empty($_SESSION['cart'])): ?>
      <p class="muted">Giỏ hàng của bạn đang trống.</p>
      <a class="btn" href="/thongdong/customer/shop.php">Tiếp tục mua sắm</a>

    <?php else: ?>
      <form method="post">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Sản phẩm</th>
              <th>Giá</th>
              <th>Số lượng</th>
              <th>Tạm tính</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
              $total = 0;
              foreach ($_SESSION['cart'] as $pid => $qty):
                $p = findProductById($pid, $PRODUCTS);
                if (!$p) continue;
                $sub = $p['price'] * $qty;
                $total += $sub;
            ?>
              <tr>
                <td>
                  <b><?php echo htmlspecialchars($p['name']); ?></b><br>
                  <span class="muted"><?php echo htmlspecialchars($p['tag']); ?></span>
                </td>
                <td><?php echo formatVND($p['price']); ?></td>
                <td>
                  <input class="qty-input" type="number" name="qty[<?php echo $pid; ?>]" value="<?php echo $qty; ?>" min="0">
                </td>
                <td><?php echo formatVND($sub); ?></td>
                <td>
                  <button class="btn small outline" name="remove" value="<?php echo $pid; ?>">Xoá</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="cart-actions">
          <button class="btn outline" name="update">Cập nhật giỏ</button>
          <div class="cart-total">
            <div class="muted">Tổng cộng</div>
            <div class="price big"><?php echo formatVND($total); ?></div>
          </div>
        </div>

        <div style="margin-top:16px;">
          <a class="btn" href="/thongdong/customer/checkout.php">Thanh toán</a>
          <a class="btn outline" href="/thongdong/customer/shop.php">Mua thêm</a>
        </div>
      </form>
    <?php endif; ?>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
