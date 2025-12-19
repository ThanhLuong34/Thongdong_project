<?php
include __DIR__ . '/includes/admin-guard.php';
include __DIR__ . '/includes/products-lib.php';

$products = read_products();
$errors = [];
$flash = '';

/* ===== HANDLE ACTIONS ===== */
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = $_POST['id'] ?? $_GET['id'] ?? '';

function upload_image_if_any(string $field): ?string {
  if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;

  $tmp = $_FILES[$field]['tmp_name'];
  $name = $_FILES[$field]['name'];
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

  $allow = ['jpg','jpeg','png','webp'];
  if (!in_array($ext, $allow, true)) return null;

  $dir = __DIR__ . '/../assets/img/products';
  if (!is_dir($dir)) mkdir($dir, 0777, true);

  $newName = 'p_' . date('Ymd_His') . '_' . rand(1000,9999) . '.' . $ext;
  $dest = $dir . '/' . $newName;

  if (!move_uploaded_file($tmp, $dest)) return null;

  return '/thongdong/assets/img/products/' . $newName;
}

if ($action === 'create') {
  $new = [
    'id' => trim($_POST['new_id'] ?? ''),
    'name' => trim($_POST['name'] ?? ''),
    'price' => (int)normalize_money($_POST['price'] ?? '0'),
    'category' => trim($_POST['category'] ?? 'Nến Thơm'),
    'stock' => (int)($_POST['stock'] ?? 0),
    'status' => $_POST['status'] ?? 'Đang bán',
    'image' => trim($_POST['image'] ?? ''),
    'desc' => trim($_POST['desc'] ?? '')
  ];

  if ($new['id'] === '') $errors[] = 'Thiếu mã sản phẩm (ID).';
  if ($new['name'] === '') $errors[] = 'Thiếu tên sản phẩm.';
  if ($new['price'] <= 0) $errors[] = 'Giá phải > 0.';
  if (find_product_index($products, $new['id']) !== -1) $errors[] = 'Mã sản phẩm bị trùng.';

  $uploaded = upload_image_if_any('image_file');
  if ($uploaded) $new['image'] = $uploaded;

  if (!$errors) {
    $products[] = $new;
    write_products($products);
    header('Location: /thongdong/admin/products.php?ok=created');
    exit;
  }
}

if ($action === 'update') {
  $idx = find_product_index($products, $id);
  if ($idx === -1) $errors[] = 'Không tìm thấy sản phẩm để cập nhật.';
  else {
    $products[$idx]['name'] = trim($_POST['name'] ?? '');
    $products[$idx]['price'] = (int)normalize_money($_POST['price'] ?? '0');
    $products[$idx]['category'] = trim($_POST['category'] ?? 'Nến Thơm');
    $products[$idx]['stock'] = (int)($_POST['stock'] ?? 0);
    $products[$idx]['status'] = $_POST['status'] ?? 'Đang bán';
    $products[$idx]['desc'] = trim($_POST['desc'] ?? '');
    $products[$idx]['image'] = trim($_POST['image'] ?? ($products[$idx]['image'] ?? ''));

    $uploaded = upload_image_if_any('image_file');
    if ($uploaded) $products[$idx]['image'] = $uploaded;

    if ($products[$idx]['name'] === '') $errors[] = 'Tên sản phẩm không được rỗng.';
    if ($products[$idx]['price'] <= 0) $errors[] = 'Giá phải > 0.';

    if (!$errors) {
      write_products($products);
      header('Location: /thongdong/admin/products.php?ok=updated');
      exit;
    }
  }
}

if ($action === 'delete') {
  $idx = find_product_index($products, $id);
  if ($idx !== -1) {
    array_splice($products, $idx, 1);
    write_products($products);
  }
  header('Location: /thongdong/admin/products.php?ok=deleted');
  exit;
}

/* ===== UI DATA ===== */
$ok = $_GET['ok'] ?? '';
if ($ok === 'created') $flash = 'Đã thêm sản phẩm.';
if ($ok === 'updated') $flash = 'Đã cập nhật sản phẩm.';
if ($ok === 'deleted') $flash = 'Đã xoá sản phẩm.';

$q = trim($_GET['q'] ?? '');
$cat = trim($_GET['cat'] ?? '');
$st = trim($_GET['st'] ?? '');

