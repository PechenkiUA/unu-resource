{{header}}
</head>
<body>
{{site_header}}
{{breadcrumbs}}

<link rel="stylesheet" href="[(base_url)]assets/templates/html/css/news/archive.css">

<section class="npage">
    <div class="shell">
        <div class="npage__grid">

            <div>
                <div class="npage__head">
                    <h1 class="display">[*pagetitle*]</h1>
                    <div class="npage__date">[!NewsToday!]</div>
                </div>

                <div class="feed">
                    [!NewsArchive?
                    &parents=`[%news-dir%]`
                    &depth=`1`
                    &limit=`20`
                    &tpl=`news.feed.item`
                    &dateField=`pub_date`
                    &dateFormat=`d.m.Y`
                    &tvList=`photos`
                    &introMode=`sentence`
                    &introLimit=`1`
                    &introField=`content`
                    &showImage=`true`
                    &showIntro=`true`
                    !]
                </div>
                [+news.pager+]
            </div>

            <aside class="rail">

                <div>
                    <h3 class="rail__title">Архів</h3>
                    <div class="cal" id="cal" data-url="[~[*id*]~]">
                        <div class="cal__bar">
                            <div class="cal__month" id="calMonth">—</div>
                            <div class="cal__nav">
                                <button id="calPrev" aria-label="Попередній місяць">&lsaquo;</button>
                                <button id="calNext" aria-label="Наступний місяць">&rsaquo;</button>
                            </div>
                        </div>
                        <div class="cal__grid" id="calGrid"></div>
                        <!-- <div class="cal__years" id="calYears"></div> -->
                    </div>
                </div>

                <!--  <div>
                   <h3 class="rail__title">Вибір редактора</h3>
                   [!DocLister?
                     &parents=`[%news-dir%]`
                     &depth=`5`
                     &display=`1`
                     &orderBy=`pub_date DESC`
                     &tpl=`news.editorpick`
                     &dateSource=`pub_date`
                     &dateFormat=`%e %B %Y`
                     &tvList=`image,author`
                     &filters=`AND(TV:editor_pick:=:1)`
                   !]
                 </div> -->


                <div>
                    <h3 class="rail__title">Статті</h3>
                    <div class="arts">
                        [!DocLister?
                        &parents=`[%news-dir%]`
                        &display=`5`
                        &orderBy=`pub_date DESC`
                        &tpl=`news.article.item`
                        &tvList=`author`
                        !]
                    </div>
                </div>

            </aside>

        </div>
    </div>
</section>

<script>window.NEWS_ARCHIVE = [!NewsArchiveData? &parents=`[%news-dir%]`!];</script>
<script src="[(base_url)]assets/templates/html/css/news/archive-news.js"></script>


{{footer}}
{{footer_scripts}}

