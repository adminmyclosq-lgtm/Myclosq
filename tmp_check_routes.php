<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$url = $app->make('url');
echo "home=" . $url->route('home') . PHP_EOL;
echo "login=" . $url->route('login') . PHP_EOL;
echo "shop=" . $url->route('shop') . PHP_EOL;
echo "account=" . $url->route('account') . PHP_EOL;
echo "admin.dashboard=" . $url->route('admin.dashboard') . PHP_EOL;
