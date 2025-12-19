<?php
session_start();
require_once '../includes/data.php';
require_once '../includes/blog-data.php';

$pageTitle = "Trang chủ - Thong Dong";

// lấy 4 sản phẩm nổi bật (đơn giản: lấy 4 cái đầu)
$featured = array_slice($PRODUCTS, 0, 4);

// tìm bài blog “cách thắp nến…” (mình dùng id=3 theo data đã tạo)
$howto = findPostById(3, $BLOG_POSTS);

include '../includes/customer-layout-top.php';
?>

<main>

  <!-- HERO -->
  <section class="hero">
    <div class="container hero-inner hero-card">
      <div class="hero-logo">
        <img src="/thongdong/assets/img/patterns/thong-donglogo.png" alt="Thong Dong">
      </div>

      <p class="hero-text">Nến thơm thuần Việt dành cho người Việt.</p>

      <div class="hero-actions">
        <a href="/thongdong/customer/shop.php" class="btn">Xem cửa hàng</a>
        <a href="/thongdong/customer/blog.php" class="btn outline">Đọc Nhật ký</a>
      </div>
    </div>
  </section>
<div class="hero-media">
  <img src="/thongdong/assets/img/banner/hero.png" alt="Thong Dong banner">
</div>

  <!-- FEATURED PRODUCTS -->
  <section class="home-section">
    <div class="container">
      <div class="home-head">
        <div>
          <h2 class="home-title">Sản phẩm nổi bật</h2>
          <p class="muted" style="margin:0;">Chọn một mùi hương hợp mood hôm nay.</p>
        </div>
        <a class="btn outline" href="/thongdong/customer/shop.php">Xem tất cả</a>
      </div>

      <div class="home-products">
        <?php foreach ($featured as $p): ?>
          <article class="product-card">
            <a class="product-link" href="/thongdong/customer/product-detail.php?id=<?php echo (int)$p['id']; ?>">
              <div class="product-img">
  <img
    src="<?php echo htmlspecialchars($p['image'] ?? '/thongdong/assets/img/products/placeholder.jpg'); ?>"
    alt="<?php echo htmlspecialchars($p['name'] ?? 'Sản phẩm'); ?>"
    loading="lazy"
  >
</div>

              <div class="product-body">
                <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                <div class="price"><?php echo formatVND($p['price']); ?></div>
                <div class="tag"><?php echo htmlspecialchars($p['tag']); ?></div>
              </div>
            </a>

            <div class="product-actions">
              <a class="btn small" href="/thongdong/customer/product-detail.php?id=<?php echo (int)$p['id']; ?>">Xem chi tiết</a>
              <a class="btn small outline" href="/thongdong/customer/cart.php?add=<?php echo (int)$p['id']; ?>">Thêm giỏ</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- HOW TO (like hình số 3) -->
  <section class="home-howto">
    <div class="container">
      <div class="howto-head">
        <h2 class="home-title" style="margin:8px 0 6px;">Cách thắp nến thơm lâu</h2>
        <p class="muted" style="margin:0;">
          Một quy trình nhỏ để nến thơm đều, ít khói và dùng bền hơn.
        </p>
      </div>

      <div class="howto-steps">
        <div class="howto-step">
          <div class="step-ico">🕯️</div>
          <div class="step-title">Lần đầu thắp đủ lâu</div>
          <div class="muted step-text">Để mặt nến chảy đều 1-2 giờ để tránh lõm.</div>
        </div>

        <div class="howto-step">
          <div class="step-ico">✂️</div>
          <div class="step-title">Cắt tim nến</div>
          <div class="muted step-text">Giữ tim nến ~0.5cm trước mỗi lần thắp để giảm khói.</div>
        </div>

        <div class="howto-step">
          <div class="step-ico">🌬️</div>
          <div class="step-title">Tránh gió mạnh</div>
          <div class="muted step-text">Đặt nến nơi ít gió để cháy đều và thơm ổn định.</div>
        </div>

        <div class="howto-step">
          <div class="step-ico">🧯</div>
          <div class="step-title">Tắt an toàn</div>
          <div class="muted step-text">Dùng nắp/đồ dập nến, không thổi mạnh gây khói.</div>
        </div>
      </div>

      <div class="howto-actions">
        <a class="btn" href="/thongdong/customer/blog-detail.php?id=<?php echo (int)($howto['id'] ?? 3); ?>">
          Hiểu thêm về cách thắp
        </a>
      </div>
    </div>
  </section>

</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
