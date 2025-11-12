<!-- Top Navbar -->
<nav class="bg-white border-b border-gray-200 shadow-sm fixed top-0 left-0 right-0 lg:right-64 h-20 z-30">
    <div class="h-full px-4 lg:px-8 flex items-center justify-between">
        <!-- Search Bar -->
        <div class="flex-1 max-w-2xl">
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       placeholder="البحث..." 
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2 pr-10 pl-4 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <!-- Right Section -->
        <div class="flex items-center space-x-4 space-x-reverse mr-4">
            <!-- Notifications -->
            <div class="relative">
                <button class="p-2 rounded-lg hover:bg-gray-100 relative">
                    <i class="fas fa-bell text-gray-600 text-xl"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
            </div>
            
            <!-- Messages -->
            <div class="relative">
                <button class="p-2 rounded-lg hover:bg-gray-100 relative">
                    <i class="fas fa-envelope text-gray-600 text-xl"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full"></span>
                </button>
            </div>
            
            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gray-100">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-semibold">
                        <?php 
                        $email = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '';
                        echo mb_substr($email, 0, 1, 'UTF-8'); 
                        ?>
                    </div>
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-semibold text-gray-700">مشرف النظام</p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars(mb_substr($email, 0, 20, 'UTF-8')); ?></p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                     style="display: none;">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user ml-2"></i>
                        الملف الشخصي
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog ml-2"></i>
                        الإعدادات
                    </a>
                    <hr class="my-2">
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        تسجيل الخروج
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Alpine.js for dropdown functionality -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
