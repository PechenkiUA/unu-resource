<section class="sec">
    <div class="shell">

        <div class="sec__head">
            <div>
                <div class="eyebrow"><span class="bar"></span></div>
                <h2>[%latest-news%]</h2>
            </div>
            <a class="sec__more" href="[~[%news-dir%]~]">[%all-news%]→</a>
        </div>

        <div class="news2">
            <div class="editor">

                [!getDepDocs?
                &type=`news`
                &rowTpl=`item.first.v2`
                &outerTpl=`news.outer`
                &alt=`news_def`
                &display=`1`
                &useInner=`1`
                !]

            </div>

            <div class="feed">


                [!getDepDocs?
                &type=`news`
                &rowTpl=`news.item.v2`
                &outerTpl=`news.outer`
                &alt=`news_def`
                &display=`4`
                &useInner=`1`
                &start=`1`
                !]

            </div>
        </div>
    </div>
</section>



<style>

    .shell{ max-width: 1320px; margin: 0 auto; padding: 0 32px; }

    /* ─── Section header ─── */
    .sec{ padding: 88px 0; }
    .sec__head{
        display:grid; grid-template-columns: 1fr auto; align-items:end;
        gap: 24px; margin-bottom: 48px;
    }
    .sec__head h2{
        margin: 16px 0 0;
        font-family: var(--font-display);
        font-weight: 800;
        font-size: clamp(34px, 4.2vw, 52px);
        letter-spacing: -0.025em;
        line-height: 0.98;
        color: var(--u-blue);
    }
    .eyebrow{
        font-size: 12px; font-weight: 700;
        letter-spacing: 0.16em; text-transform: uppercase;
        color: var(--u-blue);
    }
    .eyebrow .bar{
        display:inline-block; width: 24px; height: 2px;
        background: var(--u-yellow); vertical-align: middle;
        margin-right: 10px;
    }
    .sec__more{
        font-size: 11.5px; text-transform: uppercase; letter-spacing: .12em;
        font-weight: 800; color: var(--u-blue);
        border-bottom: 2px solid var(--u-yellow); padding-bottom: 2px;
    }

    /* ─── NEWS — стрічка + вибір редактора ─── */
    .news2{
        display:grid;
        grid-template-columns: 0.6fr 1.1fr;
        gap: 40px;
    }

    /* LEFT — feed */
    .feed{
        display:flex;
        flex-direction: column;
        justify-content: space-between;

    }
    .feed__item{
        display:grid;
        grid-template-columns: 80px 1fr;
        gap: 16px;
        align-items: start;
        padding: 18px 0;
        border-bottom: 1px solid var(--u-line);

        position: relative;
    }
    .feed__item:first-child{ padding-top: 24px; }
    .feed__time{
        font-family: var(--font-mono);
        font-size: 13px; font-weight: 600;
        color: var(--u-ink-faint);
        letter-spacing: .02em;
        padding-top: 2px;
    }
    .feed__title{
        font-size: 16px;
        font-weight: 600;
        line-height: 1.35;
        color: var(--u-blue);
        letter-spacing: -0.005em;
        transition: color .15s ease;
    }
    .feed__item::before{
        content:""; position:absolute; left: -16px; top: 18px; bottom: 18px;
        width: 3px; background: var(--u-yellow);
        transform: scaleY(0); transform-origin: center;
        transition: transform .2s ease;
    }
    .feed__item:hover::before{ transform: scaleY(1); }
    .feed__item:hover .feed__title{ color: var(--u-blue-deep); }
    .feed__item:hover .feed__time{ color: var(--u-blue); }
    .feed__item.is-lead .feed__title{ font-weight: 600; font-size: 17px; }
    .feed__item.is-lead .feed__time{ color: var(--u-blue); }

    /* RIGHT — editor's pick */
    .editor{ display:flex; flex-direction: column; }
    .editor__kicker{
        font-size: 12px; font-weight: 800;
        letter-spacing: .16em; text-transform: uppercase;
        color: var(--u-blue);
        padding: 0 0 16px 40px;
    }
    .editor__card{
        position: relative;
        display:flex; flex-direction: column;
        background: var(--u-blue);
        overflow: hidden;

        flex: 1;
        min-height: 320px;
    }
    .editor__media{
        display:block;
        width: 100%;
        flex: 1;

    }
    .editor__media img{ width: 100%; height: 100%; object-fit: cover; display:block;max-height: 260px; }
    .editor__overlay{
        position: relative;
        background: var(--u-blue);
        padding: 32px 36px 36px;
        z-index: 2;
    }
    .editor__overlay::before{
        content:""; position:absolute; left: 0; top: 0; width: 64px; height: 5px;
        background: var(--u-yellow);
    }
    .editor__overlay h3{
        font-family: var(--font-display);
        font-weight: 800;
        font-size: clamp(20px, 0.9vw, 33px);
        line-height: 1.1;
        letter-spacing: -0.015em;
        color: #fff;
        margin: 0;
        text-wrap: balance;
    }
    .editor__meta{
        margin-top: 18px;
        font-size: 12px; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase;
        color: var(--u-yellow);
    }

    @media (max-width: 1080px){
        .news2{ grid-template-columns: 1fr; }
        .feed{ padding-right: 0; }
        .editor{ margin-top: 40px; }
        .editor__kicker, .editor__card{ padding-left: 0; margin-left: 0; }
    }


</style>
