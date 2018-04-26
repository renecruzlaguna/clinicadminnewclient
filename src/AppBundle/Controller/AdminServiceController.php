<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class AdminServiceController extends BaseController
{

    public function listServiceAction(Request $request)
    {
        $result = $this->getRepo('Servicio')->findAll();
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Manage:list_service.html.twig', array('services' => $result, 'message' => $message, 'error' => $error));
    }

    public function addServiceAction(Request $request)
    {
        $data = $request->request->all();
        $roles = $this->getRepo('Rol')->findAll();
        if (count($data) > 0) {
            $entity = $this->populateModel('Servicio', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:add_service.html.twig', array('errors' => $errors, 'data' => $data, 'exception' => null, 'roles' => $roles));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_service_list', array('message' => 'Servicio adicionado satisfactoriamente', 'error' => '')));

                }
                return $this->render('AppBundle:Manage:add_service.html.twig', array('errors' => null, 'data' => $data, 'exception' => $result['error']));
            }
        } else {
            return $this->render('AppBundle:Manage:add_service.html.twig', array('errors' => null, 'data' => null, 'exception' => null));
        }

    }

    public function editServiceAction(Request $request, $id)
    {
        $data = $request->request->all();

        if (count($data) > 0) {
            $data['id'] = $id;
            $entity = $this->populateModel('Servicio', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:edit_service.html.twig', array('idService' => $id, 'errors' => $errors, 'data' => $entity, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_service_list', array('message' => 'Servicio actualizado satisfactoriamente', 'error' => '')));


                }
                return $this->render('AppBundle:Manage:edit_service.html.twig', array('idService' => $id, 'errors' => null, 'data' => $entity, 'exception' => $result['error']));
            }
        } else {
            $data = $this->getRepo('Servicio')->find($id);
            return $this->render('AppBundle:Manage:edit_service.html.twig', array('idService' => $id, 'errors' => null, 'data' => $data, 'exception' => null));
        }

    }
    public function verifyExistAction(Request $request)
    {
        $id = $request->request->get('id');
        $nombre= $request->request->get('nombre');
        if (empty($id)) {
            $id = -1;
        }
        $result = $this->getRepo('Servicio')->verifyExistance($nombre, $id);

        return new JsonResponse(array('data' => array('exist' => $result)));

    }
    public function removeServiceAction($id)
    {
        try {
            $result = $this->getRepo('Servicio')->find($id);
            $resultOperation = array('success' => false, 'error' => 'No se encontró el servicio a eliminar');
            if ($result != null) {
                if($result->getFacturaserv()->count()>0){
                    return $this->redirect($this->generateUrl('manage_service_list', array('message' => '', 'error' => 'El servicio '.$result->getNombre().' posee facturas asociadas.')));
                }

                $resultOperation = $this->removeModel('Servicio', $id);
            }
            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('manage_service_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('manage_service_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_service_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }
    }
}