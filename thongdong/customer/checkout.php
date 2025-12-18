<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Thanh toán - Thong Dong";

// nếu giỏ trống thì quay về shop
if (empty($_SESSION['cart'])) {
  header('Location: /thongdong/customer/shop.php');
  exit;
}

/**
 * Chuẩn hoá cart về dạng items:
 * [
 *   ['id'=>1,'name'=>'..','price'=>189000,'qty'=>2],
 *   ...
 * ]
 */
function td_normalize_cart($cart, $PRODUCTS) {
  $items = [];

  // Case A: cart dạng [productId => qty]
  $isMap = true;
  foreach ($cart as $k => $v) {
    if (!is_numeric($k) || !is_numeric($v)) { $isMap = false; break; }
  }

  if ($isMap) {
    foreach ($cart as $pid => $qty) {
      $pid = (int)$pid;
      $qty = (int)$qty;
      if ($qty < 1) continue;

      $p = findProductById($pid, $PRODUCTS);
      if (!$p) continue;

      $items[] = [
        'id' => $pid,
        'name' => $p['name'],
        'price' => (int)$p['price'],
        'qty' => $qty,
      ];
    }
    return $items;
  }

  // Case B: cart dạng [ ['id'=>..,'qty'=>..,'price'=>..], ... ]
  foreach ($cart as $row) {
    if (!is_array($row)) continue;

    $pid = (int)($row['id'] ?? 0);
    $qty = (int)($row['qty'] ?? 1);
    if ($qty < 1) $qty = 1;

    $p = $pid ? findProductById($pid, $PRODUCTS) : null;

    $name  = $row['name']  ?? ($p['name'] ?? 'Sản phẩm');
    $price = $row['price'] ?? ($p['price'] ?? 0);
    $price = (int)$price;

    $items[] = [
      'id' => $pid,
      'name' => $name,
      'price' => $price,
      'qty' => $qty,
    ];
  }

  return $items;
}

$cartItems = td_normalize_cart($_SESSION['cart'], $PRODUCTS);

// nếu normalize xong mà rỗng -> về shop
if (!$cartItems) {
  $_SESSION['cart'] = [];
  header('Location: /thongdong/customer/shop.php');
  exit;
}

$errors = [];

// xử lý submit checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name    = trim($_POST['name'] ?? '');
  $phone   = trim($_POST['phone'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $note    = trim($_POST['note'] ?? '');
  $payment = $_POST['payment'] ?? 'cod';

  if ($name === '') $errors[] = 'Vui lòng nhập họ và tên.';
  if ($phone === '') $errors[] = 'Vui lòng nhập số điện thoại.';
  if ($address === '') $errors[] = 'Vui lòng nhập địa chỉ.';

  if (!$errors) {
    $total = 0;
    foreach ($cartItems as $it) {
      $total += ((int)$it['price']) * ((int)$it['qty']);
    }

    $order = [
      'id'      => 'TD' . date('ymdHis'),
      'time'    => date('H:i d/m/Y'),
      'name'    => $name,
      'phone'   => $phone,
      'address' => $address,
      'note'    => $note,
      'payment' => $payment,
      'items'   => $cartItems,
      'total'   => $total,
      'status'  => 'Chờ xử lý',
    ];

    // đơn gần nhất
    $_SESSION['order'] = $order;

    // lịch sử đơn (để đổi trả / account / admin đọc)
    if (!isset($_SESSION['order_history'])) $_SESSION['order_history'] = [];
    array_unshift($_SESSION['order_history'], $order);

    // clear cart
    $_SESSION['cart'] = [];

    header('Location: /thongdong/customer/order-confirmation.php');
    exit;
  }
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <section class="card">
    <h1 style="margin:0 0 14px;">Thanh toán</h1>

    <?php if (!empty($errors)): ?>
      <div class="auth-alert" style="margin:0 0 14px;">
        <?php foreach ($errors as $e): ?>
          <div>• <?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="checkout-grid">

      <!-- THÔNG TIN KHÁCH -->
      <div class="checkout-left">
        <h3>Thông tin nhận hàng</h3>

        <div class="form-group">
          <label>Họ và tên *</label>
          <input class="input" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ($_SESSION['customer']['name'] ?? '')); ?>">
        </div>

        <div class="form-group">
          <label>Số điện thoại *</label>
          <input class="input" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label>Địa chỉ *</label>
          <textarea class="input" name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
          <label>Hình thức thanh toán *</label>

          <div class="pay-box">
            <label class="pay-item">
              <input type="radio" name="payment" value="cod" <?php echo (($_POST['payment'] ?? 'cod') === 'cod') ? 'checked' : ''; ?>>
              <div>
                <b>COD</b>
                <div class="muted">Thanh toán khi nhận hàng</div>
              </div>
            </label>

            <label class="pay-item">
              <input type="radio" name="payment" value="bank" <?php echo (($_POST['payment'] ?? 'cod') === 'bank') ? 'checked' : ''; ?>>
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
          <textarea class="input" name="note" rows="3"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
        </div>
      </div>

      <!-- TÓM TẮT ĐƠN -->
      <div class="checkout-right">
        <h3>Đơn hàng của bà</h3>

        <div class="order-summary">
          <?php
            $total = 0;
            foreach ($cartItems as $it):
              $sub = ((int)$it['price']) * ((int)$it['qty']);
              $total += $sub;
          ?>
            <div class="order-row">
              <div>
                <b><?php echo htmlspecialchars($it['name']); ?></b>
                <div class="muted">x <?php echo (int)$it['qty']; ?></div>
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
