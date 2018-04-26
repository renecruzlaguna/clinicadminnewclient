<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;


class AdminDoctorController extends BaseController
{
    public function indexAction()
    {
        return $this->render('AppBundle:Manage:index.html.twig', array());
    }

    public function listDoctorAction(Request $request)
    {
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        $result = $this->getRepo('Usuario')->findBy(array('rol' => 2));
        return $this->render('AppBundle:Manage:list_doctor_user.html.twig', array('users' => $result, 'message' => $message, 'error' => $error));
    }

    public function addDoctorAction(Request $request)
    {
        $data = $request->request->all();

        $speciality = $this->getRepo('Especialidad')->findAll();
        if (count($data) > 0) {
            $data['rol'] = 2;
            $data['activo'] = 1;
            $data['edad'] = 30;
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:add_doctor_user.html.twig', array('speciality' => $speciality, 'errors' => $errors, 'data' => $data, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_doctor_user_list', array('message' => 'Médico adicionado satisfactoriamente', 'error' => '')));

                }
                return $this->render('AppBundle:Manage:add_doctor_user.html.twig',
                    array('errors' => null, 'data' => $data, 'exception' => $result['error'], 'speciality' => $speciality));
            }
        } else {
            return $this->render('AppBundle:Manage:add_doctor_user.html.twig',
                array('errors' => null, 'data' => null, 'exception' => null, 'speciality' => $speciality));
        }

    }

    public function editDoctorAction(Request $request, $id)
    {
        $data = $request->request->all();
        $speciality = $this->getRepo('Especialidad')->findAll();

        if (count($data) > 0) {
            $data['rol'] = 2;
            $data['id'] = $id;
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:edit_doctor_user.html.twig', array('speciality' => $speciality, 'id' => $id, 'errors' => $errors, 'data' => $entity, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_doctor_user_list', array('message' => 'Médico actualizado satisfactoriamente', 'error' => '')));
                }
                return $this->render('AppBundle:Manage:edit_doctor_user.html.twig', array('speciality' => $speciality, 'id' => $id, 'errors' => null, 'data' => $entity, 'exception' => $result['error']));
            }
        } else {
            $data = $this->getRepo('Usuario')->find($id);
            return $this->render('AppBundle:Manage:edit_doctor_user.html.twig', array('speciality' => $speciality, 'id' => $id, 'errors' => null, 'data' => $data, 'exception' => null));
        }

    }

    public function removeDoctorAction($id)
    {
        try {
            $result = $this->getRepo('Usuario')->find($id);
            $resultOperation = array('success' => false, 'error' => 'No se encontró el médico a eliminar');
            if ($result != null) {
                if ($result->getConsulta()->count() > 0) {
                    return $this->redirect($this->generateUrl('manage_doctor_user_list', array('message' => '', 'error' => 'El médico a eliminar posee consultas asociadas a el.')));
                }

                $resultOperation = $this->removeModel('Usuario', $id);
            }
            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('manage_doctor_user_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('manage_doctor_user_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_doctor_user_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }
    }


    public function showTemplateAction()
    {

        return $this->render('AppBundle:Manage:template.html.twig', array());
    }
}