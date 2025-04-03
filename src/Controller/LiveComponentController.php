<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LiveComponentController extends AbstractController
{
    #[Route('/live-component', name: 'app_live_component')]
    public function index(): Response
    {
        return $this->render('live_component/index.html.twig', [
            'controller_name' => 'LiveComponentController',
        ]);
    }
}
