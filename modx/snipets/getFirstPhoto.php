<?php
if (empty($photos)) return '';

$data = json_decode($photos, true);

if (empty($data['fieldValue'])) return '';

foreach ($data['fieldValue'] as $item) {
    if (!empty($item['image'])) {
        $image = $item['image'];
        if (strpos($image, '/') !== 0) {
            $image = '/' . $image;
        }
        return $image;
    }
}

return '';
