<?php

namespace App\Controller;

use App\Dto\DtoConverters;
use App\Dto\UserDto;
use App\Entity\ApiKey;
use App\Entity\Rol;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
class UsuarioController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }


    #[Route('/usuario', name: 'app_usuario')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsuarioController.php',
        ]);
    }

    #[Route('/usuario/list', name: 'app_usuario_listar', methods: ['GET'])]
    #[OA\Response(content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UserDto::class))))]
    public function listar(UsuarioRepository $usuarioRepository, DtoConverters $converters, Utils $utils): JsonResponse
    {
        $listUsuarios= $usuarioRepository->findAll();

        $listJson = array();

        foreach($listUsuarios as $user){
            $usarioDto = $converters-> usuarioToDto($user);
            $json = $utils->toJson($usarioDto,null);
            $listJson[] = json_decode($json);
        }


        return new JsonResponse($listJson, 200,[],false);

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

        $listJson = $utilidades->toJson($listUsuarios, null);

        return new JsonResponse($listJson, 200,[],true);
    }


    #[Route('/usuario/save', name: 'app_usuario_crear', methods: ['POST'])]
    public function save(Request $request, Utils $utils): JsonResponse
    {

        //CARGA DATOS
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $rolRepository = $em->getRepository(Rol::class);
        $apiKeyRepository = $em->getRepository(ApiKey::class);


        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        //Obtenemos los parámetros del JSON
        $username = $json['username'];
        $password = $json['password'];
        $rolname = $json['rol'];

        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        if($username != null and $password != null) {
            $usuarioNuevo = new Usuario();
            $usuarioNuevo->setUsername($username);
            $usuarioNuevo->setPassword($utils->hashPassword($password));

            //GESTION DEL ROL
            if ($rolname == null) {
                //Obtenemos el rol de usuario por defecto
                $rolUser = $rolRepository->findOneByIdentificador("USER");
                $usuarioNuevo->setRol($rolUser);

            } else {
                $rol = $rolRepository->findOneByIdentificador($rolname);
                $usuarioNuevo->setRol($rol);
            }

            //GUARDAR
            $userRepository->save($usuarioNuevo, true);


            $utils-> generateApiToken($usuarioNuevo,$apiKeyRepository);

            return new JsonResponse("{ mensaje: Usuario creado correctamente }", 200, [], true);
        }else{
            return new JsonResponse("{ mensaje: No ha indicado usario y contraseña }", 101, [], true);
        }

    }







}
