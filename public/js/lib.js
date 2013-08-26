	// ======================================================= //
	// ================= Global Functions ================= //
	// ======================================================= //	

	// shows the errors
	function showErrors(errors,section){
        var errorsDiv = $('#errors');
		if(errorsDiv.is(':visible')){
            errorsDiv.detach();
		}
		var errorList = '<ul id="errors">';
		if(errors instanceof Array){
			for(var i=0;i<errors.length;i++){
				errorList += "<li>"+errors[i]+"</li>";
			}
		}else{
			errorList += "<li>"+errors+"</li>";
		}
		errorList += "</ul>";
		$(section).before(errorList);
	}
	
	// adds a new message to the flash messenger
	function addMessage(message){
		var flash = $('#flash');
		if(flash.is(":visible")){
			flash.html(message);
		}else{
			flash = $('<div />',{
				id : "flash",
				text : message
			}).prependTo('body');
		}
		flash.setRemoveTimeout(5000);
	}	
	

	
	function checkEmpty(incArray){
		var emptyFields = [];

		if(typeof (incArray) === 'object'){
			for(key in incArray){
				if(isEmpty(incArray[key]))
					emptyFields.push(key);
			}
		}else{		
			for(var i=0;i<incArray.length;i++){
				if(isEmpty(incArray[i])){
					emptyFields.push(incArray[i]);
				}
			}
		}
		if(emptyFields.length > 0){
			return emptyFields;
		}
		return false;
	}
	
	function isEmpty(variable){
		if(variable == "" || variable == null || typeof variable === 'undefined'){
			return true;
		}else{
			return false;
		}
	}
	
	// it does a poll searching for new events
	function doPoll(){
		var start = new Date().getTime();
		var notificationLink = $('#notificationDisplay');
		var notificationNum = 0;
		if(notificationLink.html() != ''){
			notificationNum = notificationLink.html();
		}
		$.get(baseUrl+'/event/get-event/format/json',function(data){
			if(data.success == 1){
				notificationLink.addClass('active');
				notificationNum++;
				notificationLink.html(notificationNum);
				loadEvents(true);
			}

		    var end = new Date().getTime();
		    console.log('poll milliseconds passed', end - start);
		});		
	}	
	
	// returns an array that contains the name,extension,original path
	// of the provided image
	function getImageDetails($imagePath){
		var splitImg = $imagePath.split('/');
		var fullImageName = splitImg[splitImg.length - 1];
		var fullImageNameArray = fullImageName.split('.');
		var imagelessPath = $imagePath.substring(0,fullImageName.length);
		var imageDetails = [fullImageNameArray[0],fullImageNameArray[1],imagelessPath];
		return imageDetails;
	}

	function loadEvents(isNew){
		$.get(baseUrl + '/event/list-events/isnew/'+isNew+'/format/html',function(data){
			$('#event-list').html('');
			$('#event-list').html(data);
		});
	}
	
	function getData(url, data, callback){
		return $.ajax({
			url : baseUrl + "/" + url,
			data : data
		});
	}
	
	(function($){
		var ajaxCall = function(url, data, callback) {
				return $.ajax({
				url: "http://api.dribbble.com" + url,
				dataType: "jsonp",
				data: data
				}).done(callback);
			};
	})(jQuery);
	
	// mini plugin that will hide an element according to the timout given
	(function($){
		$.fn.setRemoveTimeout = function(milisecs){
			var element = $(this);
			if(element.length > 0){
				setTimeout(function(){
					$(element).slideUp().detach();
				}, milisecs);
			}
			return element;
		};
	})(jQuery);
	
	// "turn off" the lights and focus the specific element
	(function($){
		$.fn.focusLight = function(){
			// set the default value for focusedDiv
			var element = $(this);
			//focusedDiv = typeof focusedDiv !== 'undefined' ? $(focusedDiv) : $(this);
			
			// add the shadow to the body
			$('<div />',{'id':'shadow'}).appendTo('body');
			
			element.addClass('focused');
			
			return element;
		};
	})(jQuery);
	
	// "turn on" the lights
	(function($){
		$.fn.unfocusLight = function(){
			var element = $(this);
			$('#shadow').detach();
			element.removeClass('focused');
			
			return element;
		};
	})(jQuery);

	// small jquery plugin that will show a tip box
	(function($){
		$.fn.toggleTitleBar = function(){
			var element = $(this);
			var body = $('body');
			var tip = body.children('.tip');
			if(tip.is(":visible")){
				tip.detach();
			}else{
				var title = element.attr('content-title');
				var position = element.position();
				var height = element.height();
				var top = (position.top - height) - 30;
				var paddingTop = element.css('padding-top');
				if(paddingTop < 10){
					top += paddingTop;
				}
				var left = position.left;
				$('<div />',{
					"class" : "tip",
					 css : {
						"top" : top,
						"left" : left
					 },
					 text : title
				}).prependTo(body);
				
				// we adjust the left position of the element so that it appears centered to the element
				tip = body.children('.tip');
				
				$('<div />',{
					"class" : "tipArrow",
					css : {
						"margin-left" : "-" + tip.css('padding-left')
					}
				}).appendTo(tip);
				
				var leftDif = Math.ceil(Math.ceil(tip.outerWidth() - element.outerWidth())/ 2) + 1;
				tip.css('left',left-leftDif);
			}
		};
	})(jQuery);	
	
	// plugin that will show or hide the loading image
	(function($){
		$.fn.toggleLoadingImage = function(){
			var element = $(this);
			// we use .find for more secure results
			var loadingImg = element.children('.loadingImg');
			if(loadingImg.length > 0){
				loadingImg.detach();
			}else{
				$('<div />',{'class':'loadingImg'}).appendTo(element);
			}
		};
	})(jQuery);
	
	function empty(variable){
		variable = variable.trim();
		if(variable != null && variable != "" && variable != 'undefined'){
			return false;
		}
		return true;
	}	
	