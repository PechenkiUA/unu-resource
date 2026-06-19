<section class="sec">
    <div class="shell">
        [[multiTV? &tvName=`pagelinks` &rowTpl=`pagelink-row` &outerTpl=`pagelink-outer` &emptyOutput=`1` &display=`all`]]
    </div>

</section>

<style>
    .rounded-links { width: 100%; margin: 0; }
    .rounded-links ul {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 4px;
        padding: 0;
        margin: 0;
    }
    .rounded-links li {
        height: 210px;
        border-radius: 0;
        border: none;
        background: var(--u-blue);
        position: relative;
        overflow: hidden;
        display: block;
        margin: 0;
    }
    .rounded-links li::before { display: none; }
    .rounded-links li::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(8,32,74,0.88) 0%, rgba(8,32,74,0.3) 60%, transparent 100%);
        z-index: 1;
        transition: background 0.35s;
    }
    .rounded-links li:hover::after {
        background: linear-gradient(to top, rgba(8,32,74,0.72) 0%, rgba(8,32,74,0.15) 60%, transparent 100%);
    }
    .rounded-links li a {
        position: absolute;
        inset: 0;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        line-height: 1.3;
        text-align: center;
        text-decoration: none;
        max-width: unset;
        transition: color 0.3s;
    }
    .rounded-links li a span { display: block; transition: color 0.3s; }
    .rounded-links li img {
        position: absolute;
        left: 0; top: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        opacity: 0.2;
        z-index: 0;
        transition: opacity 0.4s, transform 0.5s;
    }
    .rounded-links li:hover img  { opacity: 0.6; transform: scale(1.07); }
    .rounded-links li:hover span { color: var(--u-yellow); }

</style>