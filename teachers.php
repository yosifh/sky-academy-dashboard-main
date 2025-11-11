<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
	header('Location: login.php');
	exit;
}

$page_title = 'إدارة المعلمين';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="lg:mr-64">
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Content -->
    <main class="pt-20 min-h-screen">
        <div class="p-4 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة المعلمين</h1>
                    <p class="text-gray-600">إدارة جميع المعلمين والمدرسين في المنصة</p>
                </div>
                <button class="flex items-center space-x-2 space-x-reverse bg-primary-600 hover:bg-primary-700 text-white rounded-lg py-3 px-6 font-medium transition-colors">
                    <i class="fas fa-user-plus"></i>
                    <span>إضافة معلم جديد</span>
                </button>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">إجمالي المعلمين</p>
                            <h3 class="text-2xl font-bold text-gray-900">48</h3>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">معلمون نشطون</p>
                            <h3 class="text-2xl font-bold text-green-600">42</h3>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-check text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">دورات مدارة</p>
                            <h3 class="text-2xl font-bold text-blue-600">76</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">متوسط التقييم</p>
                            <h3 class="text-2xl font-bold text-orange-600">4.8</h3>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-star text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <!-- Table Header with Search and Filters -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                        <!-- Search -->
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   placeholder="البحث عن معلم..." 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 pr-10 pl-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <select class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option>التخصص: الكل</option>
                                <option>تطوير الويب</option>
                                <option>البرمجة</option>
                                <option>التصميم</option>
                                <option>التسويق</option>
                            </select>
                            
                            <button class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-filter text-gray-600"></i>
                            </button>
                            
                            <button class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-download text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المعلم</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">التخصص</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">عدد الدورات</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">عدد الطلاب</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">التقييم</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            أ
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">د. أحمد السيد</p>
                                            <p class="text-sm text-gray-500">ahmed.elsayed@example.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        تطوير الويب
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-medium">12 دورة</td>
                                <td class="px-6 py-4 text-gray-700 font-medium">324 طالب</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1 space-x-reverse">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <span class="text-gray-900 font-medium">4.9</span>
                                        <span class="text-gray-500 text-sm">(156)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-[6px] ml-2"></i>
                                        نشط
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button class="p-2 hover:bg-blue-50 rounded-lg transition-colors" title="عرض">
                                            <i class="fas fa-eye text-blue-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-green-50 rounded-lg transition-colors" title="تعديل">
                                            <i class="fas fa-edit text-green-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                                            <i class="fas fa-trash text-red-600"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 2 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            م
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">د. مريم عبدالله</p>
                                            <p class="text-sm text-gray-500">mariam@example.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        تطوير التطبيقات
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-medium">8 دورات</td>
                                <td class="px-6 py-4 text-gray-700 font-medium">256 طالب</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1 space-x-reverse">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <span class="text-gray-900 font-medium">4.8</span>
                                        <span class="text-gray-500 text-sm">(128)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-[6px] ml-2"></i>
                                        نشط
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button class="p-2 hover:bg-blue-50 rounded-lg transition-colors" title="عرض">
                                            <i class="fas fa-eye text-blue-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-green-50 rounded-lg transition-colors" title="تعديل">
                                            <i class="fas fa-edit text-green-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                                            <i class="fas fa-trash text-red-600"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 3 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            خ
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">د. خالد محمد</p>
                                            <p class="text-sm text-gray-500">khaled@example.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        الذكاء الاصطناعي
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-medium">6 دورات</td>
                                <td class="px-6 py-4 text-gray-700 font-medium">198 طالب</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1 space-x-reverse">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <span class="text-gray-900 font-medium">5.0</span>
                                        <span class="text-gray-500 text-sm">(94)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-[6px] ml-2"></i>
                                        نشط
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button class="p-2 hover:bg-blue-50 rounded-lg transition-colors" title="عرض">
                                            <i class="fas fa-eye text-blue-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-green-50 rounded-lg transition-colors" title="تعديل">
                                            <i class="fas fa-edit text-green-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                                            <i class="fas fa-trash text-red-600"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 4 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            ن
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">أ. نورا حسن</p>
                                            <p class="text-sm text-gray-500">nora@example.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                        التصميم الجرافيكي
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-medium">10 دورات</td>
                                <td class="px-6 py-4 text-gray-700 font-medium">167 طالب</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1 space-x-reverse">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <span class="text-gray-900 font-medium">4.7</span>
                                        <span class="text-gray-500 text-sm">(82)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-[6px] ml-2"></i>
                                        نشط
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button class="p-2 hover:bg-blue-50 rounded-lg transition-colors" title="عرض">
                                            <i class="fas fa-eye text-blue-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-green-50 rounded-lg transition-colors" title="تعديل">
                                            <i class="fas fa-edit text-green-600"></i>
                                        </button>
                                        <button class="p-2 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                                            <i class="fas fa-trash text-red-600"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        عرض <span class="font-semibold">1</span> إلى <span class="font-semibold">4</span> من <span class="font-semibold">48</span> نتيجة
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <button class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="px-3 py-2 bg-primary-600 text-white rounded-lg">1</button>
                        <button class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">2</button>
                        <button class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">3</button>
                        <button class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </main>
</div>