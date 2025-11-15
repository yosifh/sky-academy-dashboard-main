<!DOCTYPE php>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سكاي أكاديمي - منصة التعليم الرقمي</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    },
                    fontFamily: {
                        'arabic': ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body { 
            font-family: 'Cairo', sans-serif;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50">
    
    <!-- Header -->
    <header class="absolute top-0 left-0 right-0 z-50">
        <nav class="max-w-7xl mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-satellite-dish text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">سكاي أكاديمي</h1>
                        <p class="text-xs text-gray-600">منصة التعليم الرقمي</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 space-x-reverse">
                  
                    <a href="login.php" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                        <i class="fas fa-sign-in-alt ml-2"></i>
                        تسجيل الدخول
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <main class="relative min-h-screen flex items-center justify-center overflow-hidden">
        
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 right-20 w-72 h-72 bg-primary-200 rounded-full blur-3xl opacity-20 animate-float"></div>
            <div class="absolute bottom-20 left-20 w-96 h-96 bg-purple-200 rounded-full blur-3xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-blue-200 rounded-full blur-3xl opacity-10"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-6 py-32 text-center">
            
            <!-- Hero Content -->
            <div class="animate-fade-in-up">
                
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-6">
                    <i class="fas fa-star ml-2"></i>
                    منصة احترافية لإدارة المحتوى الإعلامي
                </div>
                
                <!-- Main Heading -->
                <h2 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 leading-tight">
                    مرحباً بكم في
                    <span class="bg-gradient-to-r from-primary-600 to-purple-600 bg-clip-text text-transparent">
                سكاي أكاديمي
                    </span>
                </h2>
                
                <!-- Description -->
                <p class="text-xl md:text-2xl text-gray-600 mb-12 max-w-3xl mx-auto leading-relaxed">
                    منصتك الشاملة لإدارة التعليم الرقمي والبرامج التعليمية والمحتوى الرقمي.
                    <br class="hidden md:block">
                    أنشئ، شارك، وتواصل مع جمهورك بسهولة وفعالية.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <a href="login.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-xl shadow-2xl hover:shadow-3xl transition-all transform hover:scale-105 text-lg">
                        <i class="fas fa-rocket ml-3"></i>
                        ابدأ الآن
                        <i class="fas fa-arrow-left mr-3"></i>
                    </a>
                  
                </div>
                
                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto mt-20">
                    
                    <!-- Feature 1 -->
                    <div class="bg-white bg-opacity-70 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-200">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-newspaper text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">إدارة الدورات الأعلامية التعليمية</h3>
                        <p class="text-gray-600 leading-relaxed">
                            نشر وتحرير الدورات التعليمية بسهولة مع أدوات احترافية لإدارة المحتوى التعليمي
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-white bg-opacity-70 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-200">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-microphone text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">برامج التصميم</h3>
                        <p class="text-gray-600 leading-relaxed">
                            إنشاء وإدارة البرامج التصميمية والبودكاست مع نظام متكامل للحلقات
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-white bg-opacity-70 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-200">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">إدارة الفريق</h3>
                        <p class="text-gray-600 leading-relaxed">
                            تعاون مع فريقك بكفاءة مع نظام صلاحيات متقدم وأدوات إدارية شاملة
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Stats Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-purple-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="text-white">
                    <div class="text-5xl font-bold mb-2">1000+</div>
                    <div class="text-primary-100 font-medium">مقال منشور</div>
                </div>
                <div class="text-white">
                    <div class="text-5xl font-bold mb-2">50+</div>
                    <div class="text-primary-100 font-medium">برنامج إذاعي</div>
                </div>
                <div class="text-white">
                    <div class="text-5xl font-bold mb-2">500+</div>
                    <div class="text-primary-100 font-medium">حلقة متاحة</div>
                </div>
                <div class="text-white">
                    <div class="text-5xl font-bold mb-2">24/7</div>
                    <div class="text-primary-100 font-medium">دعم متواصل</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <!-- About -->
                <div>
                    <div class="flex items-center space-x-3 space-x-reverse mb-4">
                        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-satellite-dish text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold">سكاي أكاديمي</h3>
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        منصة احترافية لإدارة المحتوى التعليمي والبرامج التعليمية
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2">
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition-colors">تسجيل الدخول</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">الدورات</a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h4 class="text-lg font-bold mb-4">الدعم</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">مركز المساعدة</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">الأسئلة الشائعة</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">اتصل بنا</a></li>
                    </ul>
                </div>
                
                <!-- Social -->
                <div>
                    <h4 class="text-lg font-bold mb-4">تابعنا</h4>
                    <div class="flex space-x-4 space-x-reverse">
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between">
                <p class="text-gray-400 text-sm">&copy; 2025 سكاي أكاديمي. جميع الحقوق محفوظة.</p>
                <div class="flex space-x-6 space-x-reverse mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">سياسة الخصوصية</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">شروط الاستخدام</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">اتصل بنا</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.grid > div').forEach(el => {
            observer.observe(el);
        });
        
        console.log('🚀 سكاي ميديا - منصة الإعلام الرقمي');
    </script>
</body>
</html>