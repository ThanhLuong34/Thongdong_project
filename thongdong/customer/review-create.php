<?php
session_start();
require_once __DIR__ . '/../includes/data.php';

$pageTitle = "Viết review - Thong Dong";

/**
 * Nếu bạn login bằng session key khác (vd: user/customer_email),
 * sửa đúng ở đây cho khớp.
 */
if (empty($_SESSION['customer'])) {
  // nếu muốn: giữ đường quay lại sau login
  $next = '/thongdong/customer/review-create.php?id=' . (int)($_GET['id'] ?? 0);
  header('Location: /thongdong/customer/login.php?next=' . urlencode($next));
  exit;
}

// Nhận product id
$pid = (int)($_GET['id'] ?? 0);
if ($pid <= 0) $pid = (int)($_GET['product'] ?? 0);
if ($pid <= 0) $pid = (int)($_POST['product_id'] ?? 0);

// Tìm product từ $PRODUCTS (đồng bộ với product-detail.php)
$product = ($pid > 0) ? findProductById($pid, $PRODUCTS) : null;

// Nếu không tìm thấy, HIỆN THÔNG BÁO (đừng redirect âm thầm nữa)
if (!$product) {
  include __DIR__ . '/../includes/customer-layout-top.php';
  ?>
  <main class="container" style="padding:32px 0 70px;">
    <section class="card">
      <h1 style="margin:0 0 8px;">Không tìm thấy sản phẩm để review</h1>
      <p class="muted" style="margin:0 0 16px;">
        Có thể ID sản phẩm không hợp lệ hoặc dữ liệu sản phẩm chưa đồng bộ.
      </p>
      <a class="btn" href="/thongdong/customer/shop.php">Quay lại cửa hàng</a>
      <a class="btn outline" href="/thongdong/customer/product-detail.php?id=<?php echo (int)$pid; ?>">Quay lại sản phẩm</a>
    </section>
  </main>
  <?php
  include __DIR__ . '/../includes/customer-layout-bottom.php';
  exit;
}

// init reviews store
if (!isset($_SESSION['reviews'])) $_SESSION['reviews'] = [];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rating  = (int)($_POST['rating'] ?? 0);
  $title   = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');

  if ($rating < 1 || $rating > 5) $errors[] = "Vui lòng chọn số sao (1–5).";
  if ($title === '') $errors[] = "Vui lòng nhập tiêu đề review.";
  if ($content === '') $errors[] = "Vui lòng nhập nội dung review.";

  if (!$errors) {
    $review = [
      'id' => 'RV' . date('ymdHis') . rand(10, 99),
      'product_id' => (int)$pid,
      'product_name' => $product['name'] ?? '',
      'customer_email' => $_SESSION['customer']['email'] ?? '',
      'customer_name'  => $_SESSION['customer']['name'] ?? 'Khách',
      'rating' => $rating,
      'title' => $title,
      'content' => $content,
      'time' => date('H:i d/m/Y'),
    ];

    array_unshift($_SESSION['reviews'], $review);

    header('Location: /thongdong/customer/my-reviews.php?success=1');
    exit;
  }
}

include __DIR__ . '/../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card review-card">
    <h1 class="page-title" style="margin:0 0 8px;">Viết review</h1>
    <p class="muted" style="margin:0 0 14px;">
      Sản phẩm: <b><?php echo htmlspecialchars($product['name'] ?? '') ?></b>
    </p>

    <?php if ($errors): ?>
      <div class="auth-alert">
        <?php foreach ($errors as $e): ?>
          <div>• <?php echo htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="review-form">
      <input type="hidden" name="product_id" value="<?php echo (int)$pid ?>">

      <div class="form-group">
        <label>Số sao *</label>
        <select class="input" name="rating" required>
          <option value="">Chọn...</option>
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?php echo $i; ?>" <?php echo ((int)($_POST['rating'] ?? 0) === $i) ? 'selected' : ''; ?>>
              <?php echo $i; ?> sao
            </option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Tiêu đề *</label>
        <input class="input" name="title" placeholder="Ví dụ: Mùi rất dễ chịu"
               value="<?php echo htmlspecialchars($_POST['title'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Nội dung *</label>
        <textarea class="input" name="content" rows="5" placeholder="Bạn chia sẻ cảm nhận thật nha..." required><?php echo htmlspecialchars($_POST['content'] ?? '') ?></textarea>
      </div>

      <div class="review-actions">
        <button class="btn" type="submit">Gửi review</button>
        <a class="btn outline" href="/thongdong/customer/product-detail.php?id=<?php echo (int)$pid; ?>">Quay lại sản phẩm</a>
        <a class="btn outline" href="/thongdong/customer/reviews.php?product=<?php echo (int)$pid; ?>">Xem review sản phẩm</a>
      </div>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/customer-layout-bottom.php'; ?>