$filtered = array_values(array_filter($products, function($p) use($q,$cat,$st){
  $name = strtolower($p['name'] ?? '');
  $id = strtolower($p['id'] ?? '');
  if ($q !== '' && strpos($name, strtolower($q)) === false && strpos($id, strtolower($q)) === false) return false;
  if ($cat !== '' && ($p['category'] ?? '') !== $cat) return false;
  if ($st !== '' && ($p['status'] ?? '') !== $st) return false;
  return true;
}));

$editingId = trim($_GET['edit'] ?? '');
$editing = null;
if ($editingId !== '') {
  $idx = find_product_index($products, $editingId);
  if ($idx !== -1) $editing = $products[$idx];
}

function fmt_vnd(int $n): string {
  return number_format($n, 0, ',', '.') . 'đ';
}

// Danh sách Category dựa trên file PDF
$categories = [
    "Nến Thơm", 
    "Bộ sưu tập mùa Lễ Hội", 
    "Tinh Dầu Thơm Phòng", 
    "Nến Trang Trí", 
    "Que Khuếch Tán Tinh Dầu", 
    "Máy Khuếch Tán Tinh Dầu", 
    "Tinh Dầu Treo", 
    "Phụ kiện nến"
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sản phẩm - Admin Thong Dong</title>
  <link rel="stylesheet" href="/thongdong/assets/css/admin.css">
</head>
<body>

<?php include __DIR__ . '/includes/admin-header.php'; ?>

<main class="container admin-page">

  <section class="glass pad-16" style="margin-bottom:12px;">
    <h1 style="margin:0; font-size:28px;">Quản lý Sản phẩm Thong Dong</h1>

    <?php if ($flash): ?>
      <div class="glass pad-12" style="margin-top:12px; border-color: rgba(20,110,70,0.25);">
        ✅ <?php echo safe_text($flash); ?>
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="glass pad-12" style="margin-top:12px; border-color: rgba(158,42,43,0.25);">
        <b>Không lưu được:</b>
        <ul style="margin:8px 0 0; padding-left:18px;">
          <?php foreach($errors as $e): ?><li><?php echo safe_text($e); ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </section>

  <section class="glass pad-16" style="margin-bottom:12px;">
    <form method="get" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <input name="q" value="<?php echo safe_text($q); ?>" placeholder="Tìm theo mã / tên..." style="padding:10px 12px; border-radius:12px; border:1px solid var(--line); min-width:240px;">
      
      <select name="cat" style="padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
        <option value="">Tất cả danh mục</option>
        <?php foreach($categories as $c): ?>
          <option value="<?php echo $c; ?>" <?php if($cat === $c) echo 'selected'; ?>><?php echo $c; ?></option>
        <?php endforeach; ?>
      </select>

      <select name="st" style="padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
        <option value="">Tất cả trạng thái</option>
        <option value="Đang bán" <?php if($st==='Đang bán') echo 'selected'; ?>>Đang bán</option>
        <option value="Ngừng bán" <?php if($st==='Ngừng bán') echo 'selected'; ?>>Ngừng bán</option>
      </select>

      <button class="btn primary" type="submit">Lọc</button>
      <a class="btn outline" href="/thongdong/admin/products.php">Reset</a>
    </form>
  </section>

  <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:12px; align-items:start;">

    <section class="glass pad-16">
      <h2 style="margin:0 0 10px; font-size:18px;">Danh sách sản phẩm (<?php echo count($filtered); ?>)</h2>

      <div style="overflow:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Ảnh</th>
              <th>Tên / Danh mục</th>
              <th>Giá</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
          <?php if (count($filtered) === 0): ?>
            <tr><td colspan="5" style="color:#4b4b4b; text-align:center; padding: 20px;">Không có sản phẩm nào.</td></tr>
          <?php endif; ?>

          <?php foreach($filtered as $p): ?>
            <tr>
              <td>
                <?php if (!empty($p['image'])): ?>
                  <img src="<?php echo safe_text($p['image']); ?>" alt="" style="width:50px; height:50px; border-radius:8px; object-fit:cover;">
                <?php else: ?>
                  <div style="width:50px; height:50px; border-radius:8px; background:#eee; display:flex; align-items:center; justify-content:center; font-size:10px; color:#999;">No Image</div>
                <?php endif; ?>
              </td>
              <td>
                <div style="font-weight:600;"><?php echo safe_text($p['name']); ?></div>
                <div style="font-size:12px; color:#666;"><?php echo safe_text($p['category'] ?? 'Chưa phân loại'); ?></div>
                <div style="font-size:11px; color:#999;">ID: <?php echo safe_text($p['id']); ?></div>
              </td>
              <td><?php echo fmt_vnd((int)$p['price']); ?></td>
              <td>
                <span style="font-size:12px; padding:2px 8px; border-radius:10px; background: <?php echo $p['status'] === 'Đang bán' ? '#e6f4ea' : '#fce8e6'; ?>; color: <?php echo $p['status'] === 'Đang bán' ? '#1e7e34' : '#d93025'; ?>;">
                    <?php echo safe_text($p['status']); ?>
                </span>
              </td>
              <td>
                <div style="display:flex; gap:5px;">
                  <a class="btn outline" style="padding:5px 10px; font-size:13px;" href="/thongdong/admin/products.php?edit=<?php echo safe_text($p['id']); ?>">Sửa</a>
                  <a class="btn outline" style="padding:5px 10px; font-size:13px; color:red;" 
                     href="/thongdong/admin/products.php?action=delete&id=<?php echo safe_text($p['id']); ?>"
                     onclick="return confirm('Xoá sản phẩm này?');">Xoá</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="glass pad-16">
      <h2 style="margin:0 0 10px; font-size:18px;"><?php echo $editing ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới'; ?></h2>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">
        <?php if ($editing): ?>
          <input type="hidden" name="id" value="<?php echo safe_text($editing['id']); ?>">
        <?php endif; ?>

        <div style="margin-bottom:10px;">
          <label style="font-size:13px; color:#666;">Mã sản phẩm (ID)</label>
          <?php if ($editing): ?>
            <div style="padding:10px; background:#f9f9f9; border-radius:8px; border:1px solid #ddd;"><?php echo safe_text($editing['id']); ?></div>
          <?php else: ?>
            <input name="new_id" placeholder="vd: n01" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
          <?php endif; ?>
        </div>

        <div style="margin-bottom:10px;">
          <label style="font-size:13px; color:#666;">Tên sản phẩm</label>
          <input name="name" value="<?php echo safe_text($editing['name'] ?? ''); ?>" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
          <div>
            <label style="font-size:13px; color:#666;">Giá (VND)</label>
            <input name="price" type="number" value="<?php echo (int)($editing['price'] ?? 0); ?>" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
          </div>
          <div>
            <label style="font-size:13px; color:#666;">Danh mục</label>
            <select name="category" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
              <?php foreach($categories as $c): ?>
                <option value="<?php echo $c; ?>" <?php if(($editing['category']??'') === $c) echo 'selected'; ?>><?php echo $c; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
          <div>
            <label style="font-size:13px; color:#666;">Tồn kho</label>
            <input name="stock" type="number" value="<?php echo (int)($editing['stock'] ?? 10); ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
          </div>
          <div>
            <label style="font-size:13px; color:#666;">Trạng thái</label>
            <select name="status" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
              <option value="Đang bán" <?php if(($editing['status']??'') === 'Đang bán') echo 'selected'; ?>>Đang bán</option>
              <option value="Ngừng bán" <?php if(($editing['status']??'') === 'Ngừng bán') echo 'selected'; ?>>Ngừng bán</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom:10px;">
          <label style="font-size:13px; color:#666;">Ảnh (Link hoặc Tải lên)</label>
          <input name="image" value="<?php echo safe_text($editing['image'] ?? ''); ?>" placeholder="Link ảnh..." style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line); margin-bottom:5px;">
          <input type="file" name="image_file" accept="image/*">
        </div>

        <div style="margin-bottom:10px;">
          <label style="font-size:13px; color:#666;">Mô tả</label>
          <textarea name="desc" rows="3" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);"><?php echo safe_text($editing['desc'] ?? ''); ?></textarea>
        </div>

        <button class="btn primary" type="submit" style="width:100%; padding:12px;"><?php echo $editing ? 'Cập nhật' : 'Thêm mới'; ?></button>
        <?php if ($editing): ?>
          <a class="btn outline" href="/thongdong/admin/products.php" style="width:100%; margin-top:8px; display:block; text-align:center;">Huỷ chỉnh sửa</a>
        <?php endif; ?>
      </form>
    </section>

  </div>

</main>
</body>
</html>