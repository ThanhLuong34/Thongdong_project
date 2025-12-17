<?php
// includes/products-data.php

function td_products(): array {
  return [
    [
      'id' => 'p001',
      'name' => 'Nến Quế Ấm',
      'price' => 189000,
      'tag' => 'tet',
    ],
    [
      'id' => 'p002',
      'name' => 'Nến Sen Nhẹ',
      'price' => 209000,
      'tag' => 'viet',
    ],
    [
      'id' => 'p003',
      'name' => 'Nến Trà Xanh',
      'price' => 199000,
      'tag' => 'viet',
    ],
    [
      'id' => 'p004',
      'name' => 'Set Quà “Thong Dong”',
      'price' => 459000,
      'tag' => 'gift',
    ],
  ];
}

function td_find_product(string $id): ?array {
  foreach (td_products() as $p) {
    if (($p['id'] ?? '') === $id) return $p;
  }
  return null;
}
