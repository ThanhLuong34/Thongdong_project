<?php
session_start();
$pageTitle = "Quên mật khẩu - Thong Dong";

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  // demo: không gửi mail thật, chỉ báo đã nhận yêu cầu
  $success = "Đã nhận yêu cầu. Nếu email <b>$email</b> tồn tại, tụi mình sẽ gửi hướng dẫn đặt lại mật khẩu.";
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <section class="card auth-card">
    <h1 style="margin:0 0 10px;">Quên mật khẩu</h1>
    <p class="muted" style="margin:0 0 16px;">Nhập email để nhận hướng dẫn đặt lại mật khẩu nha.</p>

    <?php if ($success): ?>
      <div class="auth-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">
      <div class="form-group">
        <label>Email</label>
        <input class="input" name="email" type="email" required placeholder="vd: demo@thongdong.vn">
      </div>

      <button class="btn" type="submit" style="width:100%;">Gửi yêu cầu</button>

      <div class="auth-links">
        <a class="link" href="/thongdong/customer/login.php">← Quay lại đăng nhập</a>
      </div>
    </form>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
