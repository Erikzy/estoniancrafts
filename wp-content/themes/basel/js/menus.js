
topMenuF = (sb) =>{
	if(jQuery(".kmobile").length == 0 ){
			jQuery('<div class="kmobile" ><div class="search-extended-mob" ></div></div>').insertAfter('.wrapp-header');
	 		jQuery('.search-extended-mob').append(sb.html());	
	}

/*	console.log(jQuery(window).width())
	if(jQuery(window).width() < 770 ) {
		jQuery('.search-extended').remove();

		//p.addClass('search-extended-mob');
	   //jQuery('.wrapp-header').add('div').add(sb);
	   jQuery('<div class="kmobile" ><div class="search-extended-mob" ></div></div>').insertAfter('.wrapp-header');
	   jQuery('.search-extended-mob').append(sb.html());

	}
	else{
		//console.log('cjeeeseee');
		jQuery(".kmobile").remove();
		if(jQuery(".kt-otsing").html() != o ) 
			jQuery(".kt-otsing").append(sb.html());
	}
*/
	  

}

catMenu = () =>{
	let cm =  jQuery('#category-menu-wrapper').clone();
	if(jQuery.trim(jQuery("#ccmenu").html()) == "")
		jQuery('#ccmenu').append(cm.html());
}
jQuery(document).ready(function(){

	 var sb = jQuery('.search-extended').clone();
	 catMenu();
	 topMenuF(sb);
});

jQuery(document).resize(function(){ 
	 var sb = jQuery('.search-extended').clone();
		topMenuF(sb);
});

/*
-- remove export all and export filter in orders section  1   --done

-- company name , company type and reg number /// mandatory  1  --- done

-- display product review 3  ---

-- blog --- posts always editable. 2 --done  publish --> publish

-- more posts by .... ---> view all 1 -- done
-- remove blank entry in add product view  variants.... 1 -- done

-- add text 800 x 600 in upload a product cover image ... 1  -- done

--- add more images button only when featured image is added. --- add product view ... 1 -- done

-- add new product view :  store the values when submiting the form if theres an error... 2

--  product view: display additional information.... 3 -- done


16 hrs

-- 



*/

