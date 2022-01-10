<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\UserRepository;
use App\Service\Offer\OfferService;
use App\Service\Serializer\JsonSerializer;
use App\Service\Validator\OfferValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OfferController extends AbstractController
{
    public function getOffers(
        OfferService $offerService,
        OfferValidator $validator,
        Request $request
    ): Response {
        $response = [];
        $status = Response::HTTP_OK;
        $query = $offerService->prepareGettingOfferQuery($request->query->all());
        $errors = $validator->validateGettingOfferQuery($query);
        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
            $response = $errors;
        } else {
            $response = [
                "offers" => $offerService->getOffers($query),
                "count" => $offerService->getCount($query)
            ];
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
        $status = Response::HTTP_OK;
        $offer = $serializer->deserialize($request->getContent(), Offer::class);
        if (is_null($offer)) {
            $response = $translator->trans("invalid.types", [], "validators");
            $status = Response::HTTP_BAD_REQUEST;
            return $this->json($response, $status);
        }

        $errors = $validator->validate($offer);
        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
            foreach ($errors as $error) {
                $response[] = $error->getMessage();
            }
            return $this->json($response, $status);
        }

        $offer->setCreator($this->getUser());
        $errors = $offerService->createOffer($offer);
        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
            foreach ($errors as $error) {
                $response[] = $translator->trans($error, [], "responses");
            }
        }
        return $this->json($response, $status);
    }

    public function removeOffer(
        OfferService $offerService,
        TranslatorInterface $translator,
        Request $request
    ): Response {
        $response = [];
        $status = Response::HTTP_OK;
        $offerId = $request->get("id");
        $errors = $offerService->checkRemovePermissions($this->getUser(), $offerId);
        if (count($errors) > 0) {
            $status = Response::HTTP_FORBIDDEN;
            foreach ($errors as $error) {
                $response[] = $translator->trans($error, [], "responses");
            }
        } else {
            $offerService->removeOffer($offerId);
        }
        return $this->json($response, $status);
    }

    public function acceptOffer(
        OfferService $offerService,
        TranslatorInterface $translator,
        Request $request
    ): Response {
        $response = [];
        $status = Response::HTTP_OK;
        $offerId = $request->get("id");
        $user = $this->getUser();

        $errors = $offerService->checkAcceptPermissions($user, $offerId);
        if (count($errors) > 0) {
            $status = Response::HTTP_FORBIDDEN;
            foreach ($errors as $error) {
                $response[] = $translator->trans($error, [], "responses");
            }
            return $this->json($response, $status);
        }

        $errors = $offerService->acceptOffer($user, $offerId);
        if (count($errors) > 0) {
            $status = Response::HTTP_FORBIDDEN;
            foreach ($errors as $error) {
                $response[] = $translator->trans($error, [], "responses");
            }
        }
        return $this->json($response, $status);
    }

    public function getUserOffers(
        OfferService $offerService,
        UserRepository $userRepository,
        OfferValidator $validator,
        Request $request
    ): Response {
        $response = [];
        $status = Response::HTTP_OK;

        $page = $request->query->get("page") ?? 1;

        $userId = $request->attributes->get("id");
        $user = $userRepository->find($userId);

        $errors = $validator->validateUserOffers($page, $user);
        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
            $response = $errors;
        } else {
            $isOwnOffers = !is_null($this->getUser()) && $userId == $this->getUser()->getId();
            $criteria = $offerService->setCriteria(
                Offer::STATUS_OPEN_ID,
                $userId,
                $isOwnOffers
            );

            $response = [
                "offers" => $offerService->getOffersByCriteria($page, $criteria),
                "count" => $offerService->getCountByCriteria($criteria)
            ];
        }
        return $this->json(
            $response,
            $status,
            [],
            ["groups" => ["allOffers", "allStickers", "profile"]]
        );
    }

    public function getIncomingOffers(
        OfferService $offerService,
        OfferValidator $validator,
        Request $request
    ): Response {
        $response = [];
        $status = Response::HTTP_OK;

        $page = $request->query->get("page") ?? 1;

        $errors = $validator->validateIncoming($page);
        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
            $response = $errors;
        } else {
            $criteria = $offerService->setCriteria(
                Offer::STATUS_OPEN_ID,
                $this->getUser()->getId(),
                false,
                true
            );

            $response = [
                "offers" => $offerService->getOffersByCriteria($page, $criteria),
                "count" => $offerService->getCountByCriteria($criteria)
            ];
        }
        return $this->json(
            $response,
            $status,
            [],
            ["groups" => ["allOffers", "allStickers", "profile"]]
        );
    }

    public function getUserHistory(
        OfferService $offerService,
        UserRepository $userRepository,
        OfferValidator $validator,
        Request $request
    ): Response {
        $response = [];
        $status = Response::HTTP_OK;

        $page = $request->query->get("page") ?? 1;

        $userId = $request->attributes->get("id");
        $user = $userRepository->find($userId);


        $errors = $validator->validateUserOffers($page, $user);
        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
            $response = $errors;
        } else {
            $response = [
                "offers" => $offerService->getUserHistory($page, $userId),
                "count" => $offerService->getUserHistoryCount($userId)
            ];
        }
        return $this->json(
            $response,
            $status,
            [],
            ["groups" => ["allOffers", "allStickers", "profile"]]
        );
    }
}
