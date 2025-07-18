<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $image = mysqli_real_escape_string($conn, $_POST['image']);
        $query = "INSERT INTO product (name, description, price, image, stock) VALUES ('$name', '$description', $price, '$image', $stock)";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['update_product'])) {
        $id = (int)$_POST['id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $image = mysqli_real_escape_string($conn, $_POST['image']);
        $query = "UPDATE product SET name='$name', description='$description', price=$price, image='$image', stock=$stock WHERE id=$id";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['delete_product'])) {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM product WHERE id=$id");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
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
        <h2>Manage Products</h2>
        <form action="products.php" method="post" onsubmit="return validateProductForm(this)">
            <label for="name">Name:</label>
            <input type="text" name="name" required>
            <label for="description">Description:</label>
            <textarea name="description" required></textarea>
            <label for="price">Price (NRS):</label>
            <input type="number" name="price" step="0.01" required>
            <label for="image">Image Path:</label>
            <input type="text" name="image" placeholder="assets/images/shoeX.jpg" required>
            <label for="stock">Stock:</label>
            <input type="number" name="stock" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
        <table>
            <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Action</th></tr>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM product");
            while ($product = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $product['id'] . '</td>';
                echo '<td>' . $product['name'] . '</td>';
                echo '<td>NRS ' . number_format($product['price'], 2) . '</td>';
                echo '<td>' . $product['stock'] . '</td>';
                echo '<td>
                      <form action="products.php" method="post" style="display:inline;">
                          <input type="hidden" name="id" value="' . $product['id'] . '">
                          <input type="text" name="name" value="' . $product['name'] . '" required>
                          <textarea name="description" required>' . $product['description'] . '</textarea>
                          <input type="number" name="price" value="' . $product['price'] . '" step="0.01" required>
                          <input type="text" name="image" value="' . $product['image'] . '" required>
                          <input type="number" name="stock" value="' . $product['stock'] . '" required>
                          <button type="submit" name="update_product">Update</button>
                      </form>
                      <form action="products.php" method="post" style="display:inline;">
                          <input type="hidden" name="id" value="' . $product['id'] . '">
                          <button type="submit" name="delete_product">Delete</button>
                      </form>
                      </td>';
                echo '</tr>';
            }
            ?>
        </table>
    </main>
</body>
</html>