<?php
session_start();

if (!empty($_SESSION['admin'])) {
  header('Location: /thongdong/admin/dashboard.php');
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $isAdminEmail = str_ends_with(strtolower($email), '@thongdong.com');
  $correctPassword = ($password === 'thongdongcamon');

  if (!$isAdminEmail) {
    $error = 'Admin phải dùng email @thongdong.com';
  } elseif (!$correctPassword) {
    $error = 'Mật khẩu admin không đúng';
  } else {
    $_SESSION['admin'] = [
      'email' => $email,
      'name'  => explode('@', $email)[0],
      'role'  => 'admin',
    ];
    header('Location: /thongdong/admin/dashboard.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Thong Dong</title>
  <link rel="stylesheet" href="/thongdong/assets/css/admin.css">
</head>
<body>

<?php include __DIR__ . '/includes/admin-header.php'; ?>

<main class="admin-auth">
  <section class="admin-card" style="width:min(460px, 92%);">
    <h1 class="auth-title">Admin Login</h1>
    <div class="muted">Đăng nhập bằng tài khoản quản trị được cấp.</div>

    <?php if ($error): ?>
      <div class="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="field">
        <label>Email admin</label>
        <input class="input" type="email" name="email" required placeholder="thanh@thongdong.com">
      </div>

      <div class="field">
        <label>Mật khẩu</label>
        <input class="input" type="password" name="password" required placeholder="thongdongcamon">
      </div>

      <button class="btn primary" type="submit" style="width:100%; margin-top:10px;">
        Đăng nhập
      </button>
    </form>

    <div class="muted" style="margin-top:12px;">
      • Email bắt buộc kết thúc bằng <b>@thongdong.com</b><br>
      • Mật khẩu mặc định: <b>thongdongcamon</b>
    </div>
  </section>
</main>

</body>
</html>
