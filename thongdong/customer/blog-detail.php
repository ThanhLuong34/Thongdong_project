<?php
$pageTitle = "Bài viết - Thong Dong";
require_once '../includes/blog-data.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = findPostById($id, $BLOG_POSTS);

include '../includes/customer-layout-top.php';

/**
 * Detect image path line inside content
 * Accept: /thongdong/... .jpg/.jpeg/.png/.webp (case-insensitive)
 */
function is_image_path_line(string $s): bool {
  $s = trim($s);
  if ($s === '') return false;
  if (stripos($s, '/thongdong/') !== 0) return false;
  return (bool)preg_match('/\.(jpg|jpeg|png|webp)$/i', $s);
}
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
        <span class="tag"><?php echo htmlspecialchars($post['tag'] ?? ''); ?></span>
        <span class="muted"><?php echo htmlspecialchars($post['date'] ?? ''); ?></span>
      </div>

      <h1 class="blog-detail-title"><?php echo htmlspecialchars($post['title'] ?? ''); ?></h1>

      <?php
        // 1) ưu tiên field image nếu có
        $heroImage = trim((string)($post['image'] ?? ''));

        // 2) nếu không có image, tìm ảnh trong content (dòng là đường dẫn)
        if ($heroImage === '' && !empty($post['content']) && is_array($post['content'])) {
          foreach ($post['content'] as $para) {
            if (is_string($para) && is_image_path_line($para)) {
              $heroImage = trim($para);
              break;
            }
          }
        }
      ?>

      <?php if ($heroImage !== ''): ?>
        <div class="blog-hero-img">
          <img
            src="<?php echo htmlspecialchars($heroImage); ?>"
            alt="<?php echo htmlspecialchars($post['title'] ?? 'Ảnh bài viết'); ?>"
            loading="lazy"
          >
        </div>
      <?php endif; ?>

      <?php
      
        if (!empty($post['content']) && is_array($post['content'])):
          foreach ($post['content'] as $para):
            if (!is_string($para)) continue;

            // nếu là link ảnh -> bỏ qua để không in ra text
            if (is_image_path_line($para)) continue;
      ?>
        <p class="blog-para"><?php echo htmlspecialchars($para); ?></p>
      <?php
          endforeach;
        endif;
      ?>

      <div style="margin-top:16px;">
        <a class="btn outline" href="/thongdong/customer/blog.php">← Quay lại Nhật ký</a>
        <a class="btn" href="/thongdong/customer/shop.php">Xem cửa hàng</a>
      </div>

    </article>
  <?php endif; ?>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
