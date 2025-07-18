<?php
require_once 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if ($is_admin) {
        $query = "SELECT * FROM admin WHERE username = '$identifier' AND password = '$password'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: admin/dashboard.php');
            exit;
        } else {
            echo '<p class="error">Invalid admin credentials</p>';
        }
    } else {
        $query = "SELECT * FROM user WHERE (username = '$identifier' OR email = '$identifier')";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: profile.php');
                exit;
            } else {
                echo '<p class="error">Invalid password</p>';
            }
        } else {
            echo '<p日在 class="error">User not found</p>';
        }
    }
}
?>
<h2>Login</h2>
<form action="login.php" method="post" onsubmit="return validateLoginForm(this)">
    <label for="identifier">Username or Email:</label>
    <input type="text" id="identifier" name="identifier" required>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <label><input type="checkbox" name="is_admin"> Login as Admin</label>
    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register</a></p>
<?php require_once 'includes/footer.php'; ?>