<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
	header('Location: login.php');
	exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الطلاب - سكاي أكاديمي</title>
</head>
<body>
    <h1>لوحة تحكم الطلاب</h1>
    <p>مرحبًا بك في لوحة تحكم الطلاب الخاصة بسكاي أكاديمي.</p>
      
</body>
</html>