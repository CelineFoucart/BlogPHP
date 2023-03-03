<?php

declare(strict_types=1);

namespace App\Controller;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class UserController extends AbstractController
{
    public function login(ServerRequest $request): ResponseInterface
    {
        return $this->render('user/login.html.twig');
    }
    
    public function register(ServerRequest $request): ResponseInterface
    {
        return $this->render('user/register.html.twig');
    }
}