<?php
session_start();
require __DIR__ . '/includes/admin-guard.php';

$pageTitle = "Khách hàng - Admin Thong Dong";

/**
 * Demo data (mặt hình thức trước).
 * Sau này nối JSON/DB thì replace $customers.
 */
$customers = [
  [
    'id' => 'C001',
    'name' => 'Tiên',
    'email' => 'tien@gmail.com',
    'phone' => '0939000000',
    'orders' => 3,
    'spent' => 1046000,
    'status' => 'Hoạt động',
    'joined' => '10/12/2025',
  ],
  [
    'id' => 'C002',
    'name' => 'An',
    'email' => 'an@gmail.com',
    'phone' => '0909111222',
    'orders' => 1,
    'spent' => 189000,
    'status' => 'Hoạt động',
    'joined' => '12/12/2025',
  ],
  [
    'id' => 'C003',
    'name' => 'Vy',
    'email' => 'vy@gmail.com',
    'phone' => '0988777666',
    'orders' => 2,
    'spent' => 658000,
    'status' => 'Hoạt động',
    'joined' => '13/12/2025',
  ],
  [
    'id' => 'C004',
    'name' => 'Hà',
    'email' => 'ha@gmail.com',
    'phone' => '0912345678',
    'orders' => 0,
    'spent' => 0,
    'status' => 'Tạm khoá',
    'joined' => '15/12/2025',
  ],
  [
    'id' => 'C005',
    'name' => 'Thanh',
    'email' => 'thanh@gmail.com',
    'phone' => '0939070656',
    'orders' => 1,
    'spent' => 219000,
    'status' => 'Hoạt động',
    'joined' => '16/12/2025',
  ],
];

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? 'all';

function money_vnd($n){
  return number_format((int)$n, 0, ',', '.') . 'đ';
}
function contains_i($haystack, $needle){
  return mb_stripos($haystack, $needle) !== false;
}

$filtered = array_filter($customers, function($c) use ($q, $status){
  $okStatus = ($status === 'all') || ($c['status'] === $status);
  if ($q === '') return $okStatus;

  $s = $c['id'].' '.$c['name'].' '.$c['email'].' '.$c['phone'];
  $okQ = contains_i($s, $q);

  return $okStatus && $okQ;
});

include __DIR__ . '/includes/admin-layout-top.php';
?>

<main class="container admin-main">
  <!-- HEAD CARD -->
  <section class="admin-card admin-head">
    <div class="admin-head-top">
      <div>
        <h1 class="admin-page-title">Khách hàng</h1>
        <p class="admin-page-sub muted">Quản lý danh sách khách mua hàng (demo).</p>
      </div>

      <div class="admin-actions">
        <a class="btn outline" href="/thongdong/admin/customers.php">Làm mới</a>
      </div>
    </div>

    <!-- FILTERS -->
    <form class="admin-filters" method="get" action="/thongdong/admin/customers.php">
      <div class="control">
        <label for="q">Tìm kiếm</label>
        <input
          id="q"
          class="input"
          name="q"
          value="<?php echo htmlspecialchars($q); ?>"
          placeholder="Tên, email, SĐT, mã khách..."
        >
      </div>

      <div class="control">
        <label for="status">Trạng thái</label>
        <select id="status" class="input" name="status">
          <?php
            $opts = ['all'=>'Tất cả','Hoạt động'=>'Hoạt động','Tạm khoá'=>'Tạm khoá'];
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

  <!-- TABLE CARD -->
  <section class="admin-card admin-table-card">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Mã</th>
          <th>Khách</th>
          <th>Email</th>
          <th>SĐT</th>
          <th style="text-align:right;">Số đơn</th>
          <th style="text-align:right;">Chi tiêu</th>
          <th>Trạng thái</th>
          <th>Tham gia</th>
          <th style="text-align:right;">Thao tác</th>
        </tr>
      </thead>

      <tbody>
        <?php if (count($filtered) === 0): ?>
          <tr>
            <td colspan="9" class="muted" style="padding:18px;">
              Không có khách nào phù hợp bộ lọc.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($filtered as $c): ?>
            <tr>
              <td><b><?php echo htmlspecialchars($c['id']); ?></b></td>
              <td><?php echo htmlspecialchars($c['name']); ?></td>
              <td><?php echo htmlspecialchars($c['email']); ?></td>
              <td><?php echo htmlspecialchars($c['phone']); ?></td>
              <td style="text-align:right;"><b><?php echo (int)$c['orders']; ?></b></td>
              <td class="money" style="text-align:right;"><?php echo money_vnd($c['spent']); ?></td>
              <td><span class="status"><?php echo htmlspecialchars($c['status']); ?></span></td>
              <td><?php echo htmlspecialchars($c['joined']); ?></td>

              <td style="text-align:right;">
                <div class="admin-td-actions">
                  <a class="btn outline" href="/thongdong/admin/customers.php?view=<?php echo urlencode($c['id']); ?>">Xem</a>
                  <?php if ($c['status'] === 'Hoạt động'): ?>
                    <a class="btn" href="/thongdong/admin/customers.php?lock=<?php echo urlencode($c['id']); ?>">Khoá</a>
                  <?php else: ?>
                    <a class="btn" href="/thongdong/admin/customers.php?unlock=<?php echo urlencode($c['id']); ?>">Mở</a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="muted" style="padding:10px 12px 14px;">
      *Demo UI. Khi nối dữ liệu thật, mình sẽ cho nút Khoá/Mở cập nhật JSON/DB.
    </div>
  </section>

  <!-- VIEW / LOCK / UNLOCK: demo panel -->
  <?php if (!empty($_GET['view'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 8px;">Hồ sơ khách: <?php echo htmlspecialchars($_GET['view']); ?></h3>
      <div class="muted">Chỗ này sau sẽ show lịch sử đơn hàng của khách + địa chỉ + ghi chú (khi nối dữ liệu thật).</div>
      <div style="margin-top:12px;">
        <a class="btn outline" href="/thongdong/admin/customers.php">Đóng</a>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($_GET['lock'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 8px;">Khoá khách <?php echo htmlspecialchars($_GET['lock']); ?> (demo)</h3>
      <div class="muted">Sau này mình sẽ xử lý lưu trạng thái vào JSON/DB.</div>
      <div style="margin-top:12px; display:flex; gap:10px;">
        <button class="btn" type="button">Xác nhận khoá (demo)</button>
        <a class="btn outline" href="/thongdong/admin/customers.php">Huỷ</a>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($_GET['unlock'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 8px;">Mở khoá khách <?php echo htmlspecialchars($_GET['unlock']); ?> (demo)</h3>
      <div class="muted">Sau này mình sẽ xử lý lưu trạng thái vào JSON/DB.</div>
      <div style="margin-top:12px; display:flex; gap:10px;">
        <button class="btn" type="button">Xác nhận mở (demo)</button>
        <a class="btn outline" href="/thongdong/admin/customers.php">Huỷ</a>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/admin-layout-bottom.php'; ?>
