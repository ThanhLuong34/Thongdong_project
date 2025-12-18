<?php
session_start();
require __DIR__ . '/includes/products-data.php';

$id = (int)($_GET['id'] ?? 0);
$qty = (int)($_GET['qty'] ?? 1);
if ($qty < 1) $qty = 1;

$p = td_get_product($id);
if (!$p) {
  header('Location: /thongdong/customer/shop.php');
  exit;
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$found = false;
foreach ($_SESSION['cart'] as &$item) {
  if ((int)$item['id'] === $id) {
    $item['qty'] = (int)$item['qty'] + $qty;
    $found = true;
    break;
  }
}
unset($item);

if (!$found) {
  $_SESSION['cart'][] = [
    'id'    => $p['id'],
    'name'  => $p['name'],
    'price' => $p['price'],
    'qty'   => $qty,
  ];
}

// quay lại trang trước nếu có, không thì về giỏ
$back = $_SERVER['HTTP_REFERER'] ?? '/thongdong/customer/cart.php';
header('Location: ' . $back);
exit;
