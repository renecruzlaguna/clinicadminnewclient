<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Libs\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Users;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AppController extends BaseController {

    /**
     * @ApiDoc(
     *  description="Create a new User",
     *  input="Your\Namespace\Form\Type\YourType",
     *  output="Your\Namespace\Class",
     *     headers={
     *         {
     *             "name"="X-AUTHORIZE-KEY",
     *             "description"="Authorization key",
     *              "required":true
     *         }
     *     }
     * )
     *
     * @Rest\Post("/api/token-authentication")
     * @Method({"POST","OPTIONS"})
     */
    public function tokenAuthenticationAction(Request $request) {

       try {
           $username = $request->get('username');
           $password = $request->get('password');
           $service=$this->get('app.ldap');
           $auth = $service->verifyCredential($username.'@uci.cu', $password);
            if(!$auth){
                return new View(array('success' => false, 'error' => 'Usuario o contraseña incorrecta.'), Response::HTTP_OK);
            }
           $user = $this->getRepo('Usuario')
               ->findOneBy(['nombreUsuario' => $username]);

           if (!$user) {
               return new View(array('success' => false, 'error' => 'Usuario inv&aacute;lido'), Response::HTTP_OK);
           }

           // Use LexikJWTAuthenticationBundle to create JWT token that hold only information about user name
           $token = $this->get('lexik_jwt_authentication.encoder')
               ->encode(['nombreUsuario' => $user->getUsername()]);
           $info=$service->getInformationOfPerson($username);
           $data['urlFoto']=$service->getPhoto($info[0]['postofficebox'][0]);
           $result = $this->saveModel('Usuario', array('id' => $user->getId(), 'token' => $token));
           if ($result['success'] == false) {
               return new View($result, Response::HTTP_OK);
           }
           $user= $this->normalizeResult('Usuario',$this->getRepo('Usuario')
               ->findOneBy(['nombreUsuario' => $username]));
           return new View(array('success' => true, 'user' =>$user['data']), Response::HTTP_OK);
       }
       catch (\Exception $e){
           return new View($this->manageException($e), Response::HTTP_OK);
       }
    }

    /**
     * @ApiDoc(
     *  description="Create a new User",
     *  input="Your\Namespace\Form\Type\YourType",
     *  output="Your\Namespace\Class",
     *     headers={
     *         {
     *             "name"="X-AUTHORIZE-KEY",
     *             "description"="Authorization key",
     *              "required":true
     *         }
     *     }
     * )
     *
     * @Rest\Post("/api/session")
     * @Method({"POST","OPTIONS"})
     */
    public function closeSessionAction(Request $request) {
        $token = $request->headers->get('apiKey');

        $user = $this->getRepo('AGUsuario')
                ->findOneBy(['token' => $token]);

        if (!$user) {
            return new View(array('success' => false, 'error' => 'Token inv&aacute;lido', 'code' => 3000), Response::HTTP_OK);
        }


        $result = $this->saveModel('AGUsuario', array('id' => $user->getId(), 'token' => null));
        if ($result['success'] == false) {
            return new View($result, Response::HTTP_OK);
        }
        return new View(array('success' => true), Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/logo")
     * @Method({"GET","OPTIONS"})
     */
    public function logoAction() {
        $logo = null;
        $empresa = $this->getRepo('AGEmpresa')->findOneBy(array('tipoCliente' => 3));
        if ($empresa && !empty($empresa->getLogo())) {
            $logo = '/bundles/app/images/' . $empresa->getLogo();
        }
        return new View(array('success' => true, data => array('logo' => $logo)), Response::HTTP_OK);
    }

}
