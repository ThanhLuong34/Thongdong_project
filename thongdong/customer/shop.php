<?php $pageTitle = "Cửa hàng - Thong Dong"; ?>
<?php include '../includes/customer-layout-top.php'; ?>

<main class="container shop-page">
  <section class="page-head card">
    <div>
      <h1 class="page-title">Cửa hàng</h1>
      <p class="muted">Chọn một mùi hương hợp mood hôm nay.</p>
    </div>

    <div class="shop-controls">
      <div class="control">
        <label class="control-label">Tìm kiếm</label>
        <input id="shopSearch" class="input" type="text" placeholder="Nhập tên sản phẩm..." />
      </div>

      <div class="control">
        <label class="control-label">Bộ sưu tập</label>
        <select id="shopFilter" class="input">
          <option value="all">Tất cả</option>
          <option value="tet">Tết – Đỏ Vàng</option>
          <option value="viet">Thuần Việt</option>
          <option value="gift">Quà tặng</option>
        </select>
      </div>

      <div class="control">
        <label class="control-label">Giá</label>
        <select id="priceFilter" class="input">
          <option value="all">Tất cả</option>
          <option value="under200">Dưới 200.000đ</option>
          <option value="200to300">200.000đ – 300.000đ</option>
          <option value="over300">Trên 300.000đ</option>
        </select>
      </div>
    </div>
  </section>

  <section class="shop-grid" id="shopGrid"></section>

  <section class="shop-note card">
    <h3>Gợi ý chọn nến</h3>
    <ul>
      <li><b>Nhẹ nhàng</b>: sen, bưởi, trà.</li>
      <li><b>Ấm áp</b>: quế, gừng, gỗ.</li>
      <li><b>Quà tặng</b>: set 2–3 hũ, kèm thiệp.</li>
    </ul>
  </section>
</main>

<script src="../assets/js/customer.js"></script>
<?php include '../includes/customer-layout-bottom.php'; ?>
