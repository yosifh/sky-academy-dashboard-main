<?php
// Get current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<aside id="sidebar" class="fixed right-0 top-0 h-screen w-64 bg-white shadow-xl z-40 transform transition-transform duration-300 ease-in-out lg:translate-x-0">
    <!-- Logo -->
    <div class="flex items-center justify-center h-20 border-b border-gray-200 bg-gradient-to-l from-primary-600 to-primary-700">
        <div class="flex items-center space-x-2 space-x-reverse">
            <i class="fas fa-graduation-cap text-3xl text-white"></i>
            <span class="text-xl font-bold text-white">سكاي أكاديمي</span>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="px-4 py-6 overflow-y-auto h-[calc(100vh-5rem)]">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="dashboard.php" class="sidebar-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 group">
                    <i class="fas fa-home w-6 text-gray-500 group-hover:text-primary-600"></i>
                    <span class="mr-3 font-medium">لوحة التحكم</span>
                </a>
            </li>
            
            <!-- Clients -->
            <li>
                <a href="clients.php" class="sidebar-link <?php echo $current_page === 'clients.php' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 group">
                    <i class="fas fa-users w-6 text-gray-500 group-hover:text-primary-600"></i>
                    <span class="mr-3 font-medium">العملاء</span>
                </a>
            </li>
            
            <!-- Teachers -->
            <li>
                <a href="teachers.php" class="sidebar-link <?php echo $current_page === 'teachers.php' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 group">
                    <i class="fas fa-chalkboard-teacher w-6 text-gray-500 group-hover:text-primary-600"></i>
                    <span class="mr-3 font-medium">المعلمين</span>
                </a>
            </li>
            
            <!-- Students -->
            <li>
                <a href="students.php" class="sidebar-link <?php echo $current_page === 'students.php' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 group">
                    <i class="fas fa-user-graduate w-6 text-gray-500 group-hover:text-primary-600"></i>
                    <span class="mr-3 font-medium">الطلاب</span>
                </a>
            </li>
            
            <!-- Courses -->
            <li>
                <a href="courses.php" class="sidebar-link <?php echo $current_page === 'courses.php' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 group">
                    <i class="fas fa-book w-6 text-gray-500 group-hover:text-primary-600"></i>
                    <span class="mr-3 font-medium">الدورات</span>
                </a>
            </li>
            
            <!-- Gallery -->
            <li>
                <a href="gallary.php" class="sidebar-link <?php echo $current_page === 'gallary.php' ? 'active' : ''; ?> flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 group">
                    <i class="fas fa-images w-6 text-gray-500 group-hover:text-primary-600"></i>
                    <span class="mr-3 font-medium">معرض الصور</span>
                </a>
            </li>
        </ul>
        
        <!-- Divider -->
        <div class="my-6 border-t border-gray-200"></div>
        
        <!-- Additional Links -->
        <ul class="space-y-2">
            <li>
                <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 group">
                    <i class="fas fa-cog w-6 text-gray-500 group-hover:text-primary-600"></i>
                    <span class="mr-3 font-medium text-gray-700">الإعدادات</span>
                </a>
            </li>
            <li>
                <a href="logout.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-red-50 group">
                    <i class="fas fa-sign-out-alt w-6 text-gray-500 group-hover:text-red-600"></i>
                    <span class="mr-3 font-medium text-gray-700 group-hover:text-red-600">تسجيل الخروج</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Mobile Menu Button -->
<button id="mobile-menu-button" class="lg:hidden fixed top-4 right-4 z-50 p-2 rounded-lg bg-white shadow-lg">
    <i class="fas fa-bars text-gray-700 text-xl"></i>
</button>

<!-- Overlay for mobile -->
<div id="sidebar-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

<script>
    // Mobile menu toggle
    const sidebar = document.getElementById('sidebar');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    
    mobileMenuButton.addEventListener('click', () => {
        sidebar.classList.toggle('translate-x-0');
        sidebar.classList.toggle('translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
    });
    
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.add('translate-x-full');
        sidebar.classList.remove('translate-x-0');
        sidebarOverlay.classList.add('hidden');
    });
    
    // Close sidebar on mobile when window is resized to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('translate-x-full');
            sidebar.classList.add('translate-x-0');
            sidebarOverlay.classList.add('hidden');
        } else {
            sidebar.classList.add('translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
    });
    
    // Initialize sidebar state on mobile
    if (window.innerWidth < 1024) {
        sidebar.classList.add('translate-x-full');
        sidebar.classList.remove('translate-x-0');
    }
</script>
