<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    mysqli_query($conn, "DELETE FROM user WHERE id = $user_id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        <h2>Manage Users</h2>
        <table>
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Action</th></tr>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM user");
            while ($user = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $user['id'] . '</td>';
                echo '<td>' . $user['username'] . '</td>';
                echo '<td>' . $user['email'] . '</td>';
                echo '<td><form action="users.php" method="post">
                          <input type="hidden" name="user_id" value="' . $user['id'] . '">
                          <button type="submit" name="delete_user">Delete</button>
                      </form></td>';
                echo '</tr>';
            }
            ?>
        </table>
    </main>
</body>
</html>