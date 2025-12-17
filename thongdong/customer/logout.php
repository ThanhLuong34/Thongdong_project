<?php
session_start();
unset($_SESSION['customer']);
header('Location: /thongdong/customer/index.php');
exit;
