<?php
/**
 * SNIPPET (MODX Evolution): UNUMegaMenu
 * ────────────────────────────────────────────────────────────────
 * 3-рівневе мега-меню кафедри економіки УНУ, побудоване з дерева
 * ресурсів MODX (без Wayfinder — бо Wayfinder не дає різну розмітку
 * для рівня-1 «панель» і рівня-2 «колонка-група»).
 *
 *   Рівень 1  — розділи у синьому рядку (.nav__item / .nav__link)
 *   Рівень 2  — або заголовки колонок (.mega__group-head), або
 *               прості посилання (.mega__link), якщо 3-го рівня немає
 *   Рівень 3  — підпункти колонки (.mega__sublink)
 *
 * Виводить лише <li>…</li> верхнього рівня — обгортку
 * <ul class="nav" id="nav"> дає чанк mega.header.
 *
 * ПАРАМЕТРИ
 *   &startId      id контейнера, чиї діти = пункти меню (0 — корінь)     (0)
 *   &level        макс. глибина меню: 1, 2 або 3                         (3)
 *   &excludeDocs  список id для виключення з меню (через кому)           ('')
 *   &blurbField   поле опису розділу для фіче-колонки панелі            (description)
 *                 (стандартне поле документа «Опис»; можна longtitle)
 *   &wideAfter    к-сть підпунктів, після якої колонка займає 2 шпальти  (8)
 *   &tplTopLink   чанк: верхній пункт без панелі                         (mega.top.link)
 *   &tplPanel     чанк: верхній пункт із панеллю                         (mega.top.panel)
 *   &tplGroup     чанк: група рівня-2 з підпунктами                      (mega.group)
 *   &tplGroupSolo чанк: група рівня-2 без підпунктів                     (mega.group.solo)
 *   &tplLink      чанк: посилання у пласкій панелі                       (mega.link)
 *   &tplSublink   чанк: підпункт рівня-3                                 (mega.sublink)
 *
 * ВИКЛИК (некешований):  [!UNUMegaMenu? &startId=`0` &level=`3` &excludeDocs=``!]
 */

$startId     = isset($startId)     ? (int)$startId     : 0;
$level       = isset($level)       ? (int)$level       : 3;
$excludeDocs = isset($excludeDocs) ? $excludeDocs      : '';
$blurbField  = isset($blurbField)  ? $blurbField       : 'description';
$wideAfter   = isset($wideAfter)   ? (int)$wideAfter   : 8;
$tplTopLink  = isset($tplTopLink)  ? $tplTopLink       : 'mega.top.link';
$tplPanel    = isset($tplPanel)    ? $tplPanel         : 'mega.top.panel';
$tplGroup    = isset($tplGroup)    ? $tplGroup         : 'mega.group';
$tplGroupSolo= isset($tplGroupSolo)? $tplGroupSolo     : 'mega.group.solo';
$tplLink     = isset($tplLink)     ? $tplLink          : 'mega.link';
$tplSublink  = isset($tplSublink)  ? $tplSublink       : 'mega.sublink';
$tplSearch   = isset($tplSearch)  ? $tplSearch         : 'mega.search';

if ($level < 1) { $level = 1; }

/* список виключених id */
$exclude = array();
if (trim($excludeDocs) !== '') {
    foreach (explode(',', $excludeDocs) as $e) {
        $e = (int)trim($e);
        if ($e > 0) { $exclude[$e] = true; }
    }
}

$tbl = $modx->getFullTableName('site_content');

/* активний шлях (для підсвічування поточного розділу) */
$curId   = (int)$modx->documentIdentifier;
$ancestors = array($curId);
if (method_exists($modx, 'getParentIds')) {
    $pp = $modx->getParentIds($curId);
    if (is_array($pp)) { $ancestors = array_merge($ancestors, array_map('intval', $pp)); }
}
$ancestors = array_flip($ancestors);

/* діти документа, видимі в меню, у порядку menuindex */
function unu_children($modx, $tbl, $parent, $blurbField, $exclude) {
    $fields = "id, pagetitle, menutitle, longtitle, {$blurbField} AS blurb, isfolder, type, content";
    $where  = "parent = " . (int)$parent . " AND published = 1 AND deleted = 0 AND hidemenu = 0";
    if (!empty($exclude)) {
        $where .= " AND id NOT IN (" . implode(',', array_map('intval', array_keys($exclude))) . ")";
    }
    $rs  = $modx->db->select($fields, $tbl, $where, 'menuindex ASC, id ASC');
    $out = array();
    while ($row = $modx->db->getRow($rs)) { $out[] = $row; }
    return $out;
}

/* напис пункту + коректний URL (із підтримкою посилань-weblink) */
function unu_title($r){ return trim($r['menutitle']) !== '' ? $r['menutitle'] : $r['pagetitle']; }
function unu_url($modx, $r){
    if (isset($r['type']) && $r['type'] === 'reference' && trim($r['content']) !== '') {
        return $r['content'];
    }
    return $modx->makeUrl((int)$r['id']);
}

$out = array();

foreach (unu_children($modx, $tbl, $startId, $blurbField, $exclude) as $top) {
    $tId    = (int)$top['id'];
    $tTitle = unu_title($top);
    $tUrl   = unu_url($modx, $top);
    $active = isset($ancestors[$tId]) ? ' active' : '';

    /* рівень 1 — лише верхні пункти, без панелі */
    $kids = ($level >= 2) ? unu_children($modx, $tbl, $tId, $blurbField, $exclude) : array();

    /* верхній пункт без дітей — звичайне посилання, без панелі */
    if (empty($kids)) {
        $out[] = $modx->parseChunk($tplTopLink, array(
            'title' => $tTitle, 'url' => $tUrl, 'active' => $active,
        ), '[+', '+]');
        continue;
    }

    /* чи є хоч в одного нащадка свої діти → потрібні групи (3-й рівень) */
    $hasGrand = false;
    $kidsKids = array();
    if ($level >= 3) {
        foreach ($kids as $k) {
            $sub = unu_children($modx, $tbl, (int)$k['id'], $blurbField, $exclude);
            $kidsKids[(int)$k['id']] = $sub;
            if (!empty($sub)) { $hasGrand = true; }
        }
    }

    if ($hasGrand) {
        $cells = '';
        foreach ($kids as $k) {
            $kTitle = unu_title($k);
            $kUrl   = unu_url($modx, $k);
            $subs   = $kidsKids[(int)$k['id']];
            if (!empty($subs)) {
                $sublist = '';
                foreach ($subs as $s) {
                    $sublist .= $modx->parseChunk($tplSublink, array(
                        'title' => unu_title($s), 'url' => unu_url($modx, $s),
                    ), '[+', '+]');
                }
                $wide = (count($subs) > $wideAfter) ? ' span-2' : '';
                $cells .= $modx->parseChunk($tplGroup, array(
                    'title' => $kTitle, 'url' => $kUrl, 'wide' => $wide, 'sublist' => $sublist,
                ), '[+', '+]');
            } else {
                $cells .= $modx->parseChunk($tplGroupSolo, array(
                    'title' => $kTitle, 'url' => $kUrl,
                ), '[+', '+]');
            }
        }
        $content = '<div class="mega__groups">' . $cells . '</div>';
    } else {
        $links = '';
        foreach ($kids as $k) {
            $links .= $modx->parseChunk($tplLink, array(
                'title' => unu_title($k), 'url' => unu_url($modx, $k),
            ), '[+', '+]');
        }
        $content = '<div class="mega__links">' . $links . '</div>';
    }

    $out[] = $modx->parseChunk($tplPanel, array(
        'title'   => $tTitle,
        'url'     => $tUrl,
        'active'  => $active,
        'blurb'   => trim((string)$top['blurb']),
        'content' => $content,
    ), '[+', '+]');
}

return implode("\n", $out);
