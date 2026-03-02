<?php 
//VERIF//
function isDateValid($date) {
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $date);

    if($d && $d->format($format) === $date) {
        return true;
    } else {
        return false;
    }
}

function hourToTime($hour) {
    $hour = str_replace("h", ":", $hour);
    $hour .= ":00";
    return $hour;
}
function isHourValid($hour) {
    $format = 'H:i:s';
    $d = DateTime::createFromFormat($format, $hour);

    if($d && $d->format($format) === $hour) {
        return true;
    } else {
        return false;
    }
}

function isServiceOk($service) {
    $okServices = ['midi', 'soir'];
    return(in_array($service, $okServices));
}
function serviceTextToInt($service) {
    if($service === "midi") {
        return 1;
    } elseif($service === "soir") {
        return 2;
    }
}