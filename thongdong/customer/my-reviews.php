<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Review của tôi - Thong Dong";

if (empty($_SESSION['customer'])) {
  header('Location: /thongdong/customer/login.php');
  exit;
}

$email = $_SESSION['customer']['email'] ?? '';
$all = $_SESSION['reviews'] ?? [];
$mine = array_values(array_filter($all, function($r) use ($email){
  return ($r['customer_email'] ?? '') === $email;
}));

function starsHtml($rating){
  $rating = (int)$rating;
  $html = '<div class="stars" aria-label="Rating '.$rating.'/5">';
  for ($i=1; $i<=5; $i++){
    $on = $i <= $rating ? 'on' : '';
    $html .= '<span class="star '.$on.'">★</span>';
  }
  $html .= '</div>';
  return $html;
}

$success = !empty($_GET['success']);

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card review-card">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:12px; flex-wrap:wrap;">
      <div>
        <h1 class="page-title" style="margin:0 0 6px;">Review của tôi</h1>
        <p class="muted" style="margin:0;">Tổng: <b><?php echo count($mine); ?></b> review</p>
      </div>
      <div class="review-actions">
        <a class="btn outline" href="/thongdong/customer/account.php">Về tài khoản</a>
        <a class="btn" href="/thongdong/customer/reviews.php">Xem review cộng đồng</a>
      </div>
    </div>

    <?php if ($success): ?>
      <div class="auth-alert" style="margin-top:12px;">
        <div><b>Đã gửi review thành công!</b> Cảm ơn bà nhiều nha.</div>
      </div>
    <?php endif; ?>

    <div class="review-list">
      <?php if (count($mine) === 0): ?>
        <div class="card" style="padding:16px;">
          <b>Bà chưa viết review nào.</b>
          <div class="muted" style="margin-top:6px;">Vào trang sản phẩm rồi bấm “Viết review” nha.</div>
        </div>
      <?php else: ?>
        <?php foreach ($mine as $r): ?>
          <div class="review-item">
            <div class="review-left">
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="badge"><?php echo htmlspecialchars($r['product_name'] ?? 'Sản phẩm'); ?></div>
                <?php echo starsHtml($r['rating'] ?? 0); ?>
              </div>
              <h3 class="review-title"><?php echo htmlspecialchars($r['title'] ?? ''); ?></h3>
              <div><?php echo nl2br(htmlspecialchars($r['content'] ?? '')); ?></div>
              <div class="review-meta"><?php echo htmlspecialchars($r['time'] ?? ''); ?></div>
            </div>

            <div class="review-right">
              <a class="btn small outline" href="/thongdong/customer/product-detail.php?id=<?php echo (int)($r['product_id'] ?? 0); ?>">Xem</a>
              <a class="btn small outline" href="/thongdong/customer/reviews.php?product=<?php echo (int)($r['product_id'] ?? 0); ?>">Review SP</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
