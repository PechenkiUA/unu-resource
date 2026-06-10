<?php
/**
 * SNIPPET (MODX Evolution): NewsAggregateData
 * ────────────────────────────────────────────────────────────────
 * Збирає дати публікацій з тих самих джерел, що й NewsAggregate:
 *   1) site_ocontent (імпортований контент підрозділів)
 *   2) локальні документи (news-dir / events-dir з $_SESSION['perevod'])
 * і повертає JSON-карту для календаря архіву (archive-news.js).
 *
 * Параметри:
 *   &type — news | events                                  (news)
 *
 * Виклик (НЕкешований, до підключення archive-news.js):
 *   <script>window.NEWS_ARCHIVE = [!NewsAggregateData? &type=`news`!];</script>
 *
 * Повертає: {"days":{"2026-5":[1,2,3]},"years":[{"y":2026,"n":48}]}
 * (місяць 0-індексний — як у JS Date)
 */

$type = isset($type) ? $type : 'news';
$defiTypes = array('news'=>1,'events'=>2,'staff'=>3);
if (!isset($defiTypes[$type])) { return '{"days":{},"years":[]}'; }

$langID = isset($_SESSION['evoBabel_curLang']) ? $_SESSION['evoBabel_curLang'] : 'ua';
$now    = time();

/* Фільтр за джерелом ?src=local | ?src=<depId> (дзеркально до NewsAggregate) */
$srcVar   = isset($srcVar) ? $srcVar : 'src';
$srcLocal = false;
$srcDep   = 0;
if (isset($_GET[$srcVar]) && $_GET[$srcVar] !== '') {
    if ($_GET[$srcVar] === 'local') {
        $srcLocal = true;
    } elseif (ctype_digit((string)$_GET[$srcVar])) {
        $srcDep = (int)$_GET[$srcVar];
    }
}

$stamps = array();   // список timestamp'ів усіх публікацій

/* ── 1. Підрозділи (template=25) ───────────────────────────── */
$deps = array();   // id => pagetitle
$rez = $modx->db->select("id,pagetitle", $modx->getFullTableName("site_content"), "published=1 and deleted=0 and template=25");
while ($r = $modx->db->getRow($rez)) {
    $deps[(int)$r['id']] = $r['pagetitle'];
}

/* ── 2. Імпортований контент (site_ocontent) ───────────────── */
if (count($deps) && !$srcLocal) {
    $oWhere = "dep in (".implode(",",array_keys($deps)).") and type=".$defiTypes[$type]." and lang='".$langID."' and published=1";
    if ($srcDep) { $oWhere .= " and dep = {$srcDep}"; }
    $rez = $modx->db->select(
        "pub_date",
        $modx->getFullTableName("site_ocontent"),
        $oWhere
    );
    while ($r = $modx->db->getRow($rez)) {
        $ts = (int)$r['pub_date'];
        if ($ts <= 0) { continue; }
        if ($type=="events" && $ts < $now) { continue; }   // як у NewsAggregate
        $stamps[] = $ts;
    }
}

/* ── 3. Локальні документи ─────────────────────────────────── */
if ($type=="events"){
    $parent = isset($_SESSION['perevod']['events-dir']) ? $_SESSION['perevod']['events-dir'] : 0;
} else {
    $parent = isset($_SESSION['perevod']['news-dir']) ? $_SESSION['perevod']['news-dir'] : 0;
}
if ($srcDep) { $parent = 0; }   // обрано підрозділ — локальні не враховуємо
if ($parent) {
    $rez = $modx->db->select(
        "createdon, pub_date",
        $modx->getFullTableName("site_content"),
        "published=1 and deleted=0 and parent in ($parent)"
    );
    while ($r = $modx->db->getRow($rez)) {
        $ts = max((int)$r['createdon'], (int)$r['pub_date']);
        if ($ts <= 0) { continue; }
        $stamps[] = $ts;
    }
}

/* ── 4. Карта днів і роки ──────────────────────────────────── */
$days  = array();   // "Y-m(0idx)" => [days]
$years = array();   // year => count
foreach ($stamps as $ts) {
    $y   = (int)date('Y', $ts);
    $m0  = (int)date('n', $ts) - 1;   // 0-індексний місяць
    $d   = (int)date('j', $ts);
    $key = $y . '-' . $m0;
    if (!isset($days[$key])) { $days[$key] = array(); }
    if (!in_array($d, $days[$key], true)) { $days[$key][] = $d; }
    if (!isset($years[$y])) { $years[$y] = 0; }
    $years[$y]++;
}
foreach ($days as $k => $arr) { sort($days[$k]); }
krsort($years);
$yearsOut = array();
foreach ($years as $y => $n) {
    $yearsOut[] = array('y' => $y, 'n' => $n);
}

/* ── 5. Список джерел для UI фільтра ───────────────────────── */
$sources = array();
$sources[] = array('id' => 'local', 'name' => 'Новини сайту');
foreach ($deps as $did => $name) {
    $sources[] = array('id' => $did, 'name' => $name);
}

return json_encode(array('days' => $days, 'years' => $yearsOut, 'sources' => $sources));
