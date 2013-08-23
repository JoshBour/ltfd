$(function() {

	// ======================================================= //
	// ================= Global Variables ================= //
	// ======================================================= //
	var cacheArray = [];
	var flash = $('#flash');
	var focusedDiv = "";


	// ======================================================= //
	// ============ Common content manipulation =========== //

	if (flash.is(':visible')) {
		flash.setRemoveTimeout(5000);
	}
	
	// if the shadow div exists, when you click outside of the 
	// focused box it should be reverted to normal
	$(document).on('click',function(event){
		if(event.target.id == 'shadow'){
			focusedDiv.unfocusLight();
			focusedDiv.detach();
		}
	});
	
	// This will toggle the tip box of all elements with class 'entitled'
	$(document).on('mouseenter mouseleave','.entitled',function(){
		$(this).toggleTitleBar();
	});	
	
	// This will make all images with class hoverable to be hovered (duh)
	previousImage = '';
	$('img[class*="hoverable"]').on('mouseenter',function(){
		var src = $(this).attr('src');
		previousImage = src;
		details = getImageDetails(src);
		$(this).attr('src',baseUrl + '/images/'+details[0]+'-hover.'+details[1]);
	}).on('mouseleave',function(){
		$(this).attr('src',previousImage);
		previousImage = '';
	});

	// by clicking an element that has the class "-Toggle"
	// it will toggle the "#-" element
	$('*[class$="Toggle"]').on('click',function(){
		var divElement = $(this);
		var className = divElement.attr('class');
		var elementToBeToggled = className.substr(0,(className.length - 6));
		$('#'+ elementToBeToggled).slideToggle();
	});	
	
	// Tab related code
	$('.tab-list li').on("click", function() {
				var clicked = $(this);
				var active = clicked.parent().find(".tab-active").attr('class').split(" ")[0];
				if (!$(clicked).hasClass('tab-active')) {
					var tab = clicked.attr('class').split(" ")[0];
					$('#' + active).addClass('inactive');
					$('.' + active).removeClass('tab-active');

					$('#' + tab).removeClass('inactive');
					$('.' + tab).addClass('tab-active');
				}
	});

	// search bar auto-complete
	$('#searchBox').on('keyup',function(event){
		//var key = String.fromCharCode(event.keyCode);
		var value = $(this).val();
		var resultBox = $('#resultBox');
		var resultBoxList = resultBox.children('ul');
		if(value.length >= 3){
			resultBox.show();
			var jqxhr = $.post(baseUrl + '/search/get-users/value/'+value+'/format/html', function(data){
				resultBoxList.find('li').detach();
				resultBoxList.append(data);
			}).fail(function(){
				alert('Something with wrong with the feed search, please try again.');
			});
		}else{
			resultBox.hide();
		}
	});
	
	// prevent the search box from being submitted
	$('#searchInput').on('submit',function(event){
		event.preventDefault();
	});	
	
	
	// ======================================================= //
	// ================= Home Page =================//		
	$('.slidePlay').on('click',function(e){
		var div = $(this).parent();
		var id = div.attr('class').substr(6, div.attr('class').length - 6);

		var player = $('<iframe />',{
			"id" : "ytPlayer",
			"width" : 1024,
			"frameborder" : 0,
			"height" : 580,
			"src" : "http://player.vimeo.com/video/" + id + "?autoplay=1&title=1&byline=1&portrait=1&loop=0&hd=1"
		});
		
		focusedDiv = $('<div />',{
			"class" : "videoPreview",
		}).append(player).prependTo('body').focusLight();		
	});
	
	if($('body').hasClass('homepage')){
		var slideArray = []
		var slideImages = [];
		var slideDescriptions = [];
		var slideIds = [];
		$('#slider div[class^="slide"]').each(function(){
			var slide = $(this);
			slideImages.push(slide.find('.snapshot').attr('src'));
			slideIds.push(slide.attr('class').substr(6, slide.attr('class').length - 6));
			if(slide.attr('id') == 'displayedSlide'){
				slideDescriptions.push($('#slideNavigation span').html());
			}else{
				slideDescriptions.push(slide.find('.filmDescription').html());
			}
			slideArray.push($(this));
		});		
		var currentSlide = 0;
		var previousSlide = slideArray.length - 1;
		if(slideArray.length > 1){
			var nextSlide = 1;
		}else{
			var nextSlide = 0;
		}

	}
	
	$('#leftSlideArrow').on('click',function(e){
		
		// change the slides
		nextSlide = currentSlide;
		if(currentSlide == 0){
			currentSlide = slideArray.length - 1; // rewind the slide
			previousSlide -= 1;
		}else{
			currentSlide -= 1
			if(previousSlide == 0){
				previousSlide = slideArray.length - 1
			}else{
				previousSlide -= 1;
			}
		}
		
		// make the actual changes
		var curSlide = slideArray[currentSlide];	
		var nSlide = slideArray[nextSlide];
		var prevSlide = slideArray[previousSlide];
		var displayedSlide = $('#displayedSlide');	
		var snapshot = displayedSlide.find('.snapshot');		
		
		snapshot.attr('src',slideImages[currentSlide]);
		displayedSlide.attr('class','slide-'+slideIds[currentSlide]);
		$('#slideNavigation span').html(slideDescriptions[currentSlide]);
		
	});
	
	$('#rightSlideArrow').on('click',function(e){
		var temp = currentSlide;
		currentSlide = nextSlide;
		previousSlide = temp;
		if(nextSlide == (slideArray.length-1)){
			nextSlide = 0;
		}else{
			nextSlide++;
		}

		var curSlide = slideArray[currentSlide];	
		var nSlide = slideArray[nextSlide];
		var prevSlide = slideArray[previousSlide];
		var displayedSlide = $('#displayedSlide');	
		var snapshot = displayedSlide.find('.snapshot');		
		
		snapshot.attr('src',slideImages[currentSlide]);
		displayedSlide.attr('class','slide-'+slideIds[currentSlide]);
		$('#slideNavigation span').html(slideDescriptions[currentSlide]);		
	});	
	
	
	// ======================================================= //
	// ================= Portfolio Page =================//		
	$('ul.films li, #latestFilms ul li').on('mouseenter',function(e){
		var item = $(this);
			if(!(item.find('.videoMask').length > 0)){
			$('<div />',{
				"class" : "videoMask",
			}).prependTo($(this));
		}
	});
	
	$('ul.films li, #latestFilms ul li').on('mouseleave',function(e){
		var item = $(this);
		var mask = item.find('.videoMask');
		if(mask.length > 0) mask.detach();
	});
	
	$('ul.films li, #latestFilms ul li').on('click',function(e){
		var film = $(this);
		var id = film.attr('class').substr(6, film.attr('class').length - 6);

		
		var player = $('<iframe />',{
			"id" : "vimeoVid",
			"width" : 1024,
			"frameborder" : 0,
			"height" : 580,
			"src" : "http://player.vimeo.com/video/" + id + "?autoplay=1&title=1&byline=1&portrait=1&loop=0&hd=1"
		});
		
		focusedDiv = $('<div />',{
			"class" : "videoPreview listVideo",
		}).append(player).prependTo('body').focusLight();
	});
	
	// ======================================================= //
	// ================= Data Item Page Related =================//	
	
	
	// edit the data item
	$(document).on('click','.dataItem .edit',function(){
		var edit = $(this);
		var dataItem = edit.closest('.dataItem');
		var controller = dataItem.attr('class').split(' ')[1]; // the destination controller must be the 2nd class
		var dataId = dataItem.attr('id').substr(5,dataItem.attr('id').length - 5) 
		console.log(controller);
		$.get(baseUrl + '/admin/' + controller + '/edit/'+dataId,function(data){
			if(!$.trim(data)){
				console.log('penis');
			}else{
				focusedDiv = $('<div />',{
					"class" : controller,
					"id" : "editBox",
					"html" : data
				}).prependTo('body').focusLight();
			}
		});
	});
	
	$(document).on('keyup','#updateForm input, #updateForm textarea',function(){
		$(this).addClass('edited');
	});
	
	$(document).on('focus','.dateInput',function(){
		var input = $(this);
		input.attr('gldp-id',"mydate").glDatePicker({
			onClick: function(target, cell, date, data) {
		        target.val(date.getFullYear() + '-' +
		        		("0" + (date.getMonth() + 1)).slice(-2) + '-' +
		        		("0" + date.getDate()).slice(-2));
		    }				
		});		
		if(input.closest('form').attr('id') == 'updateForm'){
			var curDate = input.val().split('/');
			var gldp = input.siblings('.gldp-default');
			var left = gldp.css('left');
			gldp.css('margin-left','-' + left);
			gldp.css('margin-top', '-120px');
		}
	})


	
	$(document).on('submit','#updateForm',function(e){
		e.preventDefault();
		var form = $(this);
		var controller = form.attr('action');
		var id = form.find('input[name$="[id]"]').attr('value');
	    var options = { 
	            url:	baseUrl + '/admin/' + controller + '/edit/'+id,
	            success:       function(data){
	    			if(data.success == 1){
	    				focusedDiv.unfocusLight();
	    				focusedDiv.detach();		
	    				form.find('input, textarea').each(function(){
	    					var input = $(this);
	    					var uneditedName = input.attr('name').split('[');
	    					if(uneditedName != undefined){
	    						if(input.hasClass('edited')){
	    							$('#main table tr[id="data-' + id + '"]').find('td[class="' + uneditedName[1].split(']')[0] + '"]').html(input.val());
	    						}
	    					}
	    				});
	    				if(data.force_refresh == 1){
	    					location.reload();
	    				}else{
	    					addMessage(data.message);
	    				}
	    			}else if(data.success == 0){
	    				info = $('#editBox .info');
	    				if(info.length != 0){
	    					info.html(data.message);
	    				}else{
		    				$('<div />',{
		    					'class' : 'info',
		    					'text'  : data.message
		    				}).insertBefore(form);	  
	    				}
	    			}else{
	    				// fix so that the errors display properly and delete the old ones
	    				$('#editBox').html(data);
	    			}
	    		}
	    }; 		
		$(this).ajaxSubmit(options);
		return false;
	});

	// delete the data item and remove it from the list
	$(document).on('click','.dataItem .delete',function(){
		var deleteBtn = $(this);
		var dataItem = deleteBtn.parent();
		if(confirm('Are you sure about the deletion?')){
			var data = {
					id : dataItem.attr('id').substr(5,dataItem.attr('id').length - 5),
			};
			var url = dataItem.attr('class').split(' ')[1];
			console.log(data);
			$.post(baseUrl + '/admin/' + url + '/delete',data,function(data){
				console.log(data);
				if(data.success == 1){
					dataItem.detach();
				}
				addMessage(data.message);
			});
		}
	});
	
	
	// ======================================================= //
	// ================= Log Page Related =================//		
	$('#sortLogsByProject select[name="projectActivity"]').on('change',function(){
		var val = $(this).val();
		if(val == 0){
			window.location.replace(baseUrl+'/logs');
		}else{
			window.location.replace(baseUrl+'/logs/sort/project/id/'+ val);
		}
	});	
	
	$('#sortLogsByWorker select[name="workerActivity"]').on('change',function(){
		var val = $(this).val();
		if(val == 0){
			window.location.replace(baseUrl+'/logs');
		}else{
			window.location.replace(baseUrl+'/logs/sort/worker/id/'+ val);
		}
	});		
});
