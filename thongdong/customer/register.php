<?php
session_start();
$pageTitle = "Đăng ký - Thong Dong";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $password2 = trim($_POST['password2'] ?? '');

  if (!$name || !$email || !$password) {
    $error = 'Vui lòng điền đầy đủ thông tin.';
  } elseif ($password !== $password2) {
    $error = 'Mật khẩu nhập lại không khớp.';
  } else {
    $users = $_SESSION['users'] ?? [];
    foreach ($users as $u) {
      if (strtolower($u['email']) === strtolower($email)) {
        $error = 'Email này đã được đăng ký.';
        break;
      }
    }

    if (!$error) {
      $users[] = ['name' => $name, 'email' => $email, 'password' => $password];
      $_SESSION['users'] = $users;

      $success = 'Đăng ký thành công. Bà đăng nhập nha.';
    }
  }
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <section class="card auth-card">
    <h1 style="margin:0 0 10px;">Tạo tài khoản</h1>
    <p class="muted" style="margin:0 0 16px;">Đăng ký để lưu thông tin và theo dõi đơn hàng nha.</p>

    <?php if ($error): ?>
      <div class="auth-alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="auth-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">
      <div class="form-group">
        <label>Họ và tên</label>
        <input class="input" name="name" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input class="input" name="email" type="email" required>
      </div>

      <div class="form-group">
        <label>Mật khẩu</label>
        <input class="input" name="password" type="password" required>
      </div>

      <div class="form-group">
        <label>Nhập lại mật khẩu</label>
        <input class="input" name="password2" type="password" required>
      </div>

      <button class="btn" type="submit" style="width:100%;">Đăng ký</button>

      <div class="auth-links">
        <a class="link" href="/thongdong/customer/login.php">← Quay lại đăng nhập</a>
      </div>
    </form>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
