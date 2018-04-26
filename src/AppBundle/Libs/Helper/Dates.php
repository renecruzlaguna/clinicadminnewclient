<?php

namespace AppBundle\Libs\Helper;

class Dates {

    private $days_dias = array(
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    );

    /* cantidad de dias de un mes en un anno */

    public function getNumbersOfDays($month, $year) {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /* cantidad de semana que tiene un mes comenzando por el domingo */

    public function getNumberOfWeek($month, $year) {
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $week_day = date("w", mktime(0, 0, 0, $month, 1, $year));
        $weeks = ceil(($days + $week_day) / 7);

        return $weeks;
    }

    /* numero del dia que comienza un mes comenzando por el domingo como 0 */

    public function firstDayOfMonth($month, $year) {

        $day = (int) date("w", strtotime(\DateTime::createFromFormat('m/d/Y', $month . '/' . '1/' . $year)->format('M') . ' 01,' . $year . ' 00:00:00'));
        return $day;
    }

    public function getLastMonthRange() {
        return $this->getRange("last");
    }

    /* array de array con los rangos por semana */

    public function getRange($option = 'now') {
        $month = (date('m', strtotime("now")));
        $year = date('Y', strtotime("now"));

        if ($option != 'now') {
            if ($month == 1) {
                $month = 12;
                $year = $year - 1;
            } else {
                $month = $month - 1;
            }
        }
        $week = $this->getNumberOfWeek($month, $year);

        $range = array();
        $start = 1;
        $end = ($start + 6) - $this->firstDayOfMonth($month, $year);
        for ($i = 1; $i <= $week; $i++) {

            if ($i != $week) {
                $range["Semana " . $i] = array($year . "-" . $month . "-" . $start, $year . "-" . $month . "-" . $end);
            } else {
                $range["Semana " . $i] = array($year . "-" . $month . "-" . $start, $year . "-" . $month . "-" . cal_days_in_month(CAL_GREGORIAN, $month, $year));
            }
            $start = $end + 1;
            $end += 7;
        }
        return $range;
    }

    public function getPresentWeek($format = "Y-m-d",$withMoreInfo=false) {
        $start_week = '';
        if (date('D') != 'Sun') {
            $start_week = strtotime("last sunday midnight");
        } else {
            $start_week = strtotime("now");
        }
        if(!$withMoreInfo){
          return $this->getDaysOfWeek($start_week, $format);  
        }
        return $this->getDaysOfWeekWithMoreInfo($start_week, $format);
    }

    public function getDaysOfWeek($firstDay, $format = "Y-m-d") {
        $range = array();

        $range["Domingo"] = date($format, $firstDay);
        $range["Lunes"] = date($format, strtotime("next monday ", $firstDay));
        $range["Martes"] = date($format, strtotime("next tuesday ", $firstDay));
        $range["Miércoles"] = date($format, strtotime("next wednesday ", $firstDay));
        $range["Jueves"] = date($format, strtotime("next thursday midnight", $firstDay));
        $range["Viernes"] = date($format, strtotime("next friday midnight", $firstDay));
        $range["Sábado"] = date($format, strtotime("next saturday midnight", $firstDay));
        return $range;
    }
    
    public function getDaysOfWeekWithMoreInfo($firstDay, $format = "Y-m-d") {
        $range = array();

        $range["Domingo ".date($format, $firstDay)] = date($format, $firstDay);
        $range["Lunes ".date($format, strtotime("next monday ", $firstDay))] = date($format, strtotime("next monday ", $firstDay));
        $range["Martes ".date($format, strtotime("next tuesday ", $firstDay))] = date($format, strtotime("next tuesday ", $firstDay));
        $range["Miércoles ". date($format, strtotime("next wednesday ", $firstDay))] = date($format, strtotime("next wednesday ", $firstDay));
        $range["Jueves ".date($format, strtotime("next thursday midnight", $firstDay))] = date($format, strtotime("next thursday midnight", $firstDay));
        $range["Viernes ".date($format, strtotime("next friday midnight", $firstDay))] = date($format, strtotime("next friday midnight", $firstDay));
        $range["Sábado ". date($format, strtotime("next saturday midnight", $firstDay))] = date($format, strtotime("next saturday midnight", $firstDay));
        return $range;
    }

    public function getDaysOfWeekBySpecificDate($date, $format = "m-d-Y") {


        $begin = \DateTime::createFromFormat($format,$date);
        $beginStart = \DateTime::createFromFormat($format, $date);
        $intervalDays = new \DateInterval('P7D');
        $periodResult=array();
        $end = $begin->add($intervalDays);

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($beginStart, $interval, $end);

        foreach ($period as $dt) {
            $periodResult[$this->days_dias[$dt->format("l")]." ".$dt->format($format)]=$dt->format($format);
        }
        
        return $periodResult;
    }
    
    public function getDaysOfSpecificDate($date,$endDate, $format = "m-d-Y") {


        $begin = \DateTime::createFromFormat($format,$date);
        $end = \DateTime::createFromFormat($format,$endDate);
        $periodResult=array();
        
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $periodResult[$this->days_dias[$dt->format("l")]." ".$dt->format($format)]=$dt->format($format);
        }
        
        return $periodResult;
    }

    public function getBeforeWeek() {
        if (date('D') != 'Sun') {
            $week = strtotime("last sunday midnight");
        } else {
            $week = strtotime("now");
        }
        $start_week = strtotime("-1 week", $week);
        return $this->getDaysOfWeek($start_week);
    }

}
