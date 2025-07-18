<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/esewa_config.php';

if (!isset($_GET['q']) || !isset($_GET['transaction_uuid']) || !isset($_GET['status'])) {
    header('Location: ' . BASE_URL . 'profile.php');
    exit;
}

$transaction_uuid = $_GET['transaction_uuid'];
$status = $_GET['status'];

try {
    $stmt = $pdo->prepare("SELECT * FROM payment WHERE transaction_uuid = ?");
    $stmt->execute([$transaction_uuid]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment) {
        $order_id = $payment['order_id'];
        if ($status === 'success') {
            $stmt = $pdo->prepare("UPDATE payment SET status = 'completed', transaction_id = ? WHERE transaction_uuid = ?");
            $stmt->execute([$_GET['transaction_uuid'], $transaction_uuid]);
            $stmt = $pdo->prepare("UPDATE `order` SET status = 'completed' WHERE id = ?");
            $stmt->execute([$order_id]);
            $message = 'Payment successful! Order #' . $order_id . ' confirmed.';
        } else {
            $stmt = $pdo->prepare("UPDATE payment SET status = 'failed' WHERE transaction_uuid = ?");
            $stmt->execute([$transaction_uuid]);
            $message = 'Payment failed. Please try again.';
        }
    } else {
        $message = 'Invalid transaction.';
    }
} catch (PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/frontend.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main>
        <h2>Payment Status</h2>
        <p class="<?php echo $status === 'success' ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <a href="<?php echo BASE_URL; ?>profile.php">Back to Profile</a>
    </main>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>