<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use AppBundle\Libs\Controller\PrototypeDecorator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class AdminReservedController extends BaseController
{

    /*Reservarle una consulta a un cliente*/

    public function registerQueryClientAction(Request $request)
    {

        $speciality = $this->getRepo('Especialidad')->findAll();

        $users = $this->getRepo('Usuario')->findBy(array('rol' => 1));
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Manage:add_query.html.twig',
            array('message' => $message, 'error' => $error, 'users' => $users, 'errors' => null, 'data' => null, 'exception' => null, 'speciality' => $speciality));
    }

    public function updateQueryClientAction(Request $request, $id)
    {

        $speciality = $this->getRepo('Especialidad')->findAll();

        $users = $this->getRepo('Usuario')->findBy(array('rol' => 1));
        $query = $this->getRepo('Consulta')->find($id);
        $doctors = $this->getRepo('Usuario')->findBy(array('rol' => 2, 'especialidad' => $query->getEspecialidad()->getId()));
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Manage:update_query.html.twig',
            array('message' => $message, 'error' => $error, 'doctors' => $doctors, 'query' => $query, 'users' => $users, 'errors' => null, 'data' => null, 'exception' => null, 'speciality' => $speciality));
    }

    public function saveUpdateReserveAdminAction(Request $request, $id)
    {
        try {
            $data = $request->request->all();


           
            $data['id'] = $id;
            /*
            $find = $this->getRepo('Consulta')->findQueryHour($id, $data['horaInicial'], $data['horaFinal'], $data['minutoInicial'], $data['minutoFinal'], $data['usuario'],$data['dia'],$data['mes'],$data['anno']);

            if ($find) {
                $initialHourQuery = $find->getHoraInicialC();
                $endHourQuery = $find->getHoraFinalC();

                $initialMinuteQuery = $query = $find->getMinutoInicialC();
                $endMinuteQuery = $query = $find->getMinutoFinalC();
                $range=$initialHourQuery.':'.$initialMinuteQuery.' - '.$endHourQuery.':'.$endMinuteQuery;
                return $this->redirect($this->generateUrl('manage_query_admin_add', array('message' => '', 'error' => 'Ya existe una consulta para el rango de tiempo:'
                    .$range)));
            }*/
            $entity = $this->populateModel('Consulta', $data);
            $entity->updateCompleteDate();
            $result = $this->saveEntity($entity);
            if ($result === true) {
                $managerEmail = $this->get('manager.email');
                $managerEmail->setReserve($entity);
                $managerEmail->sendMessage(4);
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => 'Consulta actualizada satisfactoriamente')));
            } else {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('error' => $result['error'])));
            }


        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('error' => 'Ocurrió un error en el servidor')));
        }


    }

    public function saveReserveAdminAction(Request $request)
    {
        try {
            $data = $request->request->all();


            $data['estado'] = 2;
            
            /*
             $find = $this->getRepo('Consulta')->findQueryHour(-1, $data['horaInicial'], $data['horaFinal'], $data['minutoInicial'], $data['minutoFinal'], $data['usuario'],$data['dia'],$data['mes'],$data['anno']);
             if ($find) {
                 $initialHourQuery = $find->getHoraInicialC();
                 $endHourQuery = $find->getHoraFinalC();

                 $initialMinuteQuery = $query = $find->getMinutoInicialC();
                 $endMinuteQuery = $query = $find->getMinutoFinalC();
                 $range=$initialHourQuery.':'.$initialMinuteQuery.' - '.$endHourQuery.':'.$endMinuteQuery;
                 return $this->redirect($this->generateUrl('manage_query_admin_update', array('message' => '', 'error' => 'Ya existe una consulta para el rango de tiempo:'
                     .$range)));
             }*/
            $entity = $this->populateModel('Consulta', $data);
            $entity->updateCompleteDate();
            $result = $this->saveEntity($entity);
            if ($result === true) {
                $managerEmail = $this->get('manager.email');
                $managerEmail->setReserve($entity);
                $managerEmail->sendMessage(1);
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => 'Consulta adicionada satisfactoriamente')));
            } else {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('error' => $result['error'])));
            }


        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('error' => 'Ocurrió un error en el servidor')));
        }


    }

    public function listReservedAction(Request $request, $finicio = -1, $ffin = -1, $estado = -1)
    {

        $result = $this->getRepo('Consulta')->getAllQueryByStateAndDoctor($estado, $finicio, $ffin);
        $status = $this->getRepo('Estado')->findAll();
        $message = $request->query->get('message');
        $error = $request->query->get('error');


        return $this->render('AppBundle:Manage:list_reserved.html.twig', array('finicio' => $finicio, 'ffin' => $ffin, 'estado' => $estado, 'message' => $message, 'error' => $error, 'especialities' => $result, 'statusList' => $status));
    }

    public function removeReserveAction($id)
    {
        try {


            $result = $this->getRepo('Consulta')->find($id);
            $resultOperation = array('success' => false, 'error' => 'No se encontró la consulta a eliminar');
            if ($result != null && $this->getUser() && ($this->getUser()->getRol()->getId() == 3 || $this->getUser()->getRol()->getId() == 4)) {
                if ($result->getEstado()->getId() != 2 && $result->getEstado()->getId() != 4) {
                    $resultOperation = $this->removeModel('Consulta', $id);
                } else {
                    $resultOperation = array('success' => false, 'error' => 'La consulta se encuentra en estado: ' . $result->getEstado()->getNombre());
                    return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));
                }
            }
            if (!$this->getUser()) {
                $resultOperation = array('success' => false, 'error' => 'No tiene permiso para eliminar la consulta seleccionada');
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));

            } else if ($this->getUser()->getRol()->getId() < 3) {
                $resultOperation = array('success' => false, 'error' => 'No tiene permiso para eliminar la consulta seleccionada');
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));

            }
            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }

    }

    public function updateReserveStatusAction(Request $request, $id, $status = 2)
    {

        $data = $request->request->all();
        $data['estado'] = $status;
        $data['id'] = $id;
        if ($status > 1 && $status < 5) {

            $entity = $this->populateModel('Consulta', $data);
            $result = $this->saveEntity($entity);
            if ($result === true) {
                /*si se acepta se envia el correo de aceptada*/
                if ($status == 2 || $status == 3) {
                    $managerEmail = $this->get('manager.email');
                    $managerEmail->setReserve($entity);
                    if ($status == 2)
                        $managerEmail->sendMessage(2);
                    if ($status == 3){
                      $managerEmail->sendMessage(5); 
                       $this->removeModel('Consulta', $id);
                    }
                        
                }

                return new JsonResponse(array('success' => true));

            } else {
                return new JsonResponse($result);

            }
        } else {
            return new JsonResponse(array('success' => false, 'error' => 'El estado debe ser con id entre 1 y 4'));

        }


    }


    public function addCheckQueryAction(Request $request, $id)
    {
        $data = $request->request->all();
        $queryRepo = $this->getRepo('Consulta');
        $query = $queryRepo->find($id);

        $services = $this->getRepo('Servicio')->findAll();

        if (count($data) > 0) {
            try {
                $queryRepo->beginTransaction();
                $resultSave = $this->saveModel('FacturaConsulta', $data, array(), false);

                if ($resultSave['success'] === true) {
                    $services = $data['servicio'];

                    $countServices = $data['servicesvalues'];
                    foreach ($services as $key => $service) {
                        $dataSave = array('factura' => $resultSave['data']['id'], 'servicio' => $service, 'cantidad' => $countServices[$key]);

                        $resultSaveService = $this->saveModel('FacturaServicio', $dataSave, array(), false);
                        if ($resultSaveService['success'] === false) {

                            $queryRepo->rollback();
                            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultSaveService['error'])));

                        }

                    }
                    $queryRepo->commit();

                } else {
                    return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultSave['error'])));

                }

            } catch (\Exception $e) {

                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'Error en base de datos.')));

            }

            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => 'Factura registrada satisfactoriamente', 'error' => '')));


        } else {
            $numberSave = $this->getRepo('NumeroFactura')->find(1)->getNumero();
            if (strlen($numberSave) < 7) {
                $numberSave = str_pad($numberSave, 7, "0", STR_PAD_LEFT);
            }
            $this->saveModel('NumeroFactura', array('id' => 1, 'numero' => $numberSave + 1));
            return $this->render('AppBundle:Manage:add_check.html.twig',
                array('number_generate' => $numberSave, 'errors' => null, 'data' => null, 'services' => $services, 'query' => $query, 'exception' => null));
        }

    }

    public function editCheckQueryAction(Request $request, $id)
    {
        $data = $request->request->all();
        $queryRepo = $this->getRepo('FacturaConsulta');
        $check = $queryRepo->find($id);
        $services = $this->getRepo('Servicio')->findAll();
        $servicesCheck = array();
        $serv = $check->getFacturaserv()->toArray();
        foreach ($serv as $s) {
            $servicesCheck[] = $s->getServicio();
        }


        if (count($data) > 0) {

            $data['id'] = $id;

            $entity = $this->populateModel('FacturaConsulta', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:edit_check.html.twig', array('services' => $services, 'errors' => $errors, 'factura' => $check, 'data' => $data, 'exception' => null, 'servicesCheck' => $servicesCheck));
            } else {
                try {
                    $queryRepo->beginTransaction();
                    $resultSave = $this->saveModel('FacturaConsulta', $data, array(), false);

                    if ($resultSave['success'] === true) {

                        $removingacturaService = $check->getFacturaserv()->toArray();
                        foreach ($removingacturaService as $remov) {
                            $res = $this->removeModel('FacturaServicio', $remov->getId(), array(), array(), false);
                            if ($res['success'] == false) {
                                $queryRepo->rollback();
                                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $res['error'])));
                            }

                        }

                        $services = $data['servicio'];

                        $countServices = $data['servicesvalues'];
                        foreach ($services as $key => $service) {
                            $dataSave = array('factura' => $resultSave['data']['id'], 'servicio' => $service, 'cantidad' => $countServices[$key]);

                            $resultSaveService = $this->saveModel('FacturaServicio', $dataSave, array(), false);
                            if ($resultSaveService['success'] === false) {

                                $queryRepo->rollback();
                            }
                        }
                        $queryRepo->commit();
                        return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => 'Factura actualizada satisfactoriamente', 'error' => '')));
                    } else {
                        $queryRepo->rollback();
                        return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultSave['error'])));

                    }
                } catch (\Exception $e) {

                    return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'Error en base de datos.')));


                }
            }
        } else {


            return $this->render('AppBundle:Manage:edit_check.html.twig',
                array('errors' => null, 'data' => null, 'services' => $services, 'factura' => $check, 'exception' => null, 'servicesCheck' => $servicesCheck));
        }

    }

    public  function removeCheckQueryAction($id)
    {
        try {


            $result = $this->getRepo('FacturaConsulta')->find($id);
            $resultOperation = array('success' => false, 'error' => 'No se encontró la factura a eliminar');
            if ($result != null && $this->getUser() && ($this->getUser()->getRol()->getId() == 3 || $this->getUser()->getRol()->getId() == 4)) {
                if ($result->getConsulta()->getEstado()->getId() == 4) {
                    $resultOperation = $this->removeModel('FacturaConsulta', $id);
                } else {
                    $resultOperation = array('success' => false, 'error' => 'La consulta se encuentra en estado: ' . $result->getConsulta()->getEstado()->getNombre());
                    return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));
                }
            }
            if (!$this->getUser()) {
                $resultOperation = array('success' => false, 'error' => 'No tiene permiso para eliminar la factura seleccionada');
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));

            } else if ($this->getUser()->getRol()->getId() < 3) {
                $resultOperation = array('success' => false, 'error' => 'No tiene permiso para eliminar la factura seleccionada');
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));

            }
            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {

            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }

    }

    public
    function createPDFCheckAction($id)
    {
        $result = $this->getRepo('FacturaConsulta')->find($id);

        if (!$result) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'No se encontró la factura.')));
        }

        if ($result->getFichero()) {
            @unlink($this->getParameter('kernel.root_dir') . '/../web/bundles/app/docs/generatepdf/' . $result->getFichero());
        }

        $html = $this->renderView(
            'AppBundle:Manage:export_pdf.html.twig', array(
                'factura' => $result
            )
        );

        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf = $this->get('slik_dompdf');

        // Generate the pdf
        $dompdf->getpdf($html, 'UTF-8');
        $file = uniqid() . '.pdf';
        $filename = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/docs/generatepdf/' . $file;
        // Either stream the pdf to the browser
        $dompdf->stream($filename);

        // Or get the output to handle it yourself
        $pdfoutput = $dompdf->output();
        file_put_contents($filename, $pdfoutput);


        $this->saveModel('FacturaConsulta', array('id' => $result->getId(), 'fichero' => $file, 'enviado' => 0));

    }

    public
    function getPdfAction($fileName)
    {
        $filename = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/docs/generatepdf/' . $fileName;
        $response = new Response();

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
        $response->headers->set('Content-length', filesize($filename));


        $response->sendHeaders();

        $response->setContent(file_get_contents($filename));
        return $response;
    }

    public
    function sendEmailWithPdfAction($id)
    {

        try {
            $result = $this->getRepo('FacturaConsulta')->find($id);

            if (!$result) {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'No se encontró la factura.')));
            }

            if (!$result->getFichero()) {

                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'No se ha visualizado la factura digital.')));
            }
            $managerEmail = $this->get('manager.email');
            $resultSend = $managerEmail->sendMessageCheck($result);
            if (!$resultSend) {
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'No se pudo enviar el correo al cliente.Revise las trazas del envío de correo para más información')));
            } else {
                $this->saveModel('FacturaConsulta', array('id' => $result->getId(), 'enviado' => 1));
                return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => 'Correo enviado satisfactoriamente.', 'error' => '')));
            }


        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }
    }

    public
    function viewEvolutionsAction(Request $request, $queryId)
    {
        $query = $this->getRepo('Consulta')->find($queryId);
        if (!$query) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list',
                array('message' => '', 'error' => 'No se encontró la consulta')));
        }
        if ($this->getUser()->getRol()->getId() != 3) {
            return $this->redirect($this->generateUrl('default_homepage',
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

}