<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'إدارة المعرض';
include 'includes/header.php';

// Read environment variables
$supabase_url = getenv('SUPABASE_URL') ?: 'https://qxkyfdasymxphjjzxwfn.supabase.co';
$supabase_key = getenv('SUPABASE_ANON_KEY') ?: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InF4a3lmZGFzeW14cGhqanp4d2ZuIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjI3MTYzNTksImV4cCI6MjA3ODI5MjM1OX0.8ttsxRpM98K-qRJrlE0KUjb6oLXT6GhRFkW2SCH7Gi8';

// If .env file exists, read from it
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    $lines = explode("\n", $env);
    foreach ($lines as $line) {
        if (strpos($line, 'SUPABASE_URL=') === 0) {
            $supabase_url = trim(substr($line, 13));
        } elseif (strpos($line, 'SUPABASE_ANON_KEY=') === 0) {
            $supabase_key = trim(substr($line, 18));
        }
    }
}

// Get admin token from session
$admin_token = isset($_SESSION['admin_token']) ? $_SESSION['admin_token'] : '';
$admin_auth_response = isset($_SESSION['admin_auth_response']) ? $_SESSION['admin_auth_response'] : null;

if (empty($admin_token)) {
    // If no token, redirect to login
    header('Location: login.php');
    exit;
}

// Try to extract user ID from auth response
$admin_user_id = null;
if ($admin_auth_response && is_array($admin_auth_response)) {
    // Check various possible locations for user ID
    if (isset($admin_auth_response['user']['id'])) {
        $admin_user_id = $admin_auth_response['user']['id'];
    } elseif (isset($admin_auth_response['user_id'])) {
        $admin_user_id = $admin_auth_response['user_id'];
    } elseif (isset($admin_auth_response['id'])) {
        $admin_user_id = $admin_auth_response['id'];
    } elseif (isset($admin_auth_response['data']['user']['id'])) {
        $admin_user_id = $admin_auth_response['data']['user']['id'];
    }
}
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="lg:mr-64">
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Content -->
    <main class="pt-20 min-h-screen p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">إدارة المعرض</h1>
                <button id="addImageBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 space-x-reverse transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>إضافة صورة جديدة</span>
                </button>
            </div>

            <!-- Gallery Grid -->
            <div id="galleryGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Gallery items will be loaded here -->
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">جاري تحميل الصور...</p>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="text-center py-12 hidden">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">لا توجد صور في المعرض</h3>
                <p class="text-gray-600 mb-6">ابدأ بإضافة صورة جديدة إلى المعرض</p>
                <button class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg" onclick="document.getElementById('addImageBtn').click()">
                    إضافة صورة جديدة
                </button>
            </div>
        </div>

        <!-- Add/Edit Image Modal -->
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg w-full max-w-md">
                    <div class="p-6">
                        <h2 id="modalTitle" class="text-xl font-bold mb-4">إضافة صورة جديدة</h2>
                        
                        <form id="imageForm" class="space-y-4">
                            <input type="hidden" id="imageId" name="id">
                            
                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الصورة</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                    <input type="file" id="imageFile" accept="image/*" class="hidden">
                                    <div id="imageUploadArea" class="cursor-pointer" onclick="document.getElementById('imageFile').click()">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-600">اضغط لاختيار صورة</p>
                                    </div>
                                    <div id="imagePreview" class="hidden">
                                        <img id="previewImage" class="max-w-full h-32 object-cover rounded mx-auto">
                                        <button type="button" id="changeImageBtn" class="mt-2 text-sm text-primary-600 hover:text-primary-700">تغيير الصورة</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                                <textarea id="description" name="description" rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="وصف مختصر للصورة"></textarea>
                            </div>

                            <!-- Display Order -->
                            <div>
                                <label for="displayOrder" class="block text-sm font-medium text-gray-700 mb-2">ترتيب العرض</label>
                                <input type="number" id="displayOrder" name="display_order" min="0" value="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <!-- Course Selection -->
                            <div>
                                <label for="courseId" class="block text-sm font-medium text-gray-700 mb-2">الدورة (اختياري)</label>
                                <select id="courseId" name="course_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">اختر دورة</option>
                                </select>
                            </div>

                            <!-- Form Buttons -->
                            <div class="flex space-x-3 space-x-reverse pt-4">
                                <button type="submit" id="saveBtn" 
                                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-lg transition-colors">
                                    <span id="saveText">حفظ</span>
                                    <i id="saveSpinner" class="fas fa-spinner fa-spin hidden mr-2"></i>
                                </button>
                                <button type="button" id="cancelBtn" 
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition-colors">
                                    إلغاء
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg w-full max-w-sm">
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-4 text-red-600">تأكيد الحذف</h2>
                        <p class="text-gray-700 mb-6">هل أنت متأكد من حذف هذه الصورة؟ لا يمكن التراجع عن هذا الإجراء.</p>
                        <div class="flex space-x-3 space-x-reverse">
                            <button id="confirmDeleteBtn" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors">
                                حذف
                            </button>
                            <button id="cancelDeleteBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition-colors">
                                إلغاء
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    
    <?php include 'includes/footer.php'; ?>
