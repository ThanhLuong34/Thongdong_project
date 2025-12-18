<?php
session_start();
include __DIR__ . '/includes/admin-guard.php';

$pageTitle = "Đổi trả & Hoàn tiền - Admin | Thong Dong";

// Lấy danh sách yêu cầu đổi/trả từ session
$requests = $_SESSION['return_requests'] ?? [];

// ---- helpers ----
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function badgeClass($status){
  $s = mb_strtolower($status);
  if (strpos($s, 'hoàn tất') !== false) return 'badge success';
  if (strpos($s, 'từ chối') !== false) return 'badge danger';
  if (strpos($s, 'đã nhận') !== false) return 'badge info';
  if (strpos($s, 'đang xử') !== false) return 'badge warn';
  return 'badge';
}
function typeLabel($t){
  return $t === 'refund' ? 'Hoàn tiền' : 'Đổi hàng';
}

// ---- update status ----
$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
  $rid = trim($_POST['rid'] ?? '');
  $newStatus = trim($_POST['status'] ?? '');

  if ($rid && $newStatus) {
    foreach ($requests as $i => $r) {
      if (($r['id'] ?? '') === $rid) {
        $requests[$i]['status'] = $newStatus;
        $requests[$i]['updated'] = date('H:i d/m/Y');
        break;
      }
    }
    $_SESSION['return_requests'] = $requests;
    $flash = "Đã cập nhật trạng thái cho $rid.";
  }
}

// ---- filter/search ----
$q = trim($_GET['q'] ?? '');
$fType = $_GET['type'] ?? 'all';     // all | exchange | refund
$fStatus = $_GET['status'] ?? 'all'; // all | ...
// status options (demo)
$statusOptions = [
  'Chờ xử lý',
  'Đang xử lý',
  'Đã nhận hàng',
  'Đã hoàn tiền',
  'Đã đổi hàng',
  'Từ chối',
];

$filtered = array_filter($requests, function($r) use ($q, $fType, $fStatus){
  $id = $r['id'] ?? '';
  $oid = $r['order_id'] ?? '';
  $type = $r['type'] ?? 'exchange';
  $status = $r['status'] ?? '';

  $name = $r['contact']['name'] ?? '';
  $phone = $r['contact']['phone'] ?? '';
  $reason = $r['reason'] ?? '';

  $okQ = true;
  if ($q !== '') {
    $hay = mb_strtolower($id.' '.$oid.' '.$name.' '.$phone.' '.$reason);
    $okQ = (mb_strpos($hay, mb_strtolower($q)) !== false);
  }

  $okType = ($fType === 'all') || ($type === $fType);
  $okStatus = ($fStatus === 'all') || ($status === $fStatus);

  return $okQ && $okType && $okStatus;
});

// sort newest first (created time string not reliable, but we unshift so already newest first)
$filtered = array_values($filtered);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?php echo h($pageTitle); ?></title>
  <link rel="stylesheet" href="/thongdong/assets/css/admin.css">
</head>
<body>

<?php include __DIR__ . '/includes/admin-header.php'; ?>

