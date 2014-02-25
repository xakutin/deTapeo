/**************************************
Tooltips functions
***************************************/
/**
  Stronglky modified, onky works with DOM2 compatible browsers.
  	Ricardo Galli
  From http://ljouanneau.com/softs/javascript/tooltip.php
  xakutin: Eliminada la capa de sombra, añadida opción de tooltip menu (extraido de http://jqueryfordesigners.com/coda-popup-bubbles/)  		 
 */
// create the tooltip object
function tooltip(){}
	// setup properties of tooltip object
	tooltip.id="tooltip";
	tooltip.textid="tooltip-text";	
	tooltip.x = 0;
	tooltip.y = 0;
	tooltip.offsetx = 10;
	tooltip.offsety = 20;
	tooltip.menuoffsety = 0;
	tooltip.width = 120;			
	tooltip.saveonmouseover=null;
	tooltip.ajaxtime = 100;
	tooltip.ajaxtimeout = null;
	tooltip.active = false;
	tooltip.menuhide = true;
	// animation values
	tooltip.topincrease = 10;
	tooltip.animationtime = 250;
	tooltip.hidedelaytime = 500;
	tooltip.hidemsgdelaytime = 2000;	
	tooltip.hidedelaytimeout = null;
	// tracker
	tooltip.menubeingshown = false;
	tooltip.menushown = false;
	tooltip.inmenu = false;	
	
	tooltip.cache = new JSOC();

	tooltip.ie = (document.all)? true:false;		// check if ie
	if (tooltip.ie) 
		tooltip.ie5 = (navigator.userAgent.indexOf('MSIE 5')>0);
	else 
		tooltip.ie5 = false;
	tooltip.dom2 = ((document.getElementById) && !(tooltip.ie5))? true:false; // check the W3C DOM level2 compliance. ie4, ie5, ns4 are not dom level2 compliance !! grrrr >:-(

	/**
	* Open ToolTip. The title attribute of the htmlelement is the text of the tooltip
	* Call this method on the mouseover event on your htmlelement
	* ex :  <div id="myHtmlElement" onmouseover="tooltip.show(this)"...></div>
	*/
	tooltip.show = function (event, text) {		
	  // we save text of title attribute to avoid the showing of tooltip generated by browser
		if (tooltip.dom2  == false ) return false;
		tooltip.saveonmouseover = document.onmousemove;
		document.onmousemove = tooltip.mouseMove;
		tooltip.setText(text);
		tooltip.mouseMove(event); // This already moves the div to the right position		
		$("#"+tooltip.id).css("visibility","visible");	
		tooltip.active = true;
		return false;
	};
	
	/**
	* Show a message window and hide automatically	
	*/
	tooltip.showMessage = function (text, top, left){
		// we save text of title attribute to avoid the showing of tooltip generated by browser
		if (tooltip.dom2  == false ) return false;					
		tooltip.setText(text);
		tooltip.active = true;
		tooltip.inmenu = true;
		// Set the tooltip position	  
  	tooltip.menubeingshown = true;
  	tooltip.x = left;	
  	tooltip.y = top;
    $("#"+tooltip.id).css({
    	top: tooltip.y,
      left: tooltip.x,
      visibility: 'visible'
    });
	    
	  // (we're using chaining on the popup) now animate it's opacity and position
	  if (tooltip.ie){
	  	tooltip.menubeingshown = false;
		  tooltip.menushown = true;
	  }else{
		  $("#"+tooltip.id).animate({
		  	top: '+=' + tooltip.topincrease + 'px',
		    opacity: 1
		  }, tooltip.animationtime, 'swing', function() {
		  	// once the animation is complete, set the tracker variables
		    tooltip.menubeingshown = false;
		    tooltip.menushown = true;
		  });
		}
		//Hide the message
		setTimeout("tooltip.hide()", tooltip.hidemsgdelaytime);
	};

	/**
	* show tooltip Menu
	*/
	tooltip.showMenu = function (event, text, width) {	
	  // we save text of title attribute to avoid the showing of tooltip generated by browser
		if (tooltip.dom2  == false ) return false;	
		// stops the hide event if we move from the trigger to the popup element
		if (tooltip.hidedelaytimeout) clearTimeout(tooltip.hidedelaytimeout);
		// don't trigger the animation again if we're being shown, or already visible
		if (tooltip.menubeingshown || tooltip.menushown) return false;		
		/*this.saveonmouseover = document.onmousemove;
		document.onmousemove = this.mouseMove;*/
		tooltip.setText(text);
		tooltip.setWidth(width);				
		tooltip.active = true;
		tooltip.inmenu = true;
		// Set the tooltip position	  
  	tooltip.menubeingshown = true;	
  	tooltip.setMenuPosition(event);	  		//calculate the tooltip position	      
    $("#"+tooltip.id).css({
    	top: tooltip.y,
      left: tooltip.x,
      visibility: 'visible'
    });
	    
	  // (we're using chaining on the popup) now animate it's opacity and position
	  if (tooltip.ie){
	  	tooltip.menubeingshown = false;
		  tooltip.menushown = true;
	  }else{
		  $("#"+tooltip.id).animate({
		  	top: '+=' + tooltip.topincrease + 'px',
		    opacity: 1
		  }, tooltip.animationtime, 'swing', function() {
		  	// once the animation is complete, set the tracker variables
		    tooltip.menubeingshown = false;
		    tooltip.menushown = true;
		  });
		}	  
	};
	
	tooltip.dontHide = function (event) {		
		// stops the hide event if we move from the trigger to the popup element
		if (tooltip.hidedelaytimeout) clearTimeout(tooltip.hidedelaytimeout);
	};
	
	/**
	* hide tooltip
	* call this method on the mouseout event of the html element
	* ex : <div id="myHtmlElement" ... onmouseout="tooltip.hide(this)"></div>
	*/
	tooltip.hide = function (event) {	
		if (tooltip.dom2  == false) return false;					
						
		if (tooltip.inmenu){
			if (tooltip.menuhide){			
				// reset the timer if we get fired again - avoids double animations
	  		if (tooltip.hidedelaytimeout) clearTimeout(tooltip.hidedelaytimeout);
	  		// store the timer so that it can be cleared in the mouseover if required
			  tooltip.hidedelaytimeout = setTimeout(function () {
			  	if (tooltip.ie){		  	    
			      tooltip.menushown = false;
			      tooltip.menubeingshown=false;
			      tooltip.inmenu=false;
			      tooltip.active = false;			      
			      tooltip.setText('');    
			      $("#"+tooltip.id).css({		      	
			      	top: '-1000px',
			      	left:'-1000px',
			      	visibility: 'hidden'
			      });
			  	}else{
				    //tooltip.hidedelaytimeout = null;		    	 
				    $("#"+tooltip.id).animate({
				      top: '+=' + tooltip.topincrease + 'px',
				      opacity: 0
				    }, tooltip.animationtime, 'swing', function () {
				      // once the animate is complete, set the tracker variables
				      tooltip.menushown = false;
				      tooltip.menubeingshown=false;
				      tooltip.inmenu=false;
				      tooltip.active = false;
				      // hide the popup entirely after the effect (opacity alone doesn't do the job)
				      tooltip.setText('');    
				      $("#"+tooltip.id).css({		      	
				      	top: '-1000px',
				      	left:'-1000px',
				      	visibility: 'hidden'
				      });
				    });
					}
			  }, tooltip.hidedelaytime);
			}					
		}else{
			document.onmousemove=tooltip.saveonmouseover;
			tooltip.saveonmouseover=null;						
			tooltip.setText('');
			tooltip.active = false;
			$("#"+tooltip.id).css({		      	
      	top: '-1000px',
      	left:'-1000px',
      	visibility: 'hidden'
      });
		}		
	};
	
	tooltip.setWidth = function (width) {
		if (width!=null && width!='undefined' && width!=0){
			width = $("#"+tooltip.id).attr("width");
			if (width!=null && width!='undefined' && width!=0)
				tooltip.width = width; 			
		}
		$("#"+tooltip.id).attr("width",width);
	};

	tooltip.setText = function (text) {
		if (text!=null && text!='undefined')
			$("#"+tooltip.textid).html(text);		
		return false;
	};

	tooltip.showBusy = function () {
		tooltip.setText('<p style="width:115px"><img src="' + BASE_URL + 'img/busy.gif" /><p>'); 
	};
	
	tooltip.setMenuPosition = function (e){
		if (tooltip.ie) {
			xL = event.clientX;
			yL = event.clientY;
		} else {
			xL = e.pageX;
			yL = e.pageY;
		}		
		if (tooltip.ie) {
			xL +=  document.documentElement.scrollLeft;
			yL +=  document.documentElement.scrollTop;
		}
		tooltip.x = (xL-(tooltip.width/2));		
		tooltip.y = yL + tooltip.menuoffsety;
	};

	// Moves the tooltip element
	tooltip.mouseMove = function (e) {
	   // we don't use "this", but tooltip because this method is assign to an event of document
	   // and so is dreferenced	
		if (tooltip.ie) {
			tooltip.x = event.clientX;
			tooltip.y = event.clientY;
		} else {
			tooltip.x = e.pageX;
			tooltip.y = e.pageY;
		}		
		tooltip.moveTo( tooltip.x + tooltip.offsetx , tooltip.y + tooltip.offsety);
	};

	// Move the tooltip element
	tooltip.moveTo = function (xL,yL) {				
		if (tooltip.ie) {
			xL +=  document.documentElement.scrollLeft;
			yL +=  document.documentElement.scrollTop;
		}
		/*if (tooltip.tooltipText.clientWidth > 0  && document.documentElement.clientWidth > 0 && 
		xL > document.documentElement.clientWidth * 0.55) {
			xL = xL - tooltip.tooltipText.clientWidth - 2*tooltip.offsetx;
		}*/
		$("#"+tooltip.id).css({		      	
    	top:  yL +"px",
    	left: xL + "px"    	
    });
	};
	
	tooltip.clear = function (event) {
		if (tooltip.ajaxtimeout != null) {
			clearTimeout(tooltip.ajaxtimeout);
			tooltip.ajaxtimeout = null;
		}
		tooltip.hide(event);
	};

	tooltip.ajax_delayed = function (event, script, id, maxcache) {
		maxcache = maxcache || 600000; // 10 minutes in cache
		if (tooltip.active) return false;
		if ((object = tooltip.cache.get(script+id)) != undefined) {
			tooltip.show(event, object[script+id]);
		} else {
			tooltip.show(event, '<p style="width:115px"><img src="' + BASE_URL + 'img/busy.gif" /><p>'); // Translate this to your language: it's "loading..." ;-)
			tooltip.ajaxtimeout = setTimeout("tooltip.ajax_request('"+script+"', '"+id+"', "+maxcache+")", tooltip.ajaxtime);
		}
	};
	
	tooltip.menu_ajax_delayed = function (event, script, id, maxcache) {
		if (tooltip.active) return false;
		
		if (maxcache>0) maxcache = maxcache || 600000; // 10 minutes in cache
							
		if ((object = tooltip.cache.get(script+id)) != undefined) {			
			tooltip.showMenu(event, object[script+id]);
		} else {
			tooltip.showMenu(event, '<p style="width:115px"><img src="' + BASE_URL + 'img/busy.gif" /><p>');
			tooltip.ajaxtimeout = setTimeout("tooltip.ajax_request('"+script+"', '"+id+"', "+maxcache+")", tooltip.ajaxtime);
		}
	};

	tooltip.ajax_request = function(script, id, maxcache) {
		var url = BASE_URL + 'backend/'+script+'?id='+id;
		tooltip.ajaxtimeout = null;
		$.ajax({
			url: url,
			dataType: "html",
			success: function(html) {
				if (maxcache>0) tooltip.cache.set(script+id, html, {'ttl':maxcache});
				tooltip.setText(html);
			}
		});
	};
	
	tooltip.user_vote = function(event, id) {		
		//tooltip.hidedelaytime = 2000;		
		tooltip.menu_ajax_delayed(event, 'get_user_vote.php', id, 0);				
	};
	
	tooltip.set_vote_cache = function(id, cachetime, html) {
		if (cachetime>0) tooltip.cache.set('get_user_vote.php'+id, html, {'ttl':cachetime});					
	};
	
	tooltip.msg_login= function(event) {						
		tooltip.showMenu(event, '<p class="info"><a href="' + BASE_URL + 'user_login.php" class="nar_bold">Autenticate</a> para poder votar.</p>');		
	};
	
	tooltip.menu_discard= function(event, id) {		
		script = 'get_user_vote.php';
		if ((object = tooltip.cache.get(script+id)) != undefined){	//Comprobamos si no se puede votar hasta pasado un tiempo 
			tooltip.showMenu(event, object[script+id]);
		}else{
			var menu_html = '<ul class="menu"> ' +
				//'<li><a href="#" onclick="vote_delayed(\'' + VOTE_OBSOLETE + '\','+ id +');return false;">Información obsoleta</a></li>' +
				'<li><a href="#" onclick="vote_delayed(\'' + VOTE_DUPLICATED + '\','+ id +');return false;">Duplicado</a></li>' +
				'<li><a href="#" onclick="vote_delayed(\'' + VOTE_NO_TAPA_BAR + '\','+ id +');return false;">No pone tapas gratis</a></li>' +
				'<li><a href="#" onclick="vote_delayed(\'' + VOTE_NO_EXISTS + '\','+ id +');return false;">No existe</a></li>' +
				'</ul>';
			tooltip.showMenu(event, menu_html);
		}		
	};
	
	tooltip.comment = function(event, order, bar_id) {		
		tooltip.ajax_delayed(event, 'get_comment_tooltip.php', order+'&bar_id='+bar_id, 0);
	};