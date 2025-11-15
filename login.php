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
				$error = 'فشل الطلب: ' . $curl_err;
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
								$error = 'فشل تسجيل الدخول. رمز الحالة: ' . $http_code . '. الاستجابة: ' . substr($response, 0, 2000);
						}
						// Include the last request payload in the error for debugging (sanitized)
						$error .= ' | بيانات الطلب: ' . htmlspecialchars(substr($payload, 0, 1000));
				}
		}
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>تسجيل دخول المشرف - سكاي أكاديمي</title>

		<!-- Tailwind CSS (CDN for prototype) -->
		<script src="https://cdn.tailwindcss.com"></script>
		<!-- Google Cairo font + Font Awesome to match index style -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

		<script>
			tailwind.config = {
				theme: {
					extend: {
						fontFamily: { arabic: ['Cairo', 'sans-serif'] },
						colors: {
							primary: {
								50: '#f0f9ff',
								500: '#0ea5e9',
								600: '#0284c7',
								700: '#0369a1'
							}
						}
					}
				}
			}
		</script>
		<style>body { font-family: 'Cairo', sans-serif }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex items-center justify-center p-6">
	<div class="w-full max-w-md">
		<header class="mb-6 text-center">
			<a href="index.php" class="inline-flex items-center gap-3">
				<div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center shadow-lg text-white">
					<i class="fas fa-satellite-dish"></i>
				</div>
				<div class="text-right">
					<h1 class="text-xl font-bold text-gray-900">سكاي أكاديمي</h1>
					<p class="text-xs text-gray-600">منصة التعليم الرقمي</p>
				</div>
			</a>
		</header>

		<main class="bg-white bg-opacity-90 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-8">
			<h2 class="text-lg font-semibold text-gray-800 mb-2">تسجيل دخول المشرف</h2>
			<p class="text-sm text-gray-500 mb-6">أدخل بيانات المشرف للوصول إلى لوحة التحكم</p>

			<?php if ($error): ?>
				<div role="alert" class="mb-4 rounded-md bg-red-50 border border-red-100 text-red-700 p-3 text-sm">
					<?php echo htmlspecialchars($error); ?>
				</div>
			<?php endif; ?>

			<form method="post" action="" class="space-y-4" aria-label="نموذج تسجيل دخول المشرف">
				<div>
					<label for="email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
					<div class="mt-1 relative">
						<input id="email" name="email" type="email" autocomplete="email" required
							value="<?php echo isset($email) ? htmlspecialchars($email) : $default_email; ?>"
							class="block w-full pr-10 pl-3 py-2 border border-gray-200 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500" />
						<div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
							<i class="fas fa-envelope"></i>
						</div>
					</div>
				</div>

				<div>
					<label for="pass" class="block text-sm font-medium text-gray-700">كلمة المرور</label>
					<div class="mt-1 relative">
						<input id="pass" name="pass" type="password" autocomplete="current-password" required
							value="<?php echo isset($pass) ? htmlspecialchars($pass) : $default_pass; ?>"
							class="block w-full pr-10 pl-3 py-2 border border-gray-200 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500" />
						<button type="button" id="toggle-pass" aria-label="إظهار كلمة المرور" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
							<i class="fas fa-eye"></i>
						</button>
					</div>
				</div>

				<div class="flex items-center justify-between">
					<label class="inline-flex items-center text-sm">
						<input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-primary-600 rounded" />
						<span class="mr-2 text-gray-600">تذكرني</span>
					</label>
					<a href="#" class="text-sm text-primary-600 hover:underline">هل نسيت كلمة المرور؟</a>
				</div>

				<div>
					<button type="submit" class="w-full inline-flex justify-center items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md py-2 px-4 text-sm font-semibold shadow transition">تسجيل الدخول</button>
				</div>
				<p class="text-xs text-gray-500 text-center">النموذج يحتوي على قيم افتراضية للعرض فقط.</p>
			</form>
		</main>

		<footer class="mt-6 text-center text-sm text-gray-500">&copy; 2025 سكاي أكاديمي</footer>
	</div>

	<script>
		// Toggle password visibility
		const passInput = document.getElementById('pass');
		const toggleBtn = document.getElementById('toggle-pass');
		if (toggleBtn && passInput) {
			toggleBtn.addEventListener('click', () => {
				const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
				passInput.setAttribute('type', type);
				toggleBtn.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
			});
		}
	</script>
</body>
</html>

