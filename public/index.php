<?php

use App\Kernel;
use GuzzleHttp\Psr7\ServerRequest;

require_once '../vendor/autoload.php';
require_once '../env.php';

define('PATH', dirname(__DIR__));

$kernel = new Kernel();
$response = $kernel->run(ServerRequest::fromGlobals());
Http\Response\send($response);
