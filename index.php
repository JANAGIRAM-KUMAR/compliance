<?php
session_start();

// Allow access to signup even if session exists
if (isset($_SESSION['user']) && !isset($_GET['signup'])) {
    header("Location: dashboard.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TPL Compliance Portal - Login</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: #ffffff;
            width: 400px;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 25px;
        }

        .header img {
            width: 40px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #1b8f4c;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1b8f4c;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #15733d;
        }

        .error {
            margin-top: 15px;
            text-align: center;
            color: #d32f2f;
            font-size: 14px;
        }

        .success {
            margin-top: 15px;
            text-align: center;
            color: #2e7d32;
            font-size: 14px;
        }

        .signup-link {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .signup-link a {
            color: #1b8f4c;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="header">
        <img src="images/tpl.png" alt="TPL Logo">
        <h2>Compliance Portal</h2>
    </div>

    <p class="subtitle">Secure login for authorized users</p>

    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <p class="error">Invalid username or password</p>
    <?php endif; ?>

    <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
        <p class="success">Account created successfully. Please login.</p>
    <?php endif; ?>

    <div class="signup-link">
        New user?
        <a href="signup.php">Create an account</a>
    </div>

</div>

</body>
</html>
