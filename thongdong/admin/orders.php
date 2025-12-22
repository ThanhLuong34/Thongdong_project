<?php
session_start();
require __DIR__ . '/includes/admin-guard.php';

$pageTitle = "Đơn hàng - Admin Thong Dong";
$orders = [
  [
    'id' => 'TD1021',
    'time' => '17/12/2025 10:45',
    'customer' => 'Tiên',
    'phone' => '0939000000',
    'payment' => 'COD',
    'total' => 398000,
    'status' => 'Chờ xử lý',
  ],
  [
    'id' => 'TD1020',
    'time' => '17/12/2025 09:20',
    'customer' => 'An',
    'phone' => '0909111222',
    'payment' => 'BANK',
    'total' => 189000,
    'status' => 'Đang giao',
  ],
  [
    'id' => 'TD1019',
    'time' => '16/12/2025 21:10',
    'customer' => 'Vy',
    'phone' => '0988777666',
    'payment' => 'COD',
    'total' => 459000,
    'status' => 'Hoàn tất',
  ],
  [
    'id' => 'TD1018',
    'time' => '16/12/2025 17:05',
    'customer' => 'Thanh',
    'phone' => '0939070656',
    'payment' => 'BANK',
    'total' => 219000,
    'status' => 'Đã huỷ',
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

$filtered = array_filter($orders, function($o) use ($q, $status){
  $okStatus = ($status === 'all') || ($o['status'] === $status);

  if ($q === '') return $okStatus;

  $s = $o['id'].' '.$o['customer'].' '.$o['phone'];
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
        <h1 class="admin-page-title">Đơn hàng</h1>
        <p class="admin-page-sub muted">Theo dõi và quản lý trạng thái đơn hàng.</p>
      </div>

      <div class="admin-actions">
        <a class="btn outline" href="/thongdong/admin/orders.php">Làm mới</a>
      </div>
    </div>

    <!-- FILTERS in CARD (để chữ rõ) -->
    <form class="admin-filters" method="get" action="/thongdong/admin/orders.php">
      <div class="control">
        <label for="q">Tìm kiếm</label>
        <input
          id="q"
          class="input"
          name="q"
          value="<?php echo htmlspecialchars($q); ?>"
          placeholder="Mã đơn, tên khách, SĐT..."
        >
      </div>

      <div class="control">
        <label for="status">Trạng thái</label>
        <select id="status" class="input" name="status">
          <?php
            $opts = ['all'=>'Tất cả','Chờ xử lý'=>'Chờ xử lý','Đang giao'=>'Đang giao','Hoàn tất'=>'Hoàn tất','Đã huỷ'=>'Đã huỷ'];
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
          <th>Mã đơn</th>
          <th>Thời gian</th>
          <th>Khách</th>
          <th>SĐT</th>
          <th>Thanh toán</th>
          <th style="text-align:right;">Tổng</th>
          <th>Trạng thái</th>
          <th style="text-align:right;">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($filtered) === 0): ?>
          <tr>
            <td colspan="8" class="muted" style="padding:18px;">
              Không có đơn nào phù hợp bộ lọc.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($filtered as $o): ?>
            <tr>
              <td><b>#<?php echo htmlspecialchars($o['id']); ?></b></td>
              <td><?php echo htmlspecialchars($o['time']); ?></td>
              <td><?php echo htmlspecialchars($o['customer']); ?></td>
              <td><?php echo htmlspecialchars($o['phone']); ?></td>
              <td><b><?php echo htmlspecialchars($o['payment']); ?></b></td>
              <td class="money" style="text-align:right;"><?php echo money_vnd($o['total']); ?></td>
              <td><span class="status"><?php echo htmlspecialchars($o['status']); ?></span></td>
              <td style="text-align:right;">
                <div class="admin-td-actions">
                  <a class="btn outline" href="/thongdong/admin/orders.php?view=<?php echo urlencode($o['id']); ?>">Xem</a>
                  <a class="btn" href="/thongdong/admin/orders.php?update=<?php echo urlencode($o['id']); ?>">Cập nhật</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="muted" style="padding:10px 12px 14px;">
    </div>
  </section>

  <?php if (!empty($_GET['view'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 8px;">Xem đơn #<?php echo htmlspecialchars($_GET['view']); ?></h3>
      <div class="muted">Chỗ này mình sẽ render chi tiết đơn (items, địa chỉ, ghi chú...) khi Tiên nối dữ liệu thật.</div>
      <div style="margin-top:12px;">
        <a class="btn outline" href="/thongdong/admin/orders.php">Đóng</a>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($_GET['update'])): ?>
    <section class="admin-card" style="padding:16px; margin-top:14px;">
      <h3 style="margin:0 0 8px;">Cập nhật trạng thái #<?php echo htmlspecialchars($_GET['update']); ?></h3>

      <form method="post" action="/thongdong/admin/orders.php" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
        <div class="control" style="min-width:240px; display:flex; flex-direction:column; gap:6px;">
          <label class="muted">Trạng thái mới</label>
          <select class="input" name="new_status">
            <option>Chờ xử lý</option>
            <option>Đang giao</option>
            <option>Hoàn tất</option>
            <option>Đã huỷ</option>
          </select>
        </div>

        <button class="btn" type="button">Lưu (demo)</button>
        <a class="btn outline" href="/thongdong/admin/orders.php">Huỷ</a>
      </form>

      <div class="muted" style="margin-top:10px;">
        *Demo UI. Khi nối dữ liệu thật, nút Lưu sẽ cập nhật JSON/DB.
      </div>
    </section>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/admin-layout-bottom.php'; ?>

