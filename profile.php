<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// Validate user exists
$stmt = $conn->prepare("SELECT username, email FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    session_destroy();
    header('Location: login.php');
    exit;
}
$user = $result->fetch_assoc();
$stmt->close();
?>
<h2>User Profile</h2>
<p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
<h3>Order History</h3>
<?php
$stmt = $conn->prepare("SELECT id, total_amount, status FROM `order` WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<ul>';
    while ($order = $result->fetch_assoc()) {
        $total = isset($order['total_amount']) ? $order['total_amount'] : 0;
        echo '<li>Order #' . htmlspecialchars($order['id']) . ' - Total: NRS ' . number_format($total, 2) . ' - Status: ' . htmlspecialchars($order['status']) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No orders found.</p>';
}
$stmt->close();
?>
<?php require_once 'includes/footer.php'; ?>