<?php
session_start();

$pageTitle = "Tạo yêu cầu đổi/trả - Thong Dong";
include '../includes/customer-layout-top.php';

// ---- LẤY DANH SÁCH ĐƠN (từ session order_history) ----
$orderHistory = $_SESSION['order_history'] ?? [];
// (đơn mới lên đầu sẵn nếu checkout array_unshift)

// ---- HELPER ----
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ---- DEFAULT FORM VALUE ----
$customer = $_SESSION['customer'] ?? [];
$defaultName  = $customer['name']  ?? '';
$defaultPhone = $customer['phone'] ?? '';
$defaultEmail = $customer['email'] ?? '';

$errors = [];
$successId = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderId = trim($_POST['order_id'] ?? '');
  $type    = $_POST['type'] ?? 'exchange'; // exchange | refund
  $reason  = trim($_POST['reason'] ?? '');
  $detail  = trim($_POST['detail'] ?? '');

  $name  = trim($_POST['name'] ?? $defaultName);
  $phone = trim($_POST['phone'] ?? $defaultPhone);
  $email = trim($_POST['email'] ?? $defaultEmail);

  // refund bank info
  $bank_name  = trim($_POST['bank_name'] ?? '');
  $bank_acc   = trim($_POST['bank_acc'] ?? '');
  $bank_owner = trim($_POST['bank_owner'] ?? '');

  // ---- VALIDATE ----
  if ($orderId === '') $errors[] = 'Vui lòng chọn mã đơn hàng.';
  if (!in_array($type, ['exchange', 'refund'], true)) $errors[] = 'Loại yêu cầu không hợp lệ.';
  if ($reason === '') $errors[] = 'Vui lòng chọn lý do.';
  if ($detail === '') $errors[] = 'Vui lòng nhập mô tả chi tiết.';

  if ($name === '')  $errors[] = 'Vui lòng nhập họ và tên.';
  if ($phone === '') $errors[] = 'Vui lòng nhập số điện thoại.';

  // “giống thật”: refund bắt nhập bank info
  if ($type === 'refund') {
    if ($bank_name === '')  $errors[] = 'Vui lòng nhập ngân hàng để hoàn tiền.';
    if ($bank_acc === '')   $errors[] = 'Vui lòng nhập số tài khoản để hoàn tiền.';
    if ($bank_owner === '') $errors[] = 'Vui lòng nhập chủ tài khoản để hoàn tiền.';
  }

  // orderId phải tồn tại trong order_history
  if (!$errors) {
    $found = false;
    foreach ($orderHistory as $o) {
      if (($o['id'] ?? '') === $orderId) { $found = true; break; }
    }
    if (!$found) $errors[] = 'Không tìm thấy đơn hàng. Kiểm tra lại “Đơn hàng gần đây” hoặc đặt hàng trước đã nha.';
  }

  // ---- SAVE ----
  if (!$errors) {
    $newId = 'RT' . date('ymdHis');

    $req = [
      'id'       => $newId,
      'created'  => date('H:i d/m/Y'),
      'order_id' => $orderId,
      'type'     => $type, // exchange | refund
      'reason'   => $reason,
      'detail'   => $detail,
      'status'   => 'Chờ xử lý',
      'contact'  => [
        'name'  => $name,
        'phone' => $phone,
        'email' => $email,
      ],
    ];

    if ($type === 'refund') {
      $req['refund_bank'] = [
        'bank_name'  => $bank_name,
        'bank_acc'   => $bank_acc,
        'bank_owner' => $bank_owner,
      ];
    }

    if (!isset($_SESSION['return_requests'])) $_SESSION['return_requests'] = [];
    array_unshift($_SESSION['return_requests'], $req);

    $success = true;
    $successId = $newId;
  }
}
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card" style="padding:18px;">
    <h1 style="margin:0 0 6px;">Tạo yêu cầu đổi/trả</h1>
    <p class="muted" style="margin:0 0 14px;">Điền thông tin để Thong Dong hỗ trợ nhanh nhất.</p>

    <?php if ($success): ?>
      <div class="auth-alert" style="margin-bottom:14px;">
        <b>Đã gửi yêu cầu!</b> Mã yêu cầu của bạn là <b><?php echo h($successId); ?></b>.
        <div class="muted" style="margin-top:8px;">
          <a class="btn small" href="/thongdong/customer/my-returns.php" style="display:inline-flex;">Xem lại yêu cầu đổi/trả</a>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="auth-alert" style="margin-bottom:14px;">
        <?php foreach ($errors as $e): ?>
          <div>• <?php echo h($e); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="checkout-grid" style="gap:16px;">
      <!-- LEFT -->
      <div class="checkout-left">
        <div class="form-group">
          <label>Mã đơn hàng *</label>
          <select class="input" name="order_id" required>
            <option value="">-- Chọn đơn --</option>
            <?php foreach ($orderHistory as $o): ?>
              <?php
                $oid = $o['id'] ?? '';
                $otime = $o['time'] ?? '';
                if (!$oid) continue;
                $selected = (($oid === ($_POST['order_id'] ?? '')) ? 'selected' : '');
              ?>
              <option value="<?php echo h($oid); ?>" <?php echo $selected; ?>>
                <?php echo h($oid); ?> — <?php echo h($otime); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="muted" style="margin-top:6px;">
            Nếu không thấy đơn, kiểm tra lại bạn đã đặt hàng và có “order_history”.
          </div>
        </div>

        <div class="form-group">
          <label>Loại yêu cầu *</label>
          <div class="pay-box">
            <?php $curType = $_POST['type'] ?? 'exchange'; ?>

            <label class="pay-item">
              <input type="radio" name="type" value="exchange" <?php echo ($curType === 'exchange') ? 'checked' : ''; ?>>
              <div>
                <b>Đổi hàng</b>
                <div class="muted">Đổi sang sản phẩm tương đương</div>
              </div>
            </label>

            <label class="pay-item">
              <input type="radio" name="type" value="refund" <?php echo ($curType === 'refund') ? 'checked' : ''; ?>>
              <div>
                <b>Hoàn tiền</b>
                <div class="muted">Hoàn tiền theo điều kiện áp dụng</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Refund bank info (giống thật) -->
        <div id="refundBox" class="card" style="padding:12px; margin-top:8px; display:none;">
          <b>Thông tin hoàn tiền (bắt buộc)</b>
          <div class="muted" style="margin:6px 0 12px;">
            Chỉ cần khi chọn “Hoàn tiền”.
          </div>

          <div class="form-group">
            <label>Ngân hàng *</label>
            <input class="input" name="bank_name" value="<?php echo h($_POST['bank_name'] ?? ''); ?>" placeholder="VD: Vietcombank">
          </div>

          <div class="form-group">
            <label>Số tài khoản *</label>
            <input class="input" name="bank_acc" value="<?php echo h($_POST['bank_acc'] ?? ''); ?>" placeholder="VD: 0123456789">
          </div>

          <div class="form-group">
            <label>Chủ tài khoản *</label>
            <input class="input" name="bank_owner" value="<?php echo h($_POST['bank_owner'] ?? ''); ?>" placeholder="VD: NGUYEN VAN A">
          </div>
        </div>

        <div class="form-group">
          <label>Lý do *</label>
          <?php $curReason = $_POST['reason'] ?? ''; ?>
          <select class="input" name="reason" required>
            <option value="">-- Chọn lý do --</option>
            <option value="Giao nhầm sản phẩm" <?php echo ($curReason==='Giao nhầm sản phẩm')?'selected':''; ?>>Giao nhầm sản phẩm</option>
            <option value="Sản phẩm lỗi / bể vỡ" <?php echo ($curReason==='Sản phẩm lỗi / bể vỡ')?'selected':''; ?>>Sản phẩm lỗi / bể vỡ</option>
            <option value="Chưa ưng mùi" <?php echo ($curReason==='Chưa ưng mùi')?'selected':''; ?>>Chưa ưng mùi</option>
            <option value="Khác" <?php echo ($curReason==='Khác')?'selected':''; ?>>Khác</option>
          </select>
        </div>

        <div class="form-group">
          <label>Mô tả chi tiết *</label>
          <textarea class="input" name="detail" rows="4" required
            placeholder="Mô tả tình trạng + hình ảnh (nếu có) + mong muốn đổi/hoàn..."><?php echo h($_POST['detail'] ?? ''); ?></textarea>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="checkout-right">
        <div class="card" style="padding:14px;">
          <h2 style="margin:0 0 10px; font-size:20px;">Thông tin liên hệ</h2>

          <div class="form-group">
            <label>Họ và tên *</label>
            <input class="input" name="name" required
              value="<?php echo h($_POST['name'] ?? $defaultName); ?>">
          </div>

          <div class="form-group">
            <label>Số điện thoại *</label>
            <input class="input" name="phone" required
              value="<?php echo h($_POST['phone'] ?? $defaultPhone); ?>">
          </div>

          <div class="form-group">
            <label>Email</label>
            <input class="input" name="email"
              value="<?php echo h($_POST['email'] ?? $defaultEmail); ?>">
          </div>

          <button class="btn" type="submit" style="width:100%; margin-top:10px;">
            Gửi yêu cầu
          </button>

          <div class="muted" style="margin-top:10px; text-align:center;">
            Thời gian xử lý dự kiến: 1–3 ngày làm việc (demo).
          </div>

          <div style="display:flex; gap:10px; justify-content:center; margin-top:12px; flex-wrap:wrap;">
            <a class="btn outline small" href="/thongdong/customer/returnrs.php">Xem chính sách</a>
            <a class="btn outline small" href="/thongdong/customer/account.php">Về tài khoản</a>
          </div>
        </div>
      </div>
    </form>
  </section>
</main>

<script>
  const refundBox = document.getElementById('refundBox');
  const typeRadios = document.querySelectorAll('input[name="type"]');

  function toggleRefundBox(){
    const checked = document.querySelector('input[name="type"]:checked');
    refundBox.style.display = (checked && checked.value === 'refund') ? 'block' : 'none';
  }

  typeRadios.forEach(r => r.addEventListener('change', toggleRefundBox));
  toggleRefundBox();
</script>

<?php include '../includes/customer-layout-bottom.php'; ?>
