<?php
// Path: resources/views/layouts/app.php

/**
 * ============================================================================
 * Application Base URL Helper
 * ============================================================================
 *
 * The ERP system is currently hosted under:
 *
 * https://nourtrust.com/ERP/public
 *
 * We therefore generate all internal URLs relative to the current
 * application directory instead of using absolute root paths like:
 *
 * /assets/css/app.css
 * /dashboard
 *
 * This prevents the browser from looking under:
 *
 * https://nourtrust.com/assets/...
 *
 * instead of:
 *
 * https://nourtrust.com/ERP/public/assets/...
 */

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

$baseUrl = dirname($scriptName);

if (
    $baseUrl === '.'
    || $baseUrl === '\\'
    || $baseUrl === DIRECTORY_SEPARATOR
) {
    $baseUrl = '';
}

$baseUrl = rtrim($baseUrl, '/\\');

/**
 * Generate an application-relative URL.
 *
 * Example:
 *
 * url('dashboard')
 * =>
 * /ERP/public/dashboard
 *
 * url('assets/css/app.css')
 * =>
 * /ERP/public/assets/css/app.css
 */
$url = static function (string $path = '') use ($baseUrl): string {

    $path = trim($path);

    if ($path === '') {
        return $baseUrl !== '' ? $baseUrl . '/' : '/';
    }

    return $baseUrl . '/' . ltrim($path, '/');
};

/**
 * Escape generated URLs safely for HTML attributes.
 */
$assetUrl = static function (string $path) use ($url): string {
    return htmlspecialchars(
        $url($path),
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Nour Trust ERP - <?= htmlspecialchars(
            $pageTitle ?? 'Dashboard',
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <!-- ================================================================
         Font Awesome
         ================================================================ -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <!-- ================================================================
         Core Application CSS
         ================================================================ -->
<link
    rel="stylesheet"
    href="<?= $assetUrl('assets/css/app.css') ?>"
>

    <!-- ================================================================
         Tailwind CSS CDN
         ================================================================ -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        nour: {
                            dark: '#0f172a',
                            primary: '#2563eb',
                            light: '#3b82f6'
                        }
                    }
                }
            }
        };
    </script>

</head>

