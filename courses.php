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
    <main class="pt-20 min-h-screen">
        <div class="p-4 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1>إدارة الدورات</h1>
                <button onclick="showCreateForm()">إضافة دورة جديدة</button>
            </div>
            
            <!-- Create/Edit Form -->
            <div id="courseForm" style="display: none; border: 1px solid #ccc; padding: 20px; margin-bottom: 20px;">
            <a href="lectures.php">عودة إلى المحاضرات</a>  
            <h2 id="formTitle">إضافة دورة جديدة</h2>
                <form id="courseDataForm">
                    <input type="hidden" id="courseId" name="courseId">
                    
                    <label>اسم الدورة: *</label>
                    <input type="text" id="courseName" name="name" required>
                    <br><br>
                    
                    <label>الوصف:</label>
                    <textarea id="courseDescription" name="description" rows="4"></textarea>
                    <br><br>
                    
                    <label>السعر: *</label>
                    <input type="number" id="coursePrice" name="price" step="0.01" value="0" required>
                    <br><br>
                    
                    <label>مدة الدورة:</label>
                    <input type="text" id="courseDuration" name="duration_text" placeholder="مثال: 3 أشهر">
                    <br><br>
                    
                    <label>نوع الدورة: *</label>
                    <select id="courseTypeId" name="course_type_id" required>
                        <option value="">-- اختر نوع الدورة --</option>
                    </select>
                    <br><br>
                    
                    <label>صورة الغلاف:</label>
                    <input type="file" id="courseCoverImageFile" accept="image/*" onchange="previewImage(this)">
                    <input type="hidden" id="courseCoverImage" name="cover_image_url">
                    <div id="imagePreview" style="margin-top: 10px;"></div>
                    <div id="uploadProgress" style="margin-top: 10px;"></div>
                    <br><br>
                    
                    <label>الحد الأقصى للطلاب:</label>
                    <input type="number" id="courseMaxStudents" name="max_students">
                    <br><br>
                    
                    <label>المدرسون:</label>
                    <select id="courseTeachers" name="teacher_ids" multiple size="5">
                        <option value="">جاري التحميل...</option>
                    </select>
                    <small>استخدم Ctrl+Click لاختيار متعدد</small>
                    <br><br>
                    
                    <button type="submit">حفظ</button>
                    <button type="button" onclick="hideForm()">إلغاء</button>
                </form>
            </div>
            
            <!-- Courses List -->
            <div id="coursesList">
                <h2>قائمة الدورات</h2>
                <div id="coursesContainer"></div>
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
            const { data, error } = await supabase.auth.setSession({
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
});

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
        
        const select = document.getElementById('courseTypeId');
        select.innerHTML = '<option value="">-- اختر نوع الدورة --</option>';
        
        courseTypes.forEach(type => {
            const option = document.createElement('option');
            option.value = type.id;
            option.textContent = type.name;
            select.appendChild(option);
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

function showCreateForm() {
    document.getElementById('courseForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'إضافة دورة جديدة';
    document.getElementById('courseDataForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('uploadProgress').innerHTML = '';
    
    // Clear teacher selections
    const teacherSelect = document.getElementById('courseTeachers');
    for (let i = 0; i < teacherSelect.options.length; i++) {
        teacherSelect.options[i].selected = false;
    }
    
    isEditMode = false;
    currentCourseId = null;
}

function hideForm() {
    document.getElementById('courseForm').style.display = 'none';
    document.getElementById('courseDataForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('uploadProgress').innerHTML = '';
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 200px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showEditForm(course) {
    console.log('Editing course:', course);
    document.getElementById('courseForm').style.display = 'block';
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
        document.getElementById('imagePreview').innerHTML = `<img src="${course.cover_image_url}" style="max-width: 200px; max-height: 200px;">`;
    }
    
    // Select teachers
    const teacherSelect = document.getElementById('courseTeachers');
    for (let i = 0; i < teacherSelect.options.length; i++) {
        teacherSelect.options[i].selected = false;
    }
    
    if (course.teachers && course.teachers.length > 0) {
        const teacherIds = course.teachers.map(t => t.id);
        for (let i = 0; i < teacherSelect.options.length; i++) {
            if (teacherIds.includes(teacherSelect.options[i].value)) {
                teacherSelect.options[i].selected = true;
            }
        }
    }
    
    isEditMode = true;
    currentCourseId = course.id;
}

// Load all courses
async function loadCourses() {
    try {
        console.log('Loading courses...');
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
            displayCourses(courses);
        } else {
            console.error('Failed to load courses:', courses);
            alert('فشل تحميل الدورات');
        }
    } catch (error) {
        console.error('Error loading courses:', error);
        alert('خطأ في تحميل الدورات');
    }
}

// Display courses
function displayCourses(courses) {
    const container = document.getElementById('coursesContainer');
    
    if (courses.length === 0) {
        container.innerHTML = '<p>لا توجد دورات</p>';
        return;
    }
    
    let html = '<table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">';
    html += '<tr><th>الاسم</th><th>السعر</th><th>النوع</th><th>المدرسون</th><th>الإجراءات</th></tr>';
    
    courses.forEach(course => {
        const teachers = course.teachers ? course.teachers.map(t => t.full_name).join(', ') : 'لا يوجد';
        const typeName = course.course_types ? course.course_types.name : course.course_type_id;
        
        html += `<tr>
            <td>${course.name}</td>
            <td>${course.price}</td>
            <td>${typeName}</td>
            <td>${teachers}</td>
            <td>
                <button onclick='viewCourse("${course.id}")'>عرض</button>
                <button onclick='editCourse("${course.id}")'>تعديل</button>
                <button onclick='deleteCourse("${course.id}")'>حذف</button>
            </td>
        </tr>`;
    });
    
    html += '</table>';
    container.innerHTML = html;
}

// Upload image to Supabase Storage
async function uploadCoverImage(file) {
    try {
        console.log('Uploading cover image:', file.name);
        document.getElementById('uploadProgress').textContent = 'جاري رفع الصورة...';
        
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
        
        console.log('Image uploaded successfully:', publicUrl);
        document.getElementById('uploadProgress').textContent = 'تم رفع الصورة بنجاح!';
        return publicUrl;
        
    } catch (error) {
        console.error('Error uploading image:', error);
        document.getElementById('uploadProgress').textContent = 'خطأ في رفع الصورة: ' + error.message;
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
        alert('خطأ في حفظ البيانات: ' + error.message);
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
            
            alert('تم إنشاء الدورة بنجاح');
            hideForm();
            loadCourses();
        } else {
            console.error('Failed to create course:', result);
            alert('فشل إنشاء الدورة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error creating course:', error);
        alert('خطأ في إنشاء الدورة: ' + error.message);
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
            
            alert('تم تحديث الدورة بنجاح');
            hideForm();
            loadCourses();
        } else {
            const result = await response.json();
            console.error('Failed to update course:', result);
            alert('فشل تحديث الدورة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error updating course:', error);
        alert('خطأ في تحديث الدورة: ' + error.message);
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
            alert('تفاصيل الدورة في Console');
        }
    } catch (error) {
        console.error('Error viewing course:', error);
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
            alert('فشل تحميل بيانات الدورة');
        }
    } catch (error) {
        console.error('Error loading course for edit:', error);
        alert('خطأ في تحميل بيانات الدورة');
    }
}

// Delete course
async function deleteCourse(courseId) {
    if (!confirm('هل أنت متأكد من حذف هذه الدورة؟')) {
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
            alert('تم حذف الدورة بنجاح');
            loadCourses();
        } else {
            const result = await response.json();
            console.error('Failed to delete course:', result);
            alert('فشل حذف الدورة: ' + (result.message || 'خطأ غير معروف'));
        }
    } catch (error) {
        console.error('Error deleting course:', error);
        alert('خطأ في حذف الدورة');
    }
}
</script>

</body>
</html>