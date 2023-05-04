<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Database\Statistics\StatisticsEntity;
use App\Database\Statistics\StatisticsHandler;
use App\Manager\CommentManager;
use Psr\Http\Message\ResponseInterface;

/** 
 * AdminDashboardController generates the admin dashboard page.
 */
class AdminDashboardController extends AbstractController
{
    /**
     * Displays the dashboard page with the statistics.
     */
    public function dashboard(): ResponseInterface
    {
        if (!$this->auth->logged()) {
            return $this->redirect('app_login');
        }
        if (!$this->auth->isAdmin()) {
            $this->createForbidderException('Vous ne pouvez pas consulter cette page.');
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
