<?php
session_start();
require_once '../includes/data.php';

$pageTitle = "Review khách hàng - Thong Dong";

// seed demo
if (!isset($_SESSION['reviews'])) {
  $_SESSION['reviews'] = [
    [
      'id' => 'RV' . date('ymdHis') . '01',
      'product_id' => 1,
      'product_name' => 'Nến Quế Ấm',
      'customer_name' => 'Tiên',
      'rating' => 5,
      'title' => 'Mùi ấm áp đúng gu',
      'content' => 'Đốt lên thấy thơm nhẹ, không gắt. Đóng gói xinh.',
      'time' => date('H:i d/m/Y'),
    ],
    [
      'id' => 'RV' . date('ymdHis') . '02',
      'product_id' => 4,
      'product_name' => 'Set Quà “Thong Dong”',
      'customer_name' => 'An',
      'rating' => 4,
      'title' => 'Hộp quà sang',
      'content' => 'Rất hợp tặng, mùi ổn. Mong có thêm lựa chọn mùi.',
      'time' => date('H:i d/m/Y'),
    ],
  ];
}

$pid = isset($_GET['product']) ? (int)$_GET['product'] : 0;
$all = $_SESSION['reviews'] ?? [];

$filtered = array_values(array_filter($all, function($r) use ($pid){
  if ($pid <= 0) return true;
  return (int)($r['product_id'] ?? 0) === $pid;
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

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card review-card">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:12px; flex-wrap:wrap;">
      <div>
        <h1 class="page-title" style="margin:0 0 6px;">Review khách hàng</h1>
        <p class="muted" style="margin:0;">Chỗ này để bà xem mọi người nói gì về nến Thong Dong nè.</p>
      </div>

      <div class="review-actions">
        <a class="btn outline" href="/thongdong/customer/shop.php">Qua cửa hàng</a>
        <a class="btn" href="/thongdong/customer/my-reviews.php">Review của tôi</a>
      </div>
    </div>

    <div class="review-list">
      <?php if (count($filtered) === 0): ?>
        <div class="card" style="padding:16px;">
          <b>Chưa có review nào.</b>
          <div class="muted" style="margin-top:6px;">Bà có thể là người đầu tiên viết review nha.</div>
        </div>
      <?php else: ?>
        <?php foreach ($filtered as $r): ?>
          <div class="review-item">
            <div class="review-left">
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="badge"><?php echo htmlspecialchars($r['product_name'] ?? 'Sản phẩm'); ?></div>
                <?php echo starsHtml($r['rating'] ?? 0); ?>
              </div>

              <h3 class="review-title"><?php echo htmlspecialchars($r['title'] ?? ''); ?></h3>
              <div><?php echo nl2br(htmlspecialchars($r['content'] ?? '')); ?></div>
              <div class="review-meta">
                <?php echo htmlspecialchars($r['customer_name'] ?? 'Khách'); ?> • <?php echo htmlspecialchars($r['time'] ?? ''); ?>
              </div>
            </div>

            <div class="review-right">
              <a class="btn small outline" href="/thongdong/customer/product-detail.php?id=<?php echo (int)($r['product_id'] ?? 0); ?>">Xem sản phẩm</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
