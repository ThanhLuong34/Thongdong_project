<?php
$pageTitle = "Đổi trả & Hoàn tiền - Thong Dong";
include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card return-card" style="padding:22px;">
    <h1 class="page-title" style="margin:0 0 8px;">Đổi trả & Hoàn tiền</h1>
    <p class="muted" style="margin:0 0 18px;">
      Thong Dong hỗ trợ đổi/trả/refund trong trường hợp sản phẩm lỗi, giao nhầm, hoặc chưa ưng mùi.
    </p>

    <!-- Box 1 -->
    <div class="card return-box" style="padding:22px; margin:0 0 18px;">
      <h3 style="margin:0 0 10px;">Điều kiện áp dụng</h3>
      <ul style="margin:0; padding-left:18px; line-height:1.9;">
        <li>Gửi yêu cầu trong <b>7 ngày</b> kể từ khi nhận.</li>
        <li>Sản phẩm còn nguyên vẹn, còn hộp/tem (nếu có).</li>
        <li>Chuẩn bị mã đơn + mô tả tình trạng.</li>
      </ul>
    </div>

    <!-- Box 2 -->
    <div class="card return-box" style="padding:22px; margin:0 0 22px;">
      <h3 style="margin:0 0 10px;">Hình thức xử lý</h3>
      <ul style="margin:0; padding-left:18px; line-height:1.9;">
        <li><b>Đổi sản phẩm</b> tương đương</li>
        <li><b>Hoàn tiền</b> (refund) qua chuyển khoản khi đủ điều kiện</li>
        <li><b>Voucher</b> trong một số trường hợp</li>
      </ul>
    </div>

    <!-- Actions -->
    <div class="return-actions" style="margin-top:6px; display:flex; gap:12px; flex-wrap:wrap;">
      <a class="btn" href="/thongdong/customer/return-request.php">Tạo yêu cầu đổi/trả</a>
      <a class="btn outline" href="/thongdong/customer/account.php">Xem tài khoản</a>
    </div>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
