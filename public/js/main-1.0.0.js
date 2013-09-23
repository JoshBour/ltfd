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
            if(focusedDiv.attr('id') == 'stage'){
                focusedDiv.find('iframe').detach();
                focusedDiv.hide();
            }else{
                focusedDiv.detach();
            }
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
        var value = $(this).val().trim();
        delay(function(){
            event.preventDefault();
            if(!isSearching){
                isSearching = true;
                var key = event.keyCode || event.charCode;
                var resultList = $('#gamesList');
                console.log(value);
                if(value.length == 0){
                    resultList.html(completeList);
                }else if(value.length > 2){
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
        }, 500);
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

//    $('.gameFollow').on('click',function(e){
//        e.preventDefault();
//        var btn = $(this);
//        var url = btn.attr('url');
//    });

    $(document).on('click','.gameMeta .follow, .gameMeta .unfollow',function(e){
        e.preventDefault();
        var element = $(this);
        var type = element.attr('class');
        var followers = element.siblings('.gameFollowers');
        var gameId = element.closest('li').attr('class').split(' ')[0].substring(5);
        $.ajax({
            url:baseUrl + '/game/connect/' + type + '/id/' + gameId,
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

    $(document).on('click','.feedMeta span[class^="thumb"]',function(e){
        var btn = $(this);
        var otherBtn = btn.siblings('span[class^="thumb"]');
        if(!btn.hasClass('disabled')){
            btn.addClass('disabled');
            otherBtn.removeClass('disabled');
            var rating = "up";
            var feedId = btn.closest('li').attr('class').substring(5);
            if(btn.hasClass('thumbDown')) rating = "down";

            console.log(rating);
            $.ajax({
                url: baseUrl + '/feed/rate/' + rating + '/id/' + feedId,
                timeout : 5000
            }).success(function(data){
               console.log(data);
            });

        }
    });

    $('#feeds li').on('mouseenter',function(e){
        var item = $(this);
        if(!(item.find('.videoMask').length > 0)){
            $('<div />',{
                "class" : "videoMask"
            }).prependTo($(this));
        }
    });

    $('#feeds li').on('mouseleave',function(e){
        var item = $(this);
        var mask = item.find('.videoMask');
        if(mask.length > 0) mask.detach();
    });

    $(document).on('click','#feeds li .videoMask',function(e){
        var playBtn = $(this);
        var listItem = playBtn.closest('li');
        var id = listItem.attr('data-video-id');
        var stage = $('#stage');
        var video = stage.find('iframe');
        var feedId = listItem.attr('class').substring(5);
        var commentsList = $('#stage #commentList');

        var videoPlayer = $('<iframe/>',{
            "id" : "youtubeVideo",
            "frameborder" : 0,
            "src" : "http://www.youtube.com/embed/" + id + "?autoplay=1&controls=0",
            "width" : 970,
            "height" : 546
        }).prependTo(stage.children('#frameWrapper'));

        focusedDiv = stage;
        stage.addClass('target-'+feedId).focusLight().show();

        // empty the list and get the comments
        commentsList.html('');
            $.ajax({
                url:baseUrl + '/feed/comment/list',
                data : {
                    "feedId" : feedId
                }
            }).done(function(data){
                commentsList.append(data);
            });

        // if the user watches this in the userPage, don't add it to the history
        if(!$('body').hasClass('userPage')){
            $.ajax({
                url:baseUrl + '/feed/user-feed-category',
                type: "POST",
                data : {
                    'feed' : feedId,
                    'category' : 'history'
                }
            }).done(function(data){
                console.log(data);
            });
        }
    });

    $(document).on('click','#feeds li .favorite',function(e){
        var btn = $(this);
        var category = 'favorites';
        var defAction = '';
        if(!btn.hasClass('disabled')){
            btn.addClass('disabled');
        }else{
            btn.removeClass('disabled');
            var defAction = 'unfavorite';
        }
        var listItem = btn.closest('li');
        $.ajax({
            url:baseUrl + '/feed/user-feed-category',
            type: "POST",
            data : {
                'feed' : listItem.attr('class').substring(5),
                'category' : 'favorites',
                'defAction' : defAction
            }
        }).done(function(data){
                console.log(data);
            });
    })

    $(document).on('submit','#commentForm',function(e){
        e.preventDefault();
        var form = $(this);
        var stage = $('#stage');
        var feedId= stage.attr('class').split(' ')[0].substring(7);
        $.ajax({
            url: baseUrl + '/feed/comment/add?feedId='+feedId,
            type: "POST",
            data : form.serialize()
        }).done(function(data){
               console.log(data);
        }).fail(function(jqXHR, textStatus){
            if(textStatus == 'timeout'){
                alert('The request has timed out, please try again.');
            }
                console.log(textStatus);
        });

        return false;
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
