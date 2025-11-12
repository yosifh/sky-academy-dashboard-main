<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
	header('Location: login.php');
	exit;
}

include 'includes/api_helper.php';

$page_title = 'إدارة الأساتذة';
$endpoint = 'https://qxkyfdasymxphjjzxwfn.supabase.co/functions/v1/teachers-admin';
$token = $_SESSION['admin_token'] ?? null;

$error = '';
$success = '';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_url = $endpoint . '?id=' . $_GET['id'];
    $response = api_request('DELETE', $delete_url, $token);

    if ($response['http_code'] === 200) {
        $success = 'تم حذف الأستاذ بنجاح.';
    } else {
        $error = 'فشل حذف الأستاذ: ' . ($response['data']['error'] ?? 'خطأ غير معروف.');
    }
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $full_name = $_POST['full_name'] ?? '';
    $bio = $_POST['bio'] ?? '';

    $payload = [
        'full_name' => $full_name,
        'bio' => $bio,
    ];

    // Handle Image Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_path = $_FILES['profile_image']['tmp_name'];
        $image_mime_type = mime_content_type($image_tmp_path);
        $image_base64 = base64_encode(file_get_contents($image_tmp_path));
        
        $payload['image_base64'] = $image_base64;
        $payload['image_mime_type'] = $image_mime_type;
        $payload['image_file_name'] = basename($_FILES['profile_image']['name']);
    }

    if ($id) {
        // Update
        $url = $endpoint . '?id=' . $id;
        $response = api_request('PUT', $url, $token, $payload);
        if ($response['http_code'] === 200 && !empty($response['data'])) {
            $success = 'تم تحديث الأستاذ بنجاح.';
        } else {
            $error = 'فشل تحديث الأستاذ: ' . ($response['data']['error'] ?? ($response['raw_response'] ?? 'خطأ غير معروف.'));
        }
    } else {
        // Create
        $response = api_request('POST', $endpoint, $token, $payload);
        if ($response['http_code'] === 200 && !empty($response['data'])) {
            $success = 'تمت إضافة الأستاذ بنجاح.';
        } else {
            $error = 'فشل إضافة الأستاذ: ' . ($response['data']['error'] ?? ($response['raw_response'] ?? 'خطأ غير معروف.'));
        }
    }
}

// Fetch all teachers
$teachers_response = api_request('GET', $endpoint, $token);
$teachers = [];
if ($teachers_response['http_code'] === 200) {
    $teachers = $teachers_response['data'];
} else {
    $error = 'فشل في جلب الأساتذة: ' . ($teachers_response['data']['error'] ?? ($teachers_response['raw_response'] ?? 'خطأ غير معروف.'));
}

// Get teacher to edit
$edit_teacher = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_url = $endpoint . '?id=' . $_GET['id'];
    $response = api_request('GET', $edit_url, $token);
    if ($response['http_code'] === 200 && !empty($response['data'])) {
        $edit_teacher = $response['data'];
    } else {
        $error = 'لم يتم العثور على الأستاذ.';
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
            <h1 class="text-3xl font-bold mb-6">إدارة الأساتذة</h1>

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
                <h2 class="text-2xl font-bold mb-4"><?php echo $edit_teacher ? 'تعديل معلم' : 'إضافة معلم جديد'; ?></h2>
                <form method="post" action="teachers.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $edit_teacher['id'] ?? ''; ?>">
                    
                    <div class="mb-4">
                        <label for="full_name" class="block text-gray-700 font-bold mb-2">الاسم الكامل</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($edit_teacher['full_name'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="block text-gray-700 font-bold mb-2">نبذة تعريفية</label>
                        <textarea id="bio" name="bio" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($edit_teacher['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="profile_image" class="block text-gray-700 font-bold mb-2">صورة الملف الشخصي</label>
                        <input type="file" id="profile_image" name="profile_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php if (isset($edit_teacher['profile_image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($edit_teacher['profile_image_url']); ?>" alt="Profile Image" class="mt-2 h-24 w-24 object-cover rounded-full">
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            <?php echo $edit_teacher ? 'تحديث الأستاذ' : 'إضافة معلم'; ?>
                        </button>
                        <?php if ($edit_teacher): ?>
                            <a href="teachers.php" class="text-gray-600 hover:text-gray-800">إلغاء التعديل</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Teachers Table -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">قائمة الأساتذة</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="text-right py-3 px-4 uppercase font-semibold text-sm">الصورة</th>
                                <th class="text-right py-3 px-4 uppercase font-semibold text-sm">الاسم الكامل</th>
                                <th class="text-right py-3 px-4 uppercase font-semibold text-sm">نبذة تعريفية</th>
                                <th class="text-right py-3 px-4 uppercase font-semibold text-sm">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <tr>
                                        <td class="text-right py-3 px-4">
                                            <?php if (!empty($teacher['profile_image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($teacher['profile_image_url']); ?>" alt="<?php echo htmlspecialchars($teacher['full_name']); ?>" class="h-12 w-12 object-cover rounded-full">
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right py-3 px-4"><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                        <td class="text-right py-3 px-4"><?php echo htmlspecialchars($teacher['bio']); ?></td>
                                        <td class="text-right py-3 px-4">
                                            <a href="teachers.php?action=edit&id=<?php echo $teacher['id']; ?>" class="text-blue-500 hover:text-blue-700">تعديل</a>
                                            <a href="teachers.php?action=delete&id=<?php echo $teacher['id']; ?>" class="text-red-500 hover:text-red-700 ml-4" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا الأستاذ؟');">حذف</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">لا يوجد معلمين لعرضهم.</td>
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