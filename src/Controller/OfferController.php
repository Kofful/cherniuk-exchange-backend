<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Service\Offer\OfferService;
use App\Service\Serializer\JsonSerializer;
use App\Service\StatusCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OfferController extends AbstractController
{
    public function getOffers(
        OfferService $offerService,
        TranslatorInterface $translator,
        Request $request
    ): Response {
        $response = [];
        $status = StatusCode::STATUS_OK;
        $page = $request->query->get("page") ?? 1;
        if (!is_numeric($page) || $page < 1) {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $response = $translator->trans("invalid.page", [], "responses");
        } else {
            $response = $offerService->getOffers($page);
        }
        return $this->json($response, $status, [], ["groups" => ["allOffers", "allStickers", "profile"]]);
    }

    public function createOffer(
        OfferService $offerService,
        JsonSerializer $serializer,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        Request $request
    ): Response {
        $response = [];
        $status = StatusCode::STATUS_OK;
        $offer = $serializer->deserialize($request->getContent(), Offer::class);
        if (is_null($offer)) {
            $response = $translator->trans("invalid.types", [], "validators");
            $status = StatusCode::STATUS_BAD_REQUEST;
        } else {
            $errors = $validator->validate($offer);
            if (count($errors) > 0) {
                $status = StatusCode::STATUS_BAD_REQUEST;
                foreach ($errors as $error) {
                    $response[] = $error->getMessage();
                }
            } else {
                $offer->setCreator($this->getUser());
                $errors = $offerService->createOffer($offer);
                if (count($errors) > 0) {
                    $status = StatusCode::STATUS_BAD_REQUEST;
                    foreach ($errors as $error) {
                        $response[] = $translator->trans($error, [], "responses");
                    }
                }
            }
        }
        return $this->json($response, $status);
    }
}
