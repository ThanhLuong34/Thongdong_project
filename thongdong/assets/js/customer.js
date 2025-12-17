(function () {
  const grid = document.getElementById('shopGrid');
  if (!grid) return;

  const searchEl = document.getElementById('shopSearch');
  const filterEl = document.getElementById('shopFilter');
  const priceEl = document.getElementById('priceFilter');

  const products = [
    { id: 1, name: 'Nến Quế Ấm', price: 189000, cat: 'tet', tag: 'Tết – Đỏ Vàng' },
    { id: 2, name: 'Nến Sen Nhẹ', price: 209000, cat: 'viet', tag: 'Thuần Việt' },
    { id: 3, name: 'Nến Trà Xanh', price: 199000, cat: 'viet', tag: 'Thuần Việt' },
    { id: 4, name: 'Set Quà “Thong Dong”', price: 459000, cat: 'gift', tag: 'Quà tặng' },
    { id: 5, name: 'Nến Bưởi Sáng', price: 219000, cat: 'viet', tag: 'Thuần Việt' },
    { id: 6, name: 'Nến Gừng Nồng', price: 189000, cat: 'tet', tag: 'Tết – Đỏ Vàng' },
    { id: 7, name: 'Set Quà “Tân Niên”', price: 529000, cat: 'gift', tag: 'Quà tặng' },
    { id: 8, name: 'Nến Gỗ Mộc', price: 239000, cat: 'tet', tag: 'Tết – Đỏ Vàng' },
  ];

  const fmt = (n) => n.toLocaleString('vi-VN') + 'đ';

  function matchPriceRange(price, range) {
    if (range === 'all') return true;
    if (range === 'under200') return price < 200000;
    if (range === '200to300') return price >= 200000 && price <= 300000;
    if (range === 'over300') return price > 300000;
    return true;
  }

  function getView() {
    const q = (searchEl?.value || '').trim().toLowerCase();
    const cat = filterEl?.value || 'all';
    const range = priceEl?.value || 'all';

    return products.filter(p => {
      const okCat = cat === 'all' || p.cat === cat;
      const okQ = !q || p.name.toLowerCase().includes(q);
      const okPrice = matchPriceRange(p.price, range);
      return okCat && okQ && okPrice;
    });
  }

  function render() {
    const view = getView();

    grid.innerHTML = view.map(p => `
      <article class="card product-card">
        <div class="product-img">
          <span>Ảnh sản phẩm</span>
        </div>
        <div class="product-body">
          <h3 class="product-title">${p.name}</h3>
          <div class="product-meta">
            <span class="price">${fmt(p.price)}</span>
            <span class="tag">${p.tag}</span>
          </div>
          <div class="product-actions">
            <a class="btn small" href="/thongdong/customer/product-detail.php?id=${p.id}">Xem chi tiết</a>
          <a class="btn small outline" href="/thongdong/customer/cart.php?add=${p.id}">Thêm giỏ</a>

          </div>
        </div>
      </article>
    `).join('');

    if (view.length === 0) {
      grid.innerHTML = `
        <div class="card" style="grid-column:1/-1; padding:18px;">
          <b>Không tìm thấy sản phẩm phù hợp.</b>
          <div class="muted" style="margin-top:6px;">Thử đổi bộ lọc hoặc từ khoá tìm kiếm nhé.</div>
        </div>
      `;
    }
  }

  searchEl?.addEventListener('input', render);
  filterEl?.addEventListener('change', render);
  priceEl?.addEventListener('change', render);

  render();
})();
