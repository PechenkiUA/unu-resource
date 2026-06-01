document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.menu-toggle');
    const nav    = document.querySelector('.menu-main.nav');

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            let open = nav.classList.toggle('is-open');
            toggle.classList.toggle('is-active', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    document.querySelectorAll('.menu-main.nav .submenu-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const li = btn.parentElement;
            const expanded = li.classList.toggle('is-expanded');
            btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });
    });
});