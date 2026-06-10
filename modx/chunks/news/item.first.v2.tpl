<a class="editor__card" href="[+url+]">
    <image-slot
            id="news-editor-pick"
            class="editor__media"
            shape="rect"
            placeholder="[+pagetitle+]">
        <img src="[[phpthumb? input=`[+image+]` &options=`w=350,h=260,zc=T,f=jpeg,q=95`]]" alt="[+e.title+]" />
    </image-slot>
    <div class="editor__overlay">
        <h3>[+title+]</h3>
        <div class="editor__meta">
            [+time+]
        </div>
    </div>
</a>