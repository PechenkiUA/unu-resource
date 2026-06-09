<?php
/**
 * SNIPPET: NewsToday
 * Повертає поточну дату українською: «3 червня 2026».
 * Використання: [[!NewsToday]]
 */
$months = array(1=>'січня',2=>'лютого',3=>'березня',4=>'квітня',5=>'травня',
    6=>'червня',7=>'липня',8=>'серпня',9=>'вересня',10=>'жовтня',
    11=>'листопада',12=>'грудня');
$tz = isset($tz) ? $tz : 'Europe/Kyiv';

if (isset($_GET['date'])) {

    $dt = new DateTime($_GET['date'], new DateTimeZone($tz));
    return $dt->format('j') . ' ' . $months[(int)$dt->format('n')] . ' ' . $dt->format('Y');
}else{

    /*$dt = new DateTime('now', new DateTimeZone($tz));*/
}
