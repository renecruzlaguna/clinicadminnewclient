<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;
use Symfony\Component\Validator\Constraints\DateTime;


class WorkPlanedController extends BaseController
{
    public function indexAction(Request $request)
    {
        $data = $request->request->all();
        $monthSelected = null;
        $month = null;
        $year = null;
        $monthAux = null;
        $initialHourRange = null;
        $endHourRange = null;
        $initialMinuteRange = null;
        $endMinuteRange = null;
        $daysSelected = null;

        $monthCompare = new \DateTime();
        $monthCompare =$monthCompare->format('m');
        $yearCompare = new \DateTime();
        $yearCompare =$yearCompare->format('Y');
        if (@$data['generar'] || @$data['save']) {
            $initialHourRange = @$data['horainicial'];
            $endHourRange = @$data['horafinal'];
            $initialMinuteRange = @$data['minutoinicial'];
            $endMinuteRange = @$data['minutofinal'];


            $monthAux = $data['monthSelected'];

            if (strrpos($monthAux, '/') !== false) {
                $monthSelected = $data['monthSelected'];
                $monthSelected = explode('/', $monthSelected);

                $monthCompare=$monthSelected[0];
                $yearCompare=$monthSelected[2];
            } else {
                $monthAux = null;
            }
            $daysSelected = explode(',', $data['daysSelected']);

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
        if (@$data['save'] && $daysSelected) {

            $removed = $this->getRepo('PlanTrabajo')->findBy(array('mes' => $month, 'anno' => $year, 'usuario' => $this->getUser()->getId()));
            try {
                foreach ($removed as $entity) {
                    $this->removeModel('PlanTrabajo', $entity->getId());
                }

                foreach ($daysSelected as $day) {
                    $initHour=$initialHourRange[$day - 1];
                    $initMin=$initialMinuteRange[$day - 1];
                    $endHour= $endHourRange[$day - 1];

                    $endMin= $endMinuteRange[$day - 1];
                    if((int)($initialHourRange[$day - 1])>(int)($endHourRange[$day - 1])){
                        $initHour= $endHourRange[$day - 1];
                        $endHour= $endHourRange[$day - 1];
                    }
                    if((int)($initialMinuteRange[$day - 1])>(int)($endMinuteRange[$day - 1])){
                        $initMin= $initialMinuteRange[$day - 1];
                        $endMin= $endMinuteRange[$day - 1];
                    }


                    $dataSave = array('usuario' => $this->getUser()->getId(), 'dia' => $day, 'mes' => $month
                    , 'anno' => $year, 'horaInicial' =>$initHour, 'horaFinal' =>$endHour,
                        'minutoInicial' =>$initMin,
                        'minutoFinal' => $endMin
                    );

                    $this->saveModel('PlanTrabajo', $dataSave);
                }

            } catch (\Exception $e) {
                
            }
        }

        $finded = $this->getRepo('PlanTrabajo')->findBy(array('mes' => $month, 'anno' => $year, 'usuario' => $this->getUser()->getId()));
        $dayFind = array();
        $initialHourRange = array();
        $endHourRange = array();
        $initialMinuteRange = array();
        $endMinuteRange = array();
        $daysSelected = array();
        foreach ($finded as $entity) {
            $dayFind[] = $entity->getDia();
            $initialHourRange[$entity->getDia() - 1] = $entity->getHoraInicial();
            $endHourRange[$entity->getDia() - 1] = $entity->getHoraFinal();
            $initialMinuteRange[$entity->getDia() - 1] = $entity->getMinutoInicial();
            $endMinuteRange[$entity->getDia() - 1] = $entity->getMinutoFinal();
        }
        if (count($dayFind)) {
            $daysSelected = $dayFind;

        } else {
            $daysSelected = null;
        }


        return $this->render('AppBundle:Doctor:add_work_planed.html.twig',
            array('initialHourRange' => $initialHourRange, 'initialMinuteRange' => $initialMinuteRange,'monthCompare'=>$monthCompare,'yearCompare'=>$yearCompare,
                'endHourRange' => $endHourRange, 'endMinuteRange' => $endMinuteRange,
                'day' => $initialDay, 'totalWeek' => $totalWeek, 'totalDays' => $totalDays, 'monthAux' => $monthAux, 'daysSelected' => $daysSelected));
    }


    public function indexSecretaryAction(Request $request,$idDoctor)
    {
        $data = $request->request->all();
        $monthSelected = null;
        $month = null;
        $year = null;
        $monthAux = null;
        $initialHourRange = null;
        $endHourRange = null;
        $initialMinuteRange = null;
        $endMinuteRange = null;
        $daysSelected = null;

        $monthCompare = new \DateTime();
        $monthCompare =$monthCompare->format('m');
        $yearCompare = new \DateTime();
        $yearCompare =$yearCompare->format('Y');
        if (@$data['generar'] || @$data['save']) {
            $initialHourRange = @$data['horainicial'];
            $endHourRange = @$data['horafinal'];
            $initialMinuteRange = @$data['minutoinicial'];
            $endMinuteRange = @$data['minutofinal'];


            $monthAux = $data['monthSelected'];

            if (strrpos($monthAux, '/') !== false) {
                $monthSelected = $data['monthSelected'];
                $monthSelected = explode('/', $monthSelected);

                $monthCompare=$monthSelected[0];
                $yearCompare=$monthSelected[2];
            } else {
                $monthAux = null;
            }
            $daysSelected = explode(',', $data['daysSelected']);

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
        if (@$data['save'] && $daysSelected) {

            $removed = $this->getRepo('PlanTrabajo')->findBy(array('mes' => $month, 'anno' => $year, 'usuario' => $idDoctor));
            try {
                foreach ($removed as $entity) {
                    $this->removeModel('PlanTrabajo', $entity->getId());
                }

                foreach ($daysSelected as $day) {
                    $initHour=$initialHourRange[$day - 1];
                    $initMin=$initialMinuteRange[$day - 1];
                    $endHour= $endHourRange[$day - 1];

                    $endMin= $endMinuteRange[$day - 1];
                    if((int)($initialHourRange[$day - 1])>(int)($endHourRange[$day - 1])){
                        $initHour= $endHourRange[$day - 1];
                        $endHour= $endHourRange[$day - 1];
                    }
                    if((int)($initialMinuteRange[$day - 1])>(int)($endMinuteRange[$day - 1])){
                        $initMin= $initialMinuteRange[$day - 1];
                        $endMin= $endMinuteRange[$day - 1];
                    }


                    $dataSave = array('usuario' => $idDoctor, 'dia' => $day, 'mes' => $month
                    , 'anno' => $year, 'horaInicial' =>$initHour, 'horaFinal' =>$endHour,
                        'minutoInicial' =>$initMin,
                        'minutoFinal' => $endMin
                    );

                    $this->saveModel('PlanTrabajo', $dataSave);
                }

            } catch (\Exception $e) {
            }
        }

        $finded = $this->getRepo('PlanTrabajo')->findBy(array('mes' => $month, 'anno' => $year, 'usuario' => $idDoctor));
        $dayFind = array();
        $initialHourRange = array();
        $endHourRange = array();
        $initialMinuteRange = array();
        $endMinuteRange = array();
        $daysSelected = array();
        foreach ($finded as $entity) {
            $dayFind[] = $entity->getDia();
            $initialHourRange[$entity->getDia() - 1] = $entity->getHoraInicial();
            $endHourRange[$entity->getDia() - 1] = $entity->getHoraFinal();
            $initialMinuteRange[$entity->getDia() - 1] = $entity->getMinutoInicial();
            $endMinuteRange[$entity->getDia() - 1] = $entity->getMinutoFinal();
        }
        if (count($dayFind)) {
            $daysSelected = $dayFind;

        } else {
            $daysSelected = null;
        }

     $user=$this->getRepo('Usuario')->find($idDoctor);
        return $this->render('AppBundle:Manage:add_work_planed.html.twig',
            array('user'=>$user,'initialHourRange' => $initialHourRange, 'initialMinuteRange' => $initialMinuteRange,'monthCompare'=>$monthCompare,'yearCompare'=>$yearCompare,
                'endHourRange' => $endHourRange, 'endMinuteRange' => $endMinuteRange,
                'day' => $initialDay, 'totalWeek' => $totalWeek, 'totalDays' => $totalDays, 'monthAux' => $monthAux, 'daysSelected' => $daysSelected));
    }

}