<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/backend.css">
    <script src="../assets/js/backend.js" defer></script>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="users.php">Users</a>
            <a href="products.php">Products</a>
            <a href="orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Dashboard</h2>
        <p>Welcome, Admin!</p>
        <?php
        $users = mysqli_query($conn, "SELECT COUNT(*) as count FROM user")->fetch_assoc()['count'];
        $products = mysqli_query($conn, "SELECT COUNT(*) as count FROM product")->fetch_assoc()['count'];
        $orders = mysqli_query($conn, "SELECT COUNT(*) as count FROM `order`")->fetch_assoc()['count'];
        echo "<p>Total Users: $users</p>";
        echo "<p>Total Products: $products</p>";
        echo "<p>Total Orders: $orders</p>";
        ?>
    </main>
</body>
</html>