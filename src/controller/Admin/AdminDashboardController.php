<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\manager\CommentManager;
use App\Controller\AbstractController;
use App\Database\Statistics\StatisticsEntity;
use App\Database\Statistics\StatisticsHandler;
use Psr\Http\Message\ResponseInterface;

class AdminDashboardController  extends AbstractController
{
    public function dashboard(): ResponseInterface
    {
        $stats = (new StatisticsHandler())
            ->addEntity(new StatisticsEntity('blog_post'))
            ->addEntity(new StatisticsEntity('comment'))
            ->getStatistics()
        ;
        
        /** @var CommentManager */
        $commentManager = $this->getManager(CommentManager::class);

        return $this->render('admin/dashboard.html.twig', [
            'comments' => $commentManager->findLastNotValidated(),
            'stats' => $stats,
        ]);
    }
}