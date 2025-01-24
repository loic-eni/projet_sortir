<?php

namespace App\Utils;

use DateTime;

class DateTimeUtils
{
    public static string $HTML_DATETIME_FORMAT = "Y-m-d\TH:i";
    public static function parseHtmlDateTimeInput(string $datetime){
        return DateTime::createFromFormat(DateTimeUtils::$HTML_DATETIME_FORMAT, $datetime);
    }
}