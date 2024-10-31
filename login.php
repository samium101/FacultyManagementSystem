<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_password, $role);
        $stmt->fetch();

        if ($password === $db_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            if ($role === 'faculty') {
                header("Location: dashboard.php");
                exit();
            } elseif ($role === 'moderator') {
                header("Location: moderator_dashboard.php");
                exit();
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Faculty Approval System</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(to bottom right, #ffffff, #f2f2f2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('Resources/campus.png');
            background-size: cover;
            background-position: center;
        }

        .login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            max-width: 500px;
            /* width: 100%; */
            padding: 30px 50px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            color: #b90000; /* Somaiya red */
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            color: #333;
            font-size: 16px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        input[type="email"], input[type="password"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .forgot-link {
            font-size: 12px;
            color: #b90000;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .login-btn {
            background-color: #b90000;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #990000;
        }

        .or-divider {
            text-align: center;
            margin: 20px 0;
            color: #666;
            font-size: 14px;
            position: relative;
        }

        .or-divider:before, .or-divider:after {
            content: '';
            display: inline-block;
            width: 40%;
            height: 1px;
            background: #ccc;
            vertical-align: middle;
        }

        .or-divider:before {
            margin-right: 10px;
        }

        .or-divider:after {
            margin-left: 10px;
        }

        .somaiya-email-btn {
            background-color: #444;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .somaiya-email-btn:hover {
            background-color: #333;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="logo">
                <img src="Resources/somaiyaLogo.png" alt="Somaiya Logo">
            </div>
            <h2>Faculty Authentication System</h2>
            <p>Enter your Somaiya email</p>

            <?php if($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <label for="email">Email *</label>
                <input type="email" name="email" required>

                <label for="password">Password *</label>
                <input type="password" name="password" required>


                <button type="submit" class="login-btn">Login</button>

                

                
            </form>
        </div>
    </div>
</body>
</html>
