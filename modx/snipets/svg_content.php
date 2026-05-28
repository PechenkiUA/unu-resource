<?php
if(empty($svg))
    return false;


$svgFile = file_get_contents($svg);

return $svgFile;
