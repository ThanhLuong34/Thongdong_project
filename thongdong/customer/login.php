<?php
session_start();
$pageTitle = "Đăng nhập - Thong Dong";

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  // demo: check user đã đăng ký trong session
  $users = $_SESSION['users'] ?? [];

  $found = null;
  foreach ($users as $u) {
    if (strtolower($u['email']) === strtolower($email) && $u['password'] === $password) {
      $found = $u;
      break;
    }
  }

  // fallback demo account nếu chưa đăng ký
  if (!$found && $email === 'thongdong@gmail.vn' && $password === '123456') {
    $found = ['name' => 'Khách Demo', 'email' => $email];
  }

  if ($found) {
    $_SESSION['customer'] = [
      'name' => $found['name'] ?? 'Khách hàng',
      'email' => $found['email'] ?? $email,
    ];
    header('Location: /thongdong/customer/index.php');
    exit;
  } else {
    $error = 'Email hoặc mật khẩu chưa đúng.';
  }
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <section class="card auth-card">
    <h1 style="margin:0 0 10px;">Đăng nhập</h1>
    <p class="muted" style="margin:0 0 16px;">Chào bạn, đăng nhập để mua hàng nhanh hơn nha.</p>

    <?php if ($error): ?>
      <div class="auth-alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">
      <div class="form-group">
        <label>Email</label>
        <input class="input" name="email" type="email" required placeholder="vd: thongdong@gmail.vn">
      </div>

      <div class="form-group">
        <label>Mật khẩu</label>
        <input class="input" name="password" type="password" required placeholder="vd: 123456">
      </div>

      <button class="btn" type="submit" style="width:100%;">Đăng nhập</button>

      <div class="auth-links">
        <a class="link" href="/thongdong/customer/forgot-password.php">Quên mật khẩu?</a>
        <span class="muted">•</span>
        <a class="link" href="/thongdong/customer/register.php">Tạo tài khoản</a>
      </div>

      <div class="muted" style="margin-top:10px; text-align:center;">
      </div>
    </form>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
