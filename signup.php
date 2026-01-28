<?php
session_start();
include "db.php";

// Redirect logged-in users to dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username already exists";
    } else {
        mysqli_query(
            $conn,
            "INSERT INTO users (username, password) VALUES ('$username','$password')"
        );
        header("Location: index.php?signup=success");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TPL Compliance Portal - Sign Up</title>

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

        .signup-container {
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

        .login-link {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .login-link a {
            color: #1b8f4c;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="signup-container">

    <div class="header">
        <img src="images/tpl.png" alt="TPL Logo">
        <h2>Compliance Portal</h2>
    </div>

    <p class="subtitle">Create a new authorized account</p>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Create Account</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <div class="login-link">
        Already have an account?
        <a href="index.php">Sign In</a>
    </div>

</div>

</body>
</html>
