<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'إدارة الدورات';
include 'includes/header.php';

// Get token from session
$token = isset($_SESSION['admin_token']) ? $_SESSION['admin_token'] : '';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="lg:mr-64">
    <?php include 'includes/navbar.php'; ?>

    <!-- Page Content -->
    <main class="pt-20 min-h-screen bg-gray-50">
        <div class="p-4 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة الدورات</h1>
                    <p class="text-gray-600">إدارة وتنظيم دورات الأكاديمية</p>
                </div>
                <div class="flex space-x-2 space-x-reverse">
                    <a href="lectures.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-right mr-2"></i>
                        المحاضرات
                    </a>
                    <button onclick="showCreateForm()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة دورة جديدة
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <div id="successMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span id="successText"></span>
                </div>
            </div>

            <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span id="errorText"></span>
                </div>
            </div>

            <!-- Create/Edit Form -->
            <div id="courseForm" class="hidden bg-white rounded-lg shadow-md mb-8">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 id="formTitle" class="text-2xl font-bold text-gray-900">إضافة دورة جديدة</h2>
                </div>
                <div class="p-6">
                    <form id="courseDataForm" class="space-y-6">
                        <input type="hidden" id="courseId" name="courseId">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="courseName" class="block text-sm font-medium text-gray-700 mb-2">اسم الدورة *</label>
                                <input type="text" id="courseName" name="name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                            </div>

                            <div>
                                <label for="coursePrice" class="block text-sm font-medium text-gray-700 mb-2">السعر *</label>
                                <input
                                    type="number"
                                    id="coursePrice"
                                    name="price"
                                    value="0"
                                    min="0"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">

                            </div>
                        </div>

                        <div>
                            <label for="courseDescription" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                            <textarea id="courseDescription" name="description" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="courseDuration" class="block text-sm font-medium text-gray-700 mb-2">مدة الدورة</label>
                                <input type="text" id="courseDuration" name="duration_text" placeholder="مثال: 3 أشهر"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                            </div>

                            <div>
                                <label for="courseTypeId" class="block text-sm font-medium text-gray-700 mb-2">نوع الدورة *</label>
                                <select id="courseTypeId" name="course_type_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                                    <option value="">-- اختر نوع الدورة --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="courseCoverImageFile" class="block text-sm font-medium text-gray-700 mb-2">صورة الغلاف</label>
                                <input type="file" id="courseCoverImageFile" accept="image/*" onchange="previewImage(this)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                                <input type="hidden" id="courseCoverImage" name="cover_image_url">
                                <div id="imagePreview" class="mt-4"></div>
                                <div id="uploadProgress" class="mt-2 text-sm text-gray-600"></div>
                            </div>

                            <div>
                                <label for="courseMaxStudents" class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للطلاب</label>
                                <input type="number" id="courseMaxStudents" name="max_students"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                            </div>
                        </div>

                        <div>
                            <label for="courseTeachers" class="block text-sm font-medium text-gray-700 mb-2">المدرسون</label>
                            <select id="courseTeachers" name="teacher_ids" multiple size="5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                                <option value="">جاري التحميل...</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-600">استخدم Ctrl+Click لاختيار متعدد</p>
                        </div>

                        <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                            <button type="button" onclick="hideForm()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                                إلغاء
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition duration-150 ease-in-out">
                                <i class="fas fa-save mr-2"></i>
                                حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Courses List -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900">قائمة الدورات</h2>
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="relative">
                                <input type="text" id="searchCourses" placeholder="البحث في الدورات..."
                                    class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 pr-10 transition duration-150 ease-in-out">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <select id="filterCourseType" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                                <option value="">جميع الأنواع</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div id="coursesContainer" class="overflow-x-auto"></div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
    </main>
</div>

<script>
    const SUPABASE_URL = 'https://qxkyfdasymxphjjzxwfn.supabase.co';
    const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InF4a3lmZGFzeW14cGhqanp4d2ZuIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjI3MTYzNTksImV4cCI6MjA3ODI5MjM1OX0.8ttsxRpM98K-qRJrlE0KUjb6oLXT6GhRFkW2SCH7Gi8';
    const REST_URL = `${SUPABASE_URL}/rest/v1`;
    const TOKEN = '<?php echo $token; ?>';

    // Initialize Supabase client
    const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

    let isEditMode = false;
    let currentCourseId = null;

    // Authenticate user on page load
    async function authenticateUser() {
        try {
            if (TOKEN) {
                // Set the session with the admin token
                const {
                    data,
                    error
                } = await supabase.auth.setSession({
                    access_token: TOKEN,
                    refresh_token: TOKEN
                });

                if (error) {
                    console.warn('Authentication warning:', error);
                    // Continue anyway - we'll handle auth in upload if needed
                } else {
                    console.log('User authenticated for storage uploads');
                }
            }
        } catch (error) {
            console.error('Error authenticating user:', error);
        }
    }

    // Load courses on page load
    document.addEventListener('DOMContentLoaded', async function() {
        await authenticateUser();
        loadCourseTypes();
        loadTeachers();
        loadCourses();

        // Add search functionality
        const searchInput = document.getElementById('searchCourses');
        const filterSelect = document.getElementById('filterCourseType');

        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterCourses();
            }, 300);
        });

        filterSelect.addEventListener('change', function() {
            filterCourses();
        });
    });

    let allCourses = []; // Store all courses for filtering

    // Translate course type from English to Arabic
    function translateCourseType(type) {
        const translations = {
            'online': 'إلكتروني',
            'in_person': 'حضوري'
        };
        return translations[type] || type;
    }

    // Filter courses based on search and type
    function filterCourses() {
        const searchTerm = document.getElementById('searchCourses').value.toLowerCase();
        const selectedType = document.getElementById('filterCourseType').value;

        let filtered = allCourses.filter(course => {
            const matchesSearch = course.name.toLowerCase().includes(searchTerm) ||
                (course.description && course.description.toLowerCase().includes(searchTerm)) ||
                (course.teachers && course.teachers.some(t => t.full_name.toLowerCase().includes(searchTerm)));

            const matchesType = !selectedType || course.course_type_id == selectedType;

            return matchesSearch && matchesType;
        });

        displayCourses(filtered);
    }

    // Load course types
    async function loadCourseTypes() {
        try {
            console.log('Loading course types...');
            const response = await fetch(`${SUPABASE_URL}/rest/v1/course_types?select=*&order=id`, {
                method: 'GET',
                headers: {
                    'apikey': SUPABASE_ANON_KEY,
                    'Authorization': `Bearer ${SUPABASE_ANON_KEY}`
                }
            });

            const courseTypes = await response.json();
            console.log('Course types response:', courseTypes);

            // Populate form select
            const select = document.getElementById('courseTypeId');
            select.innerHTML = '<option value="">-- اختر نوع الدورة --</option>';

            // Populate filter select
            const filterSelect = document.getElementById('filterCourseType');
            filterSelect.innerHTML = '<option value="">جميع الأنواع</option>';

            courseTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = translateCourseType(type.name);
                select.appendChild(option);

                const filterOption = document.createElement('option');
                filterOption.value = type.id;
                filterOption.textContent = translateCourseType(type.name);
                filterSelect.appendChild(filterOption);
            });
        } catch (error) {
            console.error('Error loading course types:', error);
        }
    }

    // Load teachers
    async function loadTeachers() {
        try {
            console.log('Loading teachers...');
            const response = await fetch(`${SUPABASE_URL}/rest/v1/teachers?select=*&order=full_name`, {
                method: 'GET',
                headers: {
                    'apikey': SUPABASE_ANON_KEY,
                    'Authorization': `Bearer ${SUPABASE_ANON_KEY}`
                }
            });

            const teachers = await response.json();
            console.log('Teachers response:', teachers);

            const select = document.getElementById('courseTeachers');
            select.innerHTML = '';

            teachers.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = teacher.full_name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading teachers:', error);
        }
    }

    // Show/hide messages
    function showMessage(type, message) {
        hideMessages();
        const messageElement = document.getElementById(type + 'Message');
        const textElement = document.getElementById(type + 'Text');
        textElement.textContent = message;
        messageElement.classList.remove('hidden');

        // Auto-hide after 5 seconds
        setTimeout(() => {
            messageElement.classList.add('hidden');
        }, 5000);
    }

    function hideMessages() {
        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');
    }

    function showCreateForm() {
        document.getElementById('courseForm').classList.remove('hidden');
        document.getElementById('formTitle').textContent = 'إضافة دورة جديدة';
        document.getElementById('courseDataForm').reset();
        document.getElementById('imagePreview').innerHTML = '';
        document.getElementById('uploadProgress').innerHTML = '';

        // Clear teacher selections
        const teacherSelect = document.getElementById('courseTeachers');
        for (let i = 0; i < teacherSelect.options.length; i++) {
            teacherSelect.options[i].selected = false;
        }

        hideMessages();
        isEditMode = false;
        currentCourseId = null;

        // Scroll to form
        document.getElementById('courseForm').scrollIntoView({
            behavior: 'smooth'
        });
    }

    function hideForm() {
        document.getElementById('courseForm').classList.add('hidden');
        document.getElementById('courseDataForm').reset();
        document.getElementById('imagePreview').innerHTML = '';
        document.getElementById('uploadProgress').innerHTML = '';
        hideMessages();
    }

    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                <div class="relative inline-block">
                    <img src="${e.target.result}" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                        <i class="fas fa-check mr-1"></i>تم التحديد
                    </div>
                </div>
            `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showEditForm(course) {
        console.log('Editing course:', course);
        document.getElementById('courseForm').classList.remove('hidden');
        document.getElementById('formTitle').textContent = 'تعديل الدورة';

        document.getElementById('courseId').value = course.id;
        document.getElementById('courseName').value = course.name || '';
        document.getElementById('courseDescription').value = course.description || '';
        document.getElementById('coursePrice').value = course.price || 0;
        document.getElementById('courseDuration').value = course.duration_text || '';
        document.getElementById('courseTypeId').value = course.course_type_id || '';
        document.getElementById('courseCoverImage').value = course.cover_image_url || '';
        document.getElementById('courseMaxStudents').value = course.max_students || '';

        // Show existing image
        if (course.cover_image_url) {
            document.getElementById('imagePreview').innerHTML = `
            <div class="relative inline-block">
                <img src="${course.cover_image_url}" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                    <i class="fas fa-image mr-1"></i>الحالية
                </div>
            </div>
        `;
        }

        // Select teachers
        const teacherSelect = document.getElementById('courseTeachers');
        for (let i = 0; i < teacherSelect.options.length; i++) {
            teacherSelect.options[i].selected = false;
        }

        if (course.teachers && course.teachers.length > 0) {
            const teacherIds = course.teachers.map(t => t.id.toString());
            for (let i = 0; i < teacherSelect.options.length; i++) {
                if (teacherIds.includes(teacherSelect.options[i].value)) {
                    teacherSelect.options[i].selected = true;
                }
            }
        }

        hideMessages();
        isEditMode = true;
        currentCourseId = course.id;

        // Scroll to form
        document.getElementById('courseForm').scrollIntoView({
            behavior: 'smooth'
        });
    }

    // Load all courses
    async function loadCourses() {
        try {
            console.log('Loading courses...');

            // Show loading spinner
            const container = document.getElementById('coursesContainer');
            container.innerHTML = `
            <div class="flex justify-center items-center py-12">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i>
                    <span class="text-lg text-gray-600">جاري تحميل الدورات...</span>
                </div>
            </div>
        `;

            const url = `${REST_URL}/courses?select=*,course_types(name),teachers(id,full_name)&order=created_at.desc`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'apikey': SUPABASE_ANON_KEY
                }
            });

            const courses = await response.json();
            console.log('Courses response:', courses);

            if (Array.isArray(courses)) {
                allCourses = courses; // Store for filtering
                displayCourses(courses);
            } else {
                console.error('Failed to load courses:', courses);
                showMessage('error', 'فشل تحميل الدورات');
            }
        } catch (error) {
            console.error('Error loading courses:', error);
            showMessage('error', 'خطأ في تحميل الدورات');
        }
    }

    // Display courses
    function displayCourses(courses) {
        const container = document.getElementById('coursesContainer');

        if (courses.length === 0) {
            container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-book text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 text-lg">لا توجد دورات</p>
                <button onclick="showCreateForm()" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition duration-150 ease-in-out">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة أول دورة
                </button>
            </div>
        `;
            return;
        }

        let html = `
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصورة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدرسون</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    `;

        courses.forEach(course => {
            const teachers = course.teachers ? course.teachers.map(t => t.full_name).join(', ') : 'لا يوجد';
            const typeName = course.course_types ? translateCourseType(course.course_types.name) : translateCourseType(course.course_type_id);
            const duration = course.duration_text || '-';
            const price = course.price ? parseFloat(course.price).toFixed(2) + ' دينار' : 'مجاني';

            html += `
            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                <td class="px-6 py-4 whitespace-nowrap">
                    ${course.cover_image_url ? 
                        `<img src="${course.cover_image_url}" alt="${course.name}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">` :
                        `<div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                            <i class="fas fa-image text-gray-400 text-xl"></i>
                        </div>`
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${course.name}</div>
                    ${course.description ? `<div class="text-sm text-gray-500 truncate max-w-xs">${course.description}</div>` : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${course.price == 0 ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                        ${price}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${typeName}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${duration}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 max-w-xs truncate">${teachers}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2 space-x-reverse">
                        <button onclick='viewCourse("${course.id}")' 
                                title="عرض التفاصيل"
                                class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition duration-150 ease-in-out">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick='editCourse("${course.id}")' 
                                title="تعديل"
                                class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition duration-150 ease-in-out">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick='deleteCourse("${course.id}")' 
                                title="حذف"
                                class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition duration-150 ease-in-out">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        });

        html += `
                </tbody>
            </table>
        </div>
    `;

        container.innerHTML = html;
    }

    // Upload image to Supabase Storage
    async function uploadCoverImage(file) {
        try {
            console.log('Uploading cover image:', file.name);
            document.getElementById('uploadProgress').innerHTML = `
            <div class="flex items-center text-blue-600">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                جاري رفع الصورة...
            </div>
        `;

            // Get authenticated user
            const {
                data: {
                    user
                },
                error: userError
            } = await supabase.auth.getUser();

            if (userError || !user) {
                throw new Error('User not authenticated. Please login again.');
            }

            // Create file path with user ID prefix
            const timestamp = Date.now();
            const fileName = `${timestamp}-${file.name}`;
            const filePath = `${user.id}/${fileName}`;

            console.log('Uploading to path:', filePath);

            // Upload using Supabase client
            const {
                data,
                error
            } = await supabase.storage
                .from('course-thumbnails')
                .upload(filePath, file, {
                    upsert: false,
                    contentType: file.type
                });

            if (error) {
                console.error('Upload error:', error);
                throw new Error(error.message || 'Upload failed');
            }

            // Get public URL
            const {
                data: {
                    publicUrl
                }
            } = supabase.storage
                .from('course-thumbnails')
                .getPublicUrl(filePath);

            console.log('Image uploaded successfully:', publicUrl);
            document.getElementById('uploadProgress').innerHTML = `
            <div class="flex items-center text-green-600">
                <i class="fas fa-check-circle mr-2"></i>
                تم رفع الصورة بنجاح!
            </div>
        `;
            return publicUrl;

        } catch (error) {
            console.error('Error uploading image:', error);
            document.getElementById('uploadProgress').innerHTML = `
            <div class="flex items-center text-red-600">
                <i class="fas fa-exclamation-circle mr-2"></i>
                خطأ في رفع الصورة: ${error.message}
            </div>
        `;
            throw error;
        }
    }

    // Form submission
    document.getElementById('courseDataForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            // Upload image if selected
            const fileInput = document.getElementById('courseCoverImageFile');
            let coverImageUrl = document.getElementById('courseCoverImage').value;

            if (fileInput.files && fileInput.files[0]) {
                coverImageUrl = await uploadCoverImage(fileInput.files[0]);
                document.getElementById('courseCoverImage').value = coverImageUrl;
            }

            const formData = new FormData(e.target);
            const data = {
                name: formData.get('name'),
                description: formData.get('description') || null,
                price: parseFloat(formData.get('price')) || 0,
                duration_text: formData.get('duration_text') || null,
                course_type_id: parseInt(formData.get('course_type_id')),
                cover_image_url: coverImageUrl || null,
                max_students: formData.get('max_students') ? parseInt(formData.get('max_students')) : null
            };

            // Get selected teachers
            const teacherSelect = document.getElementById('courseTeachers');
            const selectedTeachers = Array.from(teacherSelect.selectedOptions).map(option => option.value);
            if (selectedTeachers.length > 0) {
                data.teacher_ids = selectedTeachers;
            }

            console.log('Form data to submit:', data);

            if (isEditMode) {
                await updateCourse(currentCourseId, data);
            } else {
                await createCourse(data);
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showMessage('error', 'خطأ في حفظ البيانات: ' + error.message);
        }
    });

    // Create course
    async function createCourse(data) {
        try {
            console.log('Creating course:', data);

            // Extract teacher_ids before sending to courses table
            const teacher_ids = data.teacher_ids || [];
            delete data.teacher_ids;

            const url = `${REST_URL}/courses`;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'Content-Type': 'application/json',
                    'apikey': SUPABASE_ANON_KEY,
                    'Prefer': 'return=representation'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Create course response:', result);

            if (response.ok && Array.isArray(result) && result.length > 0) {
                const courseId = result[0].id;

                // Link teachers if any
                if (teacher_ids.length > 0) {
                    await linkTeachersToCourse(courseId, teacher_ids);
                }

                showMessage('success', 'تم إنشاء الدورة بنجاح');
                hideForm();
                loadCourses();
            } else {
                console.error('Failed to create course:', result);
                showMessage('error', 'فشل إنشاء الدورة: ' + (result.message || 'خطأ غير معروف'));
            }
        } catch (error) {
            console.error('Error creating course:', error);
            showMessage('error', 'خطأ في إنشاء الدورة: ' + error.message);
        }
    }

    // Link teachers to course
    async function linkTeachersToCourse(courseId, teacherIds) {
        try {
            const links = teacherIds.map(teacherId => ({
                course_id: courseId,
                teacher_id: teacherId
            }));

            const response = await fetch(`${REST_URL}/course_teachers`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'Content-Type': 'application/json',
                    'apikey': SUPABASE_ANON_KEY
                },
                body: JSON.stringify(links)
            });

            if (!response.ok) {
                console.error('Failed to link teachers');
            }
        } catch (error) {
            console.error('Error linking teachers:', error);
        }
    }

    // Update course
    async function updateCourse(courseId, data) {
        try {
            console.log('Updating course:', courseId, data);

            // Extract teacher_ids before sending to courses table
            const teacher_ids = data.teacher_ids || [];
            delete data.teacher_ids;

            const url = `${REST_URL}/courses?id=eq.${courseId}`;

            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'Content-Type': 'application/json',
                    'apikey': SUPABASE_ANON_KEY
                },
                body: JSON.stringify(data)
            });

            if (response.ok || response.status === 204) {
                // Update teachers: delete old links and create new ones
                if (teacher_ids.length > 0) {
                    // Delete existing teacher links
                    await fetch(`${REST_URL}/course_teachers?course_id=eq.${courseId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${TOKEN}`,
                            'apikey': SUPABASE_ANON_KEY
                        }
                    });

                    // Link new teachers
                    await linkTeachersToCourse(courseId, teacher_ids);
                }

                showMessage('success', 'تم تحديث الدورة بنجاح');
                hideForm();
                loadCourses();
            } else {
                const result = await response.json();
                console.error('Failed to update course:', result);
                showMessage('error', 'فشل تحديث الدورة: ' + (result.message || 'خطأ غير معروف'));
            }
        } catch (error) {
            console.error('Error updating course:', error);
            showMessage('error', 'خطأ في تحديث الدورة: ' + error.message);
        }
    }

    // View course details
    async function viewCourse(courseId) {
        try {
            console.log('Viewing course:', courseId);
            const url = `${REST_URL}/courses?select=*,course_types(name),teachers(id,full_name)&id=eq.${courseId}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'apikey': SUPABASE_ANON_KEY
                }
            });

            const courses = await response.json();
            console.log('Course details:', courses);

            if (Array.isArray(courses) && courses.length > 0) {
                const course = courses[0];
                const teachers = course.teachers ? course.teachers.map(t => t.full_name).join(', ') : 'لا يوجد';
                const typeName = course.course_types ? translateCourseType(course.course_types.name) : 'غير محدد';

                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modal.innerHTML = `
                <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
                    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-900">تفاصيل الدورة</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">اسم الدورة</label>
                                <p class="text-lg font-semibold text-gray-900">${course.name}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">السعر</label>
                                <p class="text-lg ${course.price == 0 ? 'text-green-600' : 'text-blue-600'} font-semibold">
                                    ${course.price == 0 ? 'مجاني' : parseFloat(course.price).toFixed(2) + ' دينار'}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">نوع الدورة</label>
                                <p class="text-gray-900">${typeName}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المدة</label>
                                <p class="text-gray-900">${course.duration_text || '-'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى للطلاب</label>
                                <p class="text-gray-900">${course.max_students || 'غير محدود'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الإنشاء</label>
                                <p class="text-gray-900">${new Date(course.created_at).toLocaleDateString('ar-SA')}</p>
                            </div>
                        </div>
                        
                        ${course.description ? `
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                                <p class="text-gray-900 whitespace-pre-wrap">${course.description}</p>
                            </div>
                        ` : ''}
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">المدرسون</label>
                            <p class="text-gray-900">${teachers}</p>
                        </div>
                        
                        ${course.cover_image_url ? `
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">صورة الغلاف</label>
                                <img src="${course.cover_image_url}" alt="${course.name}" class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200">
                            </div>
                        ` : ''}
                        
                        <div class="mt-8 flex justify-end space-x-3 space-x-reverse">
                            <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                                إغلاق
                            </button>
                            <button onclick="editCourse('${course.id}'); this.closest('.fixed').remove();" class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition duration-150 ease-in-out">
                                <i class="fas fa-edit mr-2"></i>
                                تعديل
                            </button>
                        </div>
                    </div>
                </div>
            `;

                document.body.appendChild(modal);

                // Close on outside click
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.remove();
                    }
                });
            } else {
                showMessage('error', 'فشل تحميل تفاصيل الدورة');
            }
        } catch (error) {
            console.error('Error viewing course:', error);
            showMessage('error', 'خطأ في تحميل تفاصيل الدورة');
        }
    }

    // Edit course
    async function editCourse(courseId) {
        try {
            console.log('Loading course for edit:', courseId);
            const url = `${REST_URL}/courses?select=*,course_types(name),teachers(id,full_name)&id=eq.${courseId}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'apikey': SUPABASE_ANON_KEY
                }
            });

            const courses = await response.json();
            console.log('Course data for edit:', courses);

            if (Array.isArray(courses) && courses.length > 0) {
                showEditForm(courses[0]);
            } else {
                showMessage('error', 'فشل تحميل بيانات الدورة');
            }
        } catch (error) {
            console.error('Error loading course for edit:', error);
            showMessage('error', 'خطأ في تحميل بيانات الدورة');
        }
    }

    // Delete course
    async function deleteCourse(courseId) {
        // Show confirmation modal with modern styling
        const confirmed = confirm('هل أنت متأكد من حذف هذه الدورة؟\nسيتم حذف جميع البيانات المرتبطة بها نهائياً.');

        if (!confirmed) {
            return;
        }

        try {
            console.log('Deleting course:', courseId);
            const url = `${REST_URL}/courses?id=eq.${courseId}`;

            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${TOKEN}`,
                    'apikey': SUPABASE_ANON_KEY
                }
            });

            if (response.ok || response.status === 204) {
                showMessage('success', 'تم حذف الدورة بنجاح');
                loadCourses();
            } else {
                const result = await response.json();
                console.error('Failed to delete course:', result);
                showMessage('error', 'فشل حذف الدورة: ' + (result.message || 'خطأ غير معروف'));
            }
        } catch (error) {
            console.error('Error deleting course:', error);
            showMessage('error', 'خطأ في حذف الدورة');
        }
    }
</script>

</body>

</html>