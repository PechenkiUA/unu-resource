<a class="spec" href="[~[+id+]~]">
    <image-slot class="spec__media" shape="rect" placeholder="[+pagetitle+]">
        [[if? is=`[+image+]:empty` &then=`
        <img src="https://placehold.net/400x400.png" alt="[+pagetitle+]" />
        `
        &else=`

        <img src="[[phpthumb? &input=`[+image+]` &options=`w=480,h=380,far=1,f=jpeg,q=95,bg=ffffff`]]" alt="[+pagetitle+]" />

        `]]

        <h3 class="spec__title">[+pagetitle+]</h3>
    </image-slot>

    [[if? is=`[+short-descr+]:empty` &then=`` &else=`
    <p class="spec__text">[+short-descr+]</p>
    <span class="sec__more">Детальніше</span>
    `]]
</a>