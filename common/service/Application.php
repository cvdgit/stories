<?php
namespace common\service;

use DateTime;
use DateInterval;

class Application {

    /**
     * Подсчет количества дней между датами
     * @param $finish - конечная дата
     * @return number
     */
    public static function getDayCount($finish)
    {
        $now = new DateTime('00:00');       
        $finish = DateTime::createFromFormat("Y-m-d H:i:s", $finish);
        $finish = DateTime::createFromFormat("!Y-m-d", $finish->format("Y-m-d"));
        $interval = $now->diff($finish);

        return ($finish >= $now) ? $interval->d : null;
    }

    /**
     * Добавить к дате количество месяцев
     * @param $date - дата
     * @param $count_mounth - количество месяцев
     * @return date
     */
    public static function addMounth($date, $count_mounth) {
        return $date->add(new DateInterval('P'.$count_mounth.'M'));
    }
    
}
