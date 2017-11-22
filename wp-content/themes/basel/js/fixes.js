//fixes for carousel
function getPosition(el) {
  var xPos = 0;
  var yPos = 0;
 
  while (el) {
    if (el.tagName == "BODY") {
      // deal with browser quirks with body/window/document and page scroll
      var xScroll = el.scrollLeft || document.documentElement.scrollLeft;
      var yScroll = el.scrollTop || document.documentElement.scrollTop;
 
      xPos += (el.offsetLeft - xScroll + el.clientLeft);
      yPos += (el.offsetTop - yScroll + el.clientTop);
    } else {
      // for all other non-BODY elements
      xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
      yPos += (el.offsetTop - el.scrollTop + el.clientTop);
    }
 
    el = el.offsetParent;
  }
  return {
    x: xPos,
    y: yPos
  };
}
jQuery(document).ready(function(){

	OwlButton = ()=>{
		// get the first item in ther carousel

		let item = jQuery(".owl-item .image-link")[0];
		let itemPosition = getPosition(item);
		let itemWidth  = item.offsetWidth;
		let itemHeight = item.offsetHeight;
		let rightArrow = jQuery(".owl-next")[0];
		let leftArrow = jQuery(".owl-prev")[0];
		let arrowHeight = leftArrow.offsetHeight;

		let newPosition = (itemHeight/2 +10 + arrowHeight/2 -2 ) * -1;
		
		leftArrow.style.top = newPosition + "px";
		rightArrow.style.top = leftArrow.style.top;
/*		console.log(itemHeight);
		console.log(newPosition);*/
		let carouselWrapperWidth = jQuery('.owl-wrapper-outer')[0].offsetWidth;
		let carouselContainerWidth = jQuery('.thumbs-position-bottom')[0].offsetWidth;
		let space = ((carouselContainerWidth - carouselWrapperWidth)/2) - 4;
		let im = jQuery('.woocommerce-main-image')[0].style.marginLeft = space+'px';
		console.log(carouselWrapperWidth);
		console.log(carouselContainerWidth);
		console.log(space);

		if(jQuery('.owl-controls')[0].style.display === 'none'){

				jQuery('.owl-wrapper-outer')[0].style.left = (-6 + space).toString()+ 'px';
				console.log((9 + space).toString()+ 'px');
		}
	}
	baselThumbsOwlCarousel.bind(this,OwlButton());
	jQuery('.y-link').click(function(){
		this.event.preventDefault();
		alert('cheese')
	});


});

jQuery(window).resize(function(){
	baselThumbsOwlCarousel.bind(this,window.setTimeout(OwlButton , 900 ));
	//window.setTimeout(OwlButton , 500 );
})
