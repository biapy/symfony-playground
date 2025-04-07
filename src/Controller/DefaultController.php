<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_index')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'action' => 'index',
            'time' => time(),
        ]);
    }
}
