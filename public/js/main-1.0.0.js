$(function () {

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
    $(document).on('click', function (event) {
        if (event.target.id == 'shadow') {
            focusedDiv.unfocusLight();
            if (focusedDiv.attr('id') == 'stage') {
                focusedDiv.find('iframe').detach();
                focusedDiv.hide();
            } else {
                focusedDiv.detach();
            }
        }
    });

    // This will toggle the tip box of all elements with class 'entitled'
    $(document).on('mouseenter mouseleave', '.entitled', function () {
        $(this).toggleTitleBar();
    });

    // This will make all images with class hoverable to be hovered (duh)
    previousImage = '';
    $('img[class*="hoverable"]').on('mouseenter',function () {
        var src = $(this).attr('src');
        previousImage = src;
        details = getImageDetails(src);
        $(this).attr('src', baseUrl + '/images/' + details[0] + '-hover.' + details[1]);
    }).on('mouseleave', function () {
            $(this).attr('src', previousImage);
            previousImage = '';
        });

    // by clicking an element that has the class "-Toggle"
    // it will toggle the "#-" element
    $('*[class$="Toggle"]').on('click', function () {
        var divElement = $(this);
        var className = divElement.attr('class');
        var elementToBeToggled = className.substr(0, (className.length - 6));
        $('#' + elementToBeToggled).slideToggle();
    });

    // Tab related code
    $('.tab-list li').on("click", function () {
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


    // prevent the search box from being submitted
    $('#searchInput').on('submit', function (event) {
        event.preventDefault();
    });

    // ======================================================= //
    // ================= Game Page related =================//

    /**
     * @description The game search bar function.
     */
    var isSearching = false;
    var completeList = $('#gamesList').html();
    $('input[name="gameSearch"]').on('keyup', function (event) {
        var value = $(this).val().trim();
        delay(function () {
            event.preventDefault();
            if (!isSearching) {
                isSearching = true;
                var key = event.keyCode || event.charCode;
                var resultList = $('#gamesList');
                if (value.length == 0) {
                    resultList.html(completeList);
                } else if (value.length > 2) {
                    resultList.slideUp();
                    $.ajax({
                        url: baseUrl + '/game/search/name/' + value,
                        timeout: 5000
                    }).done(function (data) {
                            resultList.html('').append(data).slideDown();
                        }).fail(function (jqXHR, textStatus) {
                            if (textStatus == 'timeout') {
                                alert('The search has timed out, please try again.');
                            } else {
                                addMessage('Something with wrong with the game search, please try again.');
                            }
                        });
                }
                isSearching = false;
            }
        }, 500);
    });

    /**
     * @description Animates the list when the mouseenter event is triggered
     */
    $(document).on('mouseenter', '#gamesList li, #following li', function (e) {
        var item = $(this);
        if (!item.hasClass('animated')) {
            item.animate({
                marginTop: '-=3',
                marginBottom: '+=3'
            }, 100);
            item.addClass('animated');
        }
    });

    /**
     * @description Animates the list when the mouseleave event is triggered
     */
    $(document).on('mouseleave', '#gamesList li, #following li', function (e) {
        var item = $(this);
        if (item.hasClass('animated')) {
            item.animate({
                marginTop: '+=3',
                marginBottom: '-=3'
            }, 100);
            item.removeClass('animated');
        }
    });

    /**
     * @description Follow or unfollow a game
     */
    $(document).on('click', '.gameMeta .follow, .gameMeta .unfollow', function (e) {
        e.preventDefault();
        var element = $(this);
        var type = element.attr('class');
        var followers = element.siblings('.gameFollowers');
        var gameId = element.closest('li').attr('class').split(' ')[0].substring(5);
        console.log(type);
        delay(function () {
        $.ajax({
            url: baseUrl + '/game/connect/' + type + '/id/' + gameId,
            dataType: 'json'
        }).success(function (data) {
                if (data.success == 1) {
                    if (type == 'follow') {
                        element.replaceWith($('<a/>', {
                                "href": "#",
                                "class": "unfollow",
                                "title": "Unfollow the game",
                                "text": "Unfollow"
                            }
                        ));
                        followers.html(parseInt(followers.html())+1);
                    } else {
                        element.replaceWith($('<a/>', {
                                "href": "#",
                                "class": "follow",
                                "title": "Follow the game",
                                "text": "Follow"
                            }
                        ));
                        followers.html(parseInt(followers.html())-1);
                    }
                }
                addMessage(data.message);
            }).fail(function (jqXHR, textStatus) {
                alert(textStatus);
                console.log(jqXHR.statusText);
                if (textStatus == 'timeout') {
                    alert('The request has timed out, please try again.');
                }
            });
        },500);
    });

    /**
     * @description Rates a feed.
     */
    $(document).on('click', '.feedMeta .like', function (e) {
        var btn = $(this);
        var isDisabled = btn.hasClass('disabled');
        var type = (isDisabled) ? "unlike" : "like";
        var feed = btn.closest('li');
        var feedId = feed.attr('class').substring(5);
        $.ajax({
            url: baseUrl + '/feed/rate',
            timeout: 5000,
            type : "POST",
            data : {
                "type" : type,
                "feedId" : feedId
            }
        }).success(function (data) {
                if (data.success == 1) {
                    if(isDisabled){
                        btn.removeClass('disabled');
                    }else{
                        btn.addClass('disabled');
                    }
                    addMessage(data.message);
                } else {
                    addMessage(data.message);
                }
                console.log(data);
            });


    });

    /**
     * @description Removes a feed.
     */
    $(document).on('click', '.feedMeta .remove', function (e) {
        if(confirm("Do you really want to delete this feed?")){
            var btn = $(this);
            var feed = btn.closest('li');
            var feedId = feed.attr('class').substring(5);
            $.ajax({
                url: baseUrl + '/feed/remove',
                timeout: 5000,
                type : "POST",
                data : {
                    "feedId" : feedId
                }
            }).success(function (data) {
                    console.log(data);
                    if(data.success == 1) feed.detach();
                    addMessage(data.message);
                });
        }

    });

    /**
     * @description Add a feed to the user's favorites.
     */
    $(document).on('click', '#feeds li .favorite', function (e) {
        var btn = $(this);
        var type = 'favorite';
        var listItem = btn.closest('li');
        var feedId = listItem.attr('class').substring(5);
        var isGenerated = 0;
        var activeGame = $('a[id^="activeGame"]').attr('id').substring(11);

        if (!btn.hasClass('disabled')) {
            btn.addClass('disabled');
        } else {
            btn.removeClass('disabled');
            type = 'unfavorite';
        }
        $.ajax({
            url: baseUrl + '/feed/set-favorite',
            type: "POST",
            data: {
                'feedId': feedId,
                'type': type,
                'activeGame': activeGame
            }
        }).success(function (data) {
                if($("#gameCategories li.active a").hasClass("favorites")){
                    listItem.detach();
                }
                addMessage(data.message);
        }).fail(function (jqXHR, textStatus) {
                alert(textStatus);
                console.log(jqXHR.statusText);
                if (textStatus == 'timeout') {
                    alert('The request has timed out, please try again.');
                }
            });
    })

    $('#feeds li').on('mouseenter', function (e) {
        var item = $(this);
        if (!(item.find('.videoMask').length > 0)) {
            $('<div />', {
                "class": "videoMask"
            }).prependTo($(this));
        }
    });

    $('#feeds li').on('mouseleave', function (e) {
        var item = $(this);
        var mask = item.find('.videoMask');
        if (mask.length > 0) mask.detach();
    });

    /**
     * @description Creates the stage and plays a video.
     */
    $(document).on('click', '#feeds li .videoMask', function (e) {
        var playBtn = $(this);
        var listItem = playBtn.closest('li');
        var id = listItem.attr('data-video-id');
        var feedId = listItem.attr('class').substring(5);
        var page = 1;

        var stageWrapper = $('<div/>', {
            'id': "stageWrapper"
        }).prependTo($('body'));

        var stage = $('<div/>', {
            'id': 'stage'
        }).appendTo(stageWrapper);

        var videoWrapper = $('<div/>', {
            'id': 'videoWrapper'
        }).appendTo(stage);

        var videoPlayer = $('<iframe/>', {
            "id": "youtubeVideo",
            "frameborder": 0,
            "src": "http://www.youtube.com/embed/" + id + "?autoplay=1"
        }).appendTo(videoWrapper);

        focusedDiv = stageWrapper;
        stageWrapper.addClass('target-' + feedId).focusLight();

        //if the user watches this in the userPage, don't add it to the history
        console.log(feedId);
        if (!$('body').hasClass('userPage')) {
            $.ajax({
                url: baseUrl + '/feed/add-to-watched',
                type: "POST",
                data: {
                    'feedId': feedId
                }
            }).success(function (data) {
                    console.log(data);
            }).fail(function (jqXHR, textStatus) {
                    console.log(textStatus);
                    if (textStatus == 'timeout') {
                        alert('The request has timed out, please try again.');
                    }
                });
        }
    });


    // =================== Stage related ================= //

    $(document).on('resize', '#stage #comments', function (e) {
        var element = $(this);
        element.perfectScrollbar('update');
    });

    $(document).on('submit', '#stage #commentForm', function (e) {
        e.preventDefault();
        var form = $(this);
        var stage = $('#stageWrapper');
        var feedId = stage.attr('class').split(' ')[0].substring(7);
        $.ajax({
            url: baseUrl + '/feed/comment/add?feedId=' + feedId,
            type: "POST",
            data: form.serialize()
        }).done(function (data) {
                form.find('input').val("");
                if (data) {
                    var comments = stage.find('#comments');
                    var comList = comments.children('ul');
                    console.log(comList.length);
                    if (comList.length <= 0) {
                        comments.children('.notFound').detach();
                        comList = $('<ul />').prependTo(comments);
                    }
                    comList.prepend(data);
                    $('#stage #comments').perfectScrollbar('update');
                }
            }).fail(function (jqXHR, textStatus) {
                if (textStatus == 'timeout') {
                    alert('The request has timed out, please try again.');
                }
                console.log(textStatus);
            });

        return false;
    });

    // ======================================================= //
    // =================== Feed Page related ================= //

    $('input[name="feed[title]"]').on('keyup', function (e) {
        var input = $(this);
        var val = input.val();
        if (val.length > 50) {
            input.val(val.substr(0, 50));
        } else {
            var remainingChars = input.parent().find('.remainingChars');
            remainingChars.html(50 - val.length);

        }
    });

    $('textarea[name="feed[description]"]').on('keyup', function (e) {
        var input = $(this);
        var val = input.val();
        if (val.length > 200) {
            input.val(val.substr(0, 200));
        } else {
            var remainingChars = input.parent().find('.remainingChars');
            remainingChars.html(200 - val.length);

        }
    });

    // ======================================================= //
    // ================= User Page related =================//
    $('#following .gameMeta').on('click', function (event) {
        if (confirm('Do you really want to stop following this game?')) {
            var element = $(this);
            var gameName = element.siblings('.gameInfo').find('.gameName').html();
            $.ajax({
                url: baseUrl + '/game/' + gameName + '/connect/unfollow',
                timeout: 5000
            }).done(function (data) {
                    if (data.success == 1) {
                        element.closest('li').detach();
                    }
                    addMessage(data.message);
                }).fail(function (jqXHR, textStatus) {
                    if (textStatus == 'timeout') {
                        alert('The request has timed out, please try again.');
                    }
                });
        }
        return;
    });
});
