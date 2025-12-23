<?php
session_start();
require_once '../includes/db.php'; // 1. Kết nối DB

$pageTitle = "Tạo yêu cầu đổi/trả - Thong Dong";

// 2. Kiểm tra đăng nhập
if (empty($_SESSION['user_id']) && empty($_SESSION['customer'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'] ?? $_SESSION['customer']['id'];

// Lấy ID đơn hàng từ URL nếu có (khi bấm từ trang chi tiết đơn hàng)
$pre_order_id = (int)($_GET['order_id'] ?? 0);

// 3. Lấy danh sách đơn hàng của user (để hiện vào Dropdown chọn đơn)
$stmt = $conn->prepare("SELECT order_id, created_at FROM Orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}

// 4. Nếu đã chọn đơn hàng, lấy danh sách sản phẩm của đơn đó (để khách chọn trả món nào)
$order_items = [];
$selected_oid = ($pre_order_id > 0) ? $pre_order_id : (int)($_POST['order_id'] ?? 0);

if ($selected_oid > 0) {
    // Kiểm tra bảo mật: đơn hàng phải của user này
    $check = $conn->prepare("SELECT order_id FROM Orders WHERE order_id = ? AND user_id = ?");
    $check->bind_param("ii", $selected_oid, $user_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        // Lấy sản phẩm
        $sql_items = "SELECT oi.product_id, oi.quantity, p.name 
                      FROM OrderItems oi 
                      JOIN Products p ON oi.product_id = p.product_id 
                      WHERE oi.order_id = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $selected_oid);
        $stmt_items->execute();
        $order_items = $stmt_items->get_result();
    }
}

// ---- XỬ LÝ FORM (POST) ----
$errors = [];
$successId = '';
$success = false;

// Thông tin mặc định
$defaultName  = $_SESSION['customer']['name'] ?? '';
$defaultPhone = $_SESSION['customer']['phone'] ?? '';
$defaultEmail = $_SESSION['customer']['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nếu mới chỉ chọn đơn hàng (reload để lấy sản phẩm)
    if (isset($_POST['action']) && $_POST['action'] === 'load_items') {
        // Do nothing, logic lấy item đã chạy ở trên
    } else {
        // XỬ LÝ GỬI YÊU CẦU
        $orderId    = (int)($_POST['order_id'] ?? 0);
        $productId  = (int)($_POST['product_id'] ?? 0);
        $quantity   = (int)($_POST['quantity'] ?? 1);
        $type       = $_POST['type'] ?? 'exchange'; 
        $reason     = trim($_POST['reason'] ?? '');
        $detail     = trim($_POST['detail'] ?? '');
        
        // Bank info
        $bank_name  = trim($_POST['bank_name'] ?? '');
        $bank_acc   = trim($_POST['bank_acc'] ?? '');
        $bank_owner = trim($_POST['bank_owner'] ?? '');

        // Validate
        if ($orderId <= 0) $errors[] = 'Vui lòng chọn đơn hàng.';
        if ($productId <= 0) $errors[] = 'Vui lòng chọn sản phẩm cần trả.';
        if ($quantity <= 0) $errors[] = 'Số lượng trả không hợp lệ.';
        if ($reason === '') $errors[] = 'Vui lòng chọn lý do.';

        if ($type === 'refund') {
            if ($bank_name === '' || $bank_acc === '' || $bank_owner === '') {
                $errors[] = 'Vui lòng nhập đầy đủ thông tin ngân hàng để hoàn tiền.';
            }
        }

        if (empty($errors)) {
            // Chuẩn bị dữ liệu ngân hàng
            $bank_info = '';
            if ($type === 'refund') {
                $bank_info = "Ngân hàng: $bank_name\nSTK: $bank_acc\nChủ TK: $bank_owner";
            }
            
            $full_reason = "Lý do: $reason\nChi tiết: $detail";
            
            // QUAN TRỌNG: Lưu product_id và quantity vào DB
            $stmt_ins = $conn->prepare("INSERT INTO Returns (order_id, product_id, quantity, type, reason, status, bank_info, created_at) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())");
            $stmt_ins->bind_param("iiisss", $orderId, $productId, $quantity, $type, $full_reason, $bank_info);

            if ($stmt_ins->execute()) {
                $success = true;
                $successId = $stmt_ins->insert_id;
            } else {
                $errors[] = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card" style="padding:18px;">
    <h1 style="margin:0 0 6px;">Tạo yêu cầu đổi/trả</h1>
    <p class="muted" style="margin:0 0 14px;">Điền thông tin để Thong Dong hỗ trợ nhanh nhất.</p>

    <?php if ($success): ?>
      <div class="auth-alert" style="margin-bottom:14px; background:#e6f4ea; color:#1e7e34; border:1px solid #c3e6cb;">
        <b>Đã gửi yêu cầu thành công!</b> Mã yêu cầu là <b>#<?php echo $successId; ?></b>.
        <div class="muted" style="margin-top:8px;">
          <a class="btn small" href="account.php" style="display:inline-flex;">Về tài khoản</a>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="auth-alert" style="margin-bottom:14px; background:#fce8e6; color:#c0392b; border:1px solid #f5c6cb;">
        <?php foreach ($errors as $e): ?>
          <div>• <?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="checkout-grid" id="returnForm" style="gap:16px;">
      
      <div class="checkout-left">
        <div class="form-group">
          <label>Chọn đơn hàng cần hỗ trợ *</label>
          <select class="input" name="order_id" required onchange="reloadItems(this.value)">
            <option value="">-- Chọn đơn hàng --</option>
            <?php foreach ($orders as $o): 
                $oid = $o['order_id'];
                $selected = ($oid == $selected_oid) ? 'selected' : '';
            ?>
              <option value="<?php echo $oid; ?>" <?php echo $selected; ?>>
                Đơn #<?php echo $oid; ?> — <?php echo date('d/m/Y', strtotime($o['created_at'])); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php if (!empty($order_items) && $order_items->num_rows > 0): ?>
            <div class="form-group" style="background:#f1f1f1; padding:15px; border-radius:8px;">
                <label>Sản phẩm cần trả *</label>
                <select class="input" name="product_id" required style="width:100%; margin-bottom:10px;">
                    <?php foreach($order_items as $item): ?>
                        <option value="<?php echo $item['product_id']; ?>">
                            <?php echo htmlspecialchars($item['name']); ?> (Đã mua: <?php echo $item['quantity']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Số lượng trả *</label>
                <input type="number" name="quantity" class="input" value="1" min="1" required style="width:100px;">
            </div>
        <?php elseif ($selected_oid > 0): ?>
            <div class="muted">Không tìm thấy sản phẩm trong đơn này.</div>
        <?php endif; ?>

        <div class="form-group">
          <label>Loại yêu cầu *</label>
          <div class="pay-box">
            <label class="pay-item">
              <input type="radio" name="type" value="exchange" checked>
              <div>
                <b>Đổi hàng</b>
                <div class="muted">Đổi sản phẩm lỗi/hư hỏng</div>
              </div>
            </label>
            <label class="pay-item">
              <input type="radio" name="type" value="refund">
              <div>
                <b>Hoàn tiền</b>
                <div class="muted">Hoàn tiền vào tài khoản ngân hàng</div>
              </div>
            </label>
          </div>
        </div>

        <div id="refundBox" class="card" style="padding:12px; margin-top:8px; display:none; background:#f9f9f9;">
          <b>Thông tin nhận tiền hoàn (Bắt buộc)</b>
          <div class="form-group">
            <label>Tên Ngân hàng</label>
            <input class="input" name="bank_name" placeholder="VD: Vietcombank">
          </div>
          <div class="form-group">
            <label>Số tài khoản</label>
            <input class="input" name="bank_acc" placeholder="VD: 0123456789">
          </div>
          <div class="form-group">
            <label>Chủ tài khoản (Viết hoa không dấu)</label>
            <input class="input" name="bank_owner" placeholder="VD: NGUYEN VAN A">
          </div>
        </div>

        <div class="form-group">
          <label>Lý do *</label>
          <select class="input" name="reason" required>
            <option value="">-- Chọn lý do --</option>
            <option value="Giao nhầm sản phẩm">Giao nhầm sản phẩm</option>
            <option value="Sản phẩm lỗi / bể vỡ">Sản phẩm lỗi / bể vỡ</option>
            <option value="Chưa ưng mùi">Chưa ưng mùi (Đổi hàng)</option>
            <option value="Khác">Khác</option>
          </select>
        </div>

        <div class="form-group">
          <label>Mô tả chi tiết *</label>
          <textarea class="input" name="detail" rows="3" required placeholder="Mô tả tình trạng..."></textarea>
        </div>
      </div>

      <div class="checkout-right">
        <div class="card" style="padding:14px;">
          <h2 style="margin:0 0 10px; font-size:20px;">Thông tin liên hệ</h2>
          <div class="form-group">
            <label>Họ và tên</label>
            <input class="input" value="<?php echo htmlspecialchars($defaultName); ?>" disabled style="background:#eee;">
          </div>
          <div class="form-group">
            <label>Số điện thoại</label>
            <input class="input" value="<?php echo htmlspecialchars($defaultPhone); ?>" disabled style="background:#eee;">
          </div>
          
          <button class="btn" type="submit" style="width:100%; margin-top:10px;">Gửi yêu cầu</button>
        </div>
      </div>
    </form>
  </section>
</main>

<form id="reloadForm" method="post" style="display:none;">
    <input type="hidden" name="action" value="load_items">
    <input type="hidden" name="order_id" id="hiddenOrderId">
</form>

<script>
  // Logic reload khi chọn đơn hàng
  function reloadItems(oid) {
      if(oid) {
          document.getElementById('hiddenOrderId').value = oid;
          document.getElementById('reloadForm').submit();
      }
  }

  // Logic hiện box hoàn tiền
  const refundBox = document.getElementById('refundBox');
  const typeRadios = document.querySelectorAll('input[name="type"]');
  function toggleRefundBox(){
    const checked = document.querySelector('input[name="type"]:checked');
    refundBox.style.display = (checked && checked.value === 'refund') ? 'block' : 'none';
  }
  typeRadios.forEach(r => r.addEventListener('change', toggleRefundBox));
  toggleRefundBox();
</script>

<?php include '../includes/customer-layout-bottom.php'; ?>