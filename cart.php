<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// Validate user exists in the user table
$stmt = $conn->prepare("SELECT id FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo '<p class="error">Invalid user. Please log in again.</p>';
    session_destroy();
    header('Location: login.php');
    exit;
}
$stmt->close();

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Validate product and stock
    $stmt = $conn->prepare("SELECT stock FROM product WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        if ($quantity <= $product['stock'] && $quantity > 0) {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE quantity = quantity + ?");
            $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
            if ($stmt->execute()) {
                echo '<p class="success">Item added to cart.</p>';
            } else {
                echo '<p class="error">Error adding item to cart: ' . $conn->error . '</p>';
            }
        } else {
            echo '<p class="error">Requested quantity exceeds available stock or is invalid.</p>';
        }
    } else {
        echo '<p class="error">Product not found.</p>';
    }
    $stmt->close();
}

// Remove from cart
if (isset($_POST['remove_from_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo '<p class="success">Item removed from cart.</p>';
    } else {
        echo '<p class="error">Error removing item from cart.</p>';
    }
    $stmt->close();
}

// Update quantity
if (isset($_POST['update_quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Validate stock
    $stmt = $conn->prepare("SELECT stock FROM product WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        if ($quantity <= $product['stock'] && $quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            if ($stmt->execute()) {
                echo '<p class="success">Cart updated.</p>';
            } else {
                echo '<p class="error">Error updating cart.</p>';
            }
        } else {
            echo '<p class="error">Invalid quantity or exceeds stock.</p>';
        }
    } else {
        echo '<p class="error">Product not found.</p>';
    }
    $stmt->close();
}
?>
<h2>Your Cart</h2>
<?php
$stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.image, p.stock 
                        FROM cart c 
                        JOIN product p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total = 0;

if ($result->num_rows > 0) {
    echo '<form action="cart.php" method="post">';
    echo '<table><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th></tr>';
    while ($item = $result->fetch_assoc()) {
        $item_total = $item['price'] * $item['quantity'];
        $total += $item_total;
        echo '<tr>';
        echo '<td><img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '" width="50"> ' . htmlspecialchars($item['name']) . '</td>';
        echo '<td>NRS ' . number_format($item['price'], 2) . '</td>';
        echo '<td><input type="number" name="quantity" value="' . $item['quantity'] . '" min="1" max="' . $item['stock'] . '" required>
                  <input type="hidden" name="product_id" value="' . $item['product_id'] . '">
                  <button type="submit" name="update_quantity">Update</button></td>';
        echo '<td>NRS ' . number_format($item_total, 2) . '</td>';
        echo '<td><button type="submit" name="remove_from_cart" formaction="cart.php">Remove</button></td>';
        echo '</tr>';
    }
    echo '<tr><td colspan="3">Total</td><td>NRS ' . number_format($total, 2) . '</td><td><a href="checkout.php">Checkout</a></td></tr>';
    echo '</table>';
    echo '</form>';
} else {
    echo '<p>Your cart is empty.</p>';
}
$stmt->close();
?>
<?php require_once 'includes/footer.php'; ?>