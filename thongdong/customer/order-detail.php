<?php
session_start();
$pageTitle = "Chi tiết đơn hàng - Thong Dong";

function safe($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function formatVND_local($n) {
  $n = (int)$n;
  return number_format($n, 0, ',', '.') . 'đ';
}

$id = $_GET['id'] ?? '';
$id = trim($id);

$orders = $_SESSION['order_history'] ?? [];
$order = null;

// tìm đơn theo id
if ($id !== '') {
  foreach ($orders as $o) {
    if (($o['id'] ?? '') === $id) { $order = $o; break; }
  }
}

// fallback: nếu bà chỉ có $_SESSION['order'] (đơn gần nhất) mà chưa có history
if (!$order && !empty($_SESSION['order']) && ($id === '' || (($_SESSION['order']['id'] ?? '') === $id))) {
  $order = $_SESSION['order'];
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card" style="padding:18px;">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 6px;">Chi tiết đơn hàng</h1>
        <p class="muted" style="margin:0;">Xem thông tin đơn và sản phẩm đã đặt.</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn outline" href="/thongdong/customer/order-history.php">← Quay lại</a>
        <a class="btn outline" href="/thongdong/customer/returns.php">Chính sách đổi trả</a>
      </div>
    </div>

    <div style="height:14px;"></div>

    <?php if (!$order): ?>
      <div class="card" style="padding:18px;">
        <b>Không tìm thấy đơn hàng.</b>
        <div class="muted" style="margin-top:6px;">Bà thử quay lại “Đơn hàng của tôi” và chọn lại đơn nhé.</div>
        <div style="margin-top:12px;">
          <a class="btn" href="/thongdong/customer/order-history.php">Về danh sách đơn</a>
        </div>
      </div>
    <?php else: ?>

      <?php
        $orderId  = $order['id'] ?? '';
        $time     = $order['time'] ?? '';
        $payment  = strtoupper((string)($order['payment'] ?? 'cod'));
        $status   = $order['status'] ?? 'Chờ xử lý';

        $name     = $order['name'] ?? '';
        $phone    = $order['phone'] ?? '';
        $address  = $order['address'] ?? '';
        $note     = $order['note'] ?? '';

        $items    = $order['items'] ?? [];
        $total    = (int)($order['total'] ?? 0);

        if ($total <= 0) {
          foreach ($items as $it) {
            $price = (int)($it['price'] ?? 0);
            $qty   = (int)($it['qty'] ?? 0);
            if ($qty <= 0) $qty = 1;
            $total += $price * $qty;
          }
        }
      ?>

      <div class="checkout-grid">
        <div class="checkout-left">
          <div class="card" style="padding:14px;">
            <h3 style="margin:0 0 10px;">Thông tin đơn</h3>

            <div class="muted">Mã đơn</div>
            <div style="margin:2px 0 10px;"><b>#<?php echo safe($orderId); ?></b></div>

            <div class="muted">Thời gian</div>
            <div style="margin:2px 0 10px;"><b><?php echo safe($time); ?></b></div>

            <div class="muted">Thanh toán</div>
            <div style="margin:2px 0 10px;"><b><?php echo safe($payment); ?></b></div>

            <div class="muted">Trạng thái</div>
            <div style="margin:2px 0 10px;"><b><?php echo safe($status); ?></b></div>
          </div>

          <div style="height:12px;"></div>

          <div class="card" style="padding:14px;">
            <h3 style="margin:0 0 10px;">Thông tin nhận hàng</h3>

            <div class="muted">Họ tên</div>
            <div style="margin:2px 0 10px;"><b><?php echo safe($name); ?></b></div>

            <div class="muted">SĐT</div>
            <div style="margin:2px 0 10px;"><b><?php echo safe($phone); ?></b></div>

            <div class="muted">Địa chỉ</div>
            <div style="margin:2px 0 10px;"><b><?php echo nl2br(safe($address)); ?></b></div>

            <?php if (trim((string)$note) !== ''): ?>
              <div class="muted">Ghi chú</div>
              <div style="margin:2px 0 0;"><b><?php echo nl2br(safe($note)); ?></b></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="checkout-right">
          <div class="card" style="padding:14px;">
            <h3 style="margin:0 0 10px;">Sản phẩm</h3>

            <?php if (empty($items)): ?>
              <div class="muted">Không có sản phẩm trong đơn.</div>
            <?php else: ?>
              <div class="checkout-lines">
                <?php foreach ($items as $it):
                  $pname = $it['name'] ?? 'Sản phẩm';
                  $qty   = (int)($it['qty'] ?? 1);
                  if ($qty <= 0) $qty = 1;
                  $price = (int)($it['price'] ?? 0);
                  $line  = $price * $qty;
                ?>
                  <div class="line">
                    <div>
                      <b><?php echo safe($pname); ?></b>
                      <div class="muted">SL: <?php echo $qty; ?></div>
                    </div>
                    <div><b><?php echo formatVND_local($line); ?></b></div>
                  </div>
                <?php endforeach; ?>

                <div class="line total">
                  <div><b>Tổng</b></div>
                  <div><b><?php echo formatVND_local($total); ?></b></div>
                </div>
              </div>
            <?php endif; ?>

            <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
              <a class="btn" href="/thongdong/customer/return-request.php?order_id=<?php echo urlencode($orderId); ?>">
                Tạo yêu cầu đổi/trả
              </a>
              <a class="btn outline" href="/thongdong/customer/shop.php">Mua thêm</a>
            </div>

            <div class="muted" style="margin-top:10px;">
              * Demo: trạng thái đơn có thể cập nhật từ Admin sau.
            </div>
          </div>
        </div>
      </div>

    <?php endif; ?>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
