<?php
include __DIR__ . '/includes/admin-guard.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Thong Dong</title>
  <link rel="stylesheet" href="/thongdong/assets/css/admin.css">
</head>
<body>

<?php include __DIR__ . '/includes/admin-header.php'; ?>

<main class="container admin-page">
  <section class="card">
    <h1>Dashboard</h1>
    <p class="muted">

    </p>

    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:12px; margin-top:16px;">
      <div class="card" style="padding:16px;">
        <div class="muted">Sản phẩm</div>
        <div style="font-size:28px; margin-top:6px;">12</div>
      </div>
      <div class="card" style="padding:16px;">
        <div class="muted">Đơn mới</div>
        <div style="font-size:28px; margin-top:6px;">3</div>
      </div>
      <div class="card" style="padding:16px;">
        <div class="muted">Doanh thu (ước tính)</div>
        <div style="font-size:28px; margin-top:6px;">1.250.000đ</div>
      </div>
      <div class="card" style="padding:16px;">
        <div class="muted">Khách hàng</div>
        <div style="font-size:28px; margin-top:6px;">8</div>
      </div>
    </div>

    <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:12px; margin-top:12px;">
      <div class="card" style="padding:16px;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
          <h2 style="margin:0; font-size:18px;">Đơn hàng gần đây</h2>
          <a class="btn outline" href="/thongdong/admin/orders.php">Xem tất cả</a>
        </div>

        <div style="margin-top:10px; overflow:auto;">
          <table style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="text-align:left;">
                <th style="padding:10px; border-bottom:1px solid var(--line);">Mã</th>
                <th style="padding:10px; border-bottom:1px solid var(--line);">Khách</th>
                <th style="padding:10px; border-bottom:1px solid var(--line);">Tổng</th>
                <th style="padding:10px; border-bottom:1px solid var(--line);">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding:10px; border-bottom:1px solid var(--line);">#TD1021</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">Tiên</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">398.000đ</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">Chờ xử lý</td>
              </tr>
              <tr>
                <td style="padding:10px; border-bottom:1px solid var(--line);">#TD1020</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">An</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">189.000đ</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">Đang giao</td>
              </tr>
              <tr>
                <td style="padding:10px; border-bottom:1px solid var(--line);">#TD1019</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">Vy</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">459.000đ</td>
                <td style="padding:10px; border-bottom:1px solid var(--line);">Hoàn tất</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card" style="padding:16px;">
        <h2 style="margin:0 0 10px; font-size:18px;">Lối tắt</h2>
        <div style="display:flex; flex-direction:column; gap:10px;">
          <a class="btn primary" href="/thongdong/admin/products.php">Quản lý sản phẩm</a>
          <a class="btn outline" href="/thongdong/admin/orders.php">Quản lý đơn hàng</a>
          <a class="btn outline" href="/thongdong/admin/blog.php">Viết nhật ký</a>
          <a class="btn outline" href="/thongdong/admin/customers.php">Danh sách khách</a>
        </div>

        <div class="muted" style="margin-top:14px;">
          Đang đăng nhập: <?php echo htmlspecialchars($_SESSION['admin']['email']); ?>
        </div>
      </div>
    </div>
  </section>
</main>

</body>
</html>
