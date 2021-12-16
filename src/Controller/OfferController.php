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
            }
        }
        return $this->json($response, $status);
    }
}
