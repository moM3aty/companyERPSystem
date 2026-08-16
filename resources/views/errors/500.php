<?php
// Path: resources/views/errors/500.php
// Standalone Layout for 500 Internal Server Error.
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | Nour Trust ERP</title>
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
        <div class="text-gray-300 text-9xl font-black opacity-30 mb-[-40px]">500</div>
        <div class="bg-white p-8 rounded-xl shadow-xl border border-gray-100 relative z-10 border-t-4 border-t-red-500">
            <div class="w-16 h-16 mx-auto bg-gray-100 text-gray-500 rounded-full flex items-center justify-center text-3xl mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Internal Server Error</h1>
            <p class="text-gray-500 text-sm mb-6">We're sorry, something went wrong on our servers while trying to process your request. The technical team has been notified.</p>
            <div class="bg-gray-50 p-3 rounded text-left border border-gray-200 mb-6 font-mono text-xs text-gray-600 overflow-x-auto">
                <span class="font-bold text-red-500">Exception:</span> Database connection timeout or query execution failure.<br>
                <span class="text-gray-400">Log Ref: ERR-<?= time() ?></span>
            </div>
            <a href="/dashboard" class="inline-block bg-nour-primary text-white font-medium text-sm px-6 py-2.5 rounded-lg shadow-md hover:bg-nour-dark transition-colors w-full">
                Back to Safety (Dashboard)
            </a>
        </div>
    </div>
</body>
</html>