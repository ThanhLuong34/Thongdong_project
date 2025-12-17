<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Thanh toán - Thong Dong";

// nếu giỏ trống thì quay về shop
if (empty($_SESSION['cart'])) {
  header('Location: /thongdong/customer/shop.php');
  exit;
}

// xử lý submit checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['order'] = [
    'name'    => trim($_POST['name'] ?? ''),
    'phone'   => trim($_POST['phone'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
    'note'    => trim($_POST['note'] ?? ''),
    'items'   => $_SESSION['cart'],
    'time'    => date('H:i d/m/Y'),
    'payment' => $_POST['payment'] ?? 'cod',

  ];

  // clear cart
  $_SESSION['cart'] = [];

  header('Location: /thongdong/customer/order-confirmation.php');
  exit;
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <section class="card">
    <h1 style="margin:0 0 14px;">Thanh toán</h1>

    <form method="post" class="checkout-grid">

      <!-- THÔNG TIN KHÁCH -->
      <div class="checkout-left">
        <h3>Thông tin nhận hàng</h3>

        <div class="form-group">
          <label>Họ và tên *</label>
          <input class="input" name="name" required>
        </div>

        <div class="form-group">
          <label>Số điện thoại *</label>
          <input class="input" name="phone" required>
        </div>

        <div class="form-group">
          <label>Địa chỉ *</label>
          <textarea class="input" name="address" rows="3" required></textarea>
        </div>
<div class="form-group">
  <label>Hình thức thanh toán *</label>

  <div class="pay-box">
    <label class="pay-item">
      <input type="radio" name="payment" value="cod" checked>
      <div>
        <b>COD</b>
        <div class="muted">Thanh toán khi nhận hàng</div>
      </div>
    </label>

    <label class="pay-item">
      <input type="radio" name="payment" value="bank">
      <div>
        <b>Chuyển khoản</b>
        <div class="muted">Chuyển khoản trước khi giao</div>
      </div>
    </label>
  </div>

  <div id="bankInfo" class="bank-info" style="display:none;">
    <b>Thông tin chuyển khoản</b>
    <div class="muted" style="margin-top:6px;">
      Ngân hàng: <b>Vietcombank</b><br>
      Số tài khoản: <b>0123456789</b><br>
      Chủ tài khoản: <b>THONG DONG</b><br>
      Nội dung: <b>TD + SĐT</b>
    </div>
  </div>
</div>

        <div class="form-group">
          <label>Ghi chú (tuỳ chọn)</label>
          <textarea class="input" name="note" rows="3"></textarea>
        </div>
      </div>

      <!-- TÓM TẮT ĐƠN -->
      <div class="checkout-right">
        <h3>Đơn hàng của bà</h3>

        <div class="order-summary">
          <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $pid => $qty):
              $p = findProductById($pid, $PRODUCTS);
              if (!$p) continue;
              $sub = $p['price'] * $qty;
              $total += $sub;
          ?>
            <div class="order-row">
              <div>
                <b><?php echo htmlspecialchars($p['name']); ?></b>
                <div class="muted">x <?php echo $qty; ?></div>
              </div>
              <div><?php echo formatVND($sub); ?></div>
            </div>
          <?php endforeach; ?>

          <div class="order-row total">
            <div>Tổng cộng</div>
            <div><?php echo formatVND($total); ?></div>
          </div>
        </div>

        <button class="btn" type="submit" style="width:100%; margin-top:12px;">
          Đặt hàng
        </button>
      </div>

    </form>
  </section>
</main>
<script>
  const bankBox = document.getElementById('bankInfo');
  const radios = document.querySelectorAll('input[name="payment"]');

  function toggleBank(){
    const checked = document.querySelector('input[name="payment"]:checked');
    if (!checked) return;
    bankBox.style.display = checked.value === 'bank' ? 'block' : 'none';
  }

  radios.forEach(r => r.addEventListener('change', toggleBank));
  toggleBank();
</script>

<?php include '../includes/customer-layout-bottom.php'; ?>
