<?php
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT id, name, email, password FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Drowsiness Detection System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-purple-900 to-gray-900 min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Drowsiness Detection</h1>
                <p class="text-gray-300">Login to access your dashboard</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
                <?php if ($error): ?>
                    <div class="bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block text-white text-sm font-semibold mb-2">Email Address</label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter your email">
                    </div>
                    
                    <div>
                        <label class="block text-white text-sm font-semibold mb-2">Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                        Login
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-300">Don't have an account? 
                        <a href="register.php" class="text-blue-400 hover:text-blue-300 font-semibold">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>