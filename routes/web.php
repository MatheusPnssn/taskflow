<?php

use App\Router\Router;
use App\Controllers\DashboardController;

Router::get(['/', 'home'], DashboardController::class, 'show')->name('dashboard');

Router::get('list', DashboardController::class, 'list')->name('list');