[[if? &is=`[+image:imageExists+]:eq:1` &then=`
<li><a href="[+link+]" title="[+alt:htmlent+]"[[if? &is=`[+winnew+]:eq:1` &then=` target="_blank"`]]><span>[+title+]</span></a><img src="[[phpthumb? &input=`[+image+]` &options=`w=200,h=200,zc=1,f=jpeg,bg=ffffff,q=85`]]" alt="[+alt:htmlent+]" /></li>
`]]
