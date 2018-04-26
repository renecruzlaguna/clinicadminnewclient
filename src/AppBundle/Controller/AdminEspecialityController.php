<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class AdminEspecialityController extends BaseController
{

    public function listEspecialityAction(Request $request)
    {
        $result = $this->getRepo('Especialidad')->findAll();
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Manage:list_especiality.html.twig', array('especialities' => $result, 'message' => $message, 'error' => $error));
    }

    public function addSpecialityAction(Request $request)
    {
        $data = $request->request->all();
        $roles = $this->getRepo('Rol')->findAll();
        if (count($data) > 0) {
            $entity = $this->populateModel('Especialidad', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:add_speciality.html.twig', array('errors' => $errors, 'data' => $data, 'exception' => null, 'roles' => $roles));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_especiality_list', array('message' => 'Especialidad adicionada satisfactoriamente', 'error' => '')));

                }
                return $this->render('AppBundle:Manage:add_speciality.html.twig', array('errors' => null, 'data' => $data, 'exception' => $result['error']));
            }
        } else {
            return $this->render('AppBundle:Manage:add_speciality.html.twig', array('errors' => null, 'data' => null, 'exception' => null));
        }

    }

    public function editSpecialityAction(Request $request, $id)
    {
        $data = $request->request->all();

        if (count($data) > 0) {
            $data['id'] = $id;
            $entity = $this->populateModel('Especialidad', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:edit_speciality.html.twig', array('idSpeciality' => $id, 'errors' => $errors, 'data' => $entity, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_especiality_list', array('message' => 'Especialidad actualizada satisfactoriamente', 'error' => '')));


                }
                return $this->render('AppBundle:Manage:edit_speciality.html.twig', array('idSpeciality' => $id, 'errors' => null, 'data' => $entity, 'exception' => $result['error']));
            }
        } else {
            $data = $this->getRepo('Especialidad')->find($id);
            return $this->render('AppBundle:Manage:edit_speciality.html.twig', array('idSpeciality' => $id, 'errors' => null, 'data' => $data, 'exception' => null));
        }

    }
    public function verifyExistAction(Request $request)
    {
        $id = $request->request->get('id');
        $nombre= $request->request->get('nombre');
        if (empty($id)) {
            $id = -1;
        }
        $result = $this->getRepo('Especialidad')->verifyExistance($nombre, $id);

        return new JsonResponse(array('data' => array('exist' => $result)));

    }
    public function removeSpecialityAction($id)
    {
        try {
            $result = $this->getRepo('Especialidad')->find($id);
            $resultOperation = array('success' => false, 'error' => 'No se encontró la especialidad a eliminar');
            if ($result != null) {
                if($result->getUsuario()->count()>0){
                    return $this->redirect($this->generateUrl('manage_especiality_list', array('message' => '', 'error' => 'La especialidad '.$result->getNombre().' posee médicos asociadas a ella.')));
                }

                $resultOperation = $this->removeModel('Especialidad', $id);
            }
            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('manage_especiality_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('manage_especiality_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_especiality_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }
    }
}