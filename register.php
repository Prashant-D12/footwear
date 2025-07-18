<?php
require_once 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO user (username, email, password) VALUES ('$username', '$email', '$password')";
    if (mysqli_query($conn, $query)) {
        echo '<p class="success">Registration successful! <a href="login.php">Login</a></p>';
    } else {
        echo '<p class="error">Error: ' . mysqli_error($conn) . '</p>';
    }
}
?>
<h2>Register</h2>
<form action="register.php" method="post" onsubmit="return validateRegisterForm(this)">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login</a></p>
<?php require_once 'includes/footer.php'; ?>