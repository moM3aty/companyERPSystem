<?php
// Path: resources/views/auth/forgot-password.php
// Standalone Layout for the Forgot Password flow.
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nour Trust ERP - Forgot Password</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        nour: { dark: '#0a1930', primary: '#005eb8', light: '#21a1f1' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background Decorative Elements -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-nour-light opacity-10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-nour-primary opacity-10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md px-4 relative z-10">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-xl bg-gradient-to-tr from-nour-dark to-nour-primary flex items-center justify-center text-white font-bold text-3xl shadow-lg mb-4">
                NT
            </div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Nour Trust ERP</h2>
            <p class="text-sm text-gray-500 mt-2">Password Recovery</p>
        </div>

        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-2 text-center">Forgot your password?</h3>
            <p class="text-sm text-gray-500 text-center mb-6">Enter your email address below and we'll send you a link to reset your password.</p>
            
            <form action="/forgot-password/submit" method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-nour-light/50 focus:border-nour-primary sm:text-sm transition-colors bg-gray-50 focus:bg-white" 
                            placeholder="admin@nourtrust.com">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-nour-dark to-nour-primary hover:from-black hover:to-nour-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nour-primary transition-all duration-200">
                        Send Reset Link <i class="fas fa-paper-plane ml-2 mt-1"></i>
                    </button>
                </div>
                
                <div class="text-center mt-4 border-t border-gray-100 pt-4">
                    <a href="/login" class="text-sm font-medium text-nour-primary hover:text-nour-dark transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            &copy; <?= date('Y') ?> Nour Trust. All rights reserved.<br>
            Secure Enterprise Portal v2.0
        </div>
    </div>
</body>
</html>