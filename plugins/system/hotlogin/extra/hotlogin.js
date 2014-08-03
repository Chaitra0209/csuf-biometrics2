if (window.MooTools) {
	window.addEvent('load', function() {
		var hldiv = document.getElementById('HLwrapper');
		var ua = navigator.userAgent;
		if (ua.indexOf('MSIE') == -1) { hldiv.setStyles( {'height':1} ); }
		var invisible_h=document.getElementById("HLhidden").offsetHeight-hoffset;
		var trigger =  document.id('HLtrigger');
		var ani = new Fx.Tween(hldiv, {duration: 700});
		ani.options.transition = Fx.Transitions.Cubic.easeOut;

		if (HL_open) {
			hldiv.setStyles({'margin-top':0});		
		} else {				
			hldiv.setStyles({'margin-top':-(invisible_h)});		
		}		
		//hldiv.setOpacity(HLopacity);		
		hldiv.setStyle('opacity',HLopacity)
		
		trigger.addEvent('click', function(event){
			if (HL_open) {
				var invisible_h=document.getElementById("HLhidden").offsetHeight-hoffset;
				ani.start('margin-top',-invisible_h);
				HL_open  = false;            
			} else {
				ani.start('margin-top',0);
				HL_open  = true;  
			}
		});
		
		$(document.body).addEvent('click',function(e) { 
			var showingParent = hldiv;
			if(showingParent && !e.target || !$(e.target).getParents().contains(showingParent)) {  
				if (HL_open) {
					var invisible_h=document.getElementById("HLhidden").offsetHeight-hoffset;
					ani.start('margin-top',-invisible_h);
					HL_open  = false;            
				} 
			} 
		}); 		
	});
} else {
	alert('Hotlogin error: MooTools is not loaded!');
}
