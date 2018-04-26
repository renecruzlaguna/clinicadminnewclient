<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;


class PerfilController extends BaseController
{



    public function editClientAction(Request $request, $id)
    {
        $data = $request->request->all();

        if (count($data) > 0) {
            $user = $this->getRepo('Usuario')->find($id);
            if(!$user){
                return $this->redirect($this->generateUrl('manage', array('error' => 'Usuario no encontrado', 'message' => '')));
            }
            if($user->getRol()->getId()!=1){
                return $this->redirect($this->generateUrl('manage', array('error' => 'El usuario no posee rol de cliente', 'message' => '')));
            }
            $data['id'] = $id;
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);
            $data['rol'] = 1;
            $data['especialidad'] = -1;
            if (count($errors) > 0) {

                return $this->render('AppBundle:Client:edit_client_user.html.twig', array('id' => $id, 'errors' => $errors, 'data' => $entity, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage', array('message' => 'Paciente actualizado satisfactoriamente', 'error' => '')));
                }
                return $this->render('AppBundle:Client:edit_client_user.html.twig', array('id' => $id, 'errors' => null, 'data' => $entity, 'exception' => $result['error']));
            }
        } else {
            $data = $this->getRepo('Usuario')->find($id);
            if(!$data){
                return $this->redirect($this->generateUrl('manage', array('error' => 'Usuario no encontrado', 'message' => '')));
            }
            return $this->render('AppBundle:Client:edit_client_user.html.twig', array('id' => $id, 'errors' => null, 'data' => $data, 'exception' => null));
        }

    }


    public function editUserAction(Request $request, $id)
    {
        $data = $request->request->all();

        if (count($data) > 0) {
            $user = $this->getRepo('Usuario')->find($id);
              if(!$user){
                  return $this->redirect($this->generateUrl('manage', array('error' => 'Usuario no encontrado', 'message' => '')));
              }
            if($user->getRol()->getId()<3){
                return $this->redirect($this->generateUrl('manage', array('error' => 'El usuario no posee rol de administrador', 'message' => '')));
            }
            $data['id'] = $id;
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:edit_user_perfil.html.twig', array('id' => $id, 'errors' => $errors, 'data' => $entity, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage', array('message' => 'Usuario actualizado satisfactoriamente', 'error' => '')));

                }
                return $this->render('AppBundle:Manage:edit_user_perfil.html.twig', array('id' => $id, 'errors' => null, 'data' => $entity, 'exception' => $result['error']));
            }
        } else {
            $data = $this->getRepo('Usuario')->find($id);
            if(!$data){
                return $this->redirect($this->generateUrl('manage', array('error' => 'Usuario no encontrado', 'message' => '')));
            }
            return $this->render('AppBundle:Manage:edit_user_perfil.html.twig', array('id' => $id, 'errors' => null, 'data' => $data, 'exception' => null));
        }

    }
}