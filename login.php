<?php
session_start();

// Supabase function endpoint
$supabase_url = 'https://qxkyfdasymxphjjzxwfn.supabase.co/functions/v1/admin-login';

$default_email = 'adminnn@gmaol.com';
$default_pass = 'Admin123@#';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = isset($_POST['email']) ? trim($_POST['email']) : '';
	$pass = isset($_POST['pass']) ? $_POST['pass'] : '';

	// send JSON body as required by the function. Some functions expect
	// `pass` or `password` — send both to maximize compatibility.
	$body = ['email' => $email, 'pass' => $pass, 'password' => $pass];
	$payload = json_encode($body);

	$ch = curl_init($supabase_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json'
	]);

	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_err = curl_error($ch);
	curl_close($ch);

	if ($response === false) {
		$error = 'Request failed: ' . $curl_err;
	} else {
		// keep raw response for debugging
		$data = json_decode($response, true);

		// Determine success: accept HTTP 200 and presence of a token or success flag
		$token = null;
		if (is_array($data)) {
			if (isset($data['access_token'])) $token = $data['access_token'];
			elseif (isset($data['token'])) $token = $data['token'];
			elseif (isset($data['data']) && isset($data['data']['token'])) $token = $data['data']['token'];
		}

		$is_success = ($http_code >= 200 && $http_code < 300) && ($token !== null || (is_array($data) && (isset($data['status']) && $data['status'] === 'success') || isset($data['user'])));

	if ($is_success) {
			// store session values
			$_SESSION['admin_logged_in'] = true;
			$_SESSION['admin_email'] = $email;
			// store entire response for debugging/usage
			$_SESSION['admin_auth_response'] = $data;
			if ($token !== null) {
				$_SESSION['admin_token'] = $token;
			}

			header('Location: dashboard.php');
			exit;
		} else {
			// Try to extract message, otherwise show raw response for debugging
			if (is_array($data) && isset($data['message'])) {
				$error = $data['message'];
			} else {
				$error = 'Login failed. HTTP ' . $http_code . '. Response: ' . substr($response, 0, 2000);
			}
			// Include the last request payload in the error for debugging (sanitized)
			$error .= ' | Request payload: ' . htmlspecialchars(substr($payload, 0, 1000));
		}
	}
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>تسجيل دخول المشرف - سكاي أكاديمي</title>
	<style>
		body{font-family:Arial, Helvetica, sans-serif;direction:rtl;padding:30px}
		.card{max-width:420px;margin:40px auto;padding:20px;border:1px solid #ddd;border-radius:6px}
		label{display:block;margin-bottom:6px}
		input{width:100%;padding:8px;margin-bottom:12px}
		.error{color:#b00020;margin-bottom:12px}
		.hint{font-size:0.9em;color:#666}
	</style>
</head>
<body>
	<div class="card">
		<h2>تسجيل دخول المشرف</h2>
		<?php if ($error): ?>
			<div class="error"><?php echo htmlspecialchars($error); ?></div>
		<?php endif; ?>
		<form method="post" action="">
			<label for="email">البريد الإلكتروني</label>
			<input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : $default_email; ?>" required>

			<label for="pass">كلمة المرور</label>
			<input type="password" id="pass" name="pass" value="<?php echo isset($pass) ? htmlspecialchars($pass) : $default_pass; ?>" required>

			<button type="submit">تسجيل الدخول</button>
		</form>
		<p class="hint">تم تزويد نموذج تسجيل الدخول بقيم افتراضية للمشرف.</p>
	</div>
</body>
</html>

