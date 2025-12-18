<?php
session_start();
$pageTitle = "Đơn hàng của tôi - Thong Dong";

$orders = $_SESSION['order_history'] ?? [];

include '../includes/customer-layout-top.php';

function safe($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function formatVND_local($n) {
  $n = (int)$n;
  return number_format($n, 0, ',', '.') . 'đ';
}
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card" style="padding:18px;">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 6px;">Đơn hàng của bà</h1>
        <p class="muted" style="margin:0;">Theo dõi đơn đã đặt tại Thong Dong.</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn outline" href="/thongdong/customer/shop.php">Mua thêm</a>
        <a class="btn outline" href="/thongdong/customer/account.php">Về tài khoản</a>
      </div>
    </div>

    <div style="height:14px;"></div>

    <?php if (empty($orders)): ?>
      <div class="card" style="padding:18px;">
        <b>Chưa có đơn nào.</b>
        <div class="muted" style="margin-top:6px;">Bà ghé Cửa hàng chọn mùi hợp mood nha.</div>
        <div style="margin-top:12px;">
          <a class="btn" href="/thongdong/customer/shop.php">Đi tới Cửa hàng</a>
        </div>
      </div>
    <?php else: ?>

      <div class="card" style="padding:14px;">
        <div style="overflow:auto;">
          <table style="width:100%; border-collapse:collapse; min-width:760px;">
            <thead>
              <tr style="text-align:left;">
                <th style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.08);">Mã đơn</th>
                <th style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.08);">Thời gian</th>
                <th style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.08);">Thanh toán</th>
                <th style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.08);">Tổng</th>
                <th style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.08);">Trạng thái</th>
                <th style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.08); text-align:right;">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): 
                $id = $o['id'] ?? '';
                $time = $o['time'] ?? '';
                $payment = strtoupper((string)($o['payment'] ?? 'cod'));
                $status = $o['status'] ?? 'Chờ xử lý';

                // total (nếu checkout chưa lưu total thì tính lại)
                $total = (int)($o['total'] ?? 0);
                if ($total <= 0) {
                  $items = $o['items'] ?? [];
                  foreach ($items as $it) {
                    $price = (int)($it['price'] ?? 0);
                    $qty   = (int)($it['qty'] ?? 0);
                    if ($qty <= 0) $qty = 1;
                    $total += $price * $qty;
                  }
                }
              ?>
                <tr>
                  <td style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.06);">
                    <b>#<?php echo safe($id); ?></b>
                  </td>
                  <td style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.06);">
                    <?php echo safe($time); ?>
                  </td>
                  <td style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.06);">
                    <?php echo safe($payment); ?>
                  </td>
                  <td style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.06);">
                    <b><?php echo formatVND_local($total); ?></b>
                  </td>
                  <td style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.06);">
                    <?php echo safe($status); ?>
                  </td>
                  <td style="padding:12px 10px; border-bottom:1px solid rgba(0,0,0,.06); text-align:right;">
                    <a class="btn outline small" href="/thongdong/customer/order-detail.php?id=<?php echo urlencode($id); ?>">Xem</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="muted" style="margin-top:10px;">
      </div>

    <?php endif; ?>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
