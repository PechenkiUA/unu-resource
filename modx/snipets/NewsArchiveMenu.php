<?php
// Враховуємо id префікс Ditto (&id=`cat-news` → параметри cat-news_year, cat-news_month)
$dittoId   = 'cat-news_'; // відповідає &id у Ditto
$newsParent = $dir?: 10; // ID батьківської сторінки новин — замінити на свій

$sql = "
    SELECT 
        YEAR(FROM_UNIXTIME(pub_date))  AS y,
        MONTH(FROM_UNIXTIME(pub_date)) AS m,
        COUNT(*) AS cnt
    FROM " . $modx->getFullTableName('site_content') . "
    WHERE parent = " . (int)$newsParent . "
      AND pub_date > 0
      AND published = 1
      AND deleted = 0
    GROUP BY y, m
    ORDER BY y DESC, m DESC
";

$months_ua = [
    1=>'Січень', 2=>'Лютий',   3=>'Березень', 4=>'Квітень',
    5=>'Травень', 6=>'Червень', 7=>'Липень',   8=>'Серпень',
    9=>'Вересень',10=>'Жовтень',11=>'Листопад',12=>'Грудень'
];

$rows  = $modx->db->makeArray($modx->db->query($sql));
if (!$rows) return '';

$activeY = isset($_GET[$dittoId.'year'])  ? (int)$_GET[$dittoId.'year']  : 0;
$activeM = isset($_GET[$dittoId.'month']) ? (int)$_GET[$dittoId.'month'] : 0;

$pageId = $modx->documentObject['id'];
$out = '<nav class="news-archive" aria-label="Архів новин">';

// Опціональний заголовок
$out .= '<div class="news-archive__header">';
$out .= '<span class="news-archive__title">Архів новин</span>';
$out .= '</div>';

$yearCounts = [];
// Рахуємо суму по роках
$yearCounts = [];
foreach ($rows as $r) {
    $y = (int)$r['y'];
    if (!isset($yearCounts[$y])) $yearCounts[$y] = 0;
    $yearCounts[$y] += (int)$r['cnt'];
}

$prevY = null;
foreach ($rows as $r) {
    $y = (int)$r['y'];
    $m = (int)$r['m'];

    if (!isset($yearCounts[$y])) $yearCounts[$y] = 0;


    if ($y !== $prevY) {
        if ($prevY !== null) $out .= '</ul></li>';

        $ycnt = $yearCounts[$y]; // ← ось тут

        $isOpenYear = ($activeY === $y) ? ' is-open' : '';
        $out .= '<li class="news-archive__year-block">';
        $out .= "<button class=\"news-archive__year-toggle{$isOpenYear}\" type=\"button\">";
        $out .= "<span class=\"news-archive__year-label\">{$y}</span>";
        // лічильник рахуй окремо
        $out .= "<span class=\"news-archive__year-count\">{$ycnt}</span>";javascript:;
        $out .= '<span class="news-archive__chevron"><i class="bi bi-arrow-down-short"></i></span>';
        $out .= '</button>';
        $out .= "<ul class=\"news-archive__months{$isOpenYear}\">";
        $prevY = $y;
    }

    $url    = $modx->makeUrl($pageId, '', "{$dittoId}year={$y}&{$dittoId}month={$m}");
    $active = ($activeY === $y && $activeM === $m) ? ' is-active' : '';
    $out .= "<li><a href=\"{$url}\" class=\"news-archive__month-link{$active}\">";
    $out .= "<span>{$months_ua[$m]}</span>";
    $out .= "<span class=\"news-archive__month-count\">{$r['cnt']}</span>";
    $out .= '</a></li>';
}
if ($prevY !== null) $out .= '</ul></li>';

$out .= '</nav>';


if ($activeY) {
    if ($activeM) {
        $yUrl  = $modx->makeUrl($pageId, '', "{$dittoId}year={$activeY}");
        $crumb = " &raquo; <a href=\"{$yUrl}\">{$activeY}</a>"
            . " &raquo; <span>{$months_ua[$activeM]} {$activeY}</span>";
    } else {
        $crumb = " &raquo; <span>{$activeY}</span>";
    }
    $crumbJs = json_encode($crumb, JSON_UNESCAPED_UNICODE);
    $out .= <<<HTML
<script>
(function(){
  var bc = document.querySelector('.b-breadcrumbs .B_crumbBox') || document.querySelector('.breadcrumbs .B_crumbBox');
  if (bc) bc.insertAdjacentHTML('beforeend', {$crumbJs});
})();
</script>
HTML;
}


return $out;
