<?php
session_start();

$pid = $_GET['id'] ?? '';
$pid = trim($pid);

if ($pid === '') {
  header('Location: shop.php');
  exit;
}

// Load products from JSON
$jsonPath = __DIR__ . '/../data/products.json';
$products = [];

if (file_exists($jsonPath)) {
  $products = json_decode(file_get_contents($jsonPath), true) ?? [];
}

// Find product
$product = null;
foreach ($products as $p) {
  if ((string)($p['id'] ?? '') === (string)$pid) {
    $product = $p;
    break;
  }
}

if (!$product) {
  header('Location: shop.php');
  exit;
}

// Add to cart (session cart)
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$cart = &$_SESSION['cart'];
if (!isset($cart[$pid])) {
  $cart[$pid] = [
    'id'    => $pid,
    'name'  => $product['name'] ?? '',
    'price' => (int)($product['price'] ?? 0),
    'qty'   => 1,
    'image' => $product['image'] ?? '',
  ];
} else {
  $cart[$pid]['qty'] += 1;
}

// quay lại trang trước nếu có, không thì về giỏ
$back = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
header('Location: ' . $back);
exit;
