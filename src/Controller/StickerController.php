<?php

namespace App\Controller;

use App\Service\Sticker\StickerService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StickerController extends AbstractController
{
    /**
     * @Route("/api/stickers", name="stickers")
     */
    public function getAll(StickerService $stickerService, Request $request): Response
    {
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 10;
        $user = $this->getUser();

        $withCoefficients = isset($user) && (new UserService())->isAdmin($user);
        $stickers = $stickerService->getAll($withCoefficients, $page, $limit);
        $count = $stickerService->getCount();

        return $this->json([
            "stickers" => $stickers,
            "count" => $count
        ]);
    }
}
