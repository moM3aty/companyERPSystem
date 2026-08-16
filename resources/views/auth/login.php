<?php
// Path: resources/views/auth/login.php
// This is a standalone layout for the authentication pages.
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nour Trust ERP - Login</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Core Application CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">

    <!-- Tailwind CSS with Brand Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        nour: {
                            dark: '#0a1930',
                            primary: '#005eb8',
                            light: '#21a1f1'
                        }
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
        
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-xl bg-gradient-to-tr from-nour-dark to-nour-primary flex items-center justify-center text-white font-bold text-3xl shadow-lg mb-4">
                NT
            </div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Nour Trust ERP</h2>
            <p class="text-sm text-gray-500 mt-2">Enterprise Resource Planning System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 text-center">Sign in to your account</h3>
            
            <form action="/login/submit" method="POST" class="space-y-5">
                
                <!-- Email Input -->
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

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="/forgot-password" class="text-xs font-medium text-nour-primary hover:text-nour-dark transition-colors">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-nour-light/50 focus:border-nour-primary sm:text-sm transition-colors bg-gray-50 focus:bg-white" 
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" 
                        class="h-4 w-4 text-nour-primary focus:ring-nour-light border-gray-300 rounded cursor-pointer">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-600 cursor-pointer">
                        Remember me for 30 days
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-nour-dark to-nour-primary hover:from-black hover:to-nour-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nour-primary transition-all duration-200">
                        Sign In <i class="fas fa-arrow-right ml-2 mt-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-400">
            &copy; <?= date('Y') ?> Nour Trust. All rights reserved.<br>
            Secure Enterprise Portal v2.0
        </div>
    </div>

</body>
</html>