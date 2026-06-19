<?php
$type = isset($type)? $type : '';
$data = isset($data)? $data : null;
$icon =  isset($icon)? $icon : null;

if(!$data)
    return;

$data = strip_tags($data);

$listContact = explode(',',$data);



$out = '';

foreach($listContact as $item){

    $href = '';
    $content = $item;
    $value = $item;
    $out .= '<span class="contact_item">';

    switch ($type){

        case 'email':

            $href = 'mailto:';
            break;

        case 'phone':

            $value = preg_replace('/[^0-9+]/m','',$item);

            if(strpos($item,'Viber')){
                $href = 'viber://chat?number=';
            }else{
                $href = 'tel:';
            }


            break;
    }

    if($icon){
        $out .= sprintf('<i class="fa %s"></i>',$icon);
    }




    $out .= "<a href='{$href}{$value}'>{$item}</a>";
    $out .= "</span>";

}

return $out;
