<?php
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../app/app.php';
require __DIR__.'/../app/config/prod.php';
require __DIR__.'/../src/controller/errorHandlers.php';
require __DIR__.'/../src/controller/controller.php';
return $app;