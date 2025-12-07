<?php
session_start();
include "db/config.php";  //  PDO connection

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = md5($_POST['password']); // MD5 hashing

    // Prepare SQL safely (PDO)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
    // Bind user inputs to the SQL query
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);
    
    //Execute the SQL query
    $stmt->execute();

     
    //Check if exactly user matched
    if ($stmt->rowCount() == 1) {
        // If valid login → set session
        $_SESSION['admin_log_in'] = true;
        $_SESSION['admin_email'] = $email;

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f0f2f5;
      font-family: "Segoe UI", sans-serif;
    }
    .login-box {
      max-width: 420px;
      margin: 5% auto;
      padding: 40px 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .logo-circle {
      width: 70px;
      height: 70px;
      background: #0d6efd;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto 15px;
      font-size: 30px;
      font-weight: bold;
      color: #fff;
    }
  </style>
</head>
<body>
<?php if (!empty($error)) : ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
<?php endif; ?>

  <div class="login-box">
    <div class="logo-circle">C</div>

    <h3 class="text-center mb-3">Admin Login</h3>
    <p class="text-muted text-center">Sign in to access your dashboard</p>

    <form action="" method="POST">
      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" placeholder="Enter admin email" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>

      <div class="d-flex justify-content-between mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="remember">
          <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <a href="#" class="text-decoration-none">Forgot Password?</a>
      </div>

      <button class="btn btn-primary w-100" type="submit">Login</button>
    </form>

    <hr>

    <p class="text-center text-muted mb-0">© 2025 All Rights Reserved</p>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
