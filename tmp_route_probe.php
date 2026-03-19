<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/login', 'GET');
$response = $kernel->handle($request);
echo $response->getStatusCode(), PHP_EOL;
echo ($response->headers->get('Location') ?? 'NO_LOCATION'), PHP_EOL;
