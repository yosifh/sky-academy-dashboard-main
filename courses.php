<?php
session_start();

// Require login
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

include 'includes/api_helper.php';

$page_title = 'إدارة الدورات';
$endpoint = 'https://qxkyfdasymxphjjzxwfn.supabase.co/functions/v1/admin-course-crud';
$token = $_SESSION['admin_token'] ?? null;

$error = '';
$success = '';

// Actions
$action = $_GET['action'] ?? 'list';
$course_id = $_GET['id'] ?? null;

// Handle Form Submissions for Create/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $duration_text = trim($_POST['duration_text'] ?? '');
    $course_type_id = $_POST['course_type_id'] ?? null;
    $cover_image_url = trim($_POST['cover_image_url'] ?? '');
    $max_students = !empty($_POST['max_students']) ? (int)$_POST['max_students'] : null;
    $teacher_ids_str = trim($_POST['teacher_ids'] ?? '');
    $teacher_ids = !empty($teacher_ids_str) ? array_map('trim', explode(',', $teacher_ids_str)) : [];

    // Validate required fields
    if (empty($course_name)) {
        $error = 'اسم الدورة مطلوب.';
    } elseif (empty($course_type_id)) {
        $error = 'نوع الدورة مطلوب.';
    } else {
        $payload = [
            'name' => $course_name,
            'description' => $description,
            'price' => (float)$price,
            'duration_text' => $duration_text,
            'course_type_id' => (int)$course_type_id,
            'cover_image_url' => $cover_image_url,
            'max_students' => $max_students,
            'teacher_ids' => $teacher_ids,
        ];

        if ($course_id) {
            // Update - using PATCH
            $url = $endpoint . '?action=update&id=' . urlencode($course_id);
            $response = api_request('PATCH', $url, $token, $payload);
            
            if ($response['http_code'] === 200 && !empty($response['data']['success'])) {
                $success = 'تم تحديث الدورة بنجاح.';
                $action = 'list';
                $course_id = null;
            } else {
                $error = 'فشل تحديث الدورة: ' . ($response['data']['error'] ?? ($response['data']['message'] ?? ($response['raw_response'] ?? 'خطأ غير معروف.')));
                $action = 'edit';
            }
        } else {
            // Create - using POST
            $url = $endpoint . '?action=create';
            $response = api_request('POST', $url, $token, $payload);
            
            if ($response['http_code'] === 201 && !empty($response['data']['success'])) {
                $success = 'تمت إضافة الدورة بنجاح.';
                $action = 'list';
            } else {
                $error = 'فشل إضافة الدورة: ' . ($response['data']['error'] ?? ($response['data']['message'] ?? ($response['raw_response'] ?? 'خطأ غير معروف.')));
                $action = 'create';
            }
        }
    }
}

// Handle Delete
if ($action === 'delete' && $course_id) {
    $cascade = isset($_GET['cascade']) && $_GET['cascade'] === 'true';
    $url = $endpoint . '?action=delete&id=' . urlencode($course_id) . ($cascade ? '&cascade=true' : '');
    $response = api_request('DELETE', $url, $token);

    if ($response['http_code'] === 200 && !empty($response['data']['success'])) {
        $success = 'تم حذف الدورة بنجاح.';
        $action = 'list';
        $course_id = null;
    } elseif ($response['http_code'] === 409 || !empty($response['data']['enrollmentCount'])) {
        $enrollCount = $response['data']['enrollmentCount'] ?? 'عدة';
        $error = 'لا يمكن حذف الدورة لوجود ' . $enrollCount . ' طلاب مسجلين فيها. <a href="courses.php?action=delete&id='.$course_id.'&cascade=true" class="font-bold text-red-700 hover:underline" onclick="return confirm(\'تحذير: سيتم حذف الدورة وجميع البيانات المتعلقة بها (التسجيلات، المحاضرات، الصور). هل أنت متأكد؟\');">اضغط هنا للحذف مع كل بياناتها المتعلقة.</a>';
        $action = 'list';
    } else {
        $error = 'فشل حذف الدورة: ' . ($response['data']['error'] ?? ($response['data']['message'] ?? 'خطأ غير معروف.'));
        $action = 'list';
    }
}

// Data fetching for views
$courses = [];
$edit_course = null;
$teachers = [];
$course_types = [
    ['id' => 1, 'name' => 'Online - دورة أونلاين'],
    ['id' => 2, 'name' => 'Offline - دورة حضورية'],
];

// Fetch all courses for list view
if ($action === 'list') {
    $limit = $_GET['limit'] ?? 20;
    $offset = $_GET['offset'] ?? 0;
    $course_type_filter = $_GET['course_type_id'] ?? '';
    
    $url = $endpoint . '?action=list&limit=' . $limit . '&offset=' . $offset;
    if (!empty($course_type_filter)) {
        $url .= '&course_type_id=' . urlencode($course_type_filter);
    }
    
    $list_response = api_request('GET', $url, $token);
    
    if ($list_response['http_code'] === 200 && !empty($list_response['data']['success'])) {
        $courses = $list_response['data']['courses'] ?? [];
        $pagination = $list_response['data']['pagination'] ?? null;
    } else {
        $error = 'فشل في جلب الدورات: ' . ($list_response['data']['error'] ?? ($list_response['raw_response'] ?? 'خطأ غير معروف.'));
    }
}

// Fetch single course for edit view
if ($action === 'edit' && $course_id) {
    $url = $endpoint . '?action=get&id=' . urlencode($course_id);
    $course_response = api_request('GET', $url, $token);
    
    if ($course_response['http_code'] === 200 && !empty($course_response['data']['success'])) {
        $edit_course = $course_response['data']['course'];
    } else {
        $error = 'فشل في جلب بيانات الدورة: ' . ($course_response['data']['error'] ?? 'خطأ غير معروف.');
        $action = 'list';
    }
}

// Fetch teachers for the form dropdown/helper
if ($action === 'create' || $action === 'edit') {
    // Using the old endpoint for teachers list
    $teachers_endpoint = 'https://qxkyfdasymxphjjzxwfn.supabase.co/functions/v1/admin-courses-lectures-teachers';
    $teachers_response = api_request('GET', $teachers_endpoint . '?resource=teachers&action=list', $token);
    
    if ($teachers_response['http_code'] === 200 && !empty($teachers_response['data']['success'])) {
        $teachers = $teachers_response['data']['teachers'] ?? [];
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">إدارة الدورات</h1>
                <?php if ($action === 'list'): ?>
                <a href="courses.php?action=create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    <i class="fas fa-plus ml-2"></i> إضافة دورة جديدة
                </a>
                <?php else: ?>
                <a href="courses.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    <i class="fas fa-arrow-right ml-2"></i> العودة إلى قائمة الدورات
                </a>
                <?php endif; ?>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border-r-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow-md mb-6" role="alert">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle ml-3 mt-1"></i>
                        <div>
                            <strong class="font-bold">خطأ!</strong>
                            <span class="block sm:inline mt-1"><?php echo $error; ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border-r-4 border-green-500 text-green-700 px-6 py-4 rounded-lg shadow-md mb-6" role="alert">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle ml-3 mt-1"></i>
                        <div>
                            <strong class="font-bold">نجاح!</strong>
                            <span class="block sm:inline mt-1"><?php echo htmlspecialchars($success); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
            <!-- Courses Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 md:mb-0">قائمة الدورات</h2>
                        
                        <!-- Filter Form -->
                        <form method="get" action="courses.php" class="flex items-center gap-2">
                            <input type="hidden" name="action" value="list">
                            <select name="course_type_id" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">جميع الأنواع</option>
                                <?php foreach ($course_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>" <?php echo (isset($_GET['course_type_id']) && $_GET['course_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="text-right py-4 px-6 uppercase font-semibold text-sm">اسم الدورة</th>
                                <th class="text-right py-4 px-6 uppercase font-semibold text-sm">النوع</th>
                                <th class="text-right py-4 px-6 uppercase font-semibold text-sm">السعر</th>
                                <th class="text-right py-4 px-6 uppercase font-semibold text-sm">المدة</th>
                                <th class="text-right py-4 px-6 uppercase font-semibold text-sm">المعلمين</th>
                                <th class="text-right py-4 px-6 uppercase font-semibold text-sm">الطلاب</th>
                                <th class="text-right py-4 px-6 uppercase font-semibold text-sm">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="text-right py-4 px-6">
                                            <div class="flex items-center">
                                                <?php if (!empty($course['cover_image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($course['cover_image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($course['name']); ?>" 
                                                         class="w-12 h-12 rounded-lg object-cover ml-3">
                                                <?php endif; ?>
                                                <div>
                                                    <div class="font-semibold text-gray-900"><?php echo htmlspecialchars($course['name']); ?></div>
                                                    <?php if (!empty($course['description'])): ?>
                                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($course['description'], 0, 50)) . (strlen($course['description']) > 50 ? '...' : ''); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right py-4 px-6">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo ($course['course_types']['id'] ?? 0) == 1 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                                <?php echo htmlspecialchars($course['course_types']['name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="text-right py-4 px-6 font-semibold text-gray-900">
                                            <?php echo number_format($course['price'], 2); ?> د.ع
                                        </td>
                                        <td class="text-right py-4 px-6 text-gray-600">
                                            <?php echo htmlspecialchars($course['duration_text'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="text-right py-4 px-6">
                                            <?php
                                            if (!empty($course['teachers'])) {
                                                $teacher_names = array_map(fn($t) => htmlspecialchars($t['full_name']), $course['teachers']);
                                                echo '<div class="text-sm text-gray-700">' . implode('<br>', array_slice($teacher_names, 0, 2)) . '</div>';
                                                if (count($teacher_names) > 2) {
                                                    echo '<div class="text-xs text-gray-500 mt-1">+' . (count($teacher_names) - 2) . ' المزيد</div>';
                                                }
                                            } else {
                                                echo '<span class="text-gray-400">لا يوجد</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right py-4 px-6">
                                            <?php if (isset($course['statistics'])): ?>
                                                <div class="text-sm">
                                                    <div class="text-gray-900 font-semibold"><?php echo $course['statistics']['total_enrollments'] ?? 0; ?></div>
                                                    <div class="text-xs text-gray-500">
                                                        <?php 
                                                        $completed = $course['statistics']['completed_enrollments'] ?? 0;
                                                        $pending = $course['statistics']['pending_enrollments'] ?? 0;
                                                        echo "مكتمل: {$completed} | معلق: {$pending}";
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-gray-400">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right py-4 px-6 whitespace-nowrap">
                                            <a href="courses.php?action=edit&id=<?php echo urlencode($course['id']); ?>" 
                                               class="text-blue-600 hover:text-blue-800 font-medium ml-4 transition duration-150">
                                                <i class="fas fa-edit ml-1"></i> تعديل
                                            </a>
                                            <a href="courses.php?action=delete&id=<?php echo urlencode($course['id']); ?>" 
                                               class="text-red-600 hover:text-red-800 font-medium transition duration-150" 
                                               onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه الدورة؟');">
                                                <i class="fas fa-trash ml-1"></i> حذف
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-12">
                                        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg">لا يوجد دورات لعرضها.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($pagination) && $pagination['total'] > $pagination['limit']): ?>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        عرض <span class="font-semibold"><?php echo $pagination['offset'] + 1; ?></span> إلى 
                        <span class="font-semibold"><?php echo min($pagination['offset'] + $pagination['limit'], $pagination['total']); ?></span> من 
                        <span class="font-semibold"><?php echo $pagination['total']; ?></span> نتيجة
                    </div>
                    <div class="flex gap-2">
                        <?php if ($pagination['offset'] > 0): ?>
                            <a href="courses.php?action=list&offset=<?php echo max(0, $pagination['offset'] - $pagination['limit']); ?>&limit=<?php echo $pagination['limit']; ?>" 
                               class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg transition duration-200">
                                السابق
                            </a>
                        <?php endif; ?>
                        <?php if ($pagination['hasMore']): ?>
                            <a href="courses.php?action=list&offset=<?php echo $pagination['offset'] + $pagination['limit']; ?>&limit=<?php echo $pagination['limit']; ?>" 
                               class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg transition duration-200">
                                التالي
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($action === 'create' || $action === 'edit'): ?>
            <!-- Add/Edit Form -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700">
                    <h2 class="text-2xl font-bold text-white">
                        <i class="fas fa-<?php echo $course_id ? 'edit' : 'plus-circle'; ?> ml-2"></i>
                        <?php echo $course_id ? 'تعديل دورة' : 'إضافة دورة جديدة'; ?>
                    </h2>
                </div>
                
                <form method="post" action="courses.php?action=<?php echo $action; ?><?php echo $course_id ? '&id='.urlencode($course_id) : ''; ?>" class="p-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Course Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-gray-700 font-bold mb-2">
                                اسم الدورة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($edit_course['name'] ?? ''); ?>" 
                                   class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   required 
                                   placeholder="أدخل اسم الدورة">
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-gray-700 font-bold mb-2">
                                الوصف
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4" 
                                      class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="أدخل وصف الدورة"><?php echo htmlspecialchars($edit_course['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-gray-700 font-bold mb-2">
                                السعر (دينار عراقي)
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   id="price" 
                                   name="price" 
                                   value="<?php echo htmlspecialchars($edit_course['price'] ?? '0.00'); ?>" 
                                   class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0.00">
                        </div>

                        <!-- Duration Text -->
                        <div>
                            <label for="duration_text" class="block text-gray-700 font-bold mb-2">
                                مدة الدورة
                            </label>
                            <input type="text" 
                                   id="duration_text" 
                                   name="duration_text" 
                                   value="<?php echo htmlspecialchars($edit_course['duration_text'] ?? ''); ?>" 
                                   class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="مثال: 8 أسابيع">
                        </div>

                        <!-- Course Type -->
                        <div>
                            <label for="course_type_id" class="block text-gray-700 font-bold mb-2">
                                نوع الدورة <span class="text-red-500">*</span>
                            </label>
                            <select id="course_type_id" 
                                    name="course_type_id" 
                                    class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                    required>
                                <option value="">اختر نوع الدورة</option>
                                <?php foreach ($course_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>" 
                                            <?php echo (isset($edit_course['course_type_id']) && $edit_course['course_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Max Students -->
                        <div>
                            <label for="max_students" class="block text-gray-700 font-bold mb-2">
                                أقصى عدد للطلاب
                            </label>
                            <input type="number" 
                                   id="max_students" 
                                   name="max_students" 
                                   value="<?php echo htmlspecialchars($edit_course['max_students'] ?? ''); ?>" 
                                   class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="اتركه فارغاً للدورات الأونلاين">
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-info-circle ml-1"></i>
                                اتركه فارغاً للدورات الأونلاين (عدد غير محدود)
                            </p>
                        </div>

                        <!-- Cover Image Upload -->
                        <div class="md:col-span-2">
                            <label for="cover_image_file" class="block text-gray-700 font-bold mb-2">
                                صورة الغلاف
                            </label>
                            <div class="flex items-start gap-4">
                                <div class="flex-1">
                                    <input type="file" 
                                           id="cover_image_file" 
                                           accept="image/*"
                                           class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <input type="hidden" 
                                           id="cover_image_url" 
                                           name="cover_image_url" 
                                           value="<?php echo htmlspecialchars($edit_course['cover_image_url'] ?? ''); ?>">
                                    <div id="image-upload-spinner" class="hidden mt-3 text-blue-600">
                                        <i class="fas fa-spinner fa-spin ml-2"></i> 
                                        <span>جاري رفع الصورة...</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-info-circle ml-1"></i>
                                        الحد الأقصى: 5MB | الصيغ المدعومة: JPG, PNG, WebP
                                    </p>
                                </div>
                                <?php if (!empty($edit_course['cover_image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($edit_course['cover_image_url']); ?>" 
                                         id="image-preview" 
                                         class="w-32 h-32 object-cover rounded-lg shadow-md border-2 border-gray-200">
                                <?php else: ?>
                                    <img src="" 
                                         id="image-preview" 
                                         class="hidden w-32 h-32 object-cover rounded-lg shadow-md border-2 border-gray-200">
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Teachers -->
                        <div class="md:col-span-2">
                            <label for="teacher_ids" class="block text-gray-700 font-bold mb-2">
                                معرفات المعلمين
                            </label>
                            <input type="text" 
                                   id="teacher_ids" 
                                   name="teacher_ids" 
                                   class="shadow-sm border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="<?php
                                   if (isset($edit_course['teachers']) && !empty($edit_course['teachers'])) {
                                       echo htmlspecialchars(implode(', ', array_map(fn($t) => $t['id'], $edit_course['teachers'])));
                                   }
                                   ?>"
                                   placeholder="teacher-uuid-1, teacher-uuid-2">
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle ml-1"></i>
                                أدخل معرفات المعلمين مفصولة بفاصلة. مثال: teacher-uuid-1, teacher-uuid-2
                            </p>
                            
                            <?php if (!empty($teachers)): ?>
                            <div class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm font-semibold text-blue-900 mb-2">
                                    <i class="fas fa-users ml-1"></i> المعلمون المتاحون:
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <?php foreach ($teachers as $teacher): ?>
                                        <div class="text-sm text-blue-700 bg-white px-3 py-2 rounded border border-blue-100">
                                            <span class="font-semibold"><?php echo htmlspecialchars($teacher['full_name'] ?? 'N/A'); ?></span>
                                            <br>
                                            <code class="text-xs text-gray-600 select-all"><?php echo htmlspecialchars($teacher['id']); ?></code>
                                            <button type="button" 
                                                    onclick="copyTeacherId('<?php echo htmlspecialchars($teacher['id']); ?>')"
                                                    class="mr-2 text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <a href="courses.php" 
                           class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                            <i class="fas fa-times ml-2"></i> إلغاء
                        </a>
                        <button type="submit" 
                                id="submit-button" 
                                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-<?php echo $course_id ? 'save' : 'plus'; ?> ml-2"></i>
                            <?php echo $course_id ? 'تحديث الدورة' : 'إضافة الدورة'; ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <script>
            // Copy teacher ID to clipboard
            function copyTeacherId(teacherId) {
                const currentInput = document.getElementById('teacher_ids');
                const currentValue = currentInput.value.trim();
                
                if (currentValue === '') {
                    currentInput.value = teacherId;
                } else {
                    // Check if ID already exists
                    const ids = currentValue.split(',').map(id => id.trim());
                    if (!ids.includes(teacherId)) {
                        currentInput.value = currentValue + ', ' + teacherId;
                    }
                }
                
                // Visual feedback
                const button = event.target.closest('button');
                const icon = button.querySelector('i');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                }, 1500);
            }
            
            // Image upload handler
            document.getElementById('cover_image_file').addEventListener('change', async function(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Validate file size (5MB max)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alert('حجم الملف كبير جداً. الحد الأقصى هو 5MB.');
                    this.value = '';
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('نوع الملف غير مدعوم. يرجى اختيار JPG أو PNG أو WebP.');
                    this.value = '';
                    return;
                }

                const spinner = document.getElementById('image-upload-spinner');
                const submitButton = document.getElementById('submit-button');
                const imageUrlInput = document.getElementById('cover_image_url');
                const imagePreview = document.getElementById('image-preview');

                spinner.classList.remove('hidden');
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = async () => {
                    const base64 = reader.result.split(',')[1];
                    
                    try {
                        const response = await fetch('https://qxkyfdasymxphjjzxwfn.supabase.co/functions/v1/admin-courses-lectures-teachers?resource=images&action=upload', {
                            method: 'POST',
                            headers: {
                                'Authorization': 'Bearer <?php echo $token; ?>',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                image_base64: base64,
                                image_mime_type: file.type,
                                image_file_name: file.name,
                                bucket: 'course_covers'
                            })
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            imageUrlInput.value = result.publicUrl;
                            imagePreview.src = result.publicUrl;
                            imagePreview.classList.remove('hidden');
                            
                            // Success notification
                            showNotification('تم رفع الصورة بنجاح!', 'success');
                        } else {
                            throw new Error(result.error || 'فشل رفع الصورة.');
                        }
                    } catch (e) {
                        showNotification('خطأ: ' + e.message, 'error');
                        imageUrlInput.value = '';
                        imagePreview.classList.add('hidden');
                    } finally {
                        spinner.classList.add('hidden');
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                };
                
                reader.onerror = () => {
                    showNotification('خطأ في قراءة الملف', 'error');
                    spinner.classList.add('hidden');
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                };
            });
            
            // Notification helper
            function showNotification(message, type = 'info') {
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    info: 'bg-blue-500'
                };
                
                const notification = document.createElement('div');
                notification.className = `fixed top-4 left-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} ml-2"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
            
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const courseTypeId = document.getElementById('course_type_id').value;
                
                if (name === '') {
                    e.preventDefault();
                    showNotification('يرجى إدخال اسم الدورة', 'error');
                    document.getElementById('name').focus();
                    return false;
                }
                
                if (courseTypeId === '') {
                    e.preventDefault();
                    showNotification('يرجى اختيار نوع الدورة', 'error');
                    document.getElementById('course_type_id').focus();
                    return false;
                }
                
                // Show loading state
                const submitButton = document.getElementById('submit-button');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الحفظ...';
            });
            </script>
            <?php endif; ?>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </main>
</div>

<script>
// Auto-hide success/error messages after 5 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>

<?php include 'includes/footer.php'; ?>