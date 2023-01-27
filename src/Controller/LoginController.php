<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Entity\ApiKey;
use App\Entity\Rol;
use App\Entity\Usuario;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;

class LoginController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }


    #[Route('/api/login', name: 'app_login', methods: ["POST"])]
    #[OA\Tag(name: 'Login')]
    #[OA\RequestBody(description: "Dto de autentificación", content: new OA\JsonContent(ref: new Model(type: LoginDto::class)))]
    public function login(Request $request, Utils $utils): JsonResponse
    {

        //CARGAR REPOSITORIOS
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);



        //Cargar datos del cuerpo
        $json_body = json_decode($request->getContent(), true);

        //Datos Usuario
        $username = $json_body["username"];
        $password = $json_body["password"];

        //Validar que los credenciales son correcto
        if($username != null and $password !=null){

            $user = $userRepository->findOneBy(array("username"=> $username));


            if($user != null){
                $verify = $utils-> verify($password, $user->getPassword());
                if($verify){

                    $token = $apikeyRepository-> findApiKeyValida($user);

                    if($token != null){
                        return $this->json([
                            'token' => $token->getToken()
                        ]);
                    }else{
                        $tokenNuevo = $utils->generateApiToken($user, $apikeyRepository);
                        return $this->json([
                            'token' => $tokenNuevo
                        ]);
                    }
                }else{
                    return $this->json([
                        'message' => "Contraseña no válida" ,
                    ]);
                }

            }
            return $this->json([
                'message' => "Usuario no válido" ,
            ]);


        }else{
            return $this->json([
                'message' => "No ha indicado usuario y contraseña" ,
            ]);

        }



    }
}
