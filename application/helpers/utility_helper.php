<?php

function seconds_to_hms($secs) {
    $hms = [];
    $hms['hours'] = floor($secs / 3600);
    $hms['minutes'] = floor($secs / 60) % 60;
    $hms['seconds'] = floor($secs) % 60;
    return $hms;
}

function format_hms($hms) {
    return $hms['hours'].' jam '.$hms['minutes'].' menit '.$hms['seconds'].' detik';
}

?>