<main class="container" style="padding:28px 0 70px;">
  <section class="card" style="padding:18px;">
    <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 6px;">Đổi trả & Hoàn tiền</h1>
        <p class="muted" style="margin:0;">Quản lý yêu cầu đổi hàng và refund từ khách.</p>
      </div>
      <a class="btn outline small" href="/thongdong/admin/returns.php">Làm mới</a>
    </div>

    <?php if ($flash): ?>
      <div class="auth-alert" style="margin-top:14px;">
        <?php echo h($flash); ?>
      </div>
    <?php endif; ?>

    <!-- Filters -->
    <form method="get" class="card" style="padding:14px; margin-top:14px;">
      <div class="filters" style="display:grid; grid-template-columns: 1.2fr 0.8fr 0.8fr 0.4fr; gap:12px;">
        <div class="form-group" style="margin:0;">
          <label>Tìm kiếm</label>
          <input class="input" name="q" value="<?php echo h($q); ?>" placeholder="Mã yêu cầu, mã đơn, tên khách, SĐT, lý do...">
        </div>
        <div class="form-group" style="margin:0;">
          <label>Loại</label>
          <select class="input" name="type">
            <option value="all" <?php echo $fType==='all'?'selected':''; ?>>Tất cả</option>
            <option value="exchange" <?php echo $fType==='exchange'?'selected':''; ?>>Đổi hàng</option>
            <option value="refund" <?php echo $fType==='refund'?'selected':''; ?>>Hoàn tiền</option>
          </select>
        </div>
        <div class="form-group" style="margin:0;">
          <label>Trạng thái</label>
          <select class="input" name="status">
            <option value="all" <?php echo $fStatus==='all'?'selected':''; ?>>Tất cả</option>
            <?php foreach ($statusOptions as $s): ?>
              <option value="<?php echo h($s); ?>" <?php echo $fStatus===$s?'selected':''; ?>>
                <?php echo h($s); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin:0; display:flex; align-items:flex-end;">
          <button class="btn" type="submit" style="width:100%;">Lọc</button>
        </div>
      </div>
    </form>

    <!-- Table -->
    <div style="margin-top:14px; overflow:auto;">
      <table class="table" style="width:100%; min-width:980px;">
        <thead>
          <tr>
            <th>Mã yêu cầu</th>
            <th>Thời gian</th>
            <th>Mã đơn</th>
            <th>Khách</th>
            <th>SĐT</th>
            <th>Loại</th>
            <th>Lý do</th>
            <th>Trạng thái</th>
            <th style="text-align:right;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($filtered) === 0): ?>
            <tr>
              <td colspan="9" class="muted" style="padding:14px;">Chưa có yêu cầu nào.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($filtered as $r): ?>
              <?php
                $rid = $r['id'] ?? '';
                $oid = $r['order_id'] ?? '';
                $created = $r['created'] ?? '';
                $type = $r['type'] ?? 'exchange';
                $reason = $r['reason'] ?? '';
                $status = $r['status'] ?? 'Chờ xử lý';
                $name = $r['contact']['name'] ?? '';
                $phone = $r['contact']['phone'] ?? '';
                $detail = $r['detail'] ?? '';
                $email = $r['contact']['email'] ?? '';

                $bank = $r['refund_bank'] ?? null; // only refund
              ?>
              <tr>
                <td><b><?php echo h($rid); ?></b></td>
                <td><?php echo h($created); ?></td>
                <td><?php echo h($oid); ?></td>
                <td><?php echo h($name); ?></td>
                <td><?php echo h($phone); ?></td>
                <td><?php echo h(typeLabel($type)); ?></td>
                <td><?php echo h($reason); ?></td>
                <td><span class="<?php echo h(badgeClass($status)); ?>"><?php echo h($status); ?></span></td>
                <td style="text-align:right;">
                  <button class="btn outline small"
                    type="button"
                    onclick="openDetail(<?php echo h(json_encode([
                      'rid'=>$rid,'oid'=>$oid,'created'=>$created,'type'=>$type,
                      'reason'=>$reason,'detail'=>$detail,'status'=>$status,
                      'name'=>$name,'phone'=>$phone,'email'=>$email,
                      'bank'=>$bank,
                      'updated'=>$r['updated'] ?? ''
                    ], JSON_UNESCAPED_UNICODE)); ?>)">
                    Xem
                  </button>

                  <button class="btn small" type="button" onclick="openUpdate('<?php echo h($rid); ?>','<?php echo h($status); ?>')">
                    Cập nhật
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<!-- Detail Modal -->
<div id="detailModal" class="modal" style="display:none;">
  <div class="modal-backdrop" onclick="closeModal('detailModal')"></div>
  <div class="modal-card card" style="max-width:820px; width:calc(100% - 24px); padding:16px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
      <div>
        <h2 style="margin:0 0 6px;">Chi tiết yêu cầu</h2>
        <div class="muted" id="dSub"></div>
      </div>
      <button class="btn outline small" type="button" onclick="closeModal('detailModal')">Đóng</button>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:14px;">
      <div class="card" style="padding:12px;">
        <b>Thông tin yêu cầu</b>
        <div class="muted" style="margin-top:8px;" id="dInfo"></div>
      </div>
      <div class="card" style="padding:12px;">
        <b>Liên hệ</b>
        <div class="muted" style="margin-top:8px;" id="dContact"></div>
      </div>
    </div>

    <div class="card" style="padding:12px; margin-top:12px;">
      <b>Mô tả chi tiết</b>
      <div style="margin-top:8px; white-space:pre-wrap;" id="dDetail"></div>
    </div>

    <div class="card" style="padding:12px; margin-top:12px; display:none;" id="dBankBox">
      <b>Thông tin hoàn tiền</b>
      <div class="muted" style="margin-top:8px;" id="dBank"></div>
    </div>
  </div>
</div>

<!-- Update Modal -->
<div id="updateModal" class="modal" style="display:none;">
  <div class="modal-backdrop" onclick="closeModal('updateModal')"></div>
  <div class="modal-card card" style="max-width:560px; width:calc(100% - 24px); padding:16px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
      <div>
        <h2 style="margin:0 0 6px;">Cập nhật trạng thái</h2>
        <div class="muted" id="uSub"></div>
      </div>
      <button class="btn outline small" type="button" onclick="closeModal('updateModal')">Đóng</button>
    </div>

    <form method="post" style="margin-top:14px;">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="rid" id="uRid" value="">

      <div class="form-group">
        <label>Trạng thái mới</label>
        <select class="input" name="status" id="uStatus">
          <?php foreach ($statusOptions as $s): ?>
            <option value="<?php echo h($s); ?>"><?php echo h($s); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button class="btn" type="submit" style="width:100%;">Lưu</button>

      <div class="muted" style="margin-top:10px;">
        (Demo) Cập nhật sẽ lưu vào session <code>return_requests</code>.
      </div>
    </form>
  </div>
</div>

<script>
function closeModal(id){ document.getElementById(id).style.display = 'none'; }
function openModal(id){ document.getElementById(id).style.display = 'block'; }

function openDetail(data){
  document.getElementById('dSub').textContent =
    `Mã: ${data.rid} • Đơn: ${data.oid} • ${data.type === 'refund' ? 'Hoàn tiền' : 'Đổi hàng'} • Trạng thái: ${data.status}`;

  document.getElementById('dInfo').innerHTML =
    `<div>Thời gian: <b>${data.created || '-'}</b></div>
     <div>Mã đơn: <b>${data.oid || '-'}</b></div>
     <div>Lý do: <b>${data.reason || '-'}</b></div>
     <div>Trạng thái: <b>${data.status || '-'}</b></div>
     ${data.updated ? `<div>Cập nhật lúc: <b>${data.updated}</b></div>` : ''}`;

  document.getElementById('dContact').innerHTML =
    `<div>Khách: <b>${data.name || '-'}</b></div>
     <div>SĐT: <b>${data.phone || '-'}</b></div>
     <div>Email: <b>${data.email || '-'}</b></div>`;

  document.getElementById('dDetail').textContent = data.detail || '';

  const bankBox = document.getElementById('dBankBox');
  const bankEl = document.getElementById('dBank');

  if (data.type === 'refund' && data.bank) {
    bankBox.style.display = 'block';
    bankEl.innerHTML =
      `<div>Ngân hàng: <b>${data.bank.bank_name || '-'}</b></div>
       <div>Số TK: <b>${data.bank.bank_acc || '-'}</b></div>
       <div>Chủ TK: <b>${data.bank.bank_owner || '-'}</b></div>`;
  } else {
    bankBox.style.display = 'none';
    bankEl.innerHTML = '';
  }

  openModal('detailModal');
}

function openUpdate(rid, currentStatus){
  document.getElementById('uSub').textContent = `Yêu cầu: ${rid}`;
  document.getElementById('uRid').value = rid;

  const sel = document.getElementById('uStatus');
  for (let i=0;i<sel.options.length;i++){
    if (sel.options[i].value === currentStatus) {
      sel.selectedIndex = i; break;
    }
  }
  openModal('updateModal');
}
</script>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
</body>
</html>
