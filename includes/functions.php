<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function generateSignature($data, $secret_key) {
    return base64_encode(hash_hmac('sha256', $data, $secret_key, true));
}
?>