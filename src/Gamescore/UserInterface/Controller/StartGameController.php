<?php

namespace Football\Gamescore\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class StartGameController extends Controller
{
    public function startGameAction(): JsonResponse
    {
        return JsonResponse::create(['naisss']);
    }
}