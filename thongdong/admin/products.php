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
    'price' => normalize_money($_POST['price'] ?? '0'),
    'category' => trim($_POST['category'] ?? 'ThuanViet'),
    'stock' => (int)($_POST['stock'] ?? 0),
    'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
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
    $products[$idx]['price'] = normalize_money($_POST['price'] ?? '0');
    $products[$idx]['category'] = trim($_POST['category'] ?? 'ThuanViet');
    $products[$idx]['stock'] = (int)($_POST['stock'] ?? 0);
    $products[$idx]['status'] = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
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
    <h1 style="margin:0; font-size:28px;">Sản phẩm</h1>
   

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

  <!-- FILTER -->
  <section class="glass pad-16" style="margin-bottom:12px;">
    <form method="get" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <input name="q" value="<?php echo safe_text($q); ?>" placeholder="Tìm theo mã / tên..." style="padding:10px 12px; border-radius:12px; border:1px solid var(--line); min-width:240px;">
      <select name="cat" style="padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
        <option value="">Tất cả bộ sưu tập</option>
        <option value="Tet-DoVang" <?php if($cat==='Tet-DoVang') echo 'selected'; ?>>Tết – Đỏ Vàng</option>
        <option value="ThuanViet" <?php if($cat==='ThuanViet') echo 'selected'; ?>>Thuần Việt</option>
        <option value="QuaTang" <?php if($cat==='QuaTang') echo 'selected'; ?>>Quà tặng</option>
      </select>
      <select name="st" style="padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
        <option value="">Tất cả trạng thái</option>
        <option value="active" <?php if($st==='active') echo 'selected'; ?>>Đang bán</option>
        <option value="inactive" <?php if($st==='inactive') echo 'selected'; ?>>Tạm ẩn</option>
      </select>

      <button class="btn primary" type="submit">Lọc</button>
      <a class="btn outline" href="/thongdong/admin/products.php">Reset</a>
    </form>
  </section>

  <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:12px; align-items:start;">

    <!-- LIST -->
    <section class="glass pad-16">
      <h2 style="margin:0 0 10px; font-size:18px;">Danh sách</h2>

      <div style="overflow:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Ảnh</th>
              <th>Mã</th>
              <th>Tên</th>
              <th>Giá</th>
              <th>Tồn</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
          <?php if (count($filtered) === 0): ?>
            <tr><td colspan="7" style="color:#4b4b4b;">Không có sản phẩm.</td></tr>
          <?php endif; ?>

          <?php foreach($filtered as $p): ?>
            <tr>
              <td>
                <?php if (!empty($p['image'])): ?>
                  <img src="<?php echo safe_text($p['image']); ?>" alt="" style="width:54px; height:54px; border-radius:12px; object-fit:cover; border:1px solid rgba(0,0,0,0.08);">
                <?php else: ?>
                  <div style="width:54px; height:54px; border-radius:12px; border:1px dashed rgba(0,0,0,0.18); display:flex; align-items:center; justify-content:center; color:#777; font-size:12px;">
                    No img
                  </div>
                <?php endif; ?>
              </td>
              <td><?php echo safe_text($p['id']); ?></td>
              <td><?php echo safe_text($p['name']); ?></td>
              <td><?php echo safe_text(fmt_vnd((int)$p['price'])); ?></td>
              <td><?php echo (int)($p['stock'] ?? 0); ?></td>
              <td><?php echo ($p['status'] ?? 'active') === 'inactive' ? 'Tạm ẩn' : 'Đang bán'; ?></td>
              <td>
  <div style="display:flex; gap:10px; align-items:center; justify-content:flex-end; flex-wrap:wrap;">
    <a class="btn outline" href="/thongdong/admin/products.php?edit=<?php echo safe_text($p['id']); ?>">Sửa</a>

    <a class="btn outline"
       href="/thongdong/admin/products.php?action=delete&id=<?php echo safe_text($p['id']); ?>"
       onclick="return confirm('Xoá sản phẩm <?php echo safe_text($p['id']); ?> ?');">
      Xoá
    </a>
  </div>
</td>

            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- FORM -->
    <section class="glass pad-16">
      <?php if ($editing): ?>
        <h2 style="margin:0 0 10px; font-size:18px;">Sửa sản phẩm</h2>

        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo safe_text($editing['id']); ?>">

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Mã</div>
            <div style="padding:10px 12px; border:1px solid var(--line); border-radius:12px; background:rgba(255,255,255,0.7);">
              <?php echo safe_text($editing['id']); ?>
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Tên</div>
            <input name="name" value="<?php echo safe_text($editing['name'] ?? ''); ?>"
              style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Giá (VND)</div>
              <input name="price" value="<?php echo safe_text((string)($editing['price'] ?? 0)); ?>"
                style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
            </div>
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Tồn kho</div>
              <input type="number" name="stock" value="<?php echo (int)($editing['stock'] ?? 0); ?>"
                style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Bộ sưu tập</div>
              <select name="category" style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
                <option value="Tet-DoVang" <?php if(($editing['category']??'')==='Tet-DoVang') echo 'selected'; ?>>Tết – Đỏ Vàng</option>
                <option value="ThuanViet" <?php if(($editing['category']??'')==='ThuanViet') echo 'selected'; ?>>Thuần Việt</option>
                <option value="QuaTang" <?php if(($editing['category']??'')==='QuaTang') echo 'selected'; ?>>Quà tặng</option>
              </select>
            </div>
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Trạng thái</div>
              <select name="status" style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
                <option value="active" <?php if(($editing['status']??'active')==='active') echo 'selected'; ?>>Đang bán</option>
                <option value="inactive" <?php if(($editing['status']??'active')==='inactive') echo 'selected'; ?>>Tạm ẩn</option>
              </select>
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Ảnh (URL) hoặc Upload</div>
            <input name="image" value="<?php echo safe_text($editing['image'] ?? ''); ?>"
              placeholder="/thongdong/assets/img/products/..."
              style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line); margin-bottom:8px;">
            <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp">
          </div>

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Mô tả</div>
            <textarea name="desc" rows="4"
              style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);"><?php echo safe_text($editing['desc'] ?? ''); ?></textarea>
          </div>

          <button class="btn primary" type="submit" style="width:100%;">Lưu thay đổi</button>
          <a class="btn outline" href="/thongdong/admin/products.php" style="width:100%; margin-top:10px;">Huỷ</a>
        </form>

      <?php else: ?>
        <h2 style="margin:0 0 10px; font-size:18px;">Thêm sản phẩm</h2>

        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="create">

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Mã (ID)</div>
            <input name="new_id" placeholder="vd: p005"
              style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
          </div>

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Tên</div>
            <input name="name" placeholder="vd: Nến Gừng Ấm"
              style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Giá (VND)</div>
              <input name="price" placeholder="vd: 199000"
                style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
            </div>
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Tồn kho</div>
              <input type="number" name="stock" value="10"
                style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Bộ sưu tập</div>
              <select name="category" style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
                <option value="Tet-DoVang">Tết – Đỏ Vàng</option>
                <option value="ThuanViet" selected>Thuần Việt</option>
                <option value="QuaTang">Quà tặng</option>
              </select>
            </div>
            <div>
              <div style="color:#4b4b4b; font-size:14px;">Trạng thái</div>
              <select name="status" style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);">
                <option value="active" selected>Đang bán</option>
                <option value="inactive">Tạm ẩn</option>
              </select>
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Ảnh (URL) hoặc Upload</div>
            <input name="image" placeholder="/thongdong/assets/img/products/..."
              style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line); margin-bottom:8px;">
            <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp">
          </div>

          <div style="margin-bottom:10px;">
            <div style="color:#4b4b4b; font-size:14px;">Mô tả</div>
            <textarea name="desc" rows="4"
              style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--line);"></textarea>
          </div>

          <button class="btn primary" type="submit" style="width:100%;">Thêm sản phẩm</button>
        </form>
      <?php endif; ?>
    </section>

  </div>

</main>
</body>
</html>
