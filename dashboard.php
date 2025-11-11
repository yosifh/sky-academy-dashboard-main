<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
	header('Location: login.php');
	exit;
}

$page_title = 'لوحة التحكم';
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
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">لوحة التحكم</h1>
                <p class="text-gray-600">مرحبًا بك، <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Students Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">إجمالي الطلاب</p>
                            <h3 class="text-3xl font-bold text-gray-900">1,284</h3>
                            <p class="text-sm text-green-600 mt-2">
                                <i class="fas fa-arrow-up"></i> 12% عن الشهر الماضي
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-graduate text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Total Teachers Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">المعلمين</p>
                            <h3 class="text-3xl font-bold text-gray-900">48</h3>
                            <p class="text-sm text-green-600 mt-2">
                                <i class="fas fa-arrow-up"></i> 5% عن الشهر الماضي
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Total Courses Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الدورات النشطة</p>
                            <h3 class="text-3xl font-bold text-gray-900">76</h3>
                            <p class="text-sm text-yellow-600 mt-2">
                                <i class="fas fa-minus"></i> 2% عن الشهر الماضي
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Revenue Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الإيرادات الشهرية</p>
                            <h3 class="text-3xl font-bold text-gray-900">$45,678</h3>
                            <p class="text-sm text-green-600 mt-2">
                                <i class="fas fa-arrow-up"></i> 18% عن الشهر الماضي
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Chart 1 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">إحصائيات التسجيل</h3>
                        <select class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option>آخر 7 أيام</option>
                            <option>آخر 30 يوم</option>
                            <option>آخر 3 شهور</option>
                        </select>
                    </div>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p>مخطط بياني للتسجيلات</p>
                            <p class="text-sm">(يمكن دمج Chart.js أو مكتبة مخططات أخرى)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Chart 2 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">الدورات الأكثر شعبية</h3>
                        <button class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                            عرض الكل <i class="fas fa-arrow-left mr-1"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 space-x-reverse flex-1">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-code text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">تطوير الويب</p>
                                    <p class="text-sm text-gray-500">324 طالب</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">95%</p>
                                <div class="w-24 h-2 bg-gray-200 rounded-full mt-1">
                                    <div class="w-[95%] h-full bg-blue-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 space-x-reverse flex-1">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-mobile-alt text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">تطوير التطبيقات</p>
                                    <p class="text-sm text-gray-500">256 طالب</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">87%</p>
                                <div class="w-24 h-2 bg-gray-200 rounded-full mt-1">
                                    <div class="w-[87%] h-full bg-purple-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 space-x-reverse flex-1">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-brain text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">الذكاء الاصطناعي</p>
                                    <p class="text-sm text-gray-500">198 طالب</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">78%</p>
                                <div class="w-24 h-2 bg-gray-200 rounded-full mt-1">
                                    <div class="w-[78%] h-full bg-green-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 space-x-reverse flex-1">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-paint-brush text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">التصميم الجرافيكي</p>
                                    <p class="text-sm text-gray-500">167 طالب</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">72%</p>
                                <div class="w-24 h-2 bg-gray-200 rounded-full mt-1">
                                    <div class="w-[72%] h-full bg-orange-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Activity -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">النشاطات الأخيرة</h3>
                        <button class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                            عرض الكل <i class="fas fa-arrow-left mr-1"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4 space-x-reverse pb-4 border-b border-gray-100 last:border-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-plus text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">تسجيل طالب جديد</p>
                                <p class="text-sm text-gray-500">محمد أحمد انضم إلى دورة تطوير الويب</p>
                                <p class="text-xs text-gray-400 mt-1">منذ 5 دقائق</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 space-x-reverse pb-4 border-b border-gray-100 last:border-0">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">اكتمال دورة</p>
                                <p class="text-sm text-gray-500">سارة علي أكملت دورة التصميم الجرافيكي</p>
                                <p class="text-xs text-gray-400 mt-1">منذ ساعة</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 space-x-reverse pb-4 border-b border-gray-100 last:border-0">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">دورة جديدة</p>
                                <p class="text-sm text-gray-500">تم إضافة دورة "التسويق الرقمي" للمنصة</p>
                                <p class="text-xs text-gray-400 mt-1">منذ 3 ساعات</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 space-x-reverse pb-4 border-b border-gray-100 last:border-0">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-star text-orange-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">تقييم جديد</p>
                                <p class="text-sm text-gray-500">حصلت دورة الذكاء الاصطناعي على تقييم 5 نجوم</p>
                                <p class="text-xs text-gray-400 mt-1">منذ 5 ساعات</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <button class="w-full flex items-center justify-center space-x-2 space-x-reverse bg-primary-600 hover:bg-primary-700 text-white rounded-lg py-3 px-4 font-medium transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>إضافة طالب جديد</span>
                        </button>
                        
                        <button class="w-full flex items-center justify-center space-x-2 space-x-reverse bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-3 px-4 font-medium transition-colors">
                            <i class="fas fa-book"></i>
                            <span>إنشاء دورة</span>
                        </button>
                        
                        <button class="w-full flex items-center justify-center space-x-2 space-x-reverse bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-3 px-4 font-medium transition-colors">
                            <i class="fas fa-user-tie"></i>
                            <span>إضافة معلم</span>
                        </button>
                        
                        <button class="w-full flex items-center justify-center space-x-2 space-x-reverse bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-3 px-4 font-medium transition-colors">
                            <i class="fas fa-file-alt"></i>
                            <span>إنشاء تقرير</span>
                        </button>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">التنبيهات</h4>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2 space-x-reverse text-sm">
                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                <span class="text-gray-700">5 طلبات تحتاج موافقة</span>
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse text-sm">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                <span class="text-gray-700">12 رسالة جديدة</span>
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse text-sm">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <span class="text-gray-700">3 دورات تحتاج مراجعة</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </main>
</div>

