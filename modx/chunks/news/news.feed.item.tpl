<a class="feed__item" href="[~[+id+]~]">
  <span class="feed__time">
	  [+date+]

	    [[if?
		  &is=`[+showImage+]:==:true`
		  &then=`
			<img style="max-width:80px;height: 80px;object-fit: cover;"src="[[getFirstPhoto? &photos=`[+photos+]`]]" alt="[+pagetitle+]">`
	  	]]
	</span>
    <span class="feed__body">
    <h3>[+pagetitle+]</h3>

	  [[if?
	  	&is=`[+showIntro+]:==:true`
	  	&then=`<p>[+introtext+]</p>`

	  ]]

  </span>
</a>
