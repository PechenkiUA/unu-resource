<?php
/**
 * SNIPPET (MODX Evolution): NewsAggregate
 * ────────────────────────────────────────────────────────────────
 * Агрегує новини/події з кількох джерел:
 *   1) site_ocontent (імпортований контент підрозділів)
 *   2) локальні документи (news-dir / events-dir)
 * і виводить їх єдиною стрічкою з пагінацією у стилі .pager
 * (3 перших + поточна з сусідами + 3 останніх) та фільтром за днем.
 *
 * ПАРАМЕТРИ
 *   &rowTpl       чанк рядка стрічки                             (обов'язково)
 *   &type         news | events                                  (обов'язково)
 *   &limit        елементів на сторінку (0 — всі)                (20)
 *   &display      синонім limit (сумісність зі старим викликом)
 *   &outerTpl     обгортка з [+wrapper+] та [+pagination+]       ('')
 *   &alt          чанк, якщо нічого не знайдено                  ('')
 *   &format       html | json                                    (html)
 *   &pageVar      GET-параметр номера сторінки                   (page)
 *   &dateVar      GET-параметр дня ?date=YYYY-MM-DD              (date)
 *   &pagerPH      плейсхолдер пагінатора                         (news.pager)
 *   &nextLabel    напис «вперед»                                 (далі »)
 *   &prevLabel    напис «назад» (порожньо — не показувати)       ('')
 *   &edge         к-сть номерів з кожного краю                   (3)
 *   &around       к-сть сусідів навколо поточної                 (1)
 *
 * ВИКЛИК (некешований):
 *   [!NewsAggregate? &rowTpl=`news.feed.item` &type=`news` &limit=`20`!]
 *   ...
 *   [+news.pager+]
 *
 * Пагінатор також підставляється у [+pagination+] всередині &outerTpl.
 */

if (!function_exists("Curl")){
    function Curl($url,$post='',$token='',$xhr=''){
        $tuCurl = curl_init();
        $header[] = "Accept-Encoding:";
        $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4";
        $header[] = "Accept: */*";
        if (!empty($token)) $header[] = "X-CSRF-Token: $token";
        if (!empty($xhr)) $header[] = "X-Requested-With: XMLHttpRequest";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        curl_setopt($tuCurl, CURLOPT_URL, $url);
        curl_setopt($tuCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36');
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $header);
        if (!empty($post)){
            curl_setopt($tuCurl, CURLOPT_POST, true);
            curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($tuCurl, CURLOPT_TIMEOUT, 200);
        $tuData = curl_exec($tuCurl);
        if (curl_errno($tuCurl)) $tuData = false;
        curl_close($tuCurl);
        return $tuData;
    }
}
if (!function_exists("uploadImageFile")){
    function uploadImageFile($f,$c,$id){
        $fn = str_replace($c,"",$f);
        $fn = str_replace("/images/","/images/dep_$id/",$fn);
        $fn = str_replace(" ","-",$fn);
        if (file_exists(MODX_BASE_PATH.$fn)) return $fn;
        $arrContextOptions = array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false));
        $file = @file_get_contents($f,false, stream_context_create($arrContextOptions));
        if (empty($file)) return '';
        $path = explode("/",$fn);
        $t = count($path);
        if ($t){
            $newdir = MODX_BASE_PATH.'assets';
            for ($i=0;$i<$t-1;$i++){
                if ($path[$i]=="assets" || $path[$i]=="" || $path[$i]=="/") continue;
                $newdir .= '/'.str_replace(" ","-",$path[$i]);
                @mkdir($newdir);
            }
            file_put_contents($newdir.'/'.$path[$t-1],$file);
        }
        return $fn;
    }
}

libxml_use_internal_errors(true);

/* ── Параметри ─────────────────────────────────────────────── */
$rowTpl    = isset($rowTpl) ? $rowTpl : '';
$format    = isset($format) ? $format : 'html';
$type      = isset($type) ? $type : '';
$limit     = isset($limit) ? (int)$limit : (isset($display) ? (int)$display : 20);
$pageVar   = isset($pageVar) ? $pageVar : 'page';
$dateVar   = isset($dateVar) ? $dateVar : 'date';
$srcVar    = isset($srcVar) ? $srcVar : 'src';
$pagerPH   = isset($pagerPH) ? $pagerPH : 'news.pager';
$nextLabel = isset($nextLabel) ? $nextLabel : 'далі »';
$prevLabel = isset($prevLabel) ? $prevLabel : '';
$edge      = isset($edge) ? max(1,(int)$edge) : 3;
$around    = isset($around) ? (int)$around : 1;
$showImage = isset($showImage) ? $showImage : false;
$showIntro = isset($showIntro) ? $showIntro : false;

$defiTypes = array('news'=>1,'events'=>2,'staff'=>3);
if (empty($rowTpl)) return '';
if (!isset($defiTypes[$type])) return '';

$curId  = (int)$modx->documentIdentifier;
$langID = isset($_SESSION['evoBabel_curLang']) ? $_SESSION['evoBabel_curLang'] : 'ua';

/* ── Фільтр за днем ?date=YYYY-MM-DD ───────────────────────── */
$dayStart = 0; $dayEnd = 0; $dayParam = '';
if (isset($_GET[$dateVar]) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $_GET[$dateVar], $m)) {
    $dayStart = mktime(0, 0, 0, (int)$m[2], (int)$m[3], (int)$m[1]);
    $dayEnd   = $dayStart + 86400;
    $dayParam = '&' . $dateVar . '=' . $_GET[$dateVar];
}

/* ── Фільтр за джерелом ?src=local | ?src=<depId> ──────────── */
$srcLocal = false;   // показувати лише локальні документи
$srcDep   = 0;       // показувати лише конкретний підрозділ
$srcParam = '';
if (isset($_GET[$srcVar]) && $_GET[$srcVar] !== '') {
    if ($_GET[$srcVar] === 'local') {
        $srcLocal = true;
        $srcParam = '&' . $srcVar . '=local';
    } elseif (ctype_digit((string)$_GET[$srcVar])) {
        $srcDep   = (int)$_GET[$srcVar];
        $srcParam = '&' . $srcVar . '=' . $srcDep;
    }
}

$items = array();
$deps = $importDates = $exDeps = array();

/* ── 1. Підрозділи ─────────────────────────────────────────── */
$rez = $modx->db->select("id,pagetitle",$modx->getFullTableName("site_content"),"published=1 and deleted=0 and template=25");
while($r=$modx->db->getRow($rez)){
    $r['dep-email'] = '';
    $r['dep-site'] = '';
    $r['dep-export'] = 1;
    $r['dep-filter'] = 2;
    $deps[$r['id']] = $r;
}
if (count($deps)) {
    $tv = array(54=>'dep-email',55=>'dep-site',56=>'dep-export',57=>'dep-filter');
    $rez = $modx->db->select("contentid,tmplvarid,value",$modx->getFullTableName("site_tmplvar_contentvalues"),"tmplvarid in (".implode(",",array_keys($tv)).") and contentid in (".implode(",",array_keys($deps)).")");
    while($r=$modx->db->getRow($rez)){
        $deps[$r['contentid']][$tv[$r['tmplvarid']]] = $r['value'];
    }
    $rez = $modx->db->select("dep,time",$modx->getFullTableName("site_import"),"type=".$defiTypes[$type]." and dep in (".implode(",",array_keys($deps)).")");
    while($r=$modx->db->getRow($rez)){
        $importDates[$r['dep']] = $r['time'];
    }

    /* ── 2. Імпортований контент (site_ocontent) ───────────── */
    if (!$srcLocal) {
        $oWhere = "dep in (".implode(",",array_keys($deps)).") and type=".$defiTypes[$type]." and lang='".$langID."'";
        if ($srcDep) {
            $oWhere .= " and dep = {$srcDep}";
        }
        if ($dayStart) {
            $oWhere .= " and pub_date >= {$dayStart} and pub_date < {$dayEnd}";
        }
        $rez = $modx->db->select("id,url,title,image,description,pub_date,showe,dep,published",$modx->getFullTableName("site_ocontent"),$oWhere);
        $now = time();
        while($r=$modx->db->getRow($rez)){
            $plh = $r;
            unset($plh['id']);
            $time = $plh['pub_date'];
            $plh['e.title'] = $plh['title'];
            $plh['target'] = ' target="_blank"';
            $plh['time'] = date("d.m.Y",$time);
            if (!isset($exDeps[$r['dep']])) $exDeps[$r['dep']] = array();
            $exDeps[$r['dep']][$plh['url']] = 1;
            if (!$plh['image']){
                $exDeps[$r['dep']][$plh['url']] = array('imerror'=>$r['id']);
            }
            if ($type=="events"){
                if ($time < $now) continue;
                $plh['time'] = '<div class="date"><span>'.date("d.m.Y H:i",$time).'</span></div>';
            }
            $plh['short-descr'] = $plh['description'];
            $plh['showImage'] = $showImage;
            $plh['showIntro'] = $showIntro;
            $plh['dep-name'] = $deps[$plh['dep']]['pagetitle'];
            $site = trim($deps[$r['dep']]['dep-site']);
            $site = str_replace("http://","https://",$site);
            $plh['dep-url'] = empty($site) ? '' : '<p><a href="'.$site.'" target="_blank" title="'.htmlspecialchars($plh['dep-name']).'" class="site-url">'.$plh['dep-name'].'</a></p>';
            while(isset($items[$time])) $time++;
            if ($plh['published']==1) $items[$time] = array('out' => $modx->parseChunk($rowTpl,$plh,"[+","+]"));
        }
    } // !srcLocal
}

/* ── 3. (вимкнено) Імпорт з зовнішніх сайтів ───────────────── */
$SKIP_EVO_SEARCH = TRUE;
if (!$SKIP_EVO_SEARCH && !empty($type) && count($deps)){
    include_once MODX_BASE_PATH."assets/plugins/evoSearch/plugin.class.php";
    $eSP = new evoSearchPlugin($modx, array('dicts'=>'rus,eng,uk_ua'));
    foreach ($deps as $did=>$dep){
        if ($dep['dep-export']==2) continue;
        $site = trim($dep['dep-site']);
        $site = str_replace("http://","https://",$site);
        if (empty($site)) continue;
        if (substr($site,-1)!="/") $site .= "/";
        $url = $site.$langID."/export.html";
        $post = array('type='.$type, "filtered=".($dep['dep-filter']==1 ? 1 : 0));
        $data = Curl($url,implode("&",$post));
        if ($data===false || empty($data)) continue;
        if (($xml=simplexml_load_string($data))===false) continue;
        $ty = isset($xml['type']) ? (string)$xml['type'] : '';
        if (empty($ty)) continue;
        if (isset($xml->items->empty)) continue;
        $fields = array('time'=>time());
        if (isset($importDates[$did])){
            $modx->db->update($fields,$modx->getFullTableName("site_import"),"dep=$did and type=".$defiTypes[$type]);
        } else {
            $fields['dep'] = $did;
            $fields['type'] = $defiTypes[$type];
            $modx->db->insert($fields,$modx->getFullTableName("site_import"));
        }
        foreach ($xml->items->item as $item){
            $pls = array();
            $pls['url'] = (string)$item->url;
            $pls['title'] = (string)$item->title;
            $pls['e.title'] = htmlspecialchars($pls['title']);
            $pls['target'] = ' target="_blank"';
            $publishedon = (int)$item->pub_date;
            $createdon = (int)$item->createdon;
            $pls['time'] = date("d.m.Y",max($createdon,$publishedon));
            $show = 1;
            if ($type=='events') {
                if ($publishedon < time()) continue;
                $show = isset($item->show) ? (int)$item->show : 1;
                $pls['time'] = $show==1 ? '<div class="date"><span>'.date("d.m.Y H:i",$publishedon).'</span></div>' : '';
            }
            $im_link = uploadImageFile((string)$item->image,$site,$did);
            $pls['image'] = $im_link;
            $pls['short-descr'] = trim((string)$item->description);
            $pls['showImage'] = $showImage;
            $pls['showIntro'] = $showIntro;
            $pls['dep-name'] = $dep['pagetitle'];
            $pls['dep-url'] = '<p><a href="'.$site.'" target="_blank" title="'.$dep['pagetitle'].'" class="site-url">'.$dep['pagetitle'].'</a></p>';
            $t = ($type=="events") ? $publishedon : max($createdon,$publishedon);
            if (isset($exDeps[$did][$pls['url']]['imerror']) && $im_link){
                $modx->db->update(array('image'=>$modx->db->escape($im_link)), $modx->getFullTableName("site_ocontent"), "id=".$exDeps[$did][$pls['url']]['imerror']);
            }
            if (isset($exDeps[$did][$pls['url']])) continue;
            $content = $pls['title']." ".$pls['short-descr'];
            $fields = array(
                'type'=>$defiTypes[$type],
                'url'=>$modx->db->escape($pls['url']),
                'title'=>$modx->db->escape($pls['title']),
                'image'=>$modx->db->escape($pls['image']),
                'description'=>$modx->db->escape($pls['short-descr']),
                'pub_date'=>$t,
                'showe'=>$show==1 ? 1 : 0,
                'published'=>1,
                'dep'=>$did,
                'lang'=>$langID,
                'content_with_tv'=>$modx->db->escape($modx->stripTags($content)),
                'content_with_tv_index'=>$modx->db->escape($eSP->Words2BaseForm(mb_strtoupper($content, 'UTF-8')))
            );
            $modx->db->insert($fields,$modx->getFullTableName("site_ocontent"));
            // фільтр за днем для щойно імпортованих
            if ($dayStart && ($t < $dayStart || $t >= $dayEnd)) continue;
            while(isset($items[$t])) $t++;
            $items[$t] = array('out' => $modx->parseChunk($rowTpl,$pls,"[+","+]"));
        }
    }
}

/* ── 4. Локальні документи ─────────────────────────────────── */
if ($type=="events"){
    $parent = isset($_SESSION['perevod']['events-dir']) ? $_SESSION['perevod']['events-dir'] : 0;
    $tv = array();
} else {
    $parent = isset($_SESSION['perevod']['news-dir']) ? $_SESSION['perevod']['news-dir'] : 0;
    $tv = array(14=>'photos',15=>'short-descr');
}
if ($srcDep) { $parent = 0; }   // обрано підрозділ — локальні не показуємо
if ($parent) {
    $docs = array();
    $dWhere = "published=1 and deleted=0 and parent in ($parent)";
    $rez = $modx->db->select("id,pagetitle,createdon,pub_date",$modx->getFullTableName("site_content"),$dWhere);
    while($r=$modx->db->getRow($rez)){
        $time = max($r['createdon'],$r['pub_date']);
        if ($dayStart && ($time < $dayStart || $time >= $dayEnd)) continue;
        $r['time'] = $time;
        $docs[$r['id']] = $r;
    }
    if (count($docs) && count($tv)){
        $rez = $modx->db->select("contentid,tmplvarid,value",$modx->getFullTableName("site_tmplvar_contentvalues"),"contentid in (".implode(",",array_keys($docs)).") and tmplvarid in (".implode(",",array_keys($tv)).")");
        while($r=$modx->db->getRow($rez)){
            $tvname = $tv[$r['tmplvarid']];
            $doc = $r['contentid'];
            $value = $r['value'];
            if ($tvname=='photos'){
                $tvname = 'image';
                if (!empty($value)) {
                    $photos = json_decode($value,true);
                    $photos = $photos['fieldValue'];
                    $value = isset($photos[0]) ? $photos[0]['image'] : '';
                }
            }
            $docs[$doc][$tvname] = $value;
        }
    }
    foreach ($docs as $docid=>$dv){
        $time = $dv['time'];
        $dv['url'] = $modx->makeUrl($docid);
        $dv['e.title'] = htmlspecialchars($dv['pagetitle']);
        $dv['title'] = $dv['pagetitle'];
        $dv['time'] = date("d.m.Y",$time);
        $dv['target'] = '';
        $dv['showImage'] = $showImage;
        $dv['showIntro'] = $showIntro;
        $out = $modx->parseChunk($rowTpl,$dv,"[+","+]");
        while(isset($items[$time])) $time++;
        $items[$time] = array('out'=>$out);
    }
}

/* ── 5. Сортування ─────────────────────────────────────────── */
if ($type=="events") { ksort($items); } else { krsort($items); }

if ($format=="json") return json_encode($items);

/* ── 6. Пагінація + рендер ─────────────────────────────────── */
$output = '';
$pager  = '';

if (count($items)) {
    $total = count($items);
    $page  = isset($_GET[$pageVar]) ? (int)$_GET[$pageVar] : 1;
    if ($page < 1) $page = 1;

    if ($limit > 0) {
        $pages = max(1, (int)ceil($total / $limit));
        if ($page > $pages) $page = $pages;
        $offset = ($page - 1) * $limit;
        $items = array_slice($items, $offset, $limit, true);

        if ($pages > 1) {
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

            $pi = array();
            $pi[] = '<span class="pager__lbl">Сторінки</span>';

            if ($prevLabel !== '' && $page > 1) {
                $url = $modx->makeUrl($curId, '', $pageVar . '=' . ($page - 1) . $dayParam . $srcParam);
                $pi[] = '<a class="prev" href="' . $url . '">' . $prevLabel . '</a>';
            }

            $prevNum = 0;
            foreach ($show as $i) {
                if ($prevNum && $i - $prevNum > 1) {
                    $pi[] = '<span class="pager__gap">…</span>';
                }
                if ($i === $page) {
                    $pi[] = '<a class="is-on">' . $i . '</a>';
                } else {
                    $url = $modx->makeUrl($curId, '', $pageVar . '=' . $i . $dayParam . $srcParam);
                    $pi[] = '<a href="' . $url . '">' . $i . '</a>';
                }
                $prevNum = $i;
            }

            if ($page < $pages) {
                $url = $modx->makeUrl($curId, '', $pageVar . '=' . ($page + 1) . $dayParam . $srcParam);
                $pi[] = '<a class="next" href="' . $url . '">' . $nextLabel . '</a>';
            }

            $pager = '<nav class="pager">' . implode('', $pi) . '</nav>';
        }
    }

    foreach ($items as $item) $output .= $item['out'];

    $outerTpl = isset($outerDepTpl) ? $outerDepTpl : (isset($outerTpl) ? $outerTpl : '');
    if (!empty($outerTpl) && !empty($output)){
        $tpl = $modx->getChunk($outerTpl);
        $tpl = str_replace("[+ditto+]",$output,$tpl);
        $tpl = str_replace("[+wrapper+]",$output,$tpl);
        $tpl = str_replace("[+pagination+]",$pager,$tpl);
        $output = $tpl;
    }
} else {
    $alt = isset($alt) ? $alt : '';
    if (!empty($alt)) $output = $modx->getChunk($alt);
}

$modx->setPlaceholder($pagerPH, $pager);

return $output;
