<section class="sec">
    <div class="shell">
        <div class="sec__head">
            <div>
                <h2>[%about_specialites%]</h2>
            </div>
            <!--     <a class="sec__more" href="#">Усі спеціальності</a> -->
        </div>

        <div class="specs">
            [[Ditto?
            &id=`sbl`
            &parents=`[%spec_id%]`
            &tpl=`spec.item`
            &orderBy=`menuindex asc`
            ]]
        </div>
    </div>
</section>

<style>
    .specs-block{ --u-blue:#0D2C60; --u-blue-deep:#08204A; --u-yellow:#E2E419; --u-line:#DEE2EA; }
    /* секція + шапка */

    .sec__head{ display:grid; grid-template-columns:1fr auto; align-items:end; gap:24px; margin-bottom:48px; }

    .eyebrow{ font-size:12px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:#0D2C60; }
    .eyebrow .bar{ display:inline-block; width:24px; height:2px; background:#E2E419; vertical-align:middle; margin-right:10px; }
    .sec__more{ font-size:11.5px; text-transform:uppercase; letter-spacing:.12em; font-weight:800; color:#0D2C60; padding-bottom:2px; text-decoration:none; }

    /* картки спеціальностей */
    .specs{ display:grid; grid-template-columns:repeat(3,1fr); gap:15px; border:none;}
    .spec {
        padding: 10px;
        display: flex;
        flex-direction: column;
        /* cursor: default; */
        text-decoration: none;
        border: 1px solid #ebd8d8;
        border-radius: 5px;
        position:relative;
    }
    .spec .spec__media{
        overflow: hidden;
    }
    .spec:hover img{
        transform: scale(1.1);
        transition: .3s all;
    }

    .spec:hover .sec__more:after{
        margin-left: 10px;
        transition: .3s all;
    }


    .spec__hr{ border-top:2px solid #0D2C60; }
    .spec__hr::after{ content:""; display:block; margin-top:5px; border-top:2px dotted #0D2C60; opacity:.45; }
    .spec__media {
        display: block;
        width: 100%;
        height: 250px;
        /* margin-top: 26px; */
        background: #DEE2EA;
        border-radius: 5px;
    }
    .spec__media img{ width:100%; height:100%; object-fit:cover; display:block; transition: .3s all; }
    .spec__title {
        margin: 24px 0 0;
        font-weight: 800;
        font-size: 19px;
        line-height: 1.25;
        letter-spacing: -.01em;
        color: #0D2C60;
        /* text-decoration: underline; */
        text-underline-offset: 3px;
        text-decoration-thickness: 2px;
        transition: color .15s ease;
        position: absolute;
        background: #ffffffe8;
        left: 10px;
        right: 10px;
        bottom: 10px;
        text-align: center;
        /* filter: blur(1px); */
        padding: 10px;
        /* border-radius: 10px 0; */
        display: flex;
        height: 50px;
        align-items: center;
        justify-content: center;
    }
    .spec:hover .spec__title{ color:#08204A; }
    .spec__dots{ display:block; margin:16px 0 18px; border-top:2px dotted #0D2C60; opacity:.45; }
    .spec__text{ margin:0; font-size:14.5px; line-height:1.62; color:#33405c; display:-webkit-box; -webkit-line-clamp:8; -webkit-box-orient:vertical; overflow:hidden; }
    .spec__more{ margin-top:18px; font-size:11px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; color:#0D2C60; display:inline-flex; align-items:center; gap:8px; align-self:flex-start; }


    @media (max-width:1080px){ .specs{ grid-template-columns:1fr 1fr; gap:32px; } }
    @media (max-width:680px){ .specs{ grid-template-columns:1fr; } .spec__text{ -webkit-line-clamp:unset; } }

</style>