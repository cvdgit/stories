<?php


namespace common\helpers;


class SmartDate
{
    public static function dateSmart($timestamp, $time = false): ?string
    {
        // месяца
        $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        if ($time === true) {
            if (date('z') === date('z', $timestamp)) {
                return 'сегодня в ' . date('G:i', $timestamp);
            }
            else if (date('z') === (int) date('z', $timestamp) + 1) {
                return 'вчера в ' . date('G:i', $timestamp);
            }
            else {
                return (int)date('d', $timestamp) . ' ' . $months[(int)date('m', $timestamp) - 1] . (date('Y') !== date('Y', $timestamp) ? ' ' . date('Y', $timestamp) . ' г. ' : ' ') . 'в ' . date('G:i', $timestamp);
            }
        } else {
            return (int) date('d', $timestamp) . ' ' . $months[(int) date('m', $timestamp) - 1] . (date('Y') !== date('Y', $timestamp) ? ' ' . date('Y', $timestamp) . ' г. ' : '') ;
        }
    }
}