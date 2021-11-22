<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InventoryItemController extends AbstractController
{
    public function getUserItems(Request $request): Response
    {
        $query = $request->query;
        return $this->json();
    }
}
