<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Xác nhận đơn hàng - Thong Dong";

$order = $_SESSION['order'] ?? null;
include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:40px 0 70px;">
  <section class="card" style="text-align:center;">
    <?php if (!$order): ?>
      <h1>Không có đơn hàng</h1>
      <p class="muted">Vui lòng quay lại cửa hàng.</p>
      <a class="btn" href="/thongdong/customer/shop.php">Về cửa hàng</a>
    <?php else: ?>
      <h1>Đặt hàng thành công 🎉</h1>
      <p class="muted" style="margin-bottom:12px;">
        Cảm ơn bà đã chọn Thong Dong.
      </p>

      <div class="card" style="max-width:520px; margin:0 auto 16px;">
        <p><b>Người nhận:</b> <?php echo htmlspecialchars($order['name']); ?></p>
        <p><b>Số điện thoại:</b> <?php echo htmlspecialchars($order['phone']); ?></p>
        <p><b>Địa chỉ:</b> <?php echo htmlspecialchars($order['address']); ?></p>
        <p><b>Thời gian đặt:</b> <?php echo htmlspecialchars($order['time']); ?></p>
      </div>

      <a class="btn" href="/thongdong/customer/shop.php">Tiếp tục mua sắm</a>
    <?php endif; ?>
  </section>
</main>

<?php
unset($_SESSION['order']); // clear order info sau khi xem
include '../includes/customer-layout-bottom.php';
?>
