<?php
session_start();

unset($_SESSION['customer']);

unset($_SESSION['cart']);

header('Location: /thongdong/customer/index.php');
exit;
