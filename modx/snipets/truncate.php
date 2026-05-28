<?php
$text = strip_tags($text);
$text = str_replace('&nbsp;', ' ', $text);
$text = preg_replace('/\s+/', ' ', $text);
$text = trim($text);

$length = isset($length) ? (int)$length : 200;
if (mb_strlen($text, 'UTF-8') <= $length) return $text;
return mb_substr($text, 0, $length, 'UTF-8') . '...';
