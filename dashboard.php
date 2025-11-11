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
            
     
   
            
       
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </main>
</div>

