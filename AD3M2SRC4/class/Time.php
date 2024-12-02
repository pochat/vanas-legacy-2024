<?php

setlocale(LC_TIME, "spanish");

/**
 * Time class for easy use of time data.
 *
 * Manipulation of time data.
 *
 * @link 
 */
class Time {

    /**
     * EGMC 20150827
     * Es la variable que se utiliza para saber si 
     * la clase ya fue instanciada sirve para patrón singleton
     * @var instance de la clase Html
     */
    private static $instance;

    /**
     * EGMC 20150827
     * contructor privado para aplicar patrón singleton
     */
    private function __construct() {
        
    }

    /**
     * EGMC 20151002
     * Aplica patrón singleton
     * @return class regresa la instancia del objeto
     */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Agregar años, meses, días, horas, minutos y segundos a una fecha
     *
     * @param date $date Fecha a la que se le va agregar tiempo
     * @param string $format formato de la fecha
     * @param int $years años a sumar 
     * @param int $months meses a sumar 
     * @param int $days días a sumar 
     * @param int $hours horas a sumar 
     * @param int $minutes minutos a sumar 
     * @param int $seconds segundos a sumar 
     * @return date echa agregada
     * 
     */
    public static function dateAddTime($date, $format = "Y-m-d H:i:s", $years = 0, $months = 0, $days = 0, $hours = 0, $minutes = 0, $seconds = 0) {
        $date = getdate(strtotime($date));
        return date($format, mktime(($date["hours"] + $hours), ($date["minutes"] + $minutes), ($date["seconds"] + $seconds), ($date["mon"] + $months), ($date["mday"] + $days), ($date["year"] + $years)));
    }

    /**
     * Agregar años, meses, días, horas, minutos y segundos a una fecha
     *
     * @param date $date Fecha a la que se le va agregar tiempo
     * @param int $years años a sumar 
     * @param int $months meses a sumar 
     * @param int $days días a sumar 
     * @param int $hours horas a sumar 
     * @param int $minutes minutos a sumar 
     * @param int $seconds segundos a sumar 
     * @param string $format formato de la fecha
     * 
     * @return date fecha sumada
     * 
     */
    public static function addTimeForADate($date, $years = 0, $months = 0, $days = 0, $hours = 0, $minutes = 0, $seconds = 0, $format = "Y-m-d H:i:s") {
        $date = getdate(strtotime($date));
        return date($format, mktime(($date["hours"] + $hours), ($date["minutes"] + $minutes), ($date["seconds"] + $seconds), ($date["mon"] + $months), ($date["mday"] + $days), ($date["year"] + $years)));
    }

