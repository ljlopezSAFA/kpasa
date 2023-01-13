<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}


    #[Route('/usuario', name: 'app_usuario')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsuarioController.php',
        ]);
    }

    #[Route('/usuario/list', name: 'app_usuario_listar', methods: ['GET'])]
    public function listar(UsuarioRepository $usuarioRepository, Utils $utilidades): JsonResponse
    {
        $listUsuarios= $usuarioRepository->findAll();

        $listJson = $utilidades->toJson($listUsuarios);

        return new JsonResponse($listJson, 200,[],true);

    }

    #[Route('/usuario/buscar', name: 'app_usuario_buscar', methods: ['GET'])]
    public function buscarPorNombre(UsuarioRepository $usuarioRepository,
                                    Utils $utilidades,
                                    Request $request): JsonResponse
    {
        $nombre = $request->query->get("nombre");

        $parametrosBusqueda = array(
            'username' => $nombre
        );

        $listUsuarios = $usuarioRepository->findBy($parametrosBusqueda);

        $listJson = $utilidades->toJson($listUsuarios);

        return new JsonResponse($listJson, 200,[],true);

    }


    #[Route('/usuario/save', name: 'app_usuario_crear', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $usuarioNuevo = new Usuario();
        $usuarioNuevo->setUsername($json['username']);
        $usuarioNuevo->setPassword($json['password']);

        //GUARDAR
         $em = $this-> doctrine->getManager();
         $em->persist($usuarioNuevo);
         $em-> flush();

         return new JsonResponse("{ mensaje: Usuario creado correctamente }", 200, [], true);


    }







}
