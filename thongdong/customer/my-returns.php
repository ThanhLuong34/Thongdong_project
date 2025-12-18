<?php
session_start();
$pageTitle = "Yêu cầu đổi/trả - Thong Dong";

function safe($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$requests = $_SESSION['return_requests'] ?? [];
$selectedId = trim($_GET['id'] ?? '');

$selected = null;
if ($selectedId !== '') {
  foreach ($requests as $r) {
    if (($r['id'] ?? '') === $selectedId) {
      $selected = $r;
      break;
    }
  }
}

include '../includes/customer-layout-top.php';
?>

<main class="container" style="padding:32px 0 70px;">
  <section class="card" style="padding:18px;">
    <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <div>
        <h1 class="page-title" style="margin:0 0 6px;">Yêu cầu đổi/trả</h1>
        <p class="muted" style="margin:0;">Danh sách yêu cầu bạn đã gửi cho Thong Dong.</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="/thongdong/customer/return-request.php">Tạo yêu cầu mới</a>
        <a class="btn outline" href="/thongdong/customer/account.php">Về tài khoản</a>
      </div>
    </div>

    <div style="height:14px;"></div>

    <?php if ($selected): ?>
      <!-- DETAIL -->
      <div class="card" style="padding:14px; margin-bottom:14px;">
        <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
          <div>
            <h2 style="margin:0 0 6px; font-size:20px;">Chi tiết yêu cầu</h2>
            <div class="muted">Mã yêu cầu: <b><?php echo safe($selected['id'] ?? ''); ?></b></div>
          </div>

          <a class="btn outline" href="/thongdong/customer/my-returns.php">Đóng chi tiết</a>
        </div>

        <div style="height:10px;"></div>

        <?php
          $type = $selected['type'] ?? '';
          $typeLabel = ($type === 'refund') ? 'Hoàn tiền' : 'Đổi hàng';
          $status = $selected['status'] ?? 'Chờ xử lý';
        ?>

        <div class="checkout-lines" style="margin-top:8px;">
          <div class="line">
            <div class="muted">Loại yêu cầu</div>
            <div><b><?php echo safe($typeLabel); ?></b></div>
          </div>
          <div class="line">
            <div class="muted">Trạng thái</div>
            <div><b><?php echo safe($status); ?></b></div>
          </div>
          <div class="line">
            <div class="muted">Mã đơn hàng</div>
            <div><b><?php echo safe($selected['order_id'] ?? ''); ?></b></div>
          </div>
          <div class="line">
            <div class="muted">Thời gian gửi</div>
            <div><b><?php echo safe($selected['created'] ?? ''); ?></b></div>
          </div>
          <div class="line">
            <div class="muted">Lý do</div>
            <div><b><?php echo safe($selected['reason'] ?? ''); ?></b></div>
          </div>
        </div>

        <div style="height:12px;"></div>

        <div class="card" style="padding:12px; background:#fff;">
          <b>Mô tả chi tiết</b>
          <div class="muted" style="margin-top:6px; white-space:pre-wrap;">
            <?php echo safe($selected['detail'] ?? ''); ?>
          </div>
        </div>

        <div style="height:12px;"></div>

        <?php $c = $selected['contact'] ?? []; ?>
        <div class="card" style="padding:12px; background:#fff;">
          <b>Thông tin liên hệ</b>
          <div class="muted" style="margin-top:8px; display:grid; gap:6px;">
            <div>Họ tên: <b><?php echo safe($c['name'] ?? ''); ?></b></div>
            <div>SĐT: <b><?php echo safe($c['phone'] ?? ''); ?></b></div>
            <?php if (!empty($c['email'])): ?>
              <div>Email: <b><?php echo safe($c['email']); ?></b></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!$requests || count($requests) === 0): ?>
      <div class="card" style="padding:16px;">
        <b>Chưa có yêu cầu đổi/trả nào.</b>
        <div class="muted" style="margin-top:6px;">Bạn có thể tạo yêu cầu mới để Thong Dong hỗ trợ nha.</div>
        <div style="margin-top:12px;">
          <a class="btn" href="/thongdong/customer/return-request.php">Tạo yêu cầu đổi/trả</a>
        </div>
      </div>
    <?php else: ?>
      <!-- LIST -->
      <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding:14px; border-bottom:1px solid rgba(0,0,0,.06);">
          <b>Danh sách yêu cầu</b>
          <div class="muted" style="margin-top:4px;">Bấm “Xem” để mở chi tiết.</div>
        </div>

        <div style="padding:14px; display:grid; gap:12px;">
          <?php foreach ($requests as $r):
            $id = $r['id'] ?? '';
            if (!$id) continue;

            $type = $r['type'] ?? '';
            $typeLabel = ($type === 'refund') ? 'Hoàn tiền' : 'Đổi hàng';
            $status = $r['status'] ?? 'Chờ xử lý';
            $orderId = $r['order_id'] ?? '';
            $created = $r['created'] ?? '';
          ?>
            <div class="card" style="padding:12px; background:#fff;">
              <div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start; flex-wrap:wrap;">
                <div>
                  <div style="font-size:16px;">
                    <b><?php echo safe($id); ?></b>
                    <span class="muted"> • <?php echo safe($typeLabel); ?></span>
                  </div>
                  <div class="muted" style="margin-top:4px;">
                    Đơn: <b><?php echo safe($orderId); ?></b> • <?php echo safe($created); ?>
                  </div>
                </div>

                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                  <span class="muted">Trạng thái: <b><?php echo safe($status); ?></b></span>
                  <a class="btn outline" href="/thongdong/customer/my-returns.php?id=<?php echo urlencode($id); ?>">Xem</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </section>
</main>

<?php include '../includes/customer-layout-bottom.php'; ?>