    public function getMonth($numbreMonth) {
        $numbreMonth = $numbreMonth * 1;
        $months = array("",
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre");

        return $months[$numbreMonth];
    }

    public function getEsMonths() {
        return array(1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre");
    }

    /**
     * EGMC
     * Regresa la diferencia de dos fechas en arreglo 
     * @param string $dateLow
     * @param string $dateHigh
     * @return array
     */
    public function periodDiff($dateLow, $dateHigh) {
        $dateLow = strtotime($dateLow);
        $dateHigh = strtotime($dateHigh);
        if ($dateLow > $dateHigh) {
            $tmp = $dateLow;
            $dateLow = $dateHigh;
            $dateHigh = $tmp;
        }
        $diff = abs($dateHigh - $dateLow);
        $count['years'] = floor($diff / (365 * 60 * 60 * 24));
        $count['months'] = floor(($diff - $count['years'] * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $count['days'] = floor(($diff - $count['years'] * 365 * 60 * 60 * 24 - $count['months'] * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $count['hours'] = floor(($diff - $count['years'] * 365 * 60 * 60 * 24 - $count['months'] * 30 * 60 * 60 * 24 - $count['days'] * 60 * 60 * 24) / (60 * 60));
        $count['minuts'] = floor(($diff - $count['years'] * 365 * 60 * 60 * 24 - $count['months'] * 30 * 60 * 60 * 24 - $count['days'] * 60 * 60 * 24 - $count['hours'] * 60 * 60) / 60);
        $count['seconds'] = floor(($diff - $count['years'] * 365 * 60 * 60 * 24 - $count['months'] * 30 * 60 * 60 * 24 - $count['days'] * 60 * 60 * 24 - $count['hours'] * 60 * 60 - $count['minuts'] * 60));
        return $count;
    }

    /**
     * Regresa un arreglo de días hábiles por medio de una fecha incial
     *
     * @param string $startDate fecha incial en formato unix
     * @param int $numberGetDays numero de dias que regresara
     * @param string $format
     * @return array 
     * @access public
     */
    public function businessDays($startDate, $numberGetDays = 0, $daysOfRest = array(), $format = 'Y-m-d') {
        list($year, $month, $day) = explode('-', $startDate);

        $businessDays = array();
        if (0 == $numberGetDays)
            $numberGetDays = date("t", mktime(0, 0, 0, $month, $day, $year));

        $total = $day + $numberGetDays;

        for ($i = $day; $i <= $total; $i++) {
            if (date("N", mktime(0, 0, 0, $month, $i, $year)) < 6 && !in_array(date('Y-m-d', mktime(0, 0, 0, $month, $i, $year)), $daysOfRest))
                $businessDays[] = date($format, mktime(0, 0, 0, $month, $i, $year));
            else
                $total++;

            if (count($businessDays) == $numberGetDays)
                break;
        }

        return $businessDays;
    }

    public function isDateBetween($dateCheck, $dateStart, $dateEnd) {

        if (strtotime($dateCheck) >= strtotime($dateStart) && strtotime($dateCheck) <= strtotime($dateEnd))
            return true;

        return false;
    }

    public function setStart($endDate, $days) {
        $holidays = array();
        $duration = ($days - 1) * (60 * 60 * 24);
        $total = $duration;
        $offdays = 0;
        $newoff = -1;
        while ($offdays != $newoff) {
            $newoff = $offdays;
            $startDate = date("Y-M-d", strtotime($endDate) - $total);
            $offdays = $this->getOffDays($startDate, $endDate, $holidays) * (60 * 60 * 24);
            $total = $duration + $offdays;
        }
        return $startDate;
    }

    public function setFinish($startDate, $days) {
        $holidays = array();
        $duration = ($days - 1) * (60 * 60 * 24);
        $total = $duration;
        $offdays = 0;
        $newoff = -1;
        while ($offdays != $newoff) {
            $newoff = $offdays;
            $endDate = date("Y-M-d", strtotime($startDate) + $total);
            $offdays = $this->getOffDays($startDate, $endDate, $holidays) * (60 * 60 * 24);
            $total = $duration + $offdays;
        }
        return $endDate;
    }

    /**
     * Calcula el número de días libres entre 2 fechas
     * 
     * @param date startDate Fecha inicial
     * @param date endDate Fecha final
     * @param array $holydays arreglo con fechas no laborales
     * @return date  arreglo de días feriados para incluirlos 
     */
    public function getOffDays($startDate, $endDate, $holidays) {
        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = (strtotime($endDate) - strtotime($startDate)) / 86400 + 1;

        $no_full_weeks = floor($days / 7);
        $no_remaining_days = fmod($days, 7);

        //It will return 1 if it's Monday,.. ,7 for Sunday
        $the_first_day_of_week = date("N", strtotime($startDate));
        $the_last_day_of_week = date("N", strtotime($endDate));

        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($the_first_day_of_week <= $the_last_day_of_week) {
            if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week)
                $no_remaining_days--;
            if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week)
                $no_remaining_days--;
        } else {
            if ($the_first_day_of_week <= 6) {
                //In the case when the interval falls in two weeks, there will be a weekend for sure
                $no_remaining_days = $no_remaining_days - 2;
            }
        }

        //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
        //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
        $workingDays = $no_full_weeks * 5;
        if ($no_remaining_days > 0) {
            $workingDays += $no_remaining_days;
        }

        /* //We subtract the holidays
          foreach($holidays as $holiday){
          $time_stamp=strtotime($holiday);
          //If the holiday doesn't fall in weekend
          if (strtotime($startDate) <= $time_stamp && $time_stamp <= strtotime($endDate) && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
          $workingDays--;
          }
          /* */
        return $days - $workingDays;
    }

    /**
     * @return date Semana actual 
     */
    public function currentWeek() {
        return date('W');
    }

    /**
     * @return date Arreglo de días de la semana actual 
     */
    public function currentWeekDays() {
        $week = date('W');
        $year = date('Y');

        $lastweek = $week;

        if ($lastweek == 0) {
            $week = 52;
            $year--;
        }

        $lastweek = sprintf("%02d", $lastweek);
        for ($i = 1; $i <= 7; $i++) {
            $arrdays[] = strtotime("$year" . "W$lastweek" . "$i");
        }
        return $arrdays;
    }

    /**
     * @return date Arreglo de días de la semana pasada 
     */
    public function lastWeekDays() {
        $week = date('W');
        $year = date('Y');

        $lastweek = $week - 1;

        if ($lastweek == 0) {
            $week = 52;
            $year--;
        }

        $lastweek = sprintf("%02d", $lastweek);
        for ($i = 1; $i <= 7; $i++) {
            $arrdays[] = strtotime("$year" . "W$lastweek" . "$i");
        }
        return $arrdays;
    }

    /**
     * @return date Primer día del mes actual 
     */
    public function firstOfMonth() {
        return date("Y-M-d", strtotime(date('m') . '/01/' . date('Y') . ' 00:00:00'));
    }

    /**
     * @return date Último día del mes actual 
     */
    public function lastOfMonth() {
        return date("Y-M-d", strtotime('-1 second', strtotime('+1 month', strtotime(date('m') . '/01/' . date('Y') . ' 00:00:00'))));
    }

    /**
     * @return date Primera semana del mes actual 
     */
    public function firstWekkofMonth() {
        return date("W", strtotime(date(date('Y') . "-" . date('m') . "-01")));
    }

    /**
     * @return date Última semana del mes actual 
     */
    public function lastWekkofMonth() {
        return date("W", strtotime(date(date('Y') . "-" . date('m') . "-" . date("t"))));
    }

    /**
     * Obtiene número de días entre 2 fechas
     * @param type $lowDate
     * @param type $highDate
     * @return int número de días
     */
    public function getNumberOfDays($lowDate, $highDate) {

        $lowDate = date_parse($lowDate);
        $highDate = date_parse($highDate);

        $days = (mktime($lowDate['hour'], $lowDate['minute'], $lowDate['second'], $lowDate['month'], $lowDate['day'], $lowDate['year']) - mktime($highDate['hour'], $highDate['minute'], $highDate['second'], $highDate['month'], $highDate['day'], $highDate['year'])) / (60 * 60 * 24);
        //$days = (strtotime($startDate) - strtotime($endDate)) / (60 * 60 * 24);

        $days = abs($days);
//        echo $days . '<br>';
        $days = floor($days);
        return $days;
    }

    public function getArrayMonthByLanguage($language = 1) {
        if ($language == 1) {
            return array(
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre");
        } elseif ($language == 2) {
            return array("January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December");
        }

        return array();
    }

}
