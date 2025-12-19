<?php
$pageTitle = "Nhật ký - Thong Dong";
require_once '../includes/blog-data.php';
include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:34px 0 70px;">
  <section class="card">
    <h1 style="margin:0 0 8px;">Nhật ký</h1>
    <p class="muted">Những mẩu chuyện nhỏ - chậm lại một chút giữa đời thường.</p>
  </section>

  <section class="blog-grid">
    <?php foreach ($BLOG_POSTS as $post): ?>
      <article class="card blog-card">
        <div class="blog-top">
          <span class="tag"><?php echo htmlspecialchars($post['tag']); ?></span>
          <span class="muted"><?php echo htmlspecialchars($post['date']); ?></span>
        </div>

        <h3 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h3>
        <p class="blog-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>

        <div class="blog-actions">
          <a class="btn small" href="/thongdong/customer/blog-detail.php?id=<?php echo (int)$post['id']; ?>">
            Đọc thêm
          </a>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
