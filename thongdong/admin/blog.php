<?php
session_start();
require __DIR__ . '/includes/admin-guard.php';

$pageTitle = "Nhật ký - Admin Thong Dong";

$posts = [
  [
    'id' => 'B001',
    'title' => 'Thong Dong và một góc Tết trong căn phòng nhỏ',
    'excerpt' => 'Có những ngày mình chỉ muốn chậm lại, thắp một mùi hương ấm và nghe phố thở...',
    'category' => 'Tết Thương',
    'date' => '17/12/2025',
    'status' => 'Đã đăng',
    'img' => '/thongdong/assets/img/about/tet-1.jpg',
  ],
  [
    'id' => 'B002',
    'title' => 'Cách thắp nến lâu: 5 mẹo để nến cháy đều và thơm hơn',
    'excerpt' => 'Tỉa bấc, lần đốt đầu tiên, tránh gió, dùng nắp dập… làm đúng là nến bền hẳn.',
    'category' => 'Hướng dẫn',
    'date' => '16/12/2025',
    'status' => 'Đã đăng',
    'img' => '/thongdong/assets/img/about/tet-2.jpg',
  ],
  [
    'id' => 'B003',
    'title' => 'Thuần Việt – vì sao mùi hương lại gợi ký ức?',
    'excerpt' => 'Mùi sen, trà, bưởi… đôi khi không chỉ là mùi, mà là một đoạn ký ức quay về.',
    'category' => 'Thuần Việt',
    'date' => '15/12/2025',
    'status' => 'Nháp',
    'img' => '/thongdong/assets/img/about/tet-3.png',
  ],
];

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? 'all';

function contains_i($haystack, $needle){
  return mb_stripos($haystack, $needle) !== false;
}

$filtered = array_filter($posts, function($p) use ($q, $status){
  $okStatus = ($status === 'all') || ($p['status'] === $status);
  if ($q === '') return $okStatus;

  $s = $p['id'].' '.$p['title'].' '.$p['excerpt'].' '.$p['category'].' '.$p['date'];
  return $okStatus && contains_i($s, $q);
});

include __DIR__ . '/includes/admin-layout-top.php';
?>