<body class="antialiased">

    <!-- ================================================================
         Toast Notifications
         ================================================================ -->
    <div
        id="toast-container"
        class="fixed top-20 right-5 z-50 flex flex-col gap-2"
    ></div>


    <!-- ================================================================
         Application Container
         ================================================================ -->
    <div id="app-container">


        <!-- ============================================================
             Sidebar
             ============================================================ -->
        <aside id="sidebar">

            <!-- Logo -->
            <div class="logo-container">

                <div class="flex items-center gap-3">

                    <div
                        class="w-9 h-9 rounded-lg bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center text-white font-black text-xl shadow-lg"
                    >
                        NT
                    </div>

                    <div class="flex flex-col">

                        <span
                            class="font-bold text-white tracking-wide text-sm leading-tight"
                        >
                            Nour Trust
                        </span>

                        <span
                            class="text-[10px] text-blue-300 font-mono tracking-widest uppercase"
                        >
                            Enterprise
                        </span>

                    </div>

                </div>

            </div>


            <!-- Quick Search -->
            <div class="px-4 py-3">

                <div class="relative">

                    <i
                        class="fas fa-search absolute left-3 top-2.5 text-slate-500 text-xs"
                    ></i>

                    <input
                        type="text"
                        placeholder="Quick Search (Cmd+K)"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-lg py-1.5 pl-8 pr-3 text-xs text-slate-300 focus:bg-slate-800 focus:border-blue-500 transition-all placeholder-slate-500"
                    >

                </div>

            </div>


            <!-- ========================================================
                 Main Navigation
                 ======================================================== -->
            <nav
                class="flex-1 overflow-y-auto pb-4 custom-scrollbar px-2 mt-2"
            >

                <!-- Overview -->
                <p
                    class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-2"
                >
                    Overview
                </p>

                <a
                    href="<?= $assetUrl('dashboard') ?>"
                    class="nav-item active rounded-lg"
                >
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>


                <!-- Operations -->
                <p
                    class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-6"
                >
                    Operations
                </p>

                <a
                    href="<?= $assetUrl('sales') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-hand-holding-dollar"></i>
                    <span>Sales & CRM</span>
                </a>

                <a
                    href="<?= $assetUrl('purchasing') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-shopping-cart"></i>
                    <span>Purchasing</span>
                </a>

                <a
                    href="<?= $assetUrl('inventory') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-boxes-stacked"></i>
                    <span>Inventory</span>
                </a>

                <a
                    href="<?= $assetUrl('manufacturing') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-industry"></i>
                    <span>Manufacturing</span>
                </a>


                <!-- Finance & HR -->
                <p
                    class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-6"
                >
                    Finance & HR
                </p>

                <a
                    href="<?= $assetUrl('accounting') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Accounting</span>
                </a>

                <a
                    href="<?= $assetUrl('treasury') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-building-columns"></i>
                    <span>Treasury</span>
                </a>

                <a
                    href="<?= $assetUrl('hr') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-users"></i>
                    <span>Human Resources</span>
                </a>

                <a
                    href="<?= $assetUrl('projects') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>


                <!-- System -->
                <p
                    class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-6"
                >
                    System
                </p>

                <a
                    href="<?= $assetUrl('reports') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-chart-line"></i>
                    <span>Reports & BI</span>
                </a>

                <a
                    href="<?= $assetUrl('admin') ?>"
                    class="nav-item rounded-lg"
                >
                    <i class="fas fa-cog"></i>
                    <span>Administration</span>
                </a>

            </nav>


            <!-- ========================================================
                 User Mini Profile
                 ======================================================== -->
            <div
                class="p-4 border-t border-slate-800 bg-slate-900/50 flex items-center gap-3"
            >

                <img
                    src="https://ui-avatars.com/api/?name=Admin+User&background=2563eb&color=fff"
                    alt="Admin User"
                    class="w-8 h-8 rounded-full border border-slate-600"
                >

                <div class="flex-1 min-w-0">

                    <p
                        class="text-xs font-bold text-white truncate"
                    >
                        Admin User
                    </p>

                    <p
                        class="text-[10px] text-slate-400 truncate"
                    >
                        admin@nourtrust.com
                    </p>

                </div>

            </div>

        </aside>


        <!-- ============================================================
             Main Wrapper
             ============================================================ -->
        <div id="main-wrapper">


            <!-- ========================================================
                 Topbar
                 ======================================================== -->
            <header id="topbar">

                <div class="flex items-center gap-5">

                    <!-- Sidebar Toggle -->
                    <button
                        id="sidebar-toggle"
                        class="text-slate-400 hover:text-nour-primary transition-colors focus:outline-none bg-slate-100 hover:bg-blue-50 w-8 h-8 rounded-lg flex items-center justify-center"
                    >
                        <i class="fas fa-align-left"></i>
                    </button>


                    <!-- Current Context -->
                    <div
                        class="hidden lg:flex items-center gap-2"
                    >

                        <span
                            class="text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            Current Context:
                        </span>

                        <div
                            class="bg-blue-50 text-nour-primary px-3 py-1.5 rounded-lg border border-blue-100 text-sm font-bold flex items-center cursor-pointer hover:bg-blue-100 transition-colors"
                        >

                            <i class="fas fa-building mr-2"></i>

                            HQ - Riyadh Branch

                            <i
                                class="fas fa-chevron-down ml-2 text-[10px]"
                            ></i>

                        </div>

                    </div>

                </div>


                <!-- ====================================================
                     Global Actions
                     ==================================================== -->
                <div class="flex items-center gap-4">

                    <!-- Support -->
                    <button
                        class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-nour-primary transition-colors flex items-center justify-center"
                    >
                        <i class="fas fa-headset"></i>
                    </button>


                    <!-- Notifications -->
                    <button
                        class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-nour-primary transition-colors flex items-center justify-center relative"
                    >

                        <i class="fas fa-bell"></i>

                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 flex justify-center items-center bg-red-500 text-white font-bold text-[9px] h-4 w-4 rounded-full border-2 border-white shadow-sm"
                        >
                            3
                        </span>

                    </button>


                    <!-- Divider -->
                    <div
                        class="h-6 w-px bg-slate-200 mx-1"
                    ></div>


                    <!-- ==================================================
                         User Dropdown
                         ================================================== -->
                    <div class="relative">

                        <button
                            data-dropdown-toggle="user-dropdown"
                            class="flex items-center gap-2 focus:outline-none dropdown-menu-trigger hover:opacity-80 transition-opacity"
                        >

                            <img
                                src="https://ui-avatars.com/api/?name=Admin+User&background=2563eb&color=fff"
                                alt="User"
                                class="w-9 h-9 rounded-full border-2 border-white shadow-sm"
                            >

                            <i
                                class="fas fa-chevron-down text-[10px] text-slate-400"
                            ></i>

                        </button>


                        <!-- Dropdown -->
                        <div
                            id="user-dropdown"
                            class="dropdown-menu hidden absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50 transform origin-top-right transition-all"
                        >

                            <!-- User Info -->
                            <div
                                class="px-4 py-3 border-b border-slate-50 mb-1"
                            >

                                <p
                                    class="text-sm font-bold text-slate-800"
                                >
                                    Admin User
                                </p>

                                <p
                                    class="text-xs text-slate-500"
                                >
                                    Super Administrator
                                </p>

                            </div>


                            <!-- Profile -->
                            <a
                                href="<?= $assetUrl('profile') ?>"
                                class="flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-nour-primary transition-colors"
                            >
                                <i class="far fa-user w-5"></i>
                                My Profile
                            </a>


                            <!-- Settings -->
                            <a
                                href="<?= $assetUrl('settings') ?>"
                                class="flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-nour-primary transition-colors"
                            >
                                <i class="fas fa-sliders-h w-5"></i>
                                Settings
                            </a>


                            <!-- Divider -->
                            <div
                                class="border-t border-slate-100 my-1"
                            ></div>


                            <!-- Logout -->
                            <a
                                href="<?= $assetUrl('logout') ?>"
                                class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                            >
                                <i class="fas fa-sign-out-alt w-5"></i>
                                Logout
                            </a>

                        </div>

                    </div>

                </div>

            </header>


            <!-- ========================================================
                 Dynamic Page Content
                 ======================================================== -->
            <main id="page-content">

                <div class="max-w-7xl mx-auto">

                    <?= $content ?? '' ?>

                </div>

            </main>

        </div>

    </div>


    <!-- ================================================================
         Core Application JavaScript
         ================================================================ -->
   <script src="<?= $assetUrl('assets/js/app.js') ?>"></script>

</body>
</html>