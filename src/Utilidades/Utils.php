<?php

namespace App\Utilidades;

use App\Entity\ApiKey;
use App\Entity\Usuario;
use App\Repository\ApiKeyRepository;
use App\Repository\UsuarioRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class Utils
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }


//    public function toJson($data, ?array  $groups ): string
//    {
//        //Inicialización de serializador
//        $encoders = [new XmlEncoder(), new JsonEncoder()];
//        $normalizers = [new ObjectNormalizer()];
//        $serializer = new Serializer($normalizers, $encoders);
//
//        if($groups != null){
//            //Conversion a JSON con groups
//            $json = $serializer->serialize($data, 'json', ['groups' => $groups]);
//        }else{
//            //Conversion a JSON
//            $json = $serializer->serialize($data, 'json');
//        }
//
//        return $json;
//    }

    public function toJson($data, ?array  $groups ): string
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups("user_query")->toArray();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);


        if($groups != null){
            //Conversion a JSON con groups
            $json = $serializer->serialize($data, 'json', $context);
        }else{
            //Conversion a JSON
            $json = $serializer->serialize($data, 'json');
        }

        return $json;
    }


    //$person = $serializer->deserialize($data, Person::class, 'xml');

//    public function toJson($data, ?array  $groups ): string
//    {
//
//        $serializer = SerializerBuilder::create()->build();
//        $context = SerializationContext::create()->setGroups($groups);
//        $context->setSerializeNull(true);
//
//        $json = $serializer->serialize($data, 'json', $context);
//
//
//
//        return $json;
//    }

    public function  hashPassword($password):string
    {

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');

        return $passwordHasher->hash($password);

    }

    public function  verify($passwordPlain, $passwordBD):bool
    {
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');

        return $passwordHasher->verify($passwordBD,$passwordPlain);

    }


    public function  generateApiToken(Usuario $user, ApiKeyRepository $apiKeyRepository):string
    {

        //GENERO UN OBJETO CON API KEY NUEVO
        $apiKey = new ApiKey();
        $apiKey->setUsuario($user);
        $fechaActual5hour = date("Y-m-d H:i:s", strtotime('+5 hours'));
        $fechaExpiracion = DateTime::createFromFormat('Y-m-d H:i:s', $fechaActual5hour);
        $apiKey->setFechaExpiracion($fechaExpiracion);

        $tokenData = [
            'user_id' => $user->getId(),
            'username' => $user->getId(),
            'user_rol' => $user->getRol()->getDescripcion(),
            'fecha_expiracion' => $fechaExpiracion,
        ];

        $secret = $user->getPassword();

        $token = Token::customPayload($tokenData, $secret);

        $apiKey->setToken($token);

        $apiKeyRepository->save($apiKey,true);


        return $token;
    }

    public function comprobarPermisos(Request $request, $permiso){
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);
        $token = $request->headers->get("apikey");

        return $token != null and $this->esApiKeyValida($token, $permiso, $apikeyRepository, $userRepository);

    }


    public function esApiKeyValida($token, $permisoRequerido, ApiKeyRepository $apiKeyRepository,UsuarioRepository $usuarioRepository):bool
    {
        $apiKey = $apiKeyRepository->findOneBy(array("token" => $token));
        $fechaActual = DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
        $id_usuario = Token::getPayload($token)["user_id"];
        $rol_name= Token::getPayload($token)["user_rol"];
        $usuario= $usuarioRepository->findOneBy(array("id" => $id_usuario));

        return $apiKey == null
            or $permisoRequerido == $rol_name
            or $apiKey->getUsuario()->getId() == $id_usuario
            or $apiKey->getFechaExpiracion() <= $fechaActual
            or Token::validate($token, $usuario->getPassword());
    }



}