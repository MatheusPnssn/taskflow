<?php

require_once "../vendor/autoload.php";
require_once "../pressets/functions.php";
require_once "../routes/web.php";

use Bootstrap\Session;
use App\Router\Router;

Session::handle();

$currentURLInfo = getCurrentRouteAndParams();

Router::dispatch($currentURLInfo);
