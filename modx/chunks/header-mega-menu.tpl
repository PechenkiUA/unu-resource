<header class="hdr header-v2">
    <div class="msite">
        <div class="wrap">
            <div class="justifyleft"><a href="[%uni-link%]" title="[%uni-name%]" target="_blank" class="main">[%uni-name%]</a></div>
            <div class="justifyright"><a href="[%faculty-link%]" title="[%faculty-name%]" target="_blank">[%faculty-name%]</a></div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="shell">
        <div class="hdr__row">
            <div class="brand">
                <div class="wrap-brand">
                    <a href="/"></a>
                    <img src="[[phpthumb? &input=`[[DocInfo? &docid=`[%site-main%]` &field=`logo`]]` &options=`w=80,h=80,far=1,f=png`]]" alt="[%site-name%]" />
                    <div class="brand__name">[%site-name%]</div>

                </div>
            </div>
            <div class="contacts">
                <div>
					<span class="phone">

							<a href="tel:[[cleanphone? &input=`[[DocInfo? &docid=`[%site-main%]` &field=`phone`]]`]]">
							<i class="fa fa-phone-square"></i>
							<span>[[DocInfo? &docid=`[%site-main%]` &field=`phone`]]</span>
						</a>
					</span>

                    <span class="email">
						<a href="mailto:[[DocInfo? &docid=`[%site-main%]` &field=`email`]]">
							<i class="fa fa-envelope"></i>
							<span>[[DocInfo? &docid=`[%site-main%]` &field=`email`]]</span>
						</a>
					</span>

                    <span>[+switchLang+]</span>
                    <button class="menu-toggle" id="navToggle" type="button" aria-label="Меню" aria-expanded="false">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </div>
        </div>
        [[-
        old menu
        <div>

            [[Wayfinder?
            &startId=`[%site-start%]`
            &level=`3`
            &excludeDocs=`[[clearDocsForMenu]]`
            &outerTpl=`menu-main-outer`
            &innerTpl=`menu-main-folders`
            &rowTpl=`main-menu-row-item`
            &parentRowTpl=`main-menu-inner-row-item`
            &firstClass=`first`
            &parentClass=`has-child`
            &lastClass=``
            &hereClass=`active`
            &innerClass=`submenu`
            ]]

        </div>
        -]]

        <div class="navbar" id="navbar">
            <ul class="nav" id="nav">
                [!UNUMegaMenu?
                &startId=`[%site-start%]`
                &blurbField=`introtext`
                &wideAfter=`2`
                &level=`2`
                &excludeDocs=`[[clearDocsForMenu]]`
                !]
                <li class="nav__item nav__search">
                    <button class="nav__link nav__search-btn" type="button" aria-label="[%search-text%]" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"></circle><line x1="16.5" y1="16.5" x2="21" y2="21"></line></svg>
                    </button>
                </li>
            </ul>
            <div class="search-panel">
                <div class="search-panel__inner">
                    <form class="search-box" action="[~[%search_id%]~]" method="get" role="search">
                        <svg class="search-box__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"></circle><line x1="16.5" y1="16.5" x2="21" y2="21"></line></svg>
                        <input class="search-box__input" type="search" name="search" placeholder="[%search-text%]" autocomplete="off" aria-label="[%search-text%]">
                        <button class="search-box__submit" type="submit">[%search-text%]</button>
                        <button class="search-box__close" type="button" aria-label="Закрити">✕</button>
                    </form>
                    <div class="search-panel__hint">Введіть запит і натисніть Enter — пошук по всьому сайту.</div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="[(base_url)]assets/templates/html/css/mega-menu.css">


</header>
<script src="[(base_url)]assets/templates/html/js/mega-menu.js"></script>
<style>
    .wrap-brand{
        display: flex;
        gap:10px;
    }
    .wrap-brand .brand__name {
        font-weight: 500;
        font-size: 0.8rem;
        border-left: 2px solid #0d2c60;
        padding-left: 10px;
        display: flex;
        align-items: center;
    }
    .hdr__row{
        .menu-main-wrap{
            background:white;
            border: none;
        }
        .nav a{
            padding:0;
        }


    }


    .fixedmenu .menu-main li ul{
        top:20px;
    }


</style>