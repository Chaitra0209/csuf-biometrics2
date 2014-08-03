var isExtended = 0;
//window.addEvent('load', function(){init()});
function rizslide(){
			if(isExtended == 0){
				var src = jQuery("#sideBarTab2 img").attr("src").replace(".gif", "-active.gif");

				jQuery("#sideBarTab2 img").attr("src",src);
				jQuery("#rizsideBar2").animate({ "width" : "250px", "heigth" : "240px" }, 1200);
				jQuery("#sideBarContents").animate({ "width" : "180px", "heigth" : "200px" }, 1200);
				isExtended = 1;
			}else{
				var src = jQuery("#sideBarTab2 img").attr("src").replace("-active.gif", ".gif");

				jQuery("#sideBarTab2 img").attr("src",src);
				jQuery("#rizsideBar2").animate({ "width" : "44px", "heigth" : "123px" }, 1200);
				jQuery("#sideBarContents").animate({ "width" : "0", "heigth" : "0" }, 1200);
				isExtended = 0;
			}
	}
	