<a class="feed__item" href="[+url+]">
  <span class="feed__time">
	  [+time+]

	    [[if?
		  &is=`[+showImage+]:==:true`
		  &then=`
			<img
					style="max-width:80px;height: 80px;object-fit: cover;"
					src="[[phpthumb? input=`[+image+]` &options=`w=350,h=260,zc=T,f=jpeg,q=95`]]"
					alt="[+e.title+]">`
	  	]]
	</span>
	<span class="feed__body">
    <h3>[+title+]</h3>

	  [[if?
	  	&is=`[+showIntro+]:==:true`
	  	&then=`<p>[+short-descr+]</p>`

	  ]]

	[[if?
		  &is=`[+dep-name+]:not_empty`
		  &then=`
			<span class="feed__item_tag"><small>[+dep-name+]</small></span>
		`
	  	]]

  </span>


</a>
