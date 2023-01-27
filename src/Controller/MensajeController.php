<?php

namespace App\Controller;

use App\Entity\ApiKey;
use App\Entity\Usuario;
use App\Repository\MensajeRepository;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;


class MensajeController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }


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
    #[OA\Parameter(name: 'api_key', description: "Api de autentificación", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    public function listar(Request $request, MensajeRepository $mensajeRepository, Utils $utils): JsonResponse
    {

        //CARGAR REPOSITORIOS
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);

        $apikey = $request->query->get("api_key");
        $compruebaAcceso = $utils->esApiKeyValida($apikey, "USER", $apikeyRepository, $userRepository);


        if ($compruebaAcceso) {

            $listMensajes = $mensajeRepository->findAll();

            $listJson = $utils->toJson($listMensajes, null);

            return new JsonResponse($listJson, 200, [], true);

        } else {
            return $this->json([
                'message' => "No tiene permiso",
            ]);
        }
    }


}
