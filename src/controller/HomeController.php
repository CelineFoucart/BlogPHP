<?php

namespace App\Controller;

use App\Entity\Post;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function index(Request $request): ResponseInterface
    {
        return $this->render('home/index.html.twig');
    }
}
