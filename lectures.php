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

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="lg:mr-64">
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Content -->
    <main class="pt-20 min-h-screen">
        <div class="p-4 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1>إدارة المحاضرات</h1>
                
                <label>اختر الدورة: *</label>
                <select id="courseSelect" onchange="loadLectures()">
                    <option value="">-- اختر دورة --</option>
                </select>
                <br><br>
                
                <button onclick="showCreateForm()">إضافة محاضرة جديدة</button>
            </div>
            
            <!-- Create/Edit Form -->
            <div id="lectureForm" style="display: none; border: 1px solid #ccc; padding: 20px; margin-bottom: 20px;">
                <h2 id="formTitle">إضافة محاضرة جديدة</h2>
                <form id="lectureDataForm">
                    <input type="hidden" id="lectureId" name="lectureId">
                    <input type="hidden" id="lectureCourseId" name="course_id">
                    
                    <label>عنوان المحاضرة: *</label>
                    <input type="text" id="lectureTitle" name="title" required>
                    <br><br>
                    
                    <label>الوصف:</label>
                    <textarea id="lectureDescription" name="description" rows="4"></textarea>
                    <br><br>
                    
                    <label>الصورة المصغرة:</label>
                    <input type="file" id="lectureThumbnailFile" accept="image/*" onchange="previewThumbnail(this)">
                    <input type="hidden" id="lectureThumbnail" name="thumbnail_url">
                    <div id="thumbnailPreview" style="margin-top: 10px;"></div>
                    <div id="thumbnailUploadProgress" style="margin-top: 10px;"></div>
                    <br><br>
                    
                    <label>مدة الفيديو (بالثواني):</label>
                    <input type="number" id="lectureDuration" name="duration_seconds">
                    <br><br>
                    
                    <label>ترتيب المحاضرة:</label>
                    <input type="number" id="lectureOrder" name="lecture_order" value="0">
                    <br><br>
                    
                    <label>
                        <input type="checkbox" id="lectureIsFree" name="is_free">
                        محاضرة مجانية (معاينة)
                    </label>
                    <br><br>
                    
                    <button type="submit">حفظ المحاضرة</button>
                    <button type="button" onclick="hideForm()">إلغاء</button>
                </form>
                
                <hr style="margin: 20px 0;">
                
                <h3>رفع فيديو المحاضرة</h3>
                <div id="videoUploadSection">
                    <p><strong>ملاحظة:</strong> يجب حفظ المحاضرة أولاً قبل رفع الفيديو</p>
                    <input type="file" id="videoFile" accept="video/*">
                    <br><br>
                    <button type="button" onclick="uploadVideo()">رفع الفيديو</button>
                    <div id="videoUploadProgress" style="margin-top: 10px;"></div>
                    <div id="currentVideoUrl" style="margin-top: 10px;"></div>
                </div>
            </div>
            
            <!-- Lectures List -->
            <div id="lecturesSection" style="display: none;">
                <h2>قائمة المحاضرات</h2>
                <div id="lecturesContainer"></div>
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
                const courseType = course.course_types ? ` (${course.course_types.name})` : '';
                option.textContent = course.name + courseType;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading courses:', error);
        alert('خطأ في تحميل الدورات');
    }
}

function showCreateForm() {
    const courseId = document.getElementById('courseSelect').value;
    if (!courseId) {
        alert('الرجاء اختيار دورة أولاً');
        return;
    }
    
    document.getElementById('lectureForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'إضافة محاضرة جديدة';
    document.getElementById('lectureDataForm').reset();
    document.getElementById('lectureCourseId').value = courseId;
    document.getElementById('thumbnailPreview').innerHTML = '';
    document.getElementById('thumbnailUploadProgress').innerHTML = '';
    document.getElementById('videoUploadProgress').innerHTML = '';
    document.getElementById('currentVideoUrl').innerHTML = '';
    
    isEditMode = false;
    currentLectureId = null;
}

function hideForm() {
    document.getElementById('lectureForm').style.display = 'none';
    document.getElementById('lectureDataForm').reset();
    document.getElementById('thumbnailPreview').innerHTML = '';
    document.getElementById('thumbnailUploadProgress').innerHTML = '';
    document.getElementById('videoUploadProgress').innerHTML = '';
    document.getElementById('currentVideoUrl').innerHTML = '';
}

function previewThumbnail(input) {
    const preview = document.getElementById('thumbnailPreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 200px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showEditForm(lecture) {
    console.log('Editing lecture:', lecture);
    document.getElementById('lectureForm').style.display = 'block';
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
        document.getElementById('thumbnailPreview').innerHTML = `<img src="${lecture.thumbnail_url}" style="max-width: 200px; max-height: 200px;">`;
    }
    
    // Show current video URL
    if (lecture.video_url) {
        document.getElementById('currentVideoUrl').innerHTML = `<strong>رابط الفيديو الحالي:</strong><br>${lecture.video_url}`;
    }
    
    isEditMode = true;
    currentLectureId = lecture.id;
}

// Load lectures for selected course
async function loadLectures() {
    const courseId = document.getElementById('courseSelect').value;
    
    if (!courseId) {
        document.getElementById('lecturesSection').style.display = 'none';
        return;
    }
    
    currentCourseId = courseId;
    
    try {
        console.log('Loading lectures for course:', courseId);
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
            document.getElementById('lecturesSection').style.display = 'block';
            displayLectures(lectures);
        } else {
            console.error('Failed to load lectures:', lectures);
            alert('فشل تحميل المحاضرات');
        }
    } catch (error) {
        console.error('Error loading lectures:', error);
        alert('خطأ في تحميل المحاضرات');
    }
}

// Display lectures
function displayLectures(lectures) {
    const container = document.getElementById('lecturesContainer');
    
    if (lectures.length === 0) {
        container.innerHTML = '<p>لا توجد محاضرات لهذه الدورة</p>';
        return;
    }
    
    let html = '<table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">';
    html += '<tr><th>الترتيب</th><th>العنوان</th><th>المدة</th><th>مجانية</th><th>فيديو</th><th>الإجراءات</th></tr>';
    
    lectures.forEach(lecture => {
        const duration = lecture.duration_seconds ? `${Math.floor(lecture.duration_seconds / 60)}:${(lecture.duration_seconds % 60).toString().padStart(2, '0')}` : '-';
        const isFree = lecture.is_free ? 'نعم' : 'لا';
        const hasVideo = lecture.video_url ? 'نعم' : 'لا';
        
        html += `<tr>
            <td>${lecture.lecture_order}</td>
            <td>${lecture.title}</td>
            <td>${duration}</td>
            <td>${isFree}</td>
            <td>${hasVideo}</td>
            <td>
                <button onclick='editLecture("${lecture.id}")'>تعديل</button>
                <button onclick='deleteLecture("${lecture.id}")'>حذف</button>
            </td>
        </tr>`;
    });
    
    html += '</table>';
    container.innerHTML = html;
}

// Upload thumbnail to Supabase Storage
async function uploadThumbnail(file) {
    try {
        console.log('Uploading thumbnail:', file.name);
        document.getElementById('thumbnailUploadProgress').textContent = 'جاري رفع الصورة...';
        
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
        document.getElementById('thumbnailUploadProgress').textContent = 'تم رفع الصورة بنجاح!';
        return publicUrl;
        
    } catch (error) {
        console.error('Error uploading thumbnail:', error);
        document.getElementById('thumbnailUploadProgress').textContent = 'خطأ في رفع الصورة: ' + error.message;
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
        alert('خطأ في حفظ البيانات: ' + error.message);
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
            alert('تم إنشاء المحاضرة بنجاح. يمكنك الآن رفع الفيديو.');
            
            // Set lecture ID for video upload
            currentLectureId = result[0].id;
            document.getElementById('lectureId').value = result[0].id;
            isEditMode = true;
            
            loadLectures();
        } else {
            console.error('Failed to create lecture:', result);
            alert('فشل إنشاء المحاضرة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error creating lecture:', error);
        alert('خطأ في إنشاء المحاضرة: ' + error.message);
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
            alert('تم تحديث المحاضرة بنجاح');
            loadLectures();
        } else {
            const result = await response.json();
            console.error('Failed to update lecture:', result);
            alert('فشل تحديث المحاضرة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error updating lecture:', error);
        alert('خطأ في تحديث المحاضرة: ' + error.message);
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
            alert('لم يتم العثور على المحاضرة');
        }
    } catch (error) {
        console.error('Error loading lecture for edit:', error);
        alert('خطأ في تحميل بيانات المحاضرة');
    }
}

// Delete lecture
async function deleteLecture(lectureId) {
    if (!confirm('هل أنت متأكد من حذف هذه المحاضرة؟')) {
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
            alert('تم حذف المحاضرة بنجاح');
            loadLectures();
        } else {
            const result = await response.json();
            console.error('Failed to delete lecture:', result);
            alert('فشل حذف المحاضرة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error deleting lecture:', error);
        alert('خطأ في حذف المحاضرة');
    }
}

// Video upload functionality
async function uploadVideo() {
    const fileInput = document.getElementById('videoFile');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('الرجاء اختيار ملف فيديو');
        return;
    }
    
    const lectureId = document.getElementById('lectureId').value;
    if (!lectureId) {
        alert('الرجاء حفظ المحاضرة أولاً قبل رفع الفيديو');
        return;
    }
    
    try {
        document.getElementById('videoUploadProgress').textContent = `جاري رفع الفيديو... (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
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
            document.getElementById('videoUploadProgress').textContent = 'تم رفع الفيديو بنجاح!';
            document.getElementById('currentVideoUrl').innerHTML = `<strong>رابط الفيديو:</strong><br>${publicUrl}`;
            alert('تم رفع الفيديو بنجاح');
            loadLectures();
        } else {
            throw new Error('Failed to update lecture with video URL');
        }
        
    } catch (error) {
        console.error('Error uploading video:', error);
        document.getElementById('videoUploadProgress').textContent = 'خطأ في رفع الفيديو: ' + error.message;
        alert('خطأ في رفع الفيديو: ' + error.message);
    }
}
</script>

</body>
</html>