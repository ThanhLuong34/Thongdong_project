<?php
$pageTitle = "Bài viết - Thong Dong";
require_once '../includes/blog-data.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = findPostById($id, $BLOG_POSTS);

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <?php if (!$post): ?>
    <section class="card">
      <h1 style="margin:0 0 8px;">Không tìm thấy bài viết</h1>
      <p class="muted">Bạn thử quay lại trang Nhật ký nha.</p>
      <a class="btn" href="/thongdong/customer/blog.php">Về Nhật ký</a>
    </section>
  <?php else: ?>
    <article class="card blog-detail">
      <div class="blog-top">
        <span class="tag"><?php echo htmlspecialchars($post['tag']); ?></span>
        <span class="muted"><?php echo htmlspecialchars($post['date']); ?></span>
      </div>

      <h1 class="blog-detail-title"><?php echo htmlspecialchars($post['title']); ?></h1>

      <?php foreach ($post['content'] as $para): ?>
        <p class="blog-para"><?php echo htmlspecialchars($para); ?></p>
      <?php endforeach; ?>

      <div style="margin-top:16px;">
        <a class="btn outline" href="/thongdong/customer/blog.php">← Quay lại Nhật ký</a>
        <a class="btn" href="/thongdong/customer/shop.php">Xem cửa hàng</a>
      </div>
    </article>
  <?php endif; ?>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
