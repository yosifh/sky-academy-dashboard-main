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

            <!-- Dashboard cards -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500">إجمالي الطلاب</p>
                            <p class="text-2xl font-semibold text-gray-900">1,248</p>
                        </div>
                        <div class="bg-primary-50 text-primary-600 p-2 rounded-md">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-muted-gray">+4.6% مقارنة بالأسبوع الماضي</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500">الدورات النشطة</p>
                            <p class="text-2xl font-semibold text-gray-900">36</p>
                        </div>
                        <div class="bg-primary-50 text-primary-600 p-2 rounded-md">
                            <i class="fas fa-book-open"></i>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-muted-gray">دورتان جديدتان هذا الشهر</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500">الطلبات المعلقة</p>
                            <p class="text-2xl font-semibold text-gray-900">12</p>
                        </div>
                        <div class="bg-yellow-50 text-yellow-600 p-2 rounded-md">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-muted-gray">إدارة الموافقات</p>
                </div>
            </section>

            <!-- Main grid: table + quick actions -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-gray-700">التسجيلات الأخيرة</h2>
                        <div class="text-sm text-gray-500">عرض 10 أحدث التسجيلات</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-sm">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">الطالب</th>
                                    <th class="px-4 py-3">الدورة</th>
                                    <th class="px-4 py-3">الحالة</th>
                                    <th class="px-4 py-3">تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr>
                                    <td class="px-4 py-3">فاطمة علي</td>
                                    <td class="px-4 py-3">تصميم ويب</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-50 text-green-600">نشط</span></td>
                                    <td class="px-4 py-3">منذ يومين</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">عمر حسن</td>
                                    <td class="px-4 py-3">أساسيات بايثون</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-50 text-yellow-700">معلق</span></td>
                                    <td class="px-4 py-3">منذ 5 أيام</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">سارة محمود</td>
                                    <td class="px-4 py-3">التسويق 101</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-50 text-gray-700">تمت الدعوة</span></td>
                                    <td class="px-4 py-3">منذ أسبوع</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <aside class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">إجراءات سريعة</h3>
                    <form aria-label="نموذج إجراء سريع" class="space-y-3">
                        <div>
                            <label for="name" class="block text-xs font-medium text-gray-600">الاسم</label>
                            <input id="name" type="text" class="mt-1 block w-full border border-gray-200 rounded-md py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label for="course" class="block text-xs font-medium text-gray-600">الدورة</label>
                            <select id="course" class="mt-1 block w-full border border-gray-200 rounded-md py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option>تصميم ويب</option>
                                <option>أساسيات بايثون</option>
                                <option>التسويق 101</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-300">إضافة</button>
                            <button type="reset" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 text-sm rounded-md hover:bg-gray-50">إعادة تعيين</button>
                        </div>
                    </form>
                </aside>
            </section>

            <!-- Optional chart placeholder -->
            <section class="mt-6">
                <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">مخطط الأداء</h3>
                    <div class="h-64 flex items-center justify-center text-gray-400">مخطط (سيتم إضافته لاحقاً)</div>
                </div>
            </section>

        </div>

        <?php include 'includes/footer.php'; ?>
    </main>
</div>

