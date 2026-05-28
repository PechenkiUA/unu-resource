<?php
$tpl     = isset($tpl)     ? $tpl                  : '';
$startId = isset($startId) ? (int)$startId          : $modx->documentIdentifier;
$fields  = isset($fields)  ? explode(',', $fields)  : ['icon_svg'];

if (empty($tpl)) return '';

// Отримуємо дочірні документи і розділи
$docs = $modx->db->makeArray(
    $modx->db->select(
        'id, pagetitle, menutitle, isfolder',
        $modx->getFullTableName('site_content'),
        "parent = {$startId} AND published = 1 AND deleted = 0",
        'menuindex ASC'
    )
);

if (empty($docs)) return '';

$output = '';

foreach ($docs as $key => $doc) {
    $placeholders = [
        'num'      => sprintf('%02d', $key + 1),
        'id'       => $doc['id'],
        'title'    => !empty($doc['menutitle']) ? $doc['menutitle'] : $doc['pagetitle'],
        'link'     => $modx->makeUrl($doc['id']),
        'active'   => ($doc['id'] == $modx->documentIdentifier) ? 'active' : '',
        'isfolder' => $doc['isfolder'],
        'icon'     => '',
        'class'    => '',
    ];

    // TV поля
    foreach ($fields as $field) {
        $field = trim($field);
        $placeholders[$field] = $modx->getTemplateVar($field, '*', $doc['id'])['value'] ?? '';
    }

    $output .= $modx->parseChunk($tpl, $placeholders, '[+', '+]');
}

return $output;
