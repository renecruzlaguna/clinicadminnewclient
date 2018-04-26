<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class AdminReportController extends BaseController
{

    /*Listado de consultas realizadas por medico especialidad o todas*/


    public function listQueryMadeReportShowAction()
    {
        $result = $this->getRepo('Especialidad')->findAll();

        return $this->render('AppBundle:Manage:list_query_made_doctor_speciality.html.twig', array('especialities' => $result));
    }

    public function filterQueryMadeReportAction($speciality = -1, $doctor = -1)
    {
        $result = array();
        $repo = $this->getRepo('Consulta');

        if ($speciality == -1 && $doctor == -1) {
            $result = $repo->findBy(array('estado' => 4));
        } else {
            $result = $repo->getAllQueryByDoctorOrSpecility($speciality, $doctor);
        }
        return $this->render('AppBundle:Manage:filter_query_made_doctor_speciality.html.twig', array('especialities' => $result));


    }

    /*Listado de consultas realizadas por medico especialidad o todas*/


    public function listQueryCancelByPatientReportShowAction()
    {
        $result = $this->getRepo('Usuario')->findBy(array('rol' => 1));

        return $this->render('AppBundle:Manage:list_query_cancel.html.twig', array('users' => $result));
    }

    public function filterQueryCancelByPatientReportAction($patient = -1)
    {
        $result = array();
        $repo = $this->getRepo('Consulta');

        if ($patient == -1) {
            $result = $repo->findBy(array('estado' => 3));
        } else {
            $result = $repo->getAllQueryCancelByPatient($patient);
        }
        return $this->render('AppBundle:Manage:filter_query_cancel.html.twig', array('especialities' => $result));


    }

    /*Listado de consultas realizadas por medico especialidad o todas*/


    public function listQueryStateReportShowAction()
    {
        $result = $this->getRepo('Estado')->findAll();

        return $this->render('AppBundle:Manage:list_query_state.html.twig', array('states' => $result));
    }

    public function filterQueryStateReportAction($state = -1, $initialDate = -1, $endDate = -1)
    {
        $result = array();
        $repo = $this->getRepo('Consulta');

        if ($state == -1 && $initialDate == -1 & $endDate == -1) {
            $result = $repo->findAll();
        } else {
            $result = $repo->getAllQueryByState($state, $initialDate, $endDate);
        }
        return $this->render('AppBundle:Manage:filter_query_state.html.twig', array('especialities' => $result));


    }

    public function listQueryTotalReportShowAction()
    {
        $result = $this->getRepo('Especialidad')->findAll();

        return $this->render('AppBundle:Manage:list_query_made_doctor_speciality_time.html.twig', array('especialities' => $result));
    }

    public function filterQueryTotalReportAction($speciality = -1, $doctor = -1, $initialDate = -1, $endDate = -1)
    {

        $repo = $this->getRepo('Consulta');
        $result = $repo->getTotalMonyQuery($speciality, $doctor, $initialDate, $endDate);

        return $this->render('AppBundle:Manage:filter_query_made_doctor_speciality_time.html.twig', array('especialities' => $result));


    }


    public function listQueryTotalReportSalaryShowAction()
    {
        $result = $this->getRepo('Especialidad')->findAll();

        return $this->render('AppBundle:Manage:list_query_made_doctor_speciality_time_salary.html.twig', array('especialities' => $result));
    }

    public function filterQueryTotalReportSalaryAction($speciality = -1, $doctor = -1, $initialDate = -1, $endDate = -1)
    {

        $repo = $this->getRepo('Consulta');
        $result = $repo->getTotalMonySalaryQuery($speciality, $doctor, $initialDate, $endDate);

        return $this->render('AppBundle:Manage:filter_query_made_doctor_speciality_time_salary.html.twig', array('especialities' => $result));


    }


}