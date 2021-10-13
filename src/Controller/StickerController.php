<?php

namespace App\Controller;

use App\Service\Upload\UploadService;
use App\Service\Sticker\StickerService;
use App\Service\User\UserService;
use App\Service\Validator\StickerValidator;
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

    /**
     * @Route("/api/sticker", name="add_sticker")
     */
    public function add(UploadService $uploadService, StickerService $stickerService, StickerValidator $stickerValidator, Request $request): Response
    {
        $response = [];

        $file = $request->files->get("sticker");
        $fileName = $uploadService->saveImageToDirectory($file);
        $status = $fileName ? 200 : 400;

        if ($status == 200) {
            $sticker = $stickerService->prepareSticker(
                array_merge($request->request->all(), ["path" => $fileName])
            );
            $errors = $stickerValidator->validateSticker($sticker);

            $status = count($errors) ? 400 : 200;

            $response = $status == 200 ? $stickerService->add($sticker) : $errors;
        }

        return $this->json($response, $status);
    }

    /**
     * @Route("/api/sticker", name="update_sticker")
     */
    public function update(): Response
    {
        //TODO sticker updating logic
        return $this->json([
            "message" => "this message will be removed",
            "controller" => "StickerController::update"
        ]);
    }
}
