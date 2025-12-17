<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /thongdong/customer/shop.php');
  exit;
}

$id  = (int)($_POST['id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
if ($qty < 1) $qty = 1;

$products = [
  1 => ['id'=>1,'name'=>'Nến Quế Ấm','price'=>189000],
  2 => ['id'=>2,'name'=>'Nến Sen Nhẹ','price'=>209000],
  3 => ['id'=>3,'name'=>'Nến Trà Xanh','price'=>199000],
  4 => ['id'=>4,'name'=>'Set Quà “Thong Dong”','price'=>459000],
  5 => ['id'=>5,'name'=>'Nến Bưởi Sáng','price'=>219000],
  6 => ['id'=>6,'name'=>'Nến Gừng Nồng','price'=>189000],
  7 => ['id'=>7,'name'=>'Set Quà “Tân Niên”','price'=>529000],
  8 => ['id'=>8,'name'=>'Nến Gỗ Mộc','price'=>239000],
];

if (!isset($products[$id])) {
  header('Location: /thongdong/customer/shop.php');
  exit;
}

$p = $products[$id];

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// key theo id
$key = (string)$id;

if (isset($_SESSION['cart'][$key])) {
  $_SESSION['cart'][$key]['qty'] += $qty;
} else {
  $_SESSION['cart'][$key] = [
    'id'    => $p['id'],
    'name'  => $p['name'],
    'price' => $p['price'],
    'qty'   => $qty,
  ];
}

$back = $_SERVER['HTTP_REFERER'] ?? '/thongdong/customer/shop.php';
header('Location: ' . $back);
exit;