</div>

<script>
class GalleryManager {
    constructor() {
        this.supabaseUrl = '<?php echo $supabase_url; ?>';
        this.supabaseKey = '<?php echo $supabase_key; ?>';
        this.adminToken = '<?php echo $admin_token; ?>';
        this.adminUserId = '<?php echo $admin_user_id ?? ""; ?>';
        this.apiUrl = `${this.supabaseUrl}/functions/v1/gallary-admin`;
        this.currentImageId = null;
        this.selectedFile = null;
        
        // Debug: Log authentication info
        console.log('Auth Debug Info:');
        console.log('Admin Token:', this.adminToken ? 'Present' : 'Missing');
        console.log('Admin User ID:', this.adminUserId || 'Not found');
        console.log('Full Auth Response:', <?php echo json_encode($admin_auth_response); ?>);
        
        this.init();
    }

    init() {
        // Check if we have a valid token
        if (!this.adminToken || this.adminToken.length < 10) {
            this.showError('لم يتم العثور على رمز مصادقة صالح. يرجى تسجيل الدخول مرة أخرى.');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 3000);
            return;
        }
        
        this.bindEvents();
        this.loadGallery();
        this.loadCourses();
    }

    bindEvents() {
        // Modal events
        document.getElementById('addImageBtn').addEventListener('click', () => this.openModal());
        document.getElementById('cancelBtn').addEventListener('click', () => this.closeModal());
        document.getElementById('imageForm').addEventListener('submit', (e) => this.handleSubmit(e));
        
        // File upload events
        document.getElementById('imageFile').addEventListener('change', (e) => this.handleFileSelect(e));
        document.getElementById('changeImageBtn').addEventListener('click', () => {
            document.getElementById('imageFile').click();
        });

        // Delete modal events
        document.getElementById('cancelDeleteBtn').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => this.confirmDelete());

        // Close modals on outside click
        document.getElementById('imageModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('imageModal')) {
                this.closeModal();
            }
        });
        
        document.getElementById('deleteModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('deleteModal')) {
                this.closeDeleteModal();
            }
        });
    }

    async makeRequest(url, options = {}) {
        // Special handling for admin endpoints - try without user_id first
        const defaultOptions = {
            headers: {
                'Authorization': `Bearer ${this.getAuthToken()}`,
                'Content-Type': 'application/json',
                ...options.headers
            }
        };

        try {
            console.log('Making request to:', url);
            console.log('Request options:', { ...defaultOptions, ...options });
            
            const response = await fetch(url, { ...defaultOptions, ...options });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response text:', errorText);
                
                let errorData;
                try {
                    errorData = JSON.parse(errorText);
                } catch {
                    errorData = { error: errorText || 'Unknown error' };
                }
                
                // Check if it's a user_id column error and try alternative approach
                if (errorText.includes('column "user_id" does not exist') && options.body) {
                    console.log('Retrying without user_id...');
                    const bodyData = JSON.parse(options.body);
                    delete bodyData.user_id;
                    
                    const retryOptions = {
                        ...options,
                        body: JSON.stringify(bodyData)
                    };
                    
                    console.log('Retry request data:', bodyData);
                    return this.makeRequest(url, retryOptions);
                }
                
                throw new Error(errorData.error || errorData.message || `HTTP ${response.status}: ${errorText}`);
            }

            const responseText = await response.text();
            console.log('Success response text:', responseText);
            
            if (!responseText) {
                return {};
            }
            
            return JSON.parse(responseText);
        } catch (error) {
            console.error('API Error:', error);
            
            // Don't show error notification for retry attempts
            if (!error.message.includes('Retrying')) {
                this.showError(`خطأ في الاتصال: ${error.message}`);
            }
            throw error;
        }
    }

    getAuthToken() {
        return this.adminToken;
    }

    async loadGallery() {
        try {
            document.getElementById('loadingState').style.display = 'block';
            document.getElementById('emptyState').style.display = 'none';
            
            const data = await this.makeRequest(this.apiUrl);
            
            document.getElementById('loadingState').style.display = 'none';
            
            if (data && data.length > 0) {
                this.renderGallery(data);
            } else {
                document.getElementById('emptyState').style.display = 'block';
            }
        } catch (error) {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
        }
    }

    renderGallery(images) {
        const grid = document.getElementById('galleryGrid');
        grid.innerHTML = '';

        images.forEach(image => {
            const imageCard = this.createImageCard(image);
            grid.appendChild(imageCard);
        });
    }

    createImageCard(image) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow';
        
        card.innerHTML = `
            <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                <img src="${image.image_url}" alt="${image.description || 'صورة من المعرض'}" 
                     class="w-full h-48 object-cover">
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">${image.description || 'بدون وصف'}</p>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                    <span>الترتيب: ${image.display_order || 0}</span>
                    <span>${new Date(image.created_at).toLocaleDateString('ar')}</span>
                </div>
                <div class="flex space-x-2 space-x-reverse">
                    <button onclick="galleryManager.editImage('${image.id}')" 
                            class="flex-1 bg-primary-100 text-primary-700 py-2 px-3 rounded text-sm hover:bg-primary-200 transition-colors">
                        <i class="fas fa-edit mr-1"></i>
                        تعديل
                    </button>
                    <button onclick="galleryManager.deleteImage('${image.id}')" 
                            class="flex-1 bg-red-100 text-red-700 py-2 px-3 rounded text-sm hover:bg-red-200 transition-colors">
                        <i class="fas fa-trash mr-1"></i>
                        حذف
                    </button>
                </div>
            </div>
        `;
        
        return card;
    }

    async loadCourses() {
        // This would load courses from your courses API
        // For now, we'll just add a placeholder option
        const courseSelect = document.getElementById('courseId');
        courseSelect.innerHTML = '<option value="">اختر دورة</option>';
        // Add your courses loading logic here
    }

    openModal(imageId = null) {
        this.currentImageId = imageId;
        this.selectedFile = null;
        
        const modal = document.getElementById('imageModal');
        const form = document.getElementById('imageForm');
        const title = document.getElementById('modalTitle');
        
        form.reset();
        this.resetImagePreview();
        
        if (imageId) {
            title.textContent = 'تعديل الصورة';
            this.loadImageData(imageId);
        } else {
            title.textContent = 'إضافة صورة جديدة';
        }
        
        modal.classList.remove('hidden');
    }

    closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        this.resetImagePreview();
    }

    closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        this.currentImageId = null;
    }

    async loadImageData(imageId) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}?id=${imageId}`);
            
            if (data) {
                document.getElementById('imageId').value = data.id;
                document.getElementById('description').value = data.description || '';
                document.getElementById('displayOrder').value = data.display_order || 0;
                document.getElementById('courseId').value = data.course_id || '';
                
                // Show current image
                if (data.image_url) {
                    document.getElementById('previewImage').src = data.image_url;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('imageUploadArea').classList.add('hidden');
                }
            }
        } catch (error) {
            this.showError('خطأ في تحميل بيانات الصورة');
            this.closeModal();
        }
    }

    handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            this.showError('يرجى اختيار ملف صورة صالح');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            this.showError('حجم الملف كبير جداً. الحد الأقصى 5 ميجابايت');
            return;
        }

        this.selectedFile = file;

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('imageUploadArea').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    resetImagePreview() {
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('imageUploadArea').classList.remove('hidden');
        document.getElementById('imageFile').value = '';
        this.selectedFile = null;
    }

    async handleSubmit(event) {
        event.preventDefault();
        
        const saveBtn = document.getElementById('saveBtn');
        const saveText = document.getElementById('saveText');
        const saveSpinner = document.getElementById('saveSpinner');
        
        // Disable button and show spinner
        saveBtn.disabled = true;
        saveText.textContent = 'جاري الحفظ...';
        saveSpinner.classList.remove('hidden');

        try {
            const formData = new FormData(event.target);
            const data = {
                description: formData.get('description'),
                display_order: parseInt(formData.get('display_order')) || 0,
                course_id: formData.get('course_id') || null
            };

            // Add image data if file is selected
            if (this.selectedFile) {
                const base64 = await this.fileToBase64(this.selectedFile);
                data.image_base64 = base64;
                data.image_mime_type = this.selectedFile.type;
                data.image_file_name = `gallery_${Date.now()}.${this.selectedFile.name.split('.').pop()}`;
            }

            console.log('Submitting data:', data);

            let result;
            if (this.currentImageId) {
                // Update existing image
                result = await this.makeRequest(`${this.apiUrl}?id=${this.currentImageId}`, {
                    method: 'PUT',
                    body: JSON.stringify(data)
                });
            } else {
                // Create new image
                if (!this.selectedFile) {
                    throw new Error('يرجى اختيار صورة');
                }
                result = await this.makeRequest(this.apiUrl, {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
            }

            this.showSuccess(this.currentImageId ? 'تم تحديث الصورة بنجاح' : 'تم إضافة الصورة بنجاح');
            this.closeModal();
            this.loadGallery();

        } catch (error) {
            this.showError(error.message || 'حدث خطأ أثناء حفظ الصورة');
        } finally {
            // Re-enable button
            saveBtn.disabled = false;
            saveText.textContent = 'حفظ';
            saveSpinner.classList.add('hidden');
        }
    }

    async fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => {
                // Remove the data:image/...;base64, prefix
                const base64 = reader.result.split(',')[1];
                resolve(base64);
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    editImage(imageId) {
        this.openModal(imageId);
    }

    deleteImage(imageId) {
        this.currentImageId = imageId;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    async confirmDelete() {
        if (!this.currentImageId) return;

        try {
            await this.makeRequest(`${this.apiUrl}?id=${this.currentImageId}`, {
                method: 'DELETE'
            });

            this.showSuccess('تم حذف الصورة بنجاح');
            this.closeDeleteModal();
            this.loadGallery();

        } catch (error) {
            this.showError('حدث خطأ أثناء حذف الصورة');
        }
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 left-4 z-50 p-4 rounded-lg text-white ${
            type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
}

// Initialize gallery manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.galleryManager = new GalleryManager();
});
</script>