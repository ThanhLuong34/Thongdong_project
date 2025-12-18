<?php
session_start();
$pageTitle = "Tài khoản - Thong Dong";

// chưa login thì về login
if (empty($_SESSION['customer'])) {
  header('Location: /thongdong/customer/login.php');
  exit;
}

$customer = $_SESSION['customer'];
$order = $_SESSION['order'] ?? null;

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">

  <section class="card account-head">
    <div class="account-left">
      <h1 style="margin:0 0 8px;">Tài khoản</h1>
      <p class="muted" style="margin:0;">
        Chào <b><?php echo htmlspecialchars($customer['name']); ?></b> ✨
        <span class="muted"> (<?php echo htmlspecialchars($customer['email']); ?>)</span>
      </p>
    </div>

    <div class="account-actions">
      <a class="btn outline" href="/thongdong/customer/cart.php">Giỏ hàng</a>
      <a class="btn" href="/thongdong/customer/logout.php">Đăng xuất</a>
    </div>
  </section>

  <section class="account-grid">
    <!-- Thông tin -->
    <article class="card account-card">
      <h2 class="account-title">Thông tin của bà</h2>
      <div class="account-info">
        <div class="info-row">
          <div class="muted">Họ và tên</div>
          <div><b><?php echo htmlspecialchars($customer['name']); ?></b></div>
        </div>
        <div class="info-row">
          <div class="muted">Email</div>
          <div><b><?php echo htmlspecialchars($customer['email']); ?></b></div>
        </div>
        <div class="info-row">
          <div class="muted">Trạng thái</div>
          <div><span class="badge paid">Đã đăng nhập</span></div>
        </div>
      </div>

      <div class="muted" style="margin-top:10px;">
        (Demo front-end) Chưa có chức năng sửa hồ sơ / đổi mật khẩu. Nếu bà muốn, tui làm tiếp.
      </div>
    </article>

<?php
$orders = $_SESSION['order_history'] ?? [];
$recent = array_slice($orders, 0, 3);
?>

<div class="card" style="padding:18px;">
  <h3 style="margin:0 0 10px;">Đơn hàng gần đây</h3>

  <?php if (empty($orders)): ?>
    <div class="muted">Chưa có đơn nào. Bà ghé cửa hàng chọn nến nha.</div>
    <a class="btn" style="margin-top:12px;" href="/thongdong/customer/shop.php">Mua ngay</a>
  <?php else: ?>
    <div style="display:grid; gap:10px; margin-top:8px;">
      <?php foreach ($recent as $o): ?>
        <div class="card" style="padding:12px;">
          <div style="display:flex; justify-content:space-between; gap:12px;">
            <div>
              <b>#<?php echo htmlspecialchars($o['id'] ?? 'TD'); ?></b>
              <div class="muted" style="margin-top:4px;">
                <?php echo htmlspecialchars($o['time'] ?? ''); ?> •
                <?php echo strtoupper(htmlspecialchars($o['payment'] ?? 'cod')); ?>
              </div>
            </div>
            <div style="text-align:right;">
              <b>
                <?php
                  $t = $o['total'] ?? 0;
                  if (!$t && !empty($o['items'])) {
                    foreach ($o['items'] as $it) $t += ((int)($it['price'] ?? 0)) * ((int)($it['qty'] ?? 1));
                  }
                  echo number_format((int)$t) . 'đ';
                ?>
              </b>
              <div class="muted" style="margin-top:4px;">
                <?php echo htmlspecialchars($o['status'] ?? 'Chờ xử lý'); ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <a class="btn outline" style="margin-top:12px;" href="/thongdong/customer/order-history.php">
      Xem tất cả
    </a>
  <?php endif; ?>
</div>


    <!-- Quick links -->
    <article class="card account-card account-links">
      <h2 class="account-title">Nhanh tay</h2>
      <a class="quick-link" href="/thongdong/customer/shop.php">
        <span>🛍️</span>
        <div>
          <b>Vào cửa hàng</b>
          <div class="muted">Chọn mùi hương hợp mood</div>
        </div>
      </a>

      <a class="quick-link" href="/thongdong/customer/blog.php">
        <span>📓</span>
        <div>
          <b>Đọc Nhật ký</b>
          <div class="muted">Mẹo dùng nến & câu chuyện nhỏ</div>
        </div>
      </a>

      <a class="quick-link" href="/thongdong/customer/about.php">
        <span>🏮</span>
        <div>
          <b>Giới thiệu Thong Dong</b>
          <div class="muted">Thuần Việt – vàng đỏ – gạch bông</div>
        </div>
      </a>
    </article>

  </section>

</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
