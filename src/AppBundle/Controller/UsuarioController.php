<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Libs\Normalizer\ResultDecorator;
use JMS\SecurityExtraBundle\Annotation\Secure;


class UsuarioController extends BaseController
{


    /**
     * Return the overall User list.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall User List",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * @Secure(roles="ROLE_PLANIFICADOR")
     * @Rest\Get("/api/usuario/responsable")
     * @Method({"GET","OPTIONS"})
     *
     *
     */
    public function getAllAllowForVisitAction()
    {

        $result = $this->getRepo('Usuario')->findBy(array('rol' => 3));
        return new View($this->normalizeResult('Usuario',$result,ResultDecorator::USER_RESPONSIBLE_DECORATOR), Response::HTTP_OK);
    }

    /**
     * Return the overall User list.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall User List",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * @Rest\Get("/api/usuario")
     * @Method({"GET","OPTIONS"})
     *
     *
     */
    public function getAllAction()
    {

        return new View($this->getAllDataOfModel('Usuario'), Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/usuario/token")
     * @Method({"GET","OPTIONS"})
     */
    public function getUserByTokenAction(Request $request)
    {
        try {
            $token = $request->headers->get('apiKey');
            $userRepo = $this->getRepo('Usuario');
            $user = $userRepo->findOneBy(array('token' => $token));
            return new View($this->normalizeResult('Usuario', $user, ResultDecorator::DEFAULT_DECORATOR), Response::HTTP_OK);
        } catch (\Exception $e) {
            $result = $this->manageException($e);
            return new View($result, Response::HTTP_OK);
        }
    }



    /**
     * @Rest\Delete("/api/usuario/{id}")
     * @Method({"DELETE","OPTIONS"})
     * @Secure(roles="ROLE_ADMIN,ROLE_SUPER_ADMIN")
     */
    public function deleteAction($id)
    {

        return new View($this->removeModel('Usuario', $id), Response::HTTP_OK);
    }

    /**
     * Create a new User
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an object with property success to inform the result of response an data array with id of new resource create",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * @Rest\Post("/api/usuario")
     * @Method({"POST","OPTIONS"})
     * @Secure(roles="ROLE_ADMIN,ROLE_SUPER_ADMIN")
     */
    public function postAction(Request $request)
    {
        $data = $request->request->all();
        $service = $this->get('app.ldap');
        try {
            $info = $service->getInformationOfPerson($data['nombreUsuario']);
            if (is_array($info) && count($info) > 0) {
                $data['correo'] = $info[0]['mail'][0];
                $data['nombre'] = $info[0]['givenname'][0];
                $data['apellido'] = $info[0]['sn'][0];
                $data['urlFoto'] = $service->getPhoto($info[0]['postofficebox'][0]);
                return new View($this->saveModel('Usuario', $data), Response::HTTP_OK);
            } else {
                return new View($this->returnNotExistInLdapResponse(), Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            if ($e instanceof LdapException) {
                return new View($this->returnErrorLdapResponse(), Response::HTTP_OK);

            }
            return new View($this->manageException($e), Response::HTTP_OK);
        }
    }

    /**
     *
     * Update a User
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an object with property success to inform the result of response an data array with id of resource update",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * @Rest\Put("/api/usuario")
     * @Method({"PUT","OPTIONS"})
     * @Secure(roles="ROLE_ADMIN,ROLE_SUPER_ADMIN")
     */
    public function putAction(Request $request)
    {
        $data = $request->request->all();
        $service = $this->get('app.ldap');
        try {
            $user = $this->getRepo('Usuario')->find($data['id']);
            if (!$user) {
                return new View($this->returnNullResponse(), Response::HTTP_OK);
            }
            if ($user->getNombreUsuario() != $data['nombreUsuario']) {
                $info = $service->getInformationOfPerson($data['nombreUsuario']);
                if (is_array($info) && count($info) > 0) {
                    $data['correo'] = $info[0]['mail'][0];
                    $data['nombre'] = $info[0]['givenname'][0];
                    $data['apellido'] = $info[0]['sn'][0];
                    $data['urlFoto'] = $service->getPhoto($info[0]['postofficebox'][0]);
                    return new View($this->saveModel('Usuario', $data), Response::HTTP_OK);
                } else {
                    return new View($this->returnNotExistInLdapResponse(), Response::HTTP_OK);
                }
            } else {
                return new View($this->saveModel('Usuario', $data), Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            if ($e instanceof LdapException) {
                return new View($this->returnErrorLdapResponse(), Response::HTTP_OK);

            }
            return new View($this->manageException($e), Response::HTTP_OK);
        }
    }


}
