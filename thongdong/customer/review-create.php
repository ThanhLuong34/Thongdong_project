<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Viết review - Thong Dong";

// bắt login
if (empty($_SESSION['customer'])) {
  header('Location: /thongdong/customer/login.php');
  exit;
}

$pid = isset($_GET['product']) ? (int)$_GET['product'] : (int)($_POST['product_id'] ?? 0);
$product = $pid ? findProductById($pid, $PRODUCTS) : null;

if (!$product) {
  // không có product thì quay shop
  header('Location: /thongdong/customer/shop.php');
  exit;
}

// init reviews store
if (!isset($_SESSION['reviews'])) $_SESSION['reviews'] = [];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rating = (int)($_POST['rating'] ?? 0);
  $title  = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');

  if ($rating < 1 || $rating > 5) $errors[] = "Vui lòng chọn số sao (1–5).";
  if ($title === '') $errors[] = "Vui lòng nhập tiêu đề review.";
  if ($content === '') $errors[] = "Vui lòng nhập nội dung review.";

  if (!$errors) {
    $review = [
      'id' => 'RV' . date('ymdHis') . rand(10,99),
      'product_id' => $pid,
      'product_name' => $product['name'],
      'customer_email' => $_SESSION['customer']['email'] ?? '',
      'customer_name' => $_SESSION['customer']['name'] ?? 'Khách',
      'rating' => $rating,
      'title' => $title,
      'content' => $content,
      'time' => date('H:i d/m/Y'),
    ];

    array_unshift($_SESSION['reviews'], $review);
    $success = true;

    // PRG
    header('Location: /thongdong/customer/my-reviews.php?success=1');
    exit;
  }
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card review-card">
    <h1 class="page-title" style="margin:0 0 8px;">Viết review</h1>
    <p class="muted" style="margin:0 0 14px;">
      Sản phẩm: <b><?php echo htmlspecialchars($product['name']); ?></b>
    </p>

    <?php if ($errors): ?>
      <div class="auth-alert">
        <?php foreach ($errors as $e): ?>
          <div>• <?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="review-form">
      <input type="hidden" name="product_id" value="<?php echo (int)$pid; ?>">

      <div class="form-group">
        <label>Số sao *</label>
        <select class="input" name="rating" required>
          <option value="">Chọn...</option>
          <?php for ($i=5; $i>=1; $i--): ?>
            <option value="<?php echo $i; ?>" <?php echo ((int)($_POST['rating'] ?? 0) === $i) ? 'selected' : ''; ?>>
              <?php echo $i; ?> sao
            </option>
          <?php endfor; ?>
        </select>
        <div class="review-help">Tip: 5 sao nếu “đúng gu”, 4 sao nếu “ok nhưng muốn cải thiện”, v.v.</div>
      </div>

      <div class="form-group">
        <label>Tiêu đề *</label>
        <input class="input" name="title" placeholder="Ví dụ: Mùi rất dễ chịu" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
      </div>

      <div class="form-group">
        <label>Nội dung *</label>
        <textarea class="input" name="content" rows="5" placeholder="Bạn chia sẻ cảm nhận thật nha..." required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
      </div>

      <div class="review-actions">
        <button class="btn" type="submit">Gửi review</button>
        <a class="btn outline" href="/thongdong/customer/product-detail.php?id=<?php echo (int)$pid; ?>">Quay lại sản phẩm</a>
        <a class="btn outline" href="/thongdong/customer/reviews.php?product=<?php echo (int)$pid; ?>">Xem review sản phẩm</a>
      </div>
    </form>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
