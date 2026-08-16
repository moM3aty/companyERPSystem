<?php
// Path: resources/views/errors/404.php
// Standalone Layout for 404 Not Found error.
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Nour Trust ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { nour: { dark: '#0a1930', primary: '#005eb8' } } } }
        }
    </script>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center font-['Inter']">
    <div class="text-center max-w-lg px-4">
        <div class="text-nour-primary text-9xl font-black opacity-20 mb-[-40px]">404</div>
        <div class="bg-white p-8 rounded-xl shadow-xl border border-gray-100 relative z-10">
            <div class="w-16 h-16 mx-auto bg-red-100 text-red-500 rounded-full flex items-center justify-center text-3xl mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Page Not Found</h1>
            <p class="text-gray-500 text-sm mb-6">The page or record you are looking for doesn't exist, has been deleted, or you don't have permission to access it.</p>
            <a href="/dashboard" class="inline-block bg-nour-primary text-white font-medium text-sm px-6 py-2.5 rounded-lg shadow-md hover:bg-nour-dark transition-colors">
                Return to Dashboard
            </a>
            <button onclick="window.history.back()" class="inline-block bg-gray-100 text-gray-700 font-medium text-sm px-6 py-2.5 rounded-lg border border-gray-200 hover:bg-gray-200 transition-colors ml-2">
                Go Back
            </button>
        </div>
    </div>
</body>
</html>