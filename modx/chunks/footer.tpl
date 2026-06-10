<footer class="ft">
    <div class="shell">
        <div class="ft__top">

            <!-- Ліва колонка: логотип + контакти + соцмережі -->
            <div>
                <div class="ft__contact">
          <span style="display: inline-block; margin-top: 10px;">
			  [[DocInfo? &docid=`[%site-main%]` &field=`phone`]]</span><br>
                    [[DocInfo? &docid=`[%site-main%]` &field=`email`]]
                </div>
                <div class="ft__social">
                    [[multiTV?
                    &docid=`[%site-main%]`
                    &tvName=`social`
                    &display=`all`
                    &rowTpl=`social-link`
                    &outerTpl=`social-link-outer`
                    ]]
                </div>
            </div>

            <div class="ft__col">
                [[showFooterLinks? &num=`1` &docid=`[%site-main%]`]]
            </div>
            <!-- Колонка 1 -->
            <div class="ft__col">
                [[showFooterLinks? &num=`2` &docid=`[%site-main%]`]]
            </div>

            <!-- Колонка 2 -->
            <div class="ft__col">
                [[showFooterLinks? &num=`3` &docid=`[%site-main%]`]]
            </div>

            <!-- Колонка 3: Інші сайти -->
            <div class="ft__col">
                <h5>[%other-sites%]</h5>
                <a href="[%uni-link%]"     title="[%uni-name%]"     target="_blank">[%uni-name%]</a>
                <a href="[%lib-link%]"     title="[%lib-name%]"     target="_blank">[%lib-name%]</a>
                <a href="[%rep-link%]"     title="[%rep-name%]"     target="_blank">[%rep-name%]</a>
                <a href="[%asu-link%]"     title="[%asu-name%]"     target="_blank">[%asu-name%]</a>
            </div>

        </div>

        <!-- Нижня смуга -->
        <div class="ft__bottom">
            <div>[[DocInfo? &docid=`[%site-main%]` &tv=`1` &field=`copyright`]]</div>
            <div>[%design%] <a href="http://dpc.udau.edu.ua">DPC</a></div>
        </div>

    </div>
</footer>
<div id="to-top" title="[%to-top%]"></div>