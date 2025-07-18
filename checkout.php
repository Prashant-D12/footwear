<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/esewa_config.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['payment_method'])) {
        $error = 'Payment method is required.';
    } elseif (!in_array($_POST['payment_method'], ['esewa', 'cod'])) {
        $error = 'Invalid payment method selected.';
    } else {
        $user_id = (int)$_SESSION['user_id'];
        $payment_method = $_POST['payment_method'];

        // Calculate total from cart
        $stmt = $pdo->prepare("SELECT c.product_id, c.quantity, p.price 
                               FROM cart c 
                               JOIN product p ON c.product_id = p.id 
                               WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($cart_items)) {
            $error = 'Cart is empty.';
        } else {
            $total_amount = 0;
            foreach ($cart_items as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }

            try {
                $transaction_uuid = uniqid();
                $stmt = $pdo->prepare("INSERT INTO `order` (user_id, total_amount, payment_method) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $total_amount, $payment_method]);
                $order_id = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO payment (order_id, transaction_uuid, amount, status) VALUES (?, ?, ?, 'pending')");
                $stmt->execute([$order_id, $transaction_uuid, $total_amount]);

                // Clear cart
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);

                if ($payment_method === 'esewa') {
                    $_SESSION['order_id'] = $order_id;
                    error_log("Checkout: Setting order_id=$order_id for user_id=$user_id");
                    header('Location: ' . BASE_URL . 'payment.php');
                    exit;
                } else {
                    header('Location: ' . BASE_URL . 'profile.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
                error_log("Checkout PDO error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/frontend.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <h2>Checkout</h2>
        <form id="checkoutForm" method="POST">
            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="esewa">eSewa</option>
                <option value="cod">Cash on Delivery</option>
            </select>
            <button type="submit">Proceed to Payment</button>
        </form>
    </main>
    <?php require_once 'includes/footer.php'; ?>
    <script src="<?php echo ASSETS_URL; ?>js/frontend.js"></script>
</body>
</html>