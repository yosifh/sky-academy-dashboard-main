<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'إدارة المحاضرات';
include 'includes/header.php';

// Get token from session
$token = isset($_SESSION['admin_token']) ? $_SESSION['admin_token'] : '';
?>

<!-- Enhanced styles for modern lecture management -->
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom gradient backgrounds */
.gradient-bg-1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-bg-2 {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.gradient-bg-3 {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.gradient-bg-4 {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.gradient-bg-5 {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

/* Enhanced card hover effects */
.lecture-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
}

.lecture-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Smooth animations */
.fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-in {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Enhanced form styling */
.form-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    backdrop-filter: blur(10px);
}

/* Loading spinner enhancement */
.spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Badge improvements */
.badge-premium {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #7c2d12;
    border: 1px solid #fbbf24;
}

.badge-free {
    background: linear-gradient(45deg, #10b981, #34d399);
    color: white;
}

/* Video status indicators */
.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-has-video {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-no-video {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

/* Enhanced buttons */
.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    border: none;
    color: white;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
    transform: translateY(-1px);
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border: none;
    color: white;
    transition: all 0.2s;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-1px);
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    transition: all 0.2s;
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
}

/* Video player styles */
.video-modal {
    background-color: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(8px);
}

.video-container {
    max-width: 90vw;
    max-height: 90vh;
}

.video-player {
    width: 100%;
    height: auto;
    max-height: 80vh;
    border-radius: 8px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.3), transparent);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.lecture-card:hover .video-overlay {
    opacity: 1;
}

.play-button {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: scale(0.8);
    transition: all 0.3s ease;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.play-button:hover {
    transform: scale(1);
    background: rgba(255, 255, 255, 1);
}

.play-button i {
    font-size: 2rem;
    color: #1f2937;
    margin-left: 4px;
}

/* Progress indicators */
.progress-bar {
    background: linear-gradient(90deg, #3b82f6, #06b6d4);
    height: 4px;
    border-radius: 2px;
    overflow: hidden;
    position: relative;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    to { left: 100%; }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .lecture-card {
        margin-bottom: 1rem;
    }
    
    .lecture-card:hover {
        transform: translateY(-4px);
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .form-container {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }
}
</style>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="lg:mr-64">
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Content -->
    <main class="pt-20 min-h-screen bg-gray-50">
        <div class="p-4 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة المحاضرات</h1>
                        <p class="text-gray-600">إدارة وتنظيم محاضرات الدورات</p>
                    </div>
                    <a href="courses.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للدورات
                    </a>
                </div>
                
                <!-- Course Selection -->
                <div class="mt-6 bg-white p-6 rounded-xl shadow-lg border border-gray-100 form-container fade-in">
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <label for="courseSelect" class="text-sm font-semibold text-gray-700 whitespace-nowrap flex items-center">
                            <i class="fas fa-graduation-cap ml-2 text-blue-600"></i>
                            اختر الدورة: *
                        </label>
                        <select id="courseSelect" onchange="loadLectures()" 
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                            <option value="">-- اختر دورة --</option>
                        </select>
                        <button onclick="showCreateForm()" id="addLectureBtn" 
                                class="btn-primary inline-flex items-center px-6 py-3 rounded-lg text-sm font-medium text-white disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg" 
                                disabled>
                            <i class="fas fa-plus ml-2"></i>
                            إضافة محاضرة جديدة
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <div id="successMessage" class="hidden bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-800 px-6 py-4 rounded-lg mb-6 shadow-md fade-in">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="mr-3">
                        <span id="successText" class="font-medium"></span>
                    </div>
                </div>
            </div>
            
            <div id="errorMessage" class="hidden bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 text-red-800 px-6 py-4 rounded-lg mb-6 shadow-md fade-in">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    </div>
                    <div class="mr-3">
                        <span id="errorText" class="font-medium"></span>
                    </div>
                </div>
            </div>
            
            <!-- Create/Edit Form -->
            <div id="lectureForm" class="hidden bg-white rounded-xl shadow-xl mb-8 overflow-hidden form-container fade-in">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-5">
                    <h2 id="formTitle" class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-video ml-3 text-blue-200"></i>
                        إضافة محاضرة جديدة
                    </h2>
                </div>
                <div class="p-6">
                    <form id="lectureDataForm" class="space-y-6">
                        <input type="hidden" id="lectureId" name="lectureId">
                        <input type="hidden" id="lectureCourseId" name="course_id">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="lectureTitle" class="block text-sm font-medium text-gray-700 mb-2">عنوان المحاضرة *</label>
                                <input type="text" id="lectureTitle" name="title" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="lectureDescription" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                                <textarea id="lectureDescription" name="description" rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out"></textarea>
                            </div>
                            
                            <div>
                                <label for="lectureDuration" class="block text-sm font-medium text-gray-700 mb-2">مدة الفيديو (بالثواني)</label>
                                <input type="number" id="lectureDuration" name="duration_seconds" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                            </div>
                            
                            <div>
                                <label for="lectureOrder" class="block text-sm font-medium text-gray-700 mb-2">ترتيب المحاضرة</label>
                                <input type="number" id="lectureOrder" name="lecture_order" value="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                            </div>
                            
                            <div>
                                <label for="lectureThumbnailFile" class="block text-sm font-medium text-gray-700 mb-2">الصورة المصغرة</label>
                                <input type="file" id="lectureThumbnailFile" accept="image/*" onchange="previewThumbnail(this)" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                                <input type="hidden" id="lectureThumbnail" name="thumbnail_url">
                                <div id="thumbnailPreview" class="mt-4"></div>
                                <div id="thumbnailUploadProgress" class="mt-2 text-sm text-gray-600"></div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" id="lectureIsFree" name="is_free" 
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="lectureIsFree" class="font-medium text-gray-700">محاضرة مجانية (معاينة)</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                            <button type="button" onclick="hideForm()" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                                إلغاء
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition duration-150 ease-in-out">
                                <i class="fas fa-save mr-2"></i>
                                حفظ المحاضرة
                            </button>
                        </div>
                    </form>
                    
                    <!-- Video Upload Section -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">رفع فيديو المحاضرة</h3>
                        <div id="videoUploadSection" class="space-y-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <i class="fas fa-info-circle text-yellow-400 mt-0.5 mr-3"></i>
                                    <div class="text-sm text-yellow-700">
                                        <p class="font-medium">ملاحظة مهمة:</p>
                                        <p>يجب حفظ المحاضرة أولاً قبل رفع الفيديو</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="videoFile" class="block text-sm font-medium text-gray-700 mb-2">ملف الفيديو</label>
                                <input type="file" id="videoFile" accept="video/*" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-150 ease-in-out">
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="button" onclick="uploadVideo()" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out">
                                    <i class="fas fa-upload mr-2"></i>
                                    رفع الفيديو
                                </button>
                            </div>
                            
                            <div id="videoUploadProgress" class="text-sm text-gray-600"></div>
                            <div id="currentVideoUrl" class="text-sm text-gray-600"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lectures List -->
            <div id="lecturesSection" class="hidden bg-white rounded-xl shadow-xl overflow-hidden fade-in">
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-5">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-list ml-3 text-indigo-200"></i>
                        قائمة المحاضرات
                    </h2>
                </div>
                <div class="p-6">
                    <div id="lecturesContainer" class="overflow-x-auto"></div>
                </div>
            </div>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </main>
</div>

<!-- Video Modal -->
<div id="videoModal" class="fixed inset-0 z-50 hidden video-modal">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="video-container relative">
            <!-- Close Button -->
            <button onclick="closeVideoModal()" 
                    class="absolute top-4 right-4 z-10 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Video Player -->
            <video id="videoPlayer" class="video-player" controls poster="">
                <source id="videoSource" src="" type="video/mp4">
                متصفحك لا يدعم تشغيل الفيديو
            </video>
            
            <!-- Video Info -->
            <div class="bg-white p-4 rounded-b-lg">
                <h3 id="videoTitle" class="text-lg font-semibold text-gray-900 mb-2"></h3>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-play-circle mr-2"></i>
                        <span>اضغط مساحة للتشغيل/الإيقاف</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-expand mr-2"></i>
                        <span>اضغط F للشاشة الكاملة</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Background overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-90" onclick="closeVideoModal()"></div>
</div>

<script>
const SUPABASE_URL = 'https://qxkyfdasymxphjjzxwfn.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InF4a3lmZGFzeW14cGhqanp4d2ZuIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjI3MTYzNTksImV4cCI6MjA3ODI5MjM1OX0.8ttsxRpM98K-qRJrlE0KUjb6oLXT6GhRFkW2SCH7Gi8';
const REST_URL = `${SUPABASE_URL}/rest/v1`;
const TOKEN = '<?php echo $token; ?>';

// Initialize Supabase client
const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

let isEditMode = false;
let currentLectureId = null;
let currentCourseId = null;

// Authenticate user on page load
async function authenticateUser() {
    try {
        if (TOKEN) {
            // Set the session with the admin token
            const { data, error } = await supabase.auth.setSession({
                access_token: TOKEN,
                refresh_token: TOKEN
            });
            
            if (error) {
                console.warn('Authentication warning:', error);
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
    loadCoursesForSelect();
});

// Load courses for dropdown
async function loadCoursesForSelect() {
    try {
        console.log('Loading courses for select...');
        const url = `${REST_URL}/courses?select=id,name,course_type_id,course_types(name)&order=name`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${TOKEN}`,
                'apikey': SUPABASE_ANON_KEY
            }
        });
        
        const courses = await response.json();
        console.log('Courses for select:', courses);
        
        if (Array.isArray(courses)) {
            const select = document.getElementById('courseSelect');
            select.innerHTML = '<option value="">-- اختر دورة --</option>';
            
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.id;
                const courseType = course.course_types ? ` (${translateCourseType(course.course_types.name)})` : '';
                option.textContent = course.name + courseType;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading courses:', error);
        showMessage('error', 'خطأ في تحميل الدورات');
    }
}

// Translate course type from English to Arabic
function translateCourseType(type) {
    const translations = {
        'online': 'إلكتروني',
        'in_person': 'حضوري'
    };
    return translations[type] || type;
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
    const courseId = document.getElementById('courseSelect').value;
    if (!courseId) {
        showMessage('error', 'الرجاء اختيار دورة أولاً');
        return;
    }
    
    document.getElementById('lectureForm').classList.remove('hidden');
    document.getElementById('formTitle').textContent = 'إضافة محاضرة جديدة';
    document.getElementById('lectureDataForm').reset();
    document.getElementById('lectureCourseId').value = courseId;
    document.getElementById('thumbnailPreview').innerHTML = '';
    document.getElementById('thumbnailUploadProgress').innerHTML = '';
    document.getElementById('videoUploadProgress').innerHTML = '';
    document.getElementById('currentVideoUrl').innerHTML = '';
    
    hideMessages();
    isEditMode = false;
    currentLectureId = null;
    
    // Scroll to form
    document.getElementById('lectureForm').scrollIntoView({ behavior: 'smooth' });
}

function hideForm() {
    document.getElementById('lectureForm').classList.add('hidden');
    document.getElementById('lectureDataForm').reset();
    document.getElementById('thumbnailPreview').innerHTML = '';
    document.getElementById('thumbnailUploadProgress').innerHTML = '';
    document.getElementById('videoUploadProgress').innerHTML = '';
    document.getElementById('currentVideoUrl').innerHTML = '';
    hideMessages();
}

function previewThumbnail(input) {
    const preview = document.getElementById('thumbnailPreview');
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

function showEditForm(lecture) {
    console.log('Editing lecture:', lecture);
    document.getElementById('lectureForm').classList.remove('hidden');
    document.getElementById('formTitle').textContent = 'تعديل المحاضرة';
    
    document.getElementById('lectureId').value = lecture.id;
    document.getElementById('lectureCourseId').value = lecture.course_id;
    document.getElementById('lectureTitle').value = lecture.title || '';
    document.getElementById('lectureDescription').value = lecture.description || '';
    document.getElementById('lectureThumbnail').value = lecture.thumbnail_url || '';
    document.getElementById('lectureDuration').value = lecture.duration_seconds || '';
    document.getElementById('lectureOrder').value = lecture.lecture_order || 0;
    document.getElementById('lectureIsFree').checked = lecture.is_free || false;
    
    // Show existing thumbnail
    if (lecture.thumbnail_url) {
        document.getElementById('thumbnailPreview').innerHTML = `
            <div class="relative inline-block">
                <img src="${lecture.thumbnail_url}" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                    <i class="fas fa-image mr-1"></i>الحالية
                </div>
            </div>
        `;
    }
    
    // Show current video URL with preview option
    if (lecture.video_url) {
        document.getElementById('currentVideoUrl').innerHTML = `
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-400 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="bg-blue-500 w-10 h-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-video text-white"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-medium text-blue-900">فيديو المحاضرة الحالي:</p>
                            <button onclick="openVideoModal('${lecture.video_url}', '${lecture.title}')" 
                                    class="btn-success inline-flex items-center px-3 py-1 rounded text-xs font-medium text-white">
                                <i class="fas fa-play ml-1"></i>
                                معاينة الفيديو
                            </button>
                        </div>
                        <p class="text-sm text-blue-700 break-all bg-white bg-opacity-60 p-2 rounded border">${lecture.video_url}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    hideMessages();
    isEditMode = true;
    currentLectureId = lecture.id;
    
    // Scroll to form
    document.getElementById('lectureForm').scrollIntoView({ behavior: 'smooth' });
}

// Load lectures for selected course
async function loadLectures() {
    const courseId = document.getElementById('courseSelect').value;
    const addButton = document.getElementById('addLectureBtn');
    
    if (!courseId) {
        document.getElementById('lecturesSection').classList.add('hidden');
        addButton.disabled = true;
        return;
    }
    
    addButton.disabled = false;
    currentCourseId = courseId;
    
    try {
        console.log('Loading lectures for course:', courseId);
        
        // Show loading spinner
        const container = document.getElementById('lecturesContainer');
        container.innerHTML = `
            <div class="flex justify-center items-center py-16 fade-in">
                <div class="text-center">
                    <div class="gradient-bg-2 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <div class="spinner"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-1">جاري تحميل المحاضرات</h3>
                    <p class="text-sm text-gray-500">يرجى الانتظار...</p>
                </div>
            </div>
        `;
        
        const url = `${REST_URL}/lectures?select=*&course_id=eq.${courseId}&order=lecture_order`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${TOKEN}`,
                'apikey': SUPABASE_ANON_KEY
            }
        });
        
        const lectures = await response.json();
        console.log('Lectures response:', lectures);
        
        if (Array.isArray(lectures)) {
            document.getElementById('lecturesSection').classList.remove('hidden');
            displayLectures(lectures);
        } else {
            console.error('Failed to load lectures:', lectures);
            showMessage('error', 'فشل تحميل المحاضرات');
        }
    } catch (error) {
        console.error('Error loading lectures:', error);
        showMessage('error', 'خطأ في تحميل المحاضرات');
    }
}

// Display lectures
function displayLectures(lectures) {
    const container = document.getElementById('lecturesContainer');
    
    if (lectures.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16 fade-in">
                <div class="gradient-bg-1 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-video text-3xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد محاضرات لهذه الدورة</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة أول محاضرة لهذه الدورة</p>
                <button onclick="showCreateForm()" class="btn-primary inline-flex items-center px-6 py-3 rounded-lg text-sm font-medium text-white shadow-lg">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة أول محاضرة
                </button>
            </div>
        `;
        return;
    }
    
    let html = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">`;
    
    lectures.forEach((lecture, index) => {
        const duration = lecture.duration_seconds ? 
            `${Math.floor(lecture.duration_seconds / 60)}:${(lecture.duration_seconds % 60).toString().padStart(2, '0')}` : 
            'غير محدد';
        const isFree = lecture.is_free;
        const hasVideo = lecture.video_url;
        const gradientClass = `gradient-bg-${(index % 5) + 1}`;
        
        html += `
            <div class="lecture-card bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                <!-- Lecture Thumbnail -->
                <div class="relative">
                    ${lecture.thumbnail_url ? 
                        `<img src="${lecture.thumbnail_url}" alt="${lecture.title}" class="w-full h-48 object-cover">` :
                        `<div class="w-full h-48 ${gradientClass} flex items-center justify-center">
                            <i class="fas fa-play-circle text-6xl text-white opacity-70"></i>
                        </div>`
                    }
                    
                    <!-- Video overlay for playable videos -->
                    ${hasVideo ? 
                        `<div class="video-overlay" onclick="openVideoModal('${lecture.video_url}', '${lecture.title}')">
                            <div class="play-button">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>` : ''
                    }
                    
                    <!-- Order Badge -->
                    <div class="absolute top-4 right-4 bg-white bg-opacity-95 backdrop-blur-sm rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold text-gray-700 shadow-lg">
                        ${lecture.lecture_order}
                    </div>
                    
                    <!-- Type Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${isFree ? 'badge-free' : 'badge-premium'} shadow-md">
                            <i class="fas ${isFree ? 'fa-gift' : 'fa-crown'} ml-1"></i>
                            ${isFree ? 'مجانية' : 'مدفوعة'}
                        </span>
                    </div>
                    
                    <!-- Video Status -->
                    <div class="absolute bottom-4 left-4">
                        <span class="status-indicator ${hasVideo ? 'status-has-video' : 'status-no-video'} shadow-md">
                            <i class="fas ${hasVideo ? 'fa-check-circle' : 'fa-exclamation-circle'} ml-1"></i>
                            ${hasVideo ? 'فيديو متاح' : 'بحاجة فيديو'}
                        </span>
                    </div>
                    
                    <!-- Duration -->
                    ${lecture.duration_seconds ? 
                        `<div class="absolute bottom-4 right-4 bg-black bg-opacity-80 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                            <i class="fas fa-clock ml-1"></i>
                            ${duration}
                        </div>` : ''
                    }
                </div>
                
                <!-- Lecture Content -->
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">${lecture.title}</h3>
                        ${lecture.description ? 
                            `<p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">${lecture.description}</p>` : 
                            `<p class="text-sm text-gray-400 italic">لا يوجد وصف متاح</p>`
                        }
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-col space-y-3">
                        <!-- Video Actions -->
                        ${hasVideo ? 
                            `<div class="flex justify-center">
                                <button onclick="openVideoModal('${lecture.video_url}', '${lecture.title}')" 
                                        class="btn-success inline-flex items-center px-6 py-3 rounded-lg text-sm font-medium text-white shadow-lg w-full justify-center">
                                    <i class="fas fa-play ml-2"></i>
                                    مشاهدة المحاضرة
                                </button>
                            </div>` : 
                            `<div class="flex justify-center">
                                <div class="inline-flex items-center px-6 py-3 rounded-lg text-sm font-medium text-orange-600 bg-orange-50 border border-orange-200 w-full justify-center">
                                    <i class="fas fa-exclamation-triangle ml-2"></i>
                                    لم يتم رفع الفيديو بعد
                                </div>
                            </div>`
                        }
                        
                        <!-- Management Actions -->
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <div class="flex space-x-3 space-x-reverse">
                                <button onclick='editLecture("${lecture.id}")' 
                                        title="تعديل المحاضرة"
                                        class="btn-secondary inline-flex items-center px-4 py-2 rounded-lg text-xs font-medium text-white shadow-md">
                                    <i class="fas fa-edit ml-1"></i>
                                    تعديل
                                </button>
                                <button onclick='deleteLecture("${lecture.id}")' 
                                        title="حذف المحاضرة"
                                        class="btn-danger inline-flex items-center px-4 py-2 rounded-lg text-xs font-medium text-white shadow-md">
                                    <i class="fas fa-trash ml-1"></i>
                                    حذف
                                </button>
                            </div>
                            
                            <div class="flex items-center">
                                ${hasVideo ? 
                                    `<div class="flex items-center text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                        <i class="fas fa-video ml-1"></i>
                                        <span class="text-xs font-semibold">جاهز للعرض</span>
                                    </div>` :
                                    `<div class="flex items-center text-orange-600 bg-orange-50 px-3 py-1 rounded-full">
                                        <i class="fas fa-upload ml-1"></i>
                                        <span class="text-xs font-semibold">بحاجة لفيديو</span>
                                    </div>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `</div>`;
    
    container.innerHTML = html;
}

// Upload thumbnail to Supabase Storage
async function uploadThumbnail(file) {
    try {
        console.log('Uploading thumbnail:', file.name);
        document.getElementById('thumbnailUploadProgress').innerHTML = `
            <div class="flex items-center bg-blue-50 text-blue-700 px-4 py-3 rounded-lg border-l-4 border-blue-400">
                <div class="spinner mr-3"></div>
                <div>
                    <p class="font-medium">جاري رفع الصورة...</p>
                    <p class="text-sm opacity-75">يرجى عدم إغلاق الصفحة</p>
                </div>
            </div>
        `;
        
        // Get authenticated user
        const { data: { user }, error: userError } = await supabase.auth.getUser();
        
        if (userError || !user) {
            throw new Error('User not authenticated. Please login again.');
        }
        
        // Create file path with user ID prefix
        const timestamp = Date.now();
        const fileName = `${timestamp}-${file.name}`;
        const filePath = `${user.id}/${fileName}`;
        
        console.log('Uploading to path:', filePath);
        
        // Upload using Supabase client
        const { data, error } = await supabase.storage
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
        const { data: { publicUrl } } = supabase.storage
            .from('course-thumbnails')
            .getPublicUrl(filePath);
        
        console.log('Thumbnail uploaded successfully:', publicUrl);
        document.getElementById('thumbnailUploadProgress').innerHTML = `
            <div class="flex items-center bg-green-50 text-green-700 px-4 py-3 rounded-lg border-l-4 border-green-400">
                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                <div>
                    <p class="font-medium">تم رفع الصورة بنجاح!</p>
                    <p class="text-sm opacity-75">يمكنك الآن حفظ المحاضرة</p>
                </div>
            </div>
        `;
        return publicUrl;
        
    } catch (error) {
        console.error('Error uploading thumbnail:', error);
        document.getElementById('thumbnailUploadProgress').innerHTML = `
            <div class="flex items-center bg-red-50 text-red-700 px-4 py-3 rounded-lg border-l-4 border-red-400">
                <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                <div>
                    <p class="font-medium">خطأ في رفع الصورة</p>
                    <p class="text-sm opacity-75">${error.message}</p>
                </div>
            </div>
        `;
        throw error;
    }
}

// Form submission
document.getElementById('lectureDataForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        // Upload thumbnail if selected
        const thumbnailInput = document.getElementById('lectureThumbnailFile');
        let thumbnailUrl = document.getElementById('lectureThumbnail').value;
        
        if (thumbnailInput.files && thumbnailInput.files[0]) {
            thumbnailUrl = await uploadThumbnail(thumbnailInput.files[0]);
            document.getElementById('lectureThumbnail').value = thumbnailUrl;
        }
        
        const formData = new FormData(e.target);
        const data = {
            course_id: formData.get('course_id'),
            title: formData.get('title'),
            description: formData.get('description') || null,
            thumbnail_url: thumbnailUrl || null,
            video_url: '', // Provide empty string instead of null to satisfy NOT NULL constraint
            duration_seconds: formData.get('duration_seconds') ? parseInt(formData.get('duration_seconds')) : null,
            lecture_order: parseInt(formData.get('lecture_order')) || 0,
            is_free: document.getElementById('lectureIsFree').checked
        };
        
        console.log('Form data to submit:', data);
        
        if (isEditMode) {
            // For updates, don't include video_url unless we're specifically updating it
            delete data.video_url;
            await updateLecture(currentLectureId, data);
        } else {
            await createLecture(data);
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        showMessage('error', 'خطأ في حفظ البيانات: ' + error.message);
    }
});

// Create lecture
async function createLecture(data) {
    try {
        console.log('Creating lecture:', data);
        const url = `${REST_URL}/lectures`;
        
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
        console.log('Create lecture response:', result);
        
        if (response.ok && Array.isArray(result) && result.length > 0) {
            showMessage('success', 'تم إنشاء المحاضرة بنجاح. يمكنك الآن رفع الفيديو.');
            
            // Set lecture ID for video upload
            currentLectureId = result[0].id;
            document.getElementById('lectureId').value = result[0].id;
            isEditMode = true;
            
            loadLectures();
        } else {
            console.error('Failed to create lecture:', result);
            showMessage('error', 'فشل إنشاء المحاضرة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error creating lecture:', error);
        showMessage('error', 'خطأ في إنشاء المحاضرة: ' + error.message);
    }
}

// Update lecture
async function updateLecture(lectureId, data) {
    try {
        console.log('Updating lecture:', lectureId, data);
        const url = `${REST_URL}/lectures?id=eq.${lectureId}`;
        
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
            showMessage('success', 'تم تحديث المحاضرة بنجاح');
            loadLectures();
        } else {
            const result = await response.json();
            console.error('Failed to update lecture:', result);
            showMessage('error', 'فشل تحديث المحاضرة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error updating lecture:', error);
        showMessage('error', 'خطأ في تحديث المحاضرة: ' + error.message);
    }
}

// Edit lecture
async function editLecture(lectureId) {
    try {
        console.log('Loading lecture for edit:', lectureId);
        
        const url = `${REST_URL}/lectures?select=*&id=eq.${lectureId}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${TOKEN}`,
                'apikey': SUPABASE_ANON_KEY
            }
        });
        
        const lectures = await response.json();
        console.log('Lecture data:', lectures);
        
        if (Array.isArray(lectures) && lectures.length > 0) {
            showEditForm(lectures[0]);
        } else {
            showMessage('error', 'لم يتم العثور على المحاضرة');
        }
    } catch (error) {
        console.error('Error loading lecture for edit:', error);
        showMessage('error', 'خطأ في تحميل بيانات المحاضرة');
    }
}

// Delete lecture
async function deleteLecture(lectureId) {
    const confirmed = confirm('هل أنت متأكد من حذف هذه المحاضرة؟\nسيتم حذف جميع الملفات المرتبطة بها نهائياً.');
    
    if (!confirmed) {
        return;
    }
    
    try {
        console.log('Deleting lecture:', lectureId);
        
        // TODO: Delete video file from storage if needed
        
        const url = `${REST_URL}/lectures?id=eq.${lectureId}`;
        
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${TOKEN}`,
                'apikey': SUPABASE_ANON_KEY
            }
        });
        
        if (response.ok || response.status === 204) {
            showMessage('success', 'تم حذف المحاضرة بنجاح');
            loadLectures();
        } else {
            const result = await response.json();
            console.error('Failed to delete lecture:', result);
            showMessage('error', 'فشل حذف المحاضرة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error deleting lecture:', error);
        showMessage('error', 'خطأ في حذف المحاضرة');
    }
}

// Video upload functionality
async function uploadVideo() {
    const fileInput = document.getElementById('videoFile');
    const file = fileInput.files[0];
    
    if (!file) {
        showMessage('error', 'الرجاء اختيار ملف فيديو');
        return;
    }
    
    const lectureId = document.getElementById('lectureId').value;
    if (!lectureId) {
        showMessage('error', 'الرجاء حفظ المحاضرة أولاً قبل رفع الفيديو');
        return;
    }
    
    try {
        document.getElementById('videoUploadProgress').innerHTML = `
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="gradient-bg-1 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                        <div class="spinner"></div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">جاري رفع الفيديو...</h4>
                        <p class="text-sm text-blue-700">حجم الملف: ${(file.size / (1024 * 1024)).toFixed(2)} MB</p>
                        <p class="text-xs text-blue-600 mt-1">يرجى عدم إغلاق الصفحة أو التنقل بعيداً</p>
                    </div>
                </div>
            </div>
        `;
        console.log('Uploading video for lecture:', lectureId);
        
        // Get authenticated user
        const { data: { user }, error: userError } = await supabase.auth.getUser();
        
        if (userError || !user) {
            throw new Error('User not authenticated. Please login again.');
        }
        
        // Create file path with user ID and lecture ID
        const timestamp = Date.now();
        const fileExt = file.name.split('.').pop();
        const fileName = `${lectureId}-${timestamp}.${fileExt}`;
        const filePath = `${user.id}/${fileName}`;
        
        console.log('Uploading video to path:', filePath);
        
        // Upload using Supabase client
        const { data, error } = await supabase.storage
            .from('course-videos')
            .upload(filePath, file, { 
                upsert: false,
                contentType: file.type 
            });
        
        if (error) {
            console.error('Upload error:', error);
            throw new Error(error.message || 'Upload failed');
        }
        
        // Get public URL
        const { data: { publicUrl } } = supabase.storage
            .from('course-videos')
            .getPublicUrl(filePath);
        
        console.log('Video uploaded successfully:', publicUrl);
        
        // Update lecture with video URL
        const updateResponse = await fetch(`${REST_URL}/lectures?id=eq.${lectureId}`, {
            method: 'PATCH',
            headers: {
                'Authorization': `Bearer ${TOKEN}`,
                'Content-Type': 'application/json',
                'apikey': SUPABASE_ANON_KEY
            },
            body: JSON.stringify({ video_url: publicUrl })
        });
        
        if (updateResponse.ok || updateResponse.status === 204) {
            document.getElementById('videoUploadProgress').innerHTML = `
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-green-500 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-green-900 mb-1">تم رفع الفيديو بنجاح! 🎉</h4>
                            <p class="text-sm text-green-700">المحاضرة أصبحت جاهزة للعرض</p>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('currentVideoUrl').innerHTML = `
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-400 rounded-lg p-4">
                    <div class="flex">
                        <div class="bg-blue-500 w-10 h-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-video text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-blue-900 mb-1">رابط الفيديو الحالي:</p>
                            <p class="text-sm text-blue-700 break-all bg-white bg-opacity-60 p-2 rounded border">${publicUrl}</p>
                        </div>
                    </div>
                </div>
            `;
            showMessage('success', 'تم رفع الفيديو بنجاح');
            loadLectures();
        } else {
            throw new Error('Failed to update lecture with video URL');
        }
        
    } catch (error) {
        console.error('Error uploading video:', error);
        document.getElementById('videoUploadProgress').innerHTML = `
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="bg-red-500 w-12 h-12 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-red-900 mb-1">خطأ في رفع الفيديو</h4>
                        <p class="text-sm text-red-700">${error.message}</p>
                        <p class="text-xs text-red-600 mt-2">يرجى المحاولة مرة أخرى أو التواصل مع الدعم الفني</p>
                    </div>
                </div>
            </div>
        `;
        showMessage('error', 'خطأ في رفع الفيديو: ' + error.message);
    }
}

// Video Modal Functions
function openVideoModal(videoUrl, title) {
    const modal = document.getElementById('videoModal');
    const videoPlayer = document.getElementById('videoPlayer');
    const videoSource = document.getElementById('videoSource');
    const videoTitle = document.getElementById('videoTitle');
    
    // Set video source and title
    videoSource.src = videoUrl;
    videoTitle.textContent = title;
    videoPlayer.load();
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Focus on video player for keyboard controls
    videoPlayer.focus();
    
    // Add keyboard event listeners
    document.addEventListener('keydown', handleVideoKeydown);
    
    // Auto-play video (if browser allows)
    setTimeout(() => {
        videoPlayer.play().catch(error => {
            console.log('Auto-play prevented by browser:', error);
        });
    }, 300);
}

function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const videoPlayer = document.getElementById('videoPlayer');
    
    // Pause and reset video
    videoPlayer.pause();
    videoPlayer.currentTime = 0;
    
    // Hide modal
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Remove keyboard event listeners
    document.removeEventListener('keydown', handleVideoKeydown);
}

function handleVideoKeydown(event) {
    const videoPlayer = document.getElementById('videoPlayer');
    
    switch(event.code) {
        case 'Space':
            event.preventDefault();
            if (videoPlayer.paused) {
                videoPlayer.play();
            } else {
                videoPlayer.pause();
            }
            break;
        case 'Escape':
            closeVideoModal();
            break;
        case 'KeyF':
            event.preventDefault();
            if (videoPlayer.requestFullscreen) {
                videoPlayer.requestFullscreen();
            } else if (videoPlayer.webkitRequestFullscreen) {
                videoPlayer.webkitRequestFullscreen();
            } else if (videoPlayer.mozRequestFullScreen) {
                videoPlayer.mozRequestFullScreen();
            }
            break;
        case 'ArrowLeft':
            event.preventDefault();
            videoPlayer.currentTime = Math.max(0, videoPlayer.currentTime - 10);
            break;
        case 'ArrowRight':
            event.preventDefault();
            videoPlayer.currentTime = Math.min(videoPlayer.duration, videoPlayer.currentTime + 10);
            break;
        case 'ArrowUp':
            event.preventDefault();
            videoPlayer.volume = Math.min(1, videoPlayer.volume + 0.1);
            break;
        case 'ArrowDown':
            event.preventDefault();
            videoPlayer.volume = Math.max(0, videoPlayer.volume - 0.1);
            break;
    }
}
</script>

</body>
</html>