<?php
// settings.php
include __DIR__ . '/includes/admin-guard.php';

$pageTitle = "Cài đặt - Thong Dong";

$success = '';
$errors = [];

/**
 * Demo data.
 * Nếu bà có file JSON/DB settings thì thay phần $values này bằng code đọc dữ liệu của bà.
 */
$values = [
  'store_name'  => 'Thong Dong',
  'slogan'      => 'Nến thơm thuần Việt dành cho người Việt.',
  'hotline'     => '0900 000 000',
  'email'       => 'hello@thongdong.vn',
  'address'     => 'Đà Nẵng, Việt Nam',

  'ship_fee'    => '25000',
  'free_ship'   => '500000',

  'bank_enable' => '1',
  'bank_name'   => 'Vietcombank',
  'bank_number' => '0123456789',
  'bank_owner'  => 'THONG DONG',
  'bank_note'   => 'TD + SĐT',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($values as $k => $v) {
    if ($k === 'bank_enable') {
      $values[$k] = isset($_POST[$k]) ? '1' : '0';
      continue;
    }
    $values[$k] = trim($_POST[$k] ?? '');
  }

  if ($values['store_name'] === '') $errors[] = 'Vui lòng nhập tên cửa hàng.';
  if ($values['hotline'] === '') $errors[] = 'Vui lòng nhập hotline.';
  if ($values['email'] === '') $errors[] = 'Vui lòng nhập email.';
  if ($values['address'] === '') $errors[] = 'Vui lòng nhập địa chỉ.';

  if (!$errors) {
    // TODO: nếu bà muốn lưu JSON/DB thì thêm logic ở đây
    $success = 'Đã lưu cài đặt (demo).';
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="/thongdong/assets/css/admin.css">
</head>

<body class="admin-page admin-settings">

<?php include __DIR__ . '/includes/admin-header.php'; ?>

<main class="container" style="padding: 28px 0 80px;">
  <section class="card" style="padding:18px;">
    <h1 style="margin:0 0 6px;">Cài đặt</h1>
    <p class="muted" style="margin:0 0 14px;">Cấu hình thông tin cửa hàng, vận chuyển và thanh toán.</p>

    <?php if ($success): ?>
      <div class="auth-alert" style="border-color:#cfe9d2; background:#f3fff5;">
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="auth-alert">
        <?php foreach ($errors as $e): ?>
          <div>• <?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="settings-grid">
      <!-- LEFT -->
      <div class="card" style="padding:16px;">
        <h2 style="margin:0 0 12px; font-size:20px;">Thông tin cửa hàng</h2>

        <div class="form-grid">
          <div class="form-group">
            <label>Tên cửa hàng *</label>
            <input class="input" name="store_name" value="<?php echo htmlspecialchars($values['store_name']); ?>">
          </div>

          <div class="form-group">
            <label>Hotline *</label>
            <input class="input" name="hotline" value="<?php echo htmlspecialchars($values['hotline']); ?>">
          </div>

          <div class="form-group full">
            <label>Slogan</label>
            <input class="input" name="slogan" value="<?php echo htmlspecialchars($values['slogan']); ?>">
          </div>

          <div class="form-group full">
            <label>Email *</label>
            <input class="input" name="email" value="<?php echo htmlspecialchars($values['email']); ?>">
          </div>

          <div class="form-group full">
            <label>Địa chỉ *</label>
            <textarea class="input" name="address" rows="3"><?php echo htmlspecialchars($values['address']); ?></textarea>
          </div>
        </div>

        <hr style="margin:16px 0; border:none; border-top:1px solid var(--line);">

        <h2 style="margin:0 0 12px; font-size:20px;">Vận chuyển</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>Phí ship cố định (đ)</label>
            <input class="input" name="ship_fee" value="<?php echo htmlspecialchars($values['ship_fee']); ?>">
          </div>

          <div class="form-group">
            <label>Freeship khi đơn từ (đ)</label>
            <input class="input" name="free_ship" value="<?php echo htmlspecialchars($values['free_ship']); ?>">
          </div>
        </div>

        <div class="muted" style="margin-top:10px;">Gợi ý: freeship từ 500.000đ để dễ chốt đơn.</div>
      </div>

      <!-- RIGHT -->
      <div class="card" style="padding:16px;">
        <h2 style="margin:0 0 12px; font-size:20px;">Thanh toán chuyển khoản</h2>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:12px; border:1px solid var(--line); border-radius:12px;">
          <input type="checkbox" name="bank_enable" <?php echo ($values['bank_enable'] === '1') ? 'checked' : ''; ?> style="margin-top:2px;">
          <div>
            <b>Bật thanh toán chuyển khoản</b>
            <div class="muted">Hiển thị lựa chọn “Chuyển khoản” ở checkout.</div>
          </div>
        </label>

        <div style="height:12px;"></div>

        <div class="form-grid">
          <div class="form-group">
            <label>Ngân hàng</label>
            <input class="input" name="bank_name" value="<?php echo htmlspecialchars($values['bank_name']); ?>">
          </div>
          <div class="form-group">
            <label>Số tài khoản</label>
            <input class="input" name="bank_number" value="<?php echo htmlspecialchars($values['bank_number']); ?>">
          </div>

          <div class="form-group">
            <label>Chủ tài khoản</label>
            <input class="input" name="bank_owner" value="<?php echo htmlspecialchars($values['bank_owner']); ?>">
          </div>
          <div class="form-group">
            <label>Nội dung CK</label>
            <input class="input" name="bank_note" value="<?php echo htmlspecialchars($values['bank_note']); ?>">
          </div>
        </div>

        <button class="btn" type="submit" style="width:100%; margin-top:14px;">Lưu cài đặt</button>

        <div class="muted" style="margin-top:10px;">
          Nội dung đề xuất: <b><?php echo htmlspecialchars($values['bank_note']); ?></b>
        </div>
      </div>
    </form>
  </section>
</main>

<?php
// nếu bà có footer include thì dùng, không có thì thôi
$footer = __DIR__ . '/includes/admin-footer.php';
if (file_exists($footer)) include $footer;
?>

</body>
</html>
