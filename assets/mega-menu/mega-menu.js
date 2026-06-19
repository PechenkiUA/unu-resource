/* ───────── Мега-меню УНУ: ─────────
 *
 * Очікує розмітку з чанків mega.* : #navbar, #nav, .nav__item, .mega.
 */
(function(){
    var nav = document.getElementById("nav");
    if (!nav) return;

    var DESKTOP = "(min-width: 1081px)";
    var navbar = document.getElementById("navbar");

    /* ── Пошук: тогл панелі (форма submit'иться на сторінку результатів MODX) ── */
    var searchBtn   = nav.querySelector(".nav__search-btn");
    var searchPanel = navbar ? navbar.querySelector(".search-panel") : null;
    var searchInput = searchPanel ? searchPanel.querySelector(".search-box__input") : null;
    var searchClose = searchPanel ? searchPanel.querySelector(".search-box__close") : null;

    function openSearch(){
        if (!navbar) return;
        navbar.classList.add("search-open");
        if (searchBtn) searchBtn.setAttribute("aria-expanded", "true");
        nav.querySelectorAll(".nav__item.is-open").forEach(function(o){
            o.classList.remove("is-open"); var l = o.querySelector(".nav__link"); if (l) l.setAttribute("aria-expanded","false");
        });
        if (searchInput) setTimeout(function(){ searchInput.focus(); }, 60);
    }
    function closeSearch(){
        if (!navbar) return;
        navbar.classList.remove("search-open");
        if (searchBtn) searchBtn.setAttribute("aria-expanded", "false");
    }
    if (searchBtn){
        searchBtn.addEventListener("click", function(){
            navbar.classList.contains("search-open") ? closeSearch() : openSearch();
        });
    }
    if (searchClose) searchClose.addEventListener("click", closeSearch);
    document.addEventListener("click", function(e){
        if (navbar && navbar.classList.contains("search-open") &&
            searchPanel && !searchPanel.contains(e.target) &&
            searchBtn && !searchBtn.contains(e.target)) closeSearch();
    });

    /* ── Hover-intent (десктоп): невелика затримка перед закриттям ── */
    var closeTimer = null;
    nav.querySelectorAll(".nav__item").forEach(function(li){
        var link = li.querySelector(".nav__link");
        var hasMega = !!li.querySelector(".mega");

        li.addEventListener("mouseenter", function(){
            if (!window.matchMedia(DESKTOP).matches) return;
            clearTimeout(closeTimer);
            nav.querySelectorAll(".nav__item.is-open").forEach(function(o){
                if (o !== li){ o.classList.remove("is-open"); var l = o.querySelector(".nav__link"); if (l) l.setAttribute("aria-expanded","false"); }
            });
            if (hasMega){ li.classList.add("is-open"); link.setAttribute("aria-expanded","true"); }
        });
        li.addEventListener("mouseleave", function(){
            if (!window.matchMedia(DESKTOP).matches) return;
            closeTimer = setTimeout(function(){
                li.classList.remove("is-open"); if (link) link.setAttribute("aria-expanded","false");
            }, 140);
        });

        /* клавіатура: фокус усередині тримає панель відкритою (лише десктоп) */
        li.addEventListener("focusin", function(){
            if (!window.matchMedia(DESKTOP).matches) return;
            if (hasMega) li.classList.add("is-open");
        });
        li.addEventListener("focusout", function(e){
            if (!window.matchMedia(DESKTOP).matches) return;
            if (!li.contains(e.relatedTarget)) li.classList.remove("is-open");
        });
        /* тач / вузький екран: клік по розділу з панеллю — тогл, без переходу */
        if (hasMega && link){
            link.addEventListener("click", function(e){
                if (!window.matchMedia(DESKTOP).matches){
                    e.preventDefault();
                    var open = li.classList.toggle("is-open");
                    link.setAttribute("aria-expanded", open ? "true" : "false");
                }
            });
        }
    });

    /* Esc закриває всі панелі та пошук */
    document.addEventListener("keydown", function(e){
        if (e.key === "Escape"){
            nav.querySelectorAll(".nav__item.is-open").forEach(function(o){ o.classList.remove("is-open"); });
            if (navbar && navbar.classList.contains("search-open")) closeSearch();
        }
    });

    /* мобільний тогл усього меню */
    var burger = document.getElementById("navToggle");
    if (burger && navbar){
        burger.addEventListener("click", function(){ navbar.classList.toggle("is-open"); });
    }
})();
