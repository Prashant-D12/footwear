<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE `order` SET status='$status' WHERE id=$order_id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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
        <h2>Manage Orders</h2>
        <table>
            <tr><th>Order ID</th><th>User ID</th><th>Total</th><th>Status</th><th>Action</th></tr>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM `order`");
            while ($order = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $order['id'] . '</td>';
                echo '<td>' . $order['user_id'] . '</td>';
                echo '<td>NRS ' . number_format($order['total'], 2) . '</td>';
                echo '<td>' . $order['status'] . '</td>';
                echo '<td>
                      <form action="orders.php" method="post">
                          <input type="hidden" name="order_id" value="' . $order['id'] . '">
                          <select name="status">
                              <option value="pending" ' . ($order['status'] == 'pending' ? 'selected' : '') . '>Pending</option>
                              <option value="completed" ' . ($order['status'] == 'completed' ? 'selected' : '') . '>Completed</option>
                              <option value="cancelled" ' . ($order['status'] == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                          </select>
                          <button type="submit" name="update_status">Update</button>
                      </form>
                      </td>';
                echo '</tr>';
            }
            ?>
        </table>
    </main>
</body>
</html>