<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminGraficReportController extends BaseController {
    /* Listado de total de consultas realizadas */

    public function listTotalQueryMadeReportShowAction() {
        $result = $this->getRepo('Especialidad')->findAll();
        $state = $this->getRepo('Estado')->findAll();
        return $this->render('AppBundle:Manage:GraficReport/list_total_query_speciality_time.html.twig', array('especialities' => $result, 'states' => $state));
    }

    public function generateDataForGraphicTotalQueryMadeReportAction(Request $request) {


        $serviceDates = $this->get('app.dates');
        $data = null;
        $specialityRepo = $this->getRepo('Especialidad');
        $initialDate = $request->get('initialDate');
        if ($initialDate == -1) {

            $data = $serviceDates->getPresentWeek("m-d-Y", true);
        } else {
            $data = $serviceDates->getDaysOfWeekBySpecificDate($initialDate);
        }


        $specialities = $request->get('speciality');
        $speciality = null;
        $state = $request->get('state');

        if (!$state) {
            $state = -1;
        }
        if ($specialities == -1) {
            $speciality = $specialityRepo->findAll();
        } else {
            $speciality = $specialityRepo->getSpecialityByIds($specialities);
        }

        $result = new \stdClass();
        $result->datasets = array();
        $repoQuery = $this->getRepo('Consulta');
        $labels = array_keys($data);

        foreach ($speciality as $esp) {
            $dataset = new \stdClass();
            $dataset->label = $esp->getNombre();
            $dataset->data = array();
            foreach ($data as $days) {

                $dataset->data[] = $repoQuery->getTotalQueryBySpecialityForDay($esp->getId(), $days, $state);
            }
            $rand = rand(0, 255);
            $rand2 = rand(0, 255);
            $rand3 = rand(0, 255);
            $dataset->backgroundColor = "rgba($rand,$rand2,$rand3,0.5)";
            $dataset->borderColor = "rgba($rand,$rand2,$rand3,0.7)";
            $dataset->pointBackgroundColor = "rgba($rand,$rand2,$rand3,1)";
            $dataset->pointBorderColor = "#fff";
            $result->datasets[] = $dataset;
        }
        $result->labels = $labels;
        return new JsonResponse(array('success' => true, 'data' => $result));
    }

    /* Listado de total de servicios consumidos */

    public function listTotalServiceMadeReportShowAction() {
        $result = $this->getRepo('Servicio')->findAll();

        return $this->render('AppBundle:Manage:GraficReport/list_total_service_time.html.twig', array('services' => $result));
    }

    public function generateDataForGraphicTotalServiceMadeReportAction(Request $request) {


        $serviceDates = $this->get('app.dates');
        $data = null;
        $serviceRepo = $this->getRepo('Servicio');
        $initialDate = $request->get('initialDate');
        if ($initialDate == -1) {

            $data = $serviceDates->getPresentWeek("m-d-Y", true);
        } else {
            $data = $serviceDates->getDaysOfWeekBySpecificDate($initialDate);
        }


        $services = $request->get('service');
        $service = null;


        if ($services == -1) {
            $service = $serviceRepo->findAll();
        } else {
            $service = $serviceRepo->getServicesByIds($services);
        }

        $result = new \stdClass();
        $result->datasets = array();
        $repoQuery = $this->getRepo('Consulta');
        $labels = array_keys($data);

        foreach ($service as $serv) {
            $dataset = new \stdClass();
            $dataset->label = $serv->getNombre();
            $dataset->data = array();
            foreach ($data as $day) {

                $dataset->data[] = $repoQuery->getTotalByServiceToGraphicForDay($serv->getId(), $day);
            }
            $rand = rand(0, 255);
            $rand2 = rand(0, 255);
            $rand3 = rand(0, 255);
            $dataset->backgroundColor = "rgba($rand,$rand2,$rand3,0.5)";
            $dataset->borderColor = "rgba($rand,$rand2,$rand3,0.7)";
            $dataset->pointBackgroundColor = "rgba($rand,$rand2,$rand3,1)";
            $dataset->pointBorderColor = "#fff";
            $result->datasets[] = $dataset;
        }
        $result->labels = $labels;
        return new JsonResponse(array('success' => true, 'data' => $result));
    }

    public function listTotalQueryPatientReportShowAction() {
        $result = $this->getRepo('Especialidad')->findAll();
        $state = $this->getRepo('Estado')->findAll();
        return $this->render('AppBundle:Manage:GraficReport/list_total_patient_speciality_time.html.twig', array('especialities' => $result, 'states' => $state));
    }

    public function generateDataForGraphicTotalQueryPatientReportAction(Request $request) {


        $serviceDates = $this->get('app.dates');
        $data = null;
        $specialityRepo = $this->getRepo('Especialidad');
        $initialDate = $request->get('initialDate');
        if ($initialDate == -1) {

            $data = $serviceDates->getPresentWeek("m-d-Y", true);
        } else {
            $data = $serviceDates->getDaysOfWeekBySpecificDate($initialDate);
        }


        $specialities = $request->get('speciality');
        $speciality = null;


        if ($specialities == -1) {
            $speciality = $specialityRepo->findAll();
        } else {
            $speciality = $specialityRepo->getSpecialityByIds($specialities);
        }

        $result = new \stdClass();
        $result->datasets = array();
        $repoQuery = $this->getRepo('Consulta');
        $repoUser = $this->getRepo('Usuario');
        $labels = array_keys($data);

        foreach ($speciality as $esp) {
            $dataset = new \stdClass();
            $dataset->label = $esp->getNombre();
            $dataset->data = array();
            foreach ($data as $days) {

                $dataset->data[] = $repoUser->getTotalNewPatientBySpecialityForDay($esp->getId(), $days);
            }
            $rand = rand(0, 255);
            $rand2 = rand(0, 255);
            $rand3 = rand(0, 255);
            $dataset->backgroundColor = "rgba($rand,$rand2,$rand3,0.5)";
            $dataset->borderColor = "rgba($rand,$rand2,$rand3,0.7)";
            $dataset->pointBackgroundColor = "rgba($rand,$rand2,$rand3,1)";
            $dataset->pointBorderColor = "#fff";
            $result->datasets[] = $dataset;
        }
        $result->labels = $labels;
        return new JsonResponse(array('success' => true, 'data' => $result));
    }

    /* Listado de total de dinero por consultas realizadas */

    public function listTotalMonyQueryMadeReportShowAction() {
        $result = $this->getRepo('Especialidad')->findAll();
        return $this->render('AppBundle:Manage:GraficReport/list_total_mony_speciality_time.html.twig', array('especialities' => $result));
    }

    public function generateDataForGraphicTotalMonyQueryMadeReportAction(Request $request) {


        $serviceDates = $this->get('app.dates');
        $data = null;
        $specialityRepo = $this->getRepo('Especialidad');
        $initialDate = $request->get('initialDate');
        $endDate = $request->get('endDate');
        if ($initialDate == -1 || $endDate == -1) {
            $data = $serviceDates->getPresentWeek("m-d-Y", true);
        } else {
            $data = $serviceDates->getDaysOfSpecificDate($initialDate,$endDate);
        }


        $specialities = $request->get('speciality');
        $speciality = null;
        if ($specialities == -1) {
            $speciality = $specialityRepo->findAll();
        } else {
            $speciality = $specialityRepo->getSpecialityByIds($specialities);
        }

        $result = array();
        $repoQuery = $this->getRepo('Consulta');

        foreach ($speciality as $esp) {
            $dataset = new \stdClass();
            $dataset->label = $esp->getNombre();
            $dataset->data = array();
            foreach ($data as $days) {
                $begin = \DateTime::createFromFormat('m-d-Y', $days);
                $dataset->data[] = array($begin->getTimestamp()* 1000, $repoQuery->getTotalMonyQueryToGraphicForDay($esp->getId(), $days));


                $rand = rand(0, 255);
                $rand2 = rand(0, 255);
                $rand3 = rand(0, 255);
                $dataset->color = "rgba($rand,$rand2,$rand3,0.5)";
                $points = new \stdClass();
                $points->fillColor = "rgba($rand,$rand2,$rand3,0.5)";
                $points->show = true;
                $lines = new \stdClass();
                $lines->show = true;
                $dataset->points = $points;
                $dataset->lines = $points;

            }
            $result[] = $dataset;
        }
        return new JsonResponse(array('success' => true, 'data' => $result));
    }

}
