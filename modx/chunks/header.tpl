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

    </div>


</header>

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