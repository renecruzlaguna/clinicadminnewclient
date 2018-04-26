<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;


class AdminClientController extends BaseController
{

    public function listClientAction(Request $request)
    {
        $result = $this->getRepo('Usuario')->findBy(array('rol' => 1));
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Manage:list_client_user.html.twig', array('users' => $result, 'message' => $message, 'error' => $error));
    }

    public function viewReserveAction(Request $request, $id)
    {

        $result = $this->getRepo('Consulta')->findBy(array('usuarioRegistro' => $id), array('estado' => 'ASC'));
        $client = $this->getRepo('Usuario')->find($id);
        $status = $this->getRepo('Estado')->findAll();
        $message = $request->query->get('message');
        $error = $request->query->get('error');


        return $this->render('AppBundle:Manage:list_reserved_by_client.html.twig', array('especialities' => $result, 'client' => $client));
    }


    public function addImageAction(Request $request, $id)
    {
        $client = $this->getRepo('Usuario')->find($id);
        if (!$client) {
            return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => '', 'error' => 'No existe el cliente')));
        }

        $images = $request->files->all();
        $data = $request->request->all();

        if ($images && array_key_exists('image', $images) && $data && array_key_exists("description", $data)) {
            $uploadDirectory = $this->container->getParameter('kernel.root_dir') . '/../web/bundles/app/images/galery/' . $id;
            $fs = new Filesystem();
            $exist = $fs->exists($uploadDirectory);
            if (!$exist) {
                $fs->mkdir($uploadDirectory);
            }
            $repoUser = $this->getRepo('Usuario');
            $repoUser->beginTransaction();

            $imagesToSave = $images['image'];
            $descriptionToSave = $data['description'];
            $saveFile = array();
            try {
                $i = 0;
                foreach ($imagesToSave as $image) {


                    $FileType = $image->getMimeType();
                    $isMIME = array_search($FileType, array(
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'jpg' => 'image/jpg',
                    ), true);
                    if ($isMIME) {
                        ;
                        $name = $image->getClientOriginalName();
                        $info = pathinfo($name);
                        $extension = $info['extension'];
                        $name = uniqid() . '.' . $extension;
                        $date = new \DateTime('now');
                        $date = $date->format('Y-m-d H:i:s');


                        $description = (@$descriptionToSave[$i]) ? (@$descriptionToSave[$i]) : 'Sin descripción';
                        $dataSave = array('usuario' => $id, 'nombre' => $name, 'descripcion' => $description, 'fecha' => $date);
                        $saveResult = $this->saveModel('Imagen', $dataSave, array(), false);

                        if ($saveResult['success']) {
                            $image->move($uploadDirectory, $name);
                            $saveFile[] = $name;

                        } else {

                            throw new \Exception($saveResult['error']);
                        }
                    } else {
                        throw new \Exception("", 12345);
                    }
                    $i++;
                }
                $repoUser->commit();
                return $this->redirect($this->generateUrl('manage_client_view_galery', array('id' => $id, 'message' => 'Operación realizada satisfactoriamente', 'error' => '')));


            } catch (\Exception $e) {
                foreach ($saveFile as $file) {
                    try {
                        $fs->remove($uploadDirectory . '/' . $file);
                    } catch (\Exception $e) {
                    }
                }
                try {
                    $repoUser->rollback();
                } catch (\Exception $e) {


                }

                //  return $this->redirect($this->generateUrl('manage_client_view_galery', array('id'=>$id,'message' => '', 'error' => (($e->getCode() == 12345) ? 'Tipo de fichero no permitido' : 'Ocurrió un error en el servidor'))));
                return $this->redirect($this->generateUrl('manage_client_view_galery', array('id' => $id, 'message' => '', 'error' => $e->getMessage())));
            }

        }


        return $this->render('AppBundle:Manage:add_image.html.twig', array('client' => $client, 'message' => '', 'error' => ''));
    }

    public function viewGaleryAction(Request $request, $id)
    {
        $client = $this->getRepo('Usuario')->find($id);
        if (!$client) {
            return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => '', 'error' => 'No existe el cliente')));
        }
        $repoImage = $this->getRepo('Imagen');
        $fs = new Filesystem();
        $rute = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/images/galery/' . $id;
        $exist = $fs->exists($rute);
        $show = false;
        $files = false;
        if ($exist) {
            $filesRegister = $repoImage->findBy(array('usuario' => $id), array('fecha' => 'DESC'));

            foreach ($filesRegister as &$file) {

                if ($fs->exists($rute . '/' . $file->getNombre())) {
                    $dev[] = $file;
                }


            }

            $files = $dev;
            $show = (count($files) > 0);

        }


        return $this->render('AppBundle:Manage:list_galery_by_client.html.twig', array('client' => $client, 'show' => $show, 'files' => $files));
    }

    public function removeImagesAction(Request $request)
    {

        if ($request->request->all()) {

            $ids = ($request->request->get('ids'));

            $imageRepo = $this->getRepo('Imagen');
            $fs = new Filesystem();
            $idClient = null;
            foreach ($ids as $id) {

                $image = $imageRepo->find($id);
                if ($image) {

                    $name = $image->getNombre();
                    $idClient = $image->getUsuario()->getId();
                    $rute = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/images/galery/' . $idClient . '/' . $name;
                    $result = $this->removeEntityModel('Imagen', $image);
                    if ($result['success']) {
                        try {

                            $fs->remove($rute);
                        } catch (\Exception $e) {

                        }
                    }
                }

            }

            $existSome = $imageRepo->findBy(array('usuario' => $idClient));
            if (count($existSome) == 0) {

                $rute = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/images/galery/' . $idClient;
                try {

                    $fs->remove($rute);
                } catch (\Exception $e) {

                }
            }
            return new JsonResponse(array('success' => true));
        }

    }

    public function addClientAction(Request $request)
    {
        $data = $request->request->all();

        if (count($data) > 0) {
            $data['rol'] = 1;
            $data['especialidad'] = -1;
            $data['salario'] = 0;
            $data['activo'] = 1;
            $data['numeroHistoria'] = $this->numberClinicHistory(false,true,false,'',10);
            if (empty($data['correo'])) {
                $data['correo'] = $this->getParameter('default_email_address');
            }
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:add_client_user.html.twig', array('errors' => $errors, 'data' => $data, 'exception' => null));
            } else {
                $password = $data['password'];
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    $managerEmail = $this->get('manager.email');
                    $url = $request->getSchemeAndHttpHost();
                    $url .= '/manage/login';
                    $managerEmail->sendMessageCreateUser($entity, $password, $url);

                    return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => 'Paciente adicionado satisfactoriamente', 'error' => '')));
                }
                return $this->render('AppBundle:Manage:add_client_user.html.twig',
                    array('errors' => null, 'data' => $data, 'exception' => $result['error']));
            }
        } else {
            return $this->render('AppBundle:Manage:add_client_user.html.twig',
                array('errors' => null, 'data' => null, 'exception' => null));
        }

    }

    public function editClientAction(Request $request, $id)
    {
        $data = $request->request->all();

        if (count($data) > 0) {

            $data['id'] = $id;
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);
            $data['rol'] = 1;
            $data['especialidad'] = -1;
            if (empty($data['correo'])) {
                $data['correo'] = $this->getParameter('default_email_address');
            }
            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:edit_client_user.html.twig', array('id' => $id, 'errors' => $errors, 'data' => $entity, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => 'Paciente actualizado satisfactoriamente', 'error' => '')));
                }
                return $this->render('AppBundle:Manage:edit_client_user.html.twig', array('id' => $id, 'errors' => null, 'data' => $entity, 'exception' => $result['error']));
            }
        } else {
            $data = $this->getRepo('Usuario')->find($id);
            return $this->render('AppBundle:Manage:edit_client_user.html.twig', array('id' => $id, 'errors' => null, 'data' => $data, 'exception' => null));
        }

    }

    public function removeClientAction($id)
    {
        try {
            $result = $this->getRepo('Usuario')->find($id);
            $resultOperation = array('success' => false, 'error' => 'No se encontró el cliente a eliminar');
            if ($result != null) {

                if (count($this->getRepo('Consulta')->findBy(array('usuarioRegistro' => $id))) > 0)
                    return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => '', 'error' => 'El paciente posee consultas asociadas')));
                $resultOperation = $this->removeModel('Usuario', $id);
            }
            if ($resultOperation['success']) {
                try {
                    $fs = new Filesystem();
                    $rute = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/images/galery/' . $id;
                    $fs->remove($rute);
                } catch (\Exception $e) {

                }
                return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_client_user_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }
    }


    public function showTemplateAction()
    {

        return $this->render('AppBundle:Manage:template.html.twig', array());
    }

    private function numberClinicHistory($alpha = true, $nums = true, $usetime = false, $string = '', $length = 120)
    {
        $alpha = ($alpha == true) ? 'abcdefghijklmnopqrstuvwxyz' : '';
        $nums = ($nums == true) ? '1234567890' : '';

        if ($alpha == true || $nums == true || !empty($string)) {
            if ($alpha == true) {
                $alpha = $alpha;
                $alpha .= strtoupper($alpha);
            }
        }
        $randomstring = '';
        $totallength = $length;
        for ($na = 0; $na < $totallength; $na++) {
            $var = (bool)rand(0, 1);
            if ($var == 1 && $alpha == true) {
                $randomstring .= $alpha[(rand() % mb_strlen($alpha))];
            } else {
                $randomstring .= $nums[(rand() % mb_strlen($nums))];
            }
        }
        if ($usetime == true) {
            $randomstring = $randomstring . time();
        }
        return ($randomstring);
    }
}