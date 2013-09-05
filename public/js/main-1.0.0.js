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
    var isSearching = false;
    var completeList = $('#gamesList').html();
    $('input[name="gameSearch"]').on('keyup',function(event){
//    //            if( key == 8 || key == 46 ){
//    //                if(value.length == 0) value = 'allgames';
//    //            }
//    //        }
        event.preventDefault();
        if(!isSearching){
            isSearching = true;
            var key = event.keyCode || event.charCode;
            var resultList = $('#gamesList');
            var value = $(this).val().trim();
            if(value.length == 0){
                resultList.html(completeList);
            }else if(value.length > 1){
                resultList.slideUp();
                $.ajax({
                    url:baseUrl + '/game/search/name/' + value,
                    timeout: 5000,
                    ifModified : true
                }).done(function(data){
                        resultList.html('').append(data).slideDown();
                }).fail(function(jqXHR, textStatus){
                    if(textStatus == 'timeout'){
                        alert('The search has timed out, please try again.');
                    }else{
                        addMessage('Something with wrong with the game search, please try again.');
                    }
                });
            }
            isSearching = false;
        }
    });

    $(document).on('mouseenter','#gamesList li, #following li',function(e){
        var item = $(this);
        if(!item.hasClass('animated')){
            item.animate({
                marginTop : '-=3',
                marginBottom : '+=3'
            },100);
            item.addClass('animated');
        }
    });

    $(document).on('mouseleave','#gamesList li, #following li',function(e){
        var item = $(this);
        if(item.hasClass('animated')){
            item.animate({
                marginTop : '+=3',
                marginBottom : '-=3'
            },100);
            item.removeClass('animated');
        }
    });

    $('.gameFollow').on('click',function(e){
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('url');
    });

    $(document).on('click','.gameMeta .follow, .gameMeta .unfollow',function(e){
        e.preventDefault();
        var element = $(this);
        var type = element.attr('class');
        var followers = element.siblings('.gameFollowers');
        var gameName = element.closest('li').children('.gameName').attr('title');
        $.ajax({
            url:baseUrl + '/game/'+ gameName + '/connect/' + type,
            timeout: 5000
        }).done(function(data){
                if(data.success == 1){
                    if(type == 'follow'){
                        element.replaceWith($('<a/>',{
                                "href":"#",
                                "class":"unfollow",
                                "title" : "Unfollow the game",
                                "text" : "Unfollow"
                            }
                        ) );
                        followers.html(data.followers);
                    }else{
                        element.replaceWith($('<a/>',{
                                "href":"#",
                                "class":"follow",
                                "title" : "Follow the game",
                                "text" : "Follow"
                            }
                        ) );
                        followers.html(data.followers);
                    }
                }
                addMessage(data.message);
        }).fail(function(jqXHR, textStatus){
                if(textStatus == 'timeout'){
                    alert('The request has timed out, please try again.');
                }
        });

    });

    // ======================================================= //
    // ================= User Page related =================//
    $('#following .gameMeta').on('click',function(event){
        if(confirm('Do you really want to stop following this game?')){
            var element = $(this);
            var gameName = element.siblings('.gameInfo').find('.gameName').html();
            $.ajax({
                url:baseUrl + '/game/'+ gameName + '/connect/unfollow',
                timeout: 5000
            }).done(function(data){
                    if(data.success == 1){
                        element.closest('li').detach();
                    }
                    addMessage(data.message);
                }).fail(function(jqXHR, textStatus){
                    if(textStatus == 'timeout'){
                        alert('The request has timed out, please try again.');
                    }
                });
        }
        return;
    });
});