<main class="container admin-main">
  <!-- HEAD -->
  <section class="admin-card admin-head">
    <div class="admin-head-top">
      <div>
        <h1 class="admin-page-title">Nhật ký</h1>
        <p class="admin-page-sub muted"></p>
      </div>

      <div class="admin-actions">
        <a class="btn" href="/thongdong/admin/blog.php?new=1">+ Thêm bài</a>
        <a class="btn outline" href="/thongdong/admin/blog.php">Làm mới</a>
      </div>
    </div>

    <!-- FILTERS -->
    <form class="admin-filters" method="get" action="/thongdong/admin/blog.php">
      <div class="control">
        <label for="q">Tìm kiếm</label>
        <input
          id="q"
          class="input"
          name="q"
          value="<?php echo htmlspecialchars($q); ?>"
          placeholder="Tiêu đề, nội dung ngắn, danh mục..."
        >
      </div>

      <div class="control">
        <label for="status">Trạng thái</label>
        <select id="status" class="input" name="status">
          <?php
            $opts = ['all'=>'Tất cả','Đã đăng'=>'Đã đăng','Nháp'=>'Nháp'];
            foreach ($opts as $val => $label) {
              $sel = ($status === $val) ? 'selected' : '';
              echo "<option value=\"".htmlspecialchars($val)."\" $sel>".htmlspecialchars($label)."</option>";
            }
          ?>
        </select>
      </div>

      <button class="btn" type="submit">Lọc</button>
    </form>
  </section>

  <!-- GRID POSTS -->
  <section class="admin-blog-grid">
    <?php if (count($filtered) === 0): ?>
      <div class="admin-card" style="padding:16px;">
        <b>Không có bài phù hợp bộ lọc.</b>
        <div class="muted" style="margin-top:6px;">Thử đổi từ khoá hoặc trạng thái nha.</div>
      </div>
    <?php else: ?>
      <?php foreach ($filtered as $p): ?>
        <article class="admin-card admin-blog-card">
          <div class="admin-blog-thumb">
            <img
              src="<?php echo htmlspecialchars($p['img']); ?>"
              alt="<?php echo htmlspecialchars($p['title']); ?>"
              onerror="this.style.display='none'; this.parentElement.classList.add('noimg');"
            >
            <div class="admin-blog-thumb-fallback">Ảnh</div>
          </div>

          <div class="admin-blog-body">
            <div class="admin-blog-meta">
              <span class="pill"><?php echo htmlspecialchars($p['category']); ?></span>
              <span class="muted"><?php echo htmlspecialchars($p['date']); ?></span>
              <span class="status"><?php echo htmlspecialchars($p['status']); ?></span>
            </div>

            <h3 class="admin-blog-title"><?php echo htmlspecialchars($p['title']); ?></h3>
            <p class="admin-blog-excerpt"><?php echo htmlspecialchars($p['excerpt']); ?></p>

            <div class="admin-blog-actions">
              <a class="btn outline" href="/thongdong/admin/blog.php?view=<?php echo urlencode($p['id']); ?>">Xem</a>
              <a class="btn" href="/thongdong/admin/blog.php?edit=<?php echo urlencode($p['id']); ?>">Sửa</a>
              <a class="btn outline" href="/thongdong/admin/blog.php?delete=<?php echo urlencode($p['id']); ?>">Xoá</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <!-- PANELS (DEMO) -->
  <?php if (!empty($_GET['new'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 10px;">Thêm bài</h3>
      <form class="admin-form" method="post" action="#">
        <div class="form-row">
          <div class="control">
            <label>Tiêu đề</label>
            <input class="input" placeholder="VD: Cách thắp nến lâu..." />
          </div>
          <div class="control">
            <label>Danh mục</label>
            <input class="input" placeholder="VD: Hướng dẫn / Thuần Việt / Tết – Đỏ Vàng" />
          </div>
        </div>

        <div class="form-row">
          <div class="control">
            <label>Ảnh thumbnail (URL/path)</label>
            <input class="input" placeholder="/thongdong/assets/img/blog/blog-4.jpg" />
          </div>
          <div class="control">
            <label>Trạng thái</label>
            <select class="input">
              <option>Nháp</option>
              <option>Đã đăng</option>
            </select>
          </div>
        </div>

        <div class="control">
          <label>Mô tả ngắn</label>
          <textarea class="input" rows="3" placeholder="Đoạn teaser hiển thị trên card..."></textarea>
        </div>

        <div class="control">
          <label>Nội dung</label>
          <textarea class="input" rows="7" placeholder="Nội dung bài viết..."></textarea>
        </div>

        <div class="admin-td-actions" style="justify-content:flex-start; margin-top:12px;">
          <button class="btn" type="button">Lưu</button>
          <a class="btn outline" href="/thongdong/admin/blog.php">Huỷ</a>
        </div>
      </form>
      <div class="muted" style="margin-top:10px;">
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($_GET['edit'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 10px;">Sửa bài <?php echo htmlspecialchars($_GET['edit']); ?></h3>
      <div class="muted"></div>
      <div class="admin-td-actions" style="justify-content:flex-start; margin-top:12px;">
        <a class="btn" href="/thongdong/admin/blog.php?edit=<?php echo urlencode($_GET['edit']); ?>&save=1">Lưu</a>
        <a class="btn outline" href="/thongdong/admin/blog.php">Đóng</a>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($_GET['view'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 10px;">Xem bài <?php echo htmlspecialchars($_GET['view']); ?></h3>
      <div class="muted">Sau này sẽ hiển thị bài đầy đủ + preview giống customer/blog-detail.</div>
      <div class="admin-td-actions" style="justify-content:flex-start; margin-top:12px;">
        <a class="btn outline" href="/thongdong/admin/blog.php">Đóng</a>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($_GET['delete'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 10px;">Xoá bài <?php echo htmlspecialchars($_GET['delete']); ?></h3>
      <div class="muted"></div>
      <div class="admin-td-actions" style="justify-content:flex-start; margin-top:12px;">
        <button class="btn" type="button">Xác nhận xoá</button>
        <a class="btn outline" href="/thongdong/admin/blog.php">Huỷ</a>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/admin-layout-bottom.php'; ?>
