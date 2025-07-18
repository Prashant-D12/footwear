<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/esewa_config.php';

if (!isLoggedIn() || !isset($_SESSION['order_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$order_id = (int)$_SESSION['order_id'];

try {
    $stmt = $pdo->prepare("SELECT total_amount FROM `order` WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo '<p class="error">Invalid or completed order. Please try again.</p>';
        require_once 'includes/footer.php';
        exit;
    }

    $total_amount = $order['total_amount'];
    $transaction_uuid = uniqid();
    $stmt = $pdo->prepare("UPDATE payment SET transaction_uuid = ? WHERE order_id = ?");
    $stmt->execute([$transaction_uuid, $order_id]);

    $data = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=" . ESEWA_MERCHANT_ID;
    $signature = generateSignature($data, ESEWA_SECRET_KEY);
?>
<h2>eSewa Payment</h2>
<p>Redirecting to eSewa payment page...</p>
<form id="esewaForm" action="<?php echo ESEWA_URL; ?>" method="POST">
    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($total_amount); ?>">
    <input type="hidden" name="tax_amount" value="0">
    <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">
    <input type="hidden" name="transaction_uuid" value="<?php echo htmlspecialchars($transaction_uuid); ?>">
    <input type="hidden" name="product_code" value="<?php echo ESEWA_MERCHANT_ID; ?>">
    <input type="hidden" name="product_service_charge" value="0">
    <input type="hidden" name="product_delivery_charge" value="0">
    <input type="hidden" name="success_url" value="<?php echo SUCCESS_URL; ?>">
    <input type="hidden" name="failure_url" value="<?php echo FAILURE_URL; ?>">
    <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
    <input type="hidden" name="signature" value="<?php echo htmlspecialchars($signature); ?>">
    <button type="submit">Pay with eSewa</button>
</form>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Submitting eSewa form...');
        document.getElementById('esewaForm').submit();
    });
</script>
<?php
} catch (PDOException $e) {
    echo '<p class="error">Database error: ' . $e->getMessage() . '</p>';
}
require_once 'includes/footer.php';
?>