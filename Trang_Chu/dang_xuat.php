<?php
session_start();
session_unset();
session_destroy();
// Chuyển hướng quay trở lại trang chủ du lịch ngay sau khi thoát
header("Location: trang_chu_web.php");
exit;
?>