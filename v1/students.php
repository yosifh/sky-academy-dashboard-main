<?php
session_start();

// require login
if (empty($_SESSION['admin_logged_in'])) {
	header('Location: login.php');
	exit;
}

$page_title = 'إدارة الطلاب';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="lg:mr-64">
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Content -->
    <main class="pt-20 min-h-screen">
        <h1 class="text-3xl font-bold mb-6">إدارة الطلاب</h1>
        
        <?php include 'includes/footer.php'; ?>
    </main>
</div>