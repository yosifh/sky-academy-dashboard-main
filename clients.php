<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
	header('Location: login.php');
	exit;
}

include 'includes/api_helper.php';

$page_title = 'إدارة العملاء';
$endpoint = 'https://qxkyfdasymxphjjzxwfn.supabase.co/functions/v1/client-admin';
$token = $_SESSION['admin_token'] ?? null;

$error = '';
$success = '';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_url = $endpoint . '?id=' . $_GET['id'];
    $response = api_request('DELETE', $delete_url, $token);

    if ($response['http_code'] === 200) {
        $success = 'تم حذف العميل بنجاح.';
    } else {
        $error = 'فشل حذف العميل: ' . ($response['data']['error'] ?? 'خطأ غير معروف.');
    }
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $client_name = $_POST['client_name'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;

    $payload = [
        'client_name' => $client_name,
        'display_order' => (int)$display_order,
    ];

    // Handle Image Upload
    if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_path = $_FILES['client_image']['tmp_name'];
        $image_mime_type = mime_content_type($image_tmp_path);
        $image_base64 = base64_encode(file_get_contents($image_tmp_path));
        
        $payload['image_base64'] = $image_base64;
        $payload['image_mime_type'] = $image_mime_type;
        $payload['image_file_name'] = basename($_FILES['client_image']['name']);
    }

    if ($id) {
        // Update
        $url = $endpoint . '?id=' . $id;
        $response = api_request('PUT', $url, $token, $payload);
        if ($response['http_code'] === 200 && !empty($response['data'])) {
            $success = 'تم تحديث العميل بنجاح.';
        } else {
            $error = 'فشل تحديث العميل: ' . ($response['data']['error'] ?? ($response['raw_response'] ?? 'خطأ غير معروف.'));
        }
    } else {
        // Create
        $response = api_request('POST', $endpoint, $token, $payload);
        if ($response['http_code'] === 200 && !empty($response['data'])) {
            $success = 'تمت إضافة العميل بنجاح.';
        } else {
            $error = 'فشل إضافة العميل: ' . ($response['data']['error'] ?? ($response['raw_response'] ?? 'خطأ غير معروف.'));
        }
    }
}

// Fetch all clients
$clients_response = api_request('GET', $endpoint, $token);
$clients = [];
if ($clients_response['http_code'] === 200) {
    $clients = $clients_response['data'];
} else {
    // Use raw_response for better error debugging if JSON decoding fails
    $error = 'فشل في جلب العملاء: ' . ($clients_response['data']['error'] ?? ($clients_response['raw_response'] ?? 'خطأ غير معروف.'));
}

// Get client to edit
$edit_client = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_url = $endpoint . '?id=' . $_GET['id'];
    $response = api_request('GET', $edit_url, $token);
    if ($response['http_code'] === 200 && !empty($response['data'])) {
        // The function returns an array with one item for a single ID
        $edit_client = $response['data'][0] ?? null;
    }
    
    if (!$edit_client) {
        $error = 'لم يتم العثور على العميل أو فشل في جلبه.';
    }
}

include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="lg:mr-64">
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Content -->
    <main class="pt-20 min-h-screen bg-gray-50">
        <div class="p-4 lg:p-8">
            <h1 class="text-3xl font-bold mb-6">إدارة العملاء</h1>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">خطأ!</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">نجاح!</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                <h2 class="text-2xl font-bold mb-4"><?php echo $edit_client ? 'تعديل عميل' : 'إضافة عميل جديد'; ?></h2>
                <form method="post" action="clients.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $edit_client['id'] ?? ''; ?>">
                    
                    <div class="mb-4">
                        <label for="client_name" class="block text-gray-700 font-bold mb-2">اسم العميل</label>
                        <input type="text" id="client_name" name="client_name" value="<?php echo htmlspecialchars($edit_client['client_name'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="display_order" class="block text-gray-700 font-bold mb-2">ترتيب العرض</label>
                        <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($edit_client['display_order'] ?? '0'); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="client_image" class="block text-gray-700 font-bold mb-2">شعار العميل</label>
                        <input type="file" id="client_image" name="client_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php if (isset($edit_client['client_image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($edit_client['client_image_url']); ?>" alt="Client Logo" class="mt-2 h-16">
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            <?php echo $edit_client ? 'تحديث العميل' : 'إضافة عميل'; ?>
                        </button>
                        <?php if ($edit_client): ?>
                            <a href="clients.php" class="text-gray-600 hover:text-gray-800">إلغاء التعديل</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Clients Table -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">قائمة العملاء</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="w-1/4 text-right py-3 px-4 uppercase font-semibold text-sm">الشعار</th>
                                <th class="w-1/4 text-right py-3 px-4 uppercase font-semibold text-sm">اسم العميل</th>
                                <th class="text-right py-3 px-4 uppercase font-semibold text-sm">ترتيب العرض</th>
                                <th class="text-right py-3 px-4 uppercase font-semibold text-sm">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php if (!empty($clients)): ?>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td class="w-1/4 text-right py-3 px-4">
                                            <?php if (!empty($client['client_image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($client['client_image_url']); ?>" alt="<?php echo htmlspecialchars($client['client_name']); ?>" class="h-12 w-12 object-contain">
                                            <?php endif; ?>
                                        </td>
                                        <td class="w-1/4 text-right py-3 px-4"><?php echo htmlspecialchars($client['client_name']); ?></td>
                                        <td class="text-right py-3 px-4"><?php echo htmlspecialchars($client['display_order']); ?></td>
                                        <td class="text-right py-3 px-4">
                                            <a href="clients.php?action=edit&id=<?php echo $client['id']; ?>" class="text-blue-500 hover:text-blue-700">تعديل</a>
                                            <a href="clients.php?action=delete&id=<?php echo $client['id']; ?>" class="text-red-500 hover:text-red-700 ml-4" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا العميل؟');">حذف</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">لا يوجد عملاء لعرضهم.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </main>
</div>
