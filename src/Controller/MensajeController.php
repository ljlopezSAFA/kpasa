<?php

namespace App\Controller;

use App\Repository\MensajeRepository;
use App\Utilidades\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;


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

    #[Route('/api/mensaje/list', name: 'app_mensaje', methods: ["GET"])]
    #[OA\Tag(name: 'Mensajes')]
    public function listar(MensajeRepository $mensajeRepository, Utils $utils): JsonResponse
    {
        $listMensajes = $mensajeRepository->findAll();

        $listJson = $utils->toJson($listMensajes,null);

        return new JsonResponse($listJson, 200, [], true);
    }


}
