<?php

namespace App\Service\Serializer;

use App\Entity\Offer;
use Exception;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonSerializer
{
    private Serializer $serializer;

    public function __construct()
    {
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $this->serializer = new Serializer([$normalizer], [new JsonEncoder()]);
    }

    public function deserialize(string $content, string $type): ?object
    {
        try {
            return $this->serializer->deserialize($content, $type, "json");
        } catch (Exception $ex) {
            return null;
        }
    }
}
