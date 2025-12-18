<?php
include __DIR__ . '/includes/admin-guard.php';

$pageTitle = "Review - Thong Dong";

// ---- seed demo reviews (không dùng json) ----
if (!isset($_SESSION['admin_reviews'])) {
  $_SESSION['admin_reviews'] = [
    [
      'id' => 'RV2512180001',
      'time' => '06:00 18/12/2025',
      'customer' => 'Tiên',
      'email' => 'tien@gmail.com',
      'product' => 'Nến Quế Ấm',
      'rating' => 5,
      'title' => 'Mùi ấm áp đúng gu',
      'content' => 'Đốt lên thấy thơm nhẹ, không gắt. Đóng gói xinh.',
      'status' => 'approved', // pending | approved | hidden
    ],
    [
      'id' => 'RV2512180002',
      'time' => '06:00 18/12/2025',
      'customer' => 'An',
      'email' => 'an@gmail.com',
      'product' => 'Set Quà “Thong Dong”',
      'rating' => 4,
      'title' => 'Hộp quà sang',
      'content' => 'Rất hợp tặng, mùi ổn. Mong có thêm lựa chọn mùi.',
      'status' => 'pending',
    ],
  ];
}

$reviews = &$_SESSION['admin_reviews'];

// ---- actions: approve/hide/delete ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $id = $_POST['id'] ?? '';

  if ($id) {
    foreach ($reviews as $k => $rv) {
      if ($rv['id'] === $id) {
        if ($action === 'approve') $reviews[$k]['status'] = 'approved';
        if ($action === 'hide')    $reviews[$k]['status'] = 'hidden';
        if ($action === 'pending') $reviews[$k]['status'] = 'pending';
        if ($action === 'delete')  unset($reviews[$k]);
        break;
      }
    }
    // reindex
    $reviews = array_values($reviews);
  }

  header('Location: /thongdong/admin/reviews.php');
  exit;
}

// ---- filters ----
$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? 'all';

$filtered = array_filter($reviews, function($rv) use ($q, $status) {
  $okStatus = ($status === 'all') || ($rv['status'] === $status);

  $haystack = mb_strtolower(
    ($rv['id'] ?? '') . ' ' .
    ($rv['customer'] ?? '') . ' ' .
    ($rv['email'] ?? '') . ' ' .
    ($rv['product'] ?? '') . ' ' .
    ($rv['title'] ?? '') . ' ' .
    ($rv['content'] ?? '')
  );

  $okQ = ($q === '') || (mb_strpos($haystack, mb_strtolower($q)) !== false);
  return $okStatus && $okQ;
});

function statusBadge($st) {
  if ($st === 'approved') return '<span class="badge ok">Đã duyệt</span>';
  if ($st === 'hidden')   return '<span class="badge warn">Đã ẩn</span>';
  return '<span class="badge">Chờ duyệt</span>';
}
function stars($n) {
  $n = (int)$n;
  $s = '';
  for ($i=1;$i<=5;$i++) $s .= ($i <= $n) ? '★' : '☆';
  return $s;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="/thongdong/assets/css/admin.css">
</head>
<body>

<?php include __DIR__ . '/includes/admin-header.php'; ?>

<main class="container admin-page" style="padding:28px 0 70px;">
  <section class="card" style="padding:18px;">
    <div class="page-head">
      <div>
        <h1 class="page-title" style="margin:0 0 8px;">Review</h1>
        <p class="muted" style="margin:0;">Duyệt, ẩn hoặc xoá review khách hàng.</p>
      </div>

      <div class="page-actions">
        <a class="btn outline" href="/thongdong/admin/reviews.php">Làm mới</a>
      </div>
    </div>

    <form method="get" class="admin-filters">
      <div class="control">
        <label class="control-label">Tìm kiếm</label>
        <input class="input" name="q" placeholder="Mã review, khách, email, sản phẩm, nội dung..."
               value="<?php echo htmlspecialchars($q); ?>">
      </div>

      <div class="control">
        <label class="control-label">Trạng thái</label>
        <select class="input" name="status">
          <option value="all"      <?php echo $status==='all'?'selected':''; ?>>Tất cả</option>
          <option value="pending"  <?php echo $status==='pending'?'selected':''; ?>>Chờ duyệt</option>
          <option value="approved" <?php echo $status==='approved'?'selected':''; ?>>Đã duyệt</option>
          <option value="hidden"   <?php echo $status==='hidden'?'selected':''; ?>>Đã ẩn</option>
        </select>
      </div>

      <div class="control" style="align-self:flex-end;">
        <button class="btn small" type="submit">Lọc</button>
      </div>
    </form>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Mã</th>
            <th>Thời gian</th>
            <th>Khách</th>
            <th>Sản phẩm</th>
            <th>Đánh giá</th>
            <th>Nội dung</th>
            <th>Trạng thái</th>
            <th style="text-align:right;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($filtered) === 0): ?>
            <tr>
              <td colspan="8" class="muted" style="padding:16px;">Không có review phù hợp.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($filtered as $rv): ?>
            <tr>
              <td><b>#<?php echo htmlspecialchars($rv['id']); ?></b></td>
              <td><?php echo htmlspecialchars($rv['time']); ?></td>
              <td>
                <b><?php echo htmlspecialchars($rv['customer']); ?></b>
                <div class="muted"><?php echo htmlspecialchars($rv['email']); ?></div>
              </td>
              <td><?php echo htmlspecialchars($rv['product']); ?></td>
              <td class="stars"><?php echo stars($rv['rating']); ?></td>
              <td style="max-width:420px;">
                <b><?php echo htmlspecialchars($rv['title']); ?></b>
                <div class="muted" style="margin-top:4px;">
                  <?php echo htmlspecialchars($rv['content']); ?>
                </div>
              </td>
              <td><?php echo statusBadge($rv['status']); ?></td>
              <td style="text-align:right;">
                <div class="btn-row">
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($rv['id']); ?>">
                    <input type="hidden" name="action" value="approve">
                    <button class="btn small" type="submit">Duyệt</button>
                  </form>

                  <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($rv['id']); ?>">
                    <input type="hidden" name="action" value="hide">
                    <button class="btn small outline" type="submit">Ẩn</button>
                  </form>

                  <form method="post" style="display:inline;" onsubmit="return confirm('Xoá review này nha?');">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($rv['id']); ?>">
                    <input type="hidden" name="action" value="delete">
                    <button class="btn small outline danger" type="submit">Xoá</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </section>
</main>

</body>
</html>
