<?php
/**
 * SNIPPET (MODX Evolution): NewsArchive
 * ────────────────────────────────────────────────────────────────
 * Виводить стрічку новин з посторінковою навігацією, чия розмітка
 * 1-в-1 відповідає блоку .pager у дизайні (номери сторінок + «далі »»).
 *
 * Повертає HTML рядків стрічки, а готовий пагінатор кладе у плейсхолдер
 * (за замовч. [+news.pager+]) — щоб шаблон сам вирішував, де його показати.
 *
 * ПАРАМЕТРИ
 *   &parents      id контейнера(ів) новин через кому            (обов'язково)
 *   &depth        глибина обходу нащадків                        (5)
 *   &limit        новин на сторінку                              (20)
 *   &tpl          чанк рядка стрічки                             (news.feed.item)
 *   &tvList       TV для рядка через кому, напр. image,author    ('')
 *   &dateField    поле дати (pub_date|createdon)                 (pub_date)
 *   &dateFormat   формат date() для [+date+]                     (H:i)
 *   &introField   поле тексту                                    (introtext)
 *   &introMode    режим генерації  intro якщо sentence - генеруємо по реяенях.
 *   &introLimit   обрізати текст до N символів (0 — не різати)   (220)
 *   &pageVar      GET-параметр номера сторінки                   (page)
 *   &pagerPH      ім'я плейсхолдера пагінатора                   (news.pager)
 *   &nextLabel    напис «вперед»                                 (далі »)
 *   &prevLabel    напис «назад» (порожньо — не показувати)       ('')
 *   &range        к-сть номерів навколо поточного (0 — всі)      (0)
 *
 * ВИКЛИК (некешований):
 *   <div class="feed">[!NewsArchive? &parents=`12` &limit=`20`!]</div>
 *   ...
 *   [+news.pager+]
 */

$parents    = isset($parents) ? array_map('intval', explode(',', $parents)) : array();
$depth      = isset($depth) ? (int)$depth : 5;
$limit      = isset($limit) ? max(1, (int)$limit) : 20;
$tpl        = isset($tpl) ? $tpl : 'news.feed.item';
$tvList     = isset($tvList) && $tvList !== '' ? array_map('trim', explode(',', $tvList)) : array();
$dateField  = isset($dateField) ? $dateField : 'pub_date';
$dateFormat = isset($dateFormat) ? $dateFormat : 'H:i';
$introField = isset($introField) ? $introField : 'introtext';
$introLimit = isset($introLimit) ? (int)$introLimit : 220;
$introMode 	= isset($introMode) ? $introMode : 'symbol';
$pageVar    = isset($pageVar) ? $pageVar : 'page';
$pagerPH    = isset($pagerPH) ? $pagerPH : 'news.pager';
$nextLabel  = isset($nextLabel) ? $nextLabel : 'далі »';
$prevLabel  = isset($prevLabel) ? $prevLabel : '';
$showImage  = isset($showImage) ? $showImage : false;
$showIntro  = isset($showIntro) ? $showIntro : false;
$range      = isset($range) ? (int)$range : 0;
$dateVar = isset($dateVar) ? $dateVar : 'date';  // GET-параметр дня


if (empty($parents)) { return ''; }

$tbl    = $modx->getFullTableName('site_content');
$curId  = (int)$modx->documentIdentifier;

/* 1. Зібрати всіх нащадків контейнерів */
$ids   = $parents;
$stack = $parents;
while (!empty($stack)) {
    $p  = (int)array_shift($stack);
    $rs = $modx->db->select('id', $tbl, "parent = {$p} AND deleted = 0");
    while ($row = $modx->db->getRow($rs)) {
        $cid     = (int)$row['id'];
        $ids[]   = $cid;
        $stack[] = $cid;
    }
}
$ids    = array_unique(array_filter($ids));
$inList = implode(',', $ids);
$where  = "parent IN ({$inList}) AND published = 1 AND deleted = 0";


$dayFilter = '';
if (isset($_GET[$dateVar]) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $_GET[$dateVar], $m)) {
    $dayStart = mktime(0, 0, 0, (int)$m[2], (int)$m[3], (int)$m[1]);
    $dayEnd   = $dayStart + 86400;   // початок наступного дня
    $where   .= " AND {$dateField} >= {$dayStart} AND {$dateField} < {$dayEnd}";
}

