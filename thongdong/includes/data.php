<?php
// Danh sách sản phẩm dùng chung toàn site (không DB)
$PRODUCTS = [
  [
    'id' => 1,
    'name' => 'Nến Quế Ấm',
    'price' => 189000,
    'cat' => 'tet',
    'tag' => 'Tết – Đỏ Vàng',
    'desc' => 'Hương quế ấm nồng, gợi cảm giác sum vầy ngày Tết. Phù hợp phòng khách, góc đọc sách.',
    'burn' => '30–35 giờ',
    'size' => '180g',
  ],
  [
    'id' => 2,
    'name' => 'Nến Sen Nhẹ',
    'price' => 209000,
    'cat' => 'viet',
    'tag' => 'Thuần Việt',
    'desc' => 'Hương sen thanh, dịu. Cho tâm trí “thong dong”, hợp buổi tối thư giãn.',
    'burn' => '35–40 giờ',
    'size' => '200g',
  ],
  [
    'id' => 3,
    'name' => 'Nến Trà Xanh',
    'price' => 199000,
    'cat' => 'viet',
    'tag' => 'Thuần Việt',
    'desc' => 'Trà xanh nhẹ, sạch, hợp không gian làm việc và thiền/đọc.',
    'burn' => '30–35 giờ',
    'size' => '180g',
  ],
  [
    'id' => 4,
    'name' => 'Set Quà “Thong Dong”',
    'price' => 459000,
    'cat' => 'gift',
    'tag' => 'Quà tặng',
    'desc' => 'Set quà 2 hũ + thiệp. Gọn gàng, sang, đúng chất Việt.',
    'burn' => '2 x 25–30 giờ',
    'size' => '2 x 150g',
  ],
  [
    'id' => 5,
    'name' => 'Nến Bưởi Sáng',
    'price' => 219000,
    'cat' => 'viet',
    'tag' => 'Thuần Việt',
    'desc' => 'Bưởi tươi, sáng. Hợp sáng sớm và phòng ngủ thoáng.',
    'burn' => '35–40 giờ',
    'size' => '200g',
  ],
  [
    'id' => 6,
    'name' => 'Nến Gừng Nồng',
    'price' => 189000,
    'cat' => 'tet',
    'tag' => 'Tết – Đỏ Vàng',
    'desc' => 'Gừng ấm, tạo cảm giác cozy khi trời mưa/lạnh.',
    'burn' => '30–35 giờ',
    'size' => '180g',
  ],
  [
    'id' => 7,
    'name' => 'Set Quà “Tân Niên”',
    'price' => 529000,
    'cat' => 'gift',
    'tag' => 'Quà tặng',
    'desc' => 'Set quà Tân Niên: 3 mùi hương + thiệp chúc. Tặng gia đình, đồng nghiệp.',
    'burn' => '3 x 20–25 giờ',
    'size' => '3 x 120g',
  ],
  [
    'id' => 8,
    'name' => 'Nến Gỗ Mộc',
    'price' => 239000,
    'cat' => 'tet',
    'tag' => 'Tết – Đỏ Vàng',
    'desc' => 'Gỗ mộc trầm ấm, hợp người thích vibe cổ điển, bình yên.',
    'burn' => '35–40 giờ',
    'size' => '200g',
  ],
];

function findProductById($id, $PRODUCTS) {
  foreach ($PRODUCTS as $p) {
    if ((int)$p['id'] === (int)$id) return $p;
  }
  return null;
}

function formatVND($n) {
  return number_format((int)$n, 0, ',', '.') . 'đ';
}
