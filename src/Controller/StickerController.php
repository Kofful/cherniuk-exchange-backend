<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Image\ImageService;
use App\Service\Inventory\InventoryService;
use App\Service\StatusCode;
use App\Service\Sticker\StickerService;
use App\Service\User\UserService;
use App\Service\Validator\StickerValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class StickerController extends AbstractController
{
    public function getAll(StickerService $stickerService, Request $request): Response
    {
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 10;
        $user = $this->getUser();

        $withCoefficients = isset($user) && in_array(User::ADMIN_ROLE_NAME, $this->getUser()->getRoles());
        $stickers = $stickerService->getAll($withCoefficients, $page, $limit);
        $count = $stickerService->getCount();

        return $this->json([
            "stickers" => $stickers,
            "count" => $count
        ]);
    }

    public function add(ImageService $imageService, StickerService $stickerService, StickerValidator $stickerValidator, Request $request): Response
    {
        $body = [];
        $status = StatusCode::STATUS_OK;

        $file = $request->files->get("sticker");
        $fileName = $imageService->saveImageToDirectory($file);

        if ($fileName) {
            $sticker = $stickerService->prepareSticker(
                array_merge($request->request->all(), ["path" => $fileName]),
                ["name", "coefficient", "path"]
            );
            $errors = $stickerValidator->validateSticker($sticker);

            if (count($errors) > 0) {
                $status = StatusCode::STATUS_BAD_REQUEST;
                $body = $errors;
            } else {
                $stickerService->add($sticker);
            }
        } else {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $body = ["The file cannot be saved"];
        }

        return $this->json($body, $status);
    }

    public function update(ImageService $imageService, StickerService $stickerService, StickerValidator $stickerValidator, Request $request): Response
    {
        $body = [];
        $status = StatusCode::STATUS_OK;
        $file = $request->files->get("sticker");
        $fileName = $imageService->saveImageToDirectory($file);

        //remove path from params if it exists
        $params = $request->request->all();
        unset($params["path"]);

        //add path only if there is a new file (otherwise we will delete file without uploading new one)
        $params = isset($fileName) ? array_merge($request->request->all(), ["path" => $fileName]) : $params;

        $sticker = $stickerService->prepareSticker(
            $params,
            ["name", "coefficient", "id", "path"]
        );

        $errors = $stickerValidator->validateSticker(
            $sticker,
            ["name", "coefficient", "id"]);

        if (count($errors) > 0) {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $body = $errors;
        } else {
            $stickerService->update($sticker);
        }

        return $this->json($body, $status);
    }

    public function giveStickerToUser(
        StickerService $stickerService,
        UserService $userService,
        InventoryService $inventoryService,
        TranslatorInterface $translator
    ): Response
    {
        $status = StatusCode::STATUS_OK;

        $user = $this->getUser();

        $response = $stickerService->giveStickerToUser($user);

        if (isset($response)) {
            $inventoryService->addItem($user, $response);
            $userService->updateRewardedAt($user);
        } else {
            $response = [$translator->trans("sticker.cannot.receive", [], "responses")];
            $status = StatusCode::STATUS_ACCESS_DENIED;
        }

        return $this->json($response, $status);
    }
}
