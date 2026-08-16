<?php
// Path: routes/admin.php

declare(strict_types=1);

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;

/** @var Router $router */

// Admin Portal Routes (Session-based Auth)
$admin = new RouteGroup($router, ['prefix' => 'admin', 'middleware' => ['web', 'auth', 'tenant']]);

$admin->group(function (RouteGroup $group) {
    
    // User Management
    $group->get('/users', [\App\Modules\Administration\Users\Http\Controllers\UserController::class, 'index']);
    $group->post('/users', [\App\Modules\Administration\Users\Http\Controllers\UserController::class, 'store']);
    $group->delete('/users/{id}', [\App\Modules\Administration\Users\Http\Controllers\UserController::class, 'destroy']);
    
    // Roles & Permissions
    $group->get('/roles', [\App\Modules\Administration\Roles\Http\Controllers\RoleController::class, 'index']);
    $group->post('/roles', [\App\Modules\Administration\Roles\Http\Controllers\RoleController::class, 'store']);
    
    // Master Data
    // $group->get('/master-data/countries', [\App\Core\MasterData\Http\Controllers\CountryController::class, 'index']);
});