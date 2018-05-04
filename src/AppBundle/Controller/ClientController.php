<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;
use Symfony\Component\Validator\Constraints\DateTime;

class ClientController extends BaseController {

    public function indexAction(Request $request) {

        $speciality = $this->getRepo('Especialidad')->findAll();
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Client:register_query.html.twig', array('message' => $message, 'error' => $error, 'errors' => null, 'data' => null, 'exception' => null, 'speciality' => $speciality));
    }

    public function listMyQueryAction(Request $request) {
        $user = $this->getUser()->getId();
        $query = $this->getRepo('Consulta')->findBy(array('usuarioRegistro' => $user));
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Client:list_reserved.html.twig', array('message' => $message, 'error' => $error, 'errors' => null, 'data' => null, 'exception' => null, 'especialities' => $query));
    }

    public function cancelQueryAction(Request $request, $id) {

        $data = $request->request->all();
        $data['estado'] = 3;
        $data['id'] = $id;
        $resultOperation = array('success' => false, 'error' => 'No se encontró la reserva a eliminar');
        $query = $this->getRepo('Consulta')->find($id);
        if ($query) {
            if ($query && $query->getUsuarioRegistro()->getId() != $this->getUser()->getId()) {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => 'No tiene permiso para cancelar esta consulta.')));
            }

            if ($query && $query->getEstado()->getId() != 2) {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => 'La consulta no se encuentra en estado solicitada.')));
            }
            if ($query && $query->getEstado()->getId() == 4) {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => 'La consulta no se encuentra en estado solicitada.')));
            }
            $entity = $this->populateModel('Consulta', $data);
            $resultOperation = $this->saveEntity($entity);
            if ($resultOperation == true) {
                $managerEmail = $this->get('manager.email');
                $managerEmail->setReserve($entity);
                $managerEmail->sendMessage(5);
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } else {

            return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => $resultOperation['error'])));
        }
    }

    public function listMyQueryExecutedAction(Request $request) {
        $user = $this->getUser()->getId();
        $query = $this->getRepo('Consulta')->findBy(array('usuarioRegistro' => $user, 'estado' => 4));
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        return $this->render('AppBundle:Client:list_query_executed.html.twig', array('message' => $message, 'error' => $error, 'errors' => null, 'data' => null, 'exception' => null, 'especialities' => $query));
    }

    public function getDoctorBySpecialityAction($id) {
        $repoUser = $this->getRepo('Usuario');
        $doctors = $repoUser->findBy(array('especialidad' => $id, 'rol' => 2, 'activo' => 1));
        $data = array();
        foreach ($doctors as $value) {
            $data[] = $value->toJson();
        }
        return new JsonResponse(array('success' => true, 'data' => $data));
    }

    public function getDoctorRangeHourAction($id, $day, $month, $year) {
        
        $finded = $this->getRepo('PlanTrabajo')->findOneBy(array('dia' => $day, 'mes' => $month, 'anno' => $year, 'usuario' => $id));
        $data = array('sucess' => true);
        if ($finded != null) {
            $data['min'] = $finded->getHoraInicial() . ':' . $finded->getMinutoInicial();
            $data['max'] = $finded->getHoraFinal() . ':' . $finded->getMinutoFinal();
        }

        return new JsonResponse(array('success' => true, 'data' => $data));
    }

    public function getAllQueryOfDoctorAction($id, $day, $month, $year) {
        if ($day < 10)
            $day = '0' . $day;

        $finds = $this->getRepo('Consulta')->getAllQueryByDoctor($id,$day,$month,$year);

        $html = $this->renderView('AppBundle:Default:list_query_template.html.twig', array('queries' => $finds));

        return new JsonResponse(array('success' => true, 'data' => $html));
    }

    public function getCalendarOfDoctorAction(Request $request, $id) {
      
        $data = $request->query->all();

        $monthSelected = null;
        $month = null;
        $year = null;
        $monthAux = null;

        $daysSelected = null;


        $monthAux = $data['monthSelected'];
        if (strrpos($monthAux, '/') !== false) {
            $monthSelected = $data['monthSelected'];
            $monthSelected = explode('/', $monthSelected);
        } else {
            $monthAux = null;
        }


        if ($monthSelected) {
            $month = $monthSelected[0];
            $year = $monthSelected[2];
        } else {
            $date = new \DateTime('now');
            $year = $date->format('Y');
            $month = $date->format('m');
        }

        $util = $this->get('app.dates');
        $initialDay = $util->firstDayOfMonth($month, $year);
        $totalWeek = $util->getNumberOfWeek($month, $year);
        $totalDays = $util->getNumbersOfDays($month, $year);

        $finded = $this->getRepo('PlanTrabajo')->findBy(array('mes' => $month, 'anno' => $year, 'usuario' => $id));
        $repoQuery = $this->getRepo('Consulta');
        $dayFind = array();
        $countQuery = array();
        $hours = array();
        foreach ($finded as $entity) {
           
            $date = new \DateTime('now');
            $yearCheck = $date->format('Y');
            $monthCheck = $date->format('m');
            $dayCheck = $date->format('d');
            if (($monthCheck == $month && $dayCheck <= (int) $entity->getDia()) || $monthCheck != $month) {
                $dayFind[] = $entity->getDia();
                $result = $repoQuery->findBy(array('usuario' => $id, 'mes' => $month, 'anno' => $year, 'dia' => $entity->getDiaWithZero()));
                $horaInicial = $entity->getHoraInicial();

                $minutoFinal = $entity->getMinutoFinal();
                $minutoInicial = $entity->getMinutoInicial();
                $horaFinal = $entity->getHoraFinal();
                if ($horaInicial < 10) {
                    $horaInicial = '0' . $horaInicial;
                }
                if ($horaFinal < 10) {
                    $horaFinal = '0' . $horaFinal;
                }
                if ($minutoFinal < 10) {
                    $minutoFinal = '0' . $minutoFinal;
                }
                if ($minutoInicial < 10) {
                    $minutoInicial = '0' . $minutoInicial;
                }
                $data['min'] = $horaInicial . ':' . $minutoInicial;
                $data['max'] = $horaFinal . ':' . $minutoFinal;
                $hours[$entity->getDia()] = $data;
                $countQuery[$entity->getDia()] = count($result);
            }
        }
        if (count($dayFind)) {
            $daysSelected = $dayFind;
        } else {
            $daysSelected = null;
        }

        return $this->render('AppBundle:Client:date_register.html.twig', array('clocks' => $hours, 'countQuery' => $countQuery, 'day' => $initialDay, 'totalWeek' => $totalWeek, 'totalDays' => $totalDays, 'monthAux' => $monthAux, 'daysSelected' => $daysSelected));
    }

    public function saveReserveAction(Request $request) {
        try {
            $data = $request->request->all();
            $data['usuarioRegistro'] = $this->getUser()->getId();
            $data['estado'] = 1;
            if ($data['minutoInicial'] == 45) {
                $data['horaFinal'] = (int) ($data['horaInicial'] + 1);
                $data['minutoFinal'] = 0;
            } else {
                $data['horaFinal'] = (int) ($data['horaInicial']);
                $data['minutoFinal'] = (int) ($data['minutoInicial'] + 15);
            }
            /*
              $find = $this->getRepo('Consulta')->findQueryHour(-1, $data['horaInicial'], $data['horaFinal'], $data['minutoInicial'], $data['minutoFinal'], $data['usuario'], $data['dia'], $data['mes'], $data['anno']);
              if ($find) {
              $initialHourQuery = $find->getHoraInicialC();
              $endHourQuery = $find->getHoraFinalC();

              $initialMinuteQuery = $query = $find->getMinutoInicialC();
              $endMinuteQuery = $query = $find->getMinutoFinalC();
              $range = $initialHourQuery . ':' . $initialMinuteQuery . ' - ' . $endHourQuery . ':' . $endMinuteQuery;
              return $this->redirect($this->generateUrl('client_query_register', array('message' => '', 'error' => 'Ya existe una consulta para el rango de tiempo:'
              . $range)));
              } */
            $entity = $this->populateModel('Consulta', $data);
            $entity->updateCompleteDate();
            $result = $this->saveEntity($entity);
            if ($result === true) {
                $managerEmail = $this->get('manager.email');
                $managerEmail->setReserve($entity);
                $managerEmail->sendMessage(1);
            }
            if ($result === true) {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => 'Consulta adicionada satisfactoriamente')));
            } else {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('error' => $result['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('client_query_reserved_list', array('error' => 'Ocurrió un error en el servidor')));
        }
    }

    /* muestra la consulta para actualizarla */

    public function updateQueryAction(Request $request, $id) {

        $speciality = $this->getRepo('Especialidad')->findAll();

        $message = $request->query->get('message');
        $error = $request->query->get('error');
        $query = $this->getRepo('Consulta')->find($id);
        $doctors = $this->getRepo('Usuario')->findBy(array('rol' => 2, 'especialidad' => $query->getEspecialidad()->getId()));

        return $this->render('AppBundle:Client:update_query.html.twig', array('message' => $message, 'error' => $error, 'doctors' => $doctors, 'query' => $query, 'errors' => null, 'data' => null, 'exception' => null, 'speciality' => $speciality));
    }

    /* la  actualiza y la guarda */

    public function saveUpdateReserveAction(Request $request, $id) {
        try {
            $data = $request->request->all();


            if ($data['minutoInicial'] == 45) {
                $data['horaFinal'] = (int) ($data['horaInicial'] + 1);
                $data['minutoFinal'] = 0;
            } else {
                $data['horaFinal'] = (int) ($data['horaInicial']);
                $data['minutoFinal'] = (int) ($data['minutoInicial'] + 15);
            }
            $data['id'] = $id;
            /*
              $find = $this->getRepo('Consulta')->findQueryHour($id, $data['horaInicial'], $data['horaFinal'], $data['minutoInicial'], $data['minutoFinal'], $data['usuario'], $data['dia'], $data['mes'], $data['anno']);
              if ($find) {
              $initialHourQuery = $find->getHoraInicialC();
              $endHourQuery = $find->getHoraFinalC();

              $initialMinuteQuery = $query = $find->getMinutoInicialC();
              $endMinuteQuery = $query = $find->getMinutoFinalC();
              $range = $initialHourQuery . ':' . $initialMinuteQuery . ' - ' . $endHourQuery . ':' . $endMinuteQuery;
              return $this->redirect($this->generateUrl('cliente_query_update', array('message' => '', 'error' => 'Ya existe una consulta para el rango de tiempo:'
              . $range)));
              } */
            $entity = $this->populateModel('Consulta', $data);
            $result = $this->saveEntity($entity);
            $entity->updateCompleteDate();
            if ($result === true) {
                $managerEmail = $this->get('manager.email');
                $managerEmail->setReserve($entity);
                $managerEmail->sendMessage(4);
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => 'Consulta actualizada satisfactoriamente')));
            } else {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('error' => $result['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('client_query_reserved_list', array('error' => 'Ocurrió un error en el servidor')));
        }
    }

    public function removeReserveClientAction($id) {
        try {
            $resultOperation = array('success' => false, 'error' => 'No se encontró la reserva a eliminar');
            $result = $this->getRepo('Consulta')->find($id);
            if ($result != null && $this->getUser() && $result->getUsuarioRegistro()->getId() == $this->getUser()->getId()) {
                if ($result->getEstado()->getId() == 1) {
                    $resultOperation = $this->removeModel('Consulta', $id);
                } else {
                    $resultOperation = array('success' => false, 'error' => 'La consulta se encuentra en estado: ' . $result->getEstado()->getNombre());
                    return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => $resultOperation['error'])));
                }
            }

            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }
    }

    public function createPDFCheckAction($id) {
        $result = $this->getRepo('FacturaConsulta')->find($id);

        if (!$result) {
            return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => 'No se encontró la factura.')));
        }
        if ($result->getConsulta()->getUsuarioRegistro()->getId() != $this->getUser()->getId()) {
            return $this->redirect($this->generateUrl('client_query_reserved_list', array('message' => '', 'error' => 'No tiene permiso para ejecutar esta acción sobre la factura seleccionada.')));
        }

        if ($result->getFichero()) {
            unlink($this->getParameter('kernel.root_dir') . '/../web/bundles/app/docs/generatepdf/' . $result->getFichero());
        }

        $html = $this->renderView(
                'AppBundle:Manage:export_pdf.html.twig', array(
            'factura' => $result
                )
        );


        $dompdf = $this->get('slik_dompdf');

        // Generate the pdf
        $dompdf->getpdf($html);
        $file = uniqid() . '.pdf';
        $filename = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/docs/generatepdf/' . $file;
        // Either stream the pdf to the browser
        $dompdf->stream($filename);

        // Or get the output to handle it yourself
        $pdfoutput = $dompdf->output();
        file_put_contents($filename, $pdfoutput);
    }

}
