<?php include 'db.php'; ?>
<link rel="stylesheet" href="all.css">

<div class="container">
<h2>IslandLink Sales Distribution Network Login</h2>

<form method="post">
    <input name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="login">Login</button>
</form>

<div class="password">
<p>consider as a customer Username is a retailer1 </p>
<p>consider as a customer Password is a 1234 </p>
</div>

<?php
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = md5($_POST['password']);

    $q = $conn->query("
        SELECT users.id, roles.role_name
        FROM users
        JOIN roles ON users.role_id = roles.id
        WHERE username='$u' AND password='$p'
    ");

    if ($q->num_rows == 1) {
        $user = $q->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role_name'];
        header("Location: dashboard.php");
    } else {
        echo "Invalid login";
    }
}
?>
</div>