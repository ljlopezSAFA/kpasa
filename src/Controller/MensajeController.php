<?php

namespace App\Controller;

use App\Repository\MensajeRepository;
use App\Utilidades\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class MensajeController extends AbstractController
{
    #[Route('/mensaje', name: 'app_mensaje')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MensajeController.php',
        ]);
    }

    #[Route('/mensaje/list', name: 'app_mensaje')]
    public function listar(MensajeRepository $mensajeRepository, Utils $utils): JsonResponse
    {
        $listMensajes = $mensajeRepository->findAll();

        $listJson = $utils->toJson($listMensajes,null);

        return new JsonResponse($listJson, 200, [], true);

    }


}
