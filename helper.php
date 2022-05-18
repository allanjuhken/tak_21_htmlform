<?php
// kas kuupäev on korrektne
function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
// kas kuupäev on tulevikus
function isDateFuture($date) { // Date on YYYY-MM-DD
    $current_date = new DateTime();
    $user_date = new DateTime($date);
    if($user_date > $current_date) {
        return true;
    }
    return false;
}