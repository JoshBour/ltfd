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
    // ================= Game Page related =================//

    // search bar auto-complete
    $('input[name="gameSearch"]').on('keyup',function(event){
        var key = event.keyCode || event.charCode;
        var value = $(this).val().trim();
//        if(value.length > 1){
//            var resultList = $('#gamesList');
//            var jqxhr = $.get(baseUrl + '/game/search/name/' + value, function(data){
//                resultList.html('').append(data).slideDown();
//            }).fail(function(){
//                  addMessage('Something with wrong with the game search, please try again.');
//            });
//        }else if(value.length == 0){
//
//            if( key == 8 || key == 46 ){
//                if(value.length == 0) value = 'allgames';
//            }
//        }
        if(value.length == 0) value = 'allgames';
        if(value.length > 1){
            var resultList = $('#gamesList');
            var jqxhr = $.get(baseUrl + '/game/search/name/' + value, function(data){
                resultList.html('').append(data);
            }).fail(function(){
                    addMessage('Something with wrong with the game search, please try again.');
            });
        }
    });

    $(document).on('mouseenter','#gamesList li',function(e){
        var item = $(this);
        if(!item.is(':animated'))
            item.animate({
                "margin-top" : "7px",
                "margin-bottom" : "13px"
            },200);
    });

    $(document).on('mouseleave','#gamesList li',function(e){
        var item = $(this);
        item.animate({
            "margin-top" : "10px",
            "margin-bottom" : "10px"
        },200);
    });

    $('.gameFollow').on('click',function(e){
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('url');
    });
});
