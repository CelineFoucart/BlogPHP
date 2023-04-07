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
    /**
     * @todo Ajouter les permissions
     *
     * @return ResponseInterface
     */
    public function dashboard(): ResponseInterface
    {
        if (!$this->auth->logged()) {
            return $this->redirect('app_login');
        }
        if (!$this->auth->isAdmin()) {
            $this->createForbidderException("Vous ne pouvez pas consulter cette page.");
        }

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