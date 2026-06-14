<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        // Check if email exists
        $check_sql = "SELECT id FROM users WHERE email = '$email'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
            
            if ($conn->query($sql)) {
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=index.php");
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Drowsiness Detection System</title>
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
                <h1 class="text-4xl font-bold text-white mb-2">Create Account</h1>
                <p class="text-gray-300">Register to monitor drowsiness</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
                <?php if ($error): ?>
                    <div class="bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="bg-green-500/20 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-5">
                    <div>
                        <label class="block text-white text-sm font-semibold mb-2">Full Name</label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter your full name">
                    </div>
                    
                    <div>
                        <label class="block text-white text-sm font-semibold mb-2">Email Address</label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter your email">
                    </div>
                    
                    <div>
                        <label class="block text-white text-sm font-semibold mb-2">Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Create a password (min 6 chars)">
                    </div>
                    
                    <div>
                        <label class="block text-white text-sm font-semibold mb-2">Confirm Password</label>
                        <input type="password" name="confirm_password" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Confirm your password">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-500 to-teal-600 text-white font-semibold py-3 rounded-lg hover:from-green-600 hover:to-teal-700 transition-all duration-300 transform hover:scale-105">
                        Register
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-300">Already have an account? 
                        <a href="index.php" class="text-blue-400 hover:text-blue-300 font-semibold">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>