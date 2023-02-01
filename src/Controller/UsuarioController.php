<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Dto\DtoConverters;
use App\Dto\UserDto;
use App\Entity\ApiKey;
use App\Entity\Rol;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use JsonMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }


    #[OA\Tag(name: 'Usuarios')]
    #[Route('/api/usuario', name: 'app_usuario', methods: ["GET"])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsuarioController.php',
        ]);
    }

    #[Route('/api/usuario/list', name: 'app_usuario_listar', methods: ['GET'])]
    #[OA\Tag(name: 'Usuarios')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UserDto::class))))]
    public function listar(UsuarioRepository $usuarioRepository, DtoConverters $converters, Utils $utils, Request $request): JsonResponse
    {

        if($utils->comprobarPermisos($request, "USER")){
            $listUsuarios= $usuarioRepository->findAll();

            $listJson = array();

            foreach($listUsuarios as $user){
                $usarioDto = $converters-> usuarioToDto($user);
                $json = $utils->toJson($usarioDto,null);
                $listJson[] = json_decode($json);
            }


            return new JsonResponse($listJson, 200,[],false);
        }else{
            return new JsonResponse("{ message: Unauthorized}", 401,[],false);

        }




    }




    #[Route('/api/usuario/buscar', name: 'app_usuario_buscar', methods: ['GET'])]
    #[OA\Tag(name: 'Usuarios')]
    #[OA\Parameter(name: "nombre", description: "Nombre de usuario que vas a buscar", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UserDto::class))))]
    public function buscarPorNombre(UsuarioRepository $usuarioRepository,
                                    Utils $utils,
                                    Request $request,
                                    DtoConverters $converters): JsonResponse
    {
        $nombre = $request->query->get("nombre");

        $parametrosBusqueda = array(
            'username' => $nombre
        );

        $listUsuarios = $usuarioRepository->findBy($parametrosBusqueda);

        $listJson = array();

        foreach($listUsuarios as $user){
            $usarioDto = $converters-> usuarioToDto($user);
            $json = $utils->toJson($usarioDto,null);
            $listJson[] = json_decode($json);
        }

        return new JsonResponse($listJson, 200,[],false);
    }

    #[Route('/api/usuario/save', name: 'app_usuario_crear', methods: ['POST'])]
    #[OA\Tag(name: 'Usuarios')]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CreateUserDto::class)))]
    #[OA\Response(response: 200,description: "Usuario creado correctamente")]
    #[OA\Response(response: 101,description: "No ha indicado usario y contraseña")]
    public function save(Request $request, Utils $utils): JsonResponse
    {

        //CARGA DATOS
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $rolRepository = $em->getRepository(Rol::class);
        $apiKeyRepository = $em->getRepository(ApiKey::class);


        //Obtener Json del body y pasarlo a DTO
        $mapper = new JsonMapper();
        $createUserDto = $mapper->map(json_decode($request->getContent()), new CreateUserDto());

        //Obtenemos los parámetros del JSON
        $username =$createUserDto->getUsername();
        $password = $createUserDto->getPassword();
        $rolname = $createUserDto->getRolName();

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

            return new JsonResponse("Usuario creado correctamente", 200, [], true);
        }else{
            return new JsonResponse("No ha indicado usario y contraseña", 101, [], true);
        }

    }







}