/* 2. Загальна к-сть і к-сть сторінок */
$rs    = $modx->db->select('COUNT(*) AS c', $tbl, $where);
$total = (int)$modx->db->getValue($rs);
$pages = max(1, (int)ceil($total / $limit));

/* 3. Поточна сторінка */
$page = isset($_GET[$pageVar]) ? (int)$_GET[$pageVar] : 1;
if ($page < 1) { $page = 1; }
if ($page > $pages) { $page = $pages; }
$offset = ($page - 1) * $limit;

/* 4. Вибірка документів сторінки */
$fields = "id, pagetitle, {$introField}, {$dateField}, pub_date, createdon";
$rs = $modx->db->select($fields, $tbl, $where, 'pub_date DESC, createdon DESC', "{$offset}, {$limit}");

$out = array();
while ($row = $modx->db->getRow($rs)) {
    $id = (int)$row['id'];

    // дата
    $ts = (int)$row[$dateField];
    if ($ts <= 0) { $ts = (int)$row['pub_date']; }
    if ($ts <= 0) { $ts = (int)$row['createdon']; }
    $date = $ts > 0 ? date($dateFormat, $ts) : '';

    // текст
    $intro = strip_tags((string)$row[$introField]);

    if($introMode == 'sentence'){
        $explodeText = explode('.',trim($intro));

        if(isset($explodeText[0])){
            $intro = $explodeText[0];
        }

    }else if($introMode == 'symbol'){
        if ($introLimit > 0 && mb_strlen($intro, 'UTF-8') > $introLimit) {
            $intro = rtrim(mb_substr($intro, 0, $introLimit, 'UTF-8')) . '…';
        }
    }




    $ph = array(
        'id'        => $id,
        'pagetitle' => $row['pagetitle'],
        'introtext' => $intro,
        'showImage' => $showImage,
        'showIntro' => $showIntro,
        'date'      => $date,
        'url'       => $modx->makeUrl($id),
    );

    // TV
    if (!empty($tvList)) {
        $tvs = $modx->getTemplateVarOutput($tvList, $id);
        if (is_array($tvs)) { $ph = array_merge($ph, $tvs); }
    }

    $out[] = $modx->parseChunk($tpl, $ph, '[+', '+]');
}

/* 5. Пагінатор під .pager (3 перших + 3 останніх) */
$pager = '';
if ($pages > 1) {
    $edge    = 3;   // скільки з країв
    $around  = 1;   // скільки сусідів навколо поточної

    if ($pages <= $edge * 2) {
        $show = range(1, $pages);
    } else {
        $show = array_merge(
            range(1, $edge),
            range(max(1, $page - $around), min($pages, $page + $around)),
            range($pages - $edge + 1, $pages)
        );
    }
    $show = array_values(array_unique($show));
    sort($show);

    $items = array();
    $items[] = '<span class="pager__lbl">Сторінки</span>';

    if ($prevLabel !== '' && $page > 1) {
        $url = $modx->makeUrl($curId, '', $pageVar . '=' . ($page - 1));
        $items[] = '<a class="prev" href="' . $url . '">' . $prevLabel . '</a>';
    }

    $prev = 0;
    foreach ($show as $i) {
        if ($prev && $i - $prev > 1) {
            $items[] = '<span class="pager__gap">…</span>';
        }
        if ($i === $page) {
            $items[] = '<a class="is-on">' . $i . '</a>';
        } else {
            $url = $modx->makeUrl($curId, '', $pageVar . '=' . $i);
            $items[] = '<a href="' . $url . '">' . $i . '</a>';
        }
        $prev = $i;
    }

    if ($page < $pages) {
        $url = $modx->makeUrl($curId, '', $pageVar . '=' . ($page + 1));
        $items[] = '<a class="next" href="' . $url . '">' . $nextLabel . '</a>';
    }

    $pager = '<nav class="pager">' . implode('', $items) . '</nav>';
}
$modx->setPlaceholder($pagerPH, $pager);

return implode("\n", $out);
