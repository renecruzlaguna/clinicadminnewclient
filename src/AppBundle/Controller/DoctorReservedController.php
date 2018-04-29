<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;


class DoctorReservedController extends BaseController
{


    public function updateQueryAction(Request $request, $id)
    {

        $speciality = $this->getRepo('Especialidad')->findAll();

        $users = $this->getRepo('Usuario')->findBy(array('rol' => 1));
        $query = $this->getRepo('Consulta')->find($id);
        $doctors = $this->getRepo('Usuario')->findBy(array('rol' => 2, 'especialidad' => $query->getEspecialidad()->getId()));
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Doctor:update_query.html.twig',
            array('message' => $message, 'error' => $error, 'doctors' => $doctors, 'query' => $query, 'users' => $users, 'errors' => null, 'data' => null, 'exception' => null, 'speciality' => $speciality));
    }

    public function saveUpdateQueryAction(Request $request, $id)
    {
        try {
            $data = $request->request->all();
            $save = array();
            $save['id'] = $id;
            $save['horaInicial'] = $data['horaInicial'];
            $save['horaFinal'] = $data['horaFinal'];
            $save['minutoInicial'] = $data['minutoInicial'];
            $save['minutoFinal'] = $data['minutoFinal'];
            /*
                        $find = $this->getRepo('Consulta')->findQueryHour($id, $save['horaInicial'], $save['horaFinal'], $save['minutoInicial'], $save['minutoFinal'], $this->getUser()->getId(), $data['dia'], $data['mes'], $data['anno']);
                        if ($find) {
                            $initialHourQuery = $find->getHoraInicialC();
                            $endHourQuery = $find->getHoraFinalC();

                            $initialMinuteQuery = $query = $find->getMinutoInicialC();
                            $endMinuteQuery = $query = $find->getMinutoFinalC();
                            $range = $initialHourQuery . ':' . $initialMinuteQuery . ' - ' . $endHourQuery . ':' . $endMinuteQuery;
                            return $this->redirect($this->generateUrl('doctor_query_update', array('message' => '', 'error' => 'Ya existe una consulta para el rango de tiempo:'
                                . $range)));
                        }*/
            $entity = $this->populateModel('Consulta', $save);
            $entity->updateCompleteDate();
            $result = $this->saveEntity($entity);
            if ($result === true) {
                $managerEmail = $this->get('manager.email');
                $managerEmail->setReserve($entity);
                $managerEmail->sendMessage(4);
                return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => 'Consulta actualizada satisfactoriamente')));
            } else {
                return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('error' => $result['error'])));
            }


        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('error' => 'Ocurrió un error en el servidor')));
        }


    }

    public function checkFreeRangeAction($id, $initialHour, $endHour, $initialMinute, $endMinute, $idUser, $day, $month, $year)
    {
        try {
            $find = $this->getRepo('Consulta')->checkQueryHour($id, $initialHour, $endHour, $initialMinute, $endMinute, $idUser, $day, $month, $year);
            if (!$find) {
                return new JsonResponse(array('success' => true, 'data' => array('message' => '')));
            } else {
                $initialHourQuery = $find->getHoraInicialC();
                $endHourQuery = $find->getHoraFinalC();

                $initialMinuteQuery = $query = $find->getMinutoInicialC();
                $endMinuteQuery = $query = $find->getMinutoFinalC();
                $range = $initialHourQuery . ':' . $initialMinuteQuery . ' - ' . $endHourQuery . ':' . $endMinuteQuery;
                return new JsonResponse(array('success' => false, 'error' => 'Ya existe una consulta para el rango de tiempo: ' . $range));
            }
        } catch (\Exception $e) {

            return new JsonResponse(array('success' => false, 'error' => 'No se pudo comprobar la disponiblidad del horario seleccionado'));
        }


    }

    public function listMyQueryAction(Request $request, $finicio = -1, $ffin = -1, $estado = -1)
    {

        $user = $this->getUser()->getId();
        $result = $this->getRepo('Consulta')->getAllQueryByStateAndDoctor($estado, $finicio, $ffin, $user);


        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Doctor:list_reserved.html.twig',
            array('finicio' => $finicio, 'ffin' => $ffin, 'estado' => $estado, 'errors' => null, 'data' => null, 'exception' => null, 'especialities' => $result, 'message' => $message, 'error' => $error));
    }


    public function addResultQueryAction(Request $request, $id)
    {
        $data = $request->request->all();
        $query = $this->getRepo('Consulta')->find($id);
        if (!$query) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                array('message' => '', 'error' => 'No se encontró la consulta')));
        }
        if ($query->getUsuario()->getId() != $this->getUser()->getId()) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                array('message' => '', 'error' => 'No tiene permiso para realizar esta acción')));
        }
        $repoEv = $this->getRepo('Evolucion');
        $evolutions = $repoEv->getAllEvolutionsByPattient($query);


        if (count($data) > 0) {
            $history = $this->getRepo('ResultadoConsulta')->findOneBy(array('cliente' => $query->getUsuarioRegistro()));
            if ($history) {
                $data['id'] = $history->getId();
            }

            try {
                $repoEv->beginTransaction();

                $saveResult = $this->saveModel('ResultadoConsulta', $data, array(), false);
                if ($saveResult['success'] == true) {
                    $saveResultEv = $this->saveModel('Evolucion', array('consulta' => $data['consulta'], 'observaciones' => $data['observaciones']), array(), false);
                    if ($saveResultEv['success'] == true) {
                        $repoEv->commit();
                        return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => 'Historia clínica registrada satisfactoriamente', 'error' => '')));
                    } else {
                        $repoEv->rollback();
                        return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                            array('message' => '', 'error' => $saveResultEv['error'])));
                    }
                } else {
                    $repoEv->rollback();
                    return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                        array('message' => '', 'error' => $saveResult['error'])));
                }
            } catch (\Exception $e) {
                $repoEv->rollback();
                return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                    array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
            }


        } else {

            return $this->render('AppBundle:Doctor:add_result_query.html.twig',
                array('errors' => null, 'evolutions' => $evolutions, 'data' => null, 'query' => $query, 'exception' => null));
        }

    }

    public function viewEvolutionsAction(Request $request, $queryId)
    {
        $query = $this->getRepo('Consulta')->find($queryId);
        if (!$query) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                array('message' => '', 'error' => 'No se encontró la consulta')));
        }
        if ($query->getUsuario()->getId() != $this->getUser()->getId()) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                array('message' => '', 'error' => 'No tiene permiso para realizar esta acción')));
        }
        $repoEv = $this->getRepo('Evolucion');
        $idEvol = null;
        if ($query->getEvolucion() != null) {
            $idEvol = $query->getEvolucion()->getId();
        }
        $evolutions = $repoEv->getAllEvolutionsByPattient($query, $idEvol);

        return $this->render('AppBundle:Default:view_evolutionquery.html.twig',
            array('errors' => null, 'evolutions' => $evolutions, 'query' => $query, 'data' => null, 'exception' => null));


    }

    public function editResultQueryAction(Request $request, $id, $queryId)
    {
        $query = $this->getRepo('Consulta')->find($queryId);
        if (!$query) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                array('message' => '', 'error' => 'No se encontró la consulta')));
        }
        if ($query->getUsuario()->getId() != $this->getUser()->getId()) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                array('message' => '', 'error' => 'No tiene permiso para realizar esta acción')));
        }
        $repoEv = $this->getRepo('Evolucion');
        $idEvol = null;
        if ($query->getEvolucion() != null) {
            $idEvol = $query->getEvolucion()->getId();
        }
        $evolutions = $repoEv->getAllEvolutionsByPattient($query, $idEvol);
        $data = $request->request->all();
        $result = $this->getRepo('ResultadoConsulta')->find($id);
        if (!$result) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                array('message' => '', 'error' => 'No se encontró la historia clínica')));
        }
        if (count($data) > 0) {

            $data['id'] = $id;
            try {
                $repoEv->beginTransaction();

                $saveResult = $this->saveModel('ResultadoConsulta', $data, array(), false);
                if ($saveResult['success'] == true) {
                    $dataEvol = array('observaciones' => $data['observaciones'], 'consulta' => $queryId);
                    if ($idEvol != null) {
                        $dataEvol['id'] = $idEvol;
                    }
                    $saveResultEv = $this->saveModel('Evolucion', $dataEvol, array(), false);
                    if ($saveResultEv['success'] == true) {
                        $repoEv->commit();
                        return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => 'Historia actualizada satisfactoriamente', 'error' => '')));
                    } else {
                        $repoEv->rollback();
                        return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                            array('message' => '', 'error' => $saveResultEv['error'])));
                    }
                } else {
                    $repoEv->rollback();
                    return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                        array('message' => '', 'error' => $saveResult['error'])));
                }
            } catch (\Exception $e) {
                $repoEv->rollback();
                return $this->redirect($this->generateUrl('doctor_query_reserved_list',
                    array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
            }


        } else {
            return $this->render('AppBundle:Doctor:edit_result_query.html.twig',
                array('errors' => null, 'evolutions' => $evolutions, 'query' => $query, 'data' => null, 'result' => $result, 'exception' => null));
        }

    }


    public function removeResultQueryAction($id)
    {
        try {
            $resultOperation = array('success' => false, 'error' => 'No se encontró el resultado a eliminar');
            $result = $this->getRepo('ResultadoConsulta')->find($id);
            if ($result != null && $this->getUser() && $result->getConsulta()->getUsuario()->getId() == $this->getUser()->getId()) {
                if ($result->getConsulta()->getEstado()->getId() == 4) {
                    $resultOperation = $this->removeModel('ResultadoConsulta', $id);
                } else {
                    $resultOperation = array('success' => false, 'error' => 'La consulta se encuentra en estado: ' . $result->getConsulta()->getEstado()->getNombre());
                    return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => '', 'error' => $resultOperation['error'])));
                }


            }

            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }


    }


    public function addImageAction(Request $request, $id)
    {
        $client = $this->getRepo('Usuario')->find($id);
        if (!$client) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => '', 'error' => 'No existe el cliente')));
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
                return $this->redirect($this->generateUrl('doctor_client_view_galery', array('id' => $id, 'message' => 'Operación realizada satisfactoriamente', 'error' => '')));


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
                return $this->redirect($this->generateUrl('doctor_client_view_galery', array('id' => $id, 'message' => '', 'error' => $e->getMessage())));
            }

        }


        return $this->render('AppBundle:Doctor:add_image.html.twig', array('client' => $client, 'message' => '', 'error' => ''));
    }

    public function viewGaleryAction(Request $request, $id)
    {
        $client = $this->getRepo('Usuario')->find($id);
        if (!$client) {
            return $this->redirect($this->generateUrl('doctor_query_reserved_list', array('message' => '', 'error' => 'No existe el cliente')));
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


        return $this->render('AppBundle:Doctor:list_galery_by_client.html.twig', array('client' => $client, 'show' => $show, 'files' => $files));
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

}