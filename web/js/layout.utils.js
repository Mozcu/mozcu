$(function() {
   
   var urls = new Array();
   
   $('.wrapperPage').on('click', '.header .homeLink', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        changeMainContent(url);
    });
   
   $('.wrapperPage').on('click', '.aboutPage .header a', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        changeMainContent(url);
    });
    
   $('#liveSearchInput').autocomplete({
        source: $('#liveSearchUrl').val(),
        minLength: 2,
        select: function( event, ui ) {
            changeMainContent(ui.item.url);
            
          //event.preventDefault();
        }
    }).data("ui-autocomplete")._renderItem = (function (ul, item) {
        
        var divContainer = $('<div>');
        var divType = $('<div>', {class: 'type'});
        var divLabel = $('<div>', {class: 'result'});
        var divClear = $('<div>', {class: 'clearBoth'});
        
        divType.html(item.type);
        
        if ('image' in item) {
            divLabel.append($('<img>', {src: item.image}));
        }
        divData = $('<div>', {class: 'data'});
        divData.append(item.label);
        if ('extra' in item) {
            divData.append('<br />');
            divData.append($('<span>').html(item.extra));    
        }
        
        divLabel.append(divData)
        divLabel.append(divClear);
        
        divContainer.append(divType);
        divContainer.append(divLabel);
        divContainer.append(divClear);
        
        return $("<li>", {class: 'liveSearchItem'})
            .data( "item.autocomplete", item )
            .append($("<a>").append(divContainer))
            .appendTo(ul);
    });
    
    $('.userBarWrapper').on('click', '.loginLinkWrapper a', function(e) {
        e.preventDefault();
        
        var me = $(this);
        me.parent().find('.loginFormBubble').toggle('fast');
        
    });
    
    $('.userBarWrapper').on('click', '.wrapperSettings .nameProfile a', function(e){
        e.preventDefault();
        
        var me = $(this);
        me.parents('.wrapperSettings').find('.userBubble').toggle('fast');
    });
    
    $('.userBarWrapper').on('click', '.loginCheckButton', function(e){
        e.preventDefault();

        var me = $(this);
        var username = me.parent().find('#username').val();
        var password = me.parent().find('#password').val();
        var rememberMe = me.parent().find('#remember_me');
        var loader = me.parents('.loginLinkWrapper').find('.ajaxLoader');
        
        loader.show();
        if(rememberMe.is(':checked')) {
            loginCheck(username, password, true);
        } else {
            loginCheck(username, password);
        }
    });
    
    $('.userBarWrapper').on('click', '.userBubble .logoutLink', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        $.getJSON(url, function(data) {
            if(data.success) {
                reloadUserBar();
                reloadLeftBar();
                
                $.getJSON(data.callback_url, function(data) {
                    if(data.success) {
                        $('.mainContent').html(data.html);
                    }
                });
            }
        });
    });
    
    /* Config User and Config Profile links */
    $('.userBarWrapper').on('click', '.userBubble .config', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        $.getJSON(url, function(data) {
            if(data.success) {
                me.parents('.userBubble').toggle('fast');
                $('.mainContent').html(data.html);
            }
        });
    });
    
    $('.userBarWrapper').on('click', '.topCloud a', function(e) {
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        $.getJSON(url, function(data){
            if(data.success) {
                $('.mainContent').html(data.html);
            } 
        });
        
    });
    
    $('.wrapperPage').on('click', '.sidebarLeft .optionsMenu a', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        changeMainContent(url);
    });
    
    var changeMainContent = function(url) {
         $.getJSON(url, function(data) {
            if(data.success) {
                $('.mainContent').html(data.html);
            }
        });
    }
    
    var loginCheck = function(username, password, rememberMe) {
        var url = $('#login_check_url').val();
        var postData = {_username: username, _password: password};
        
        if(rememberMe) {
            postData._remember_me = true;
        }
        
        $.post(url, postData, function(data) {
            if(data.success) {
                reloadUserBar();
                reloadLeftBar();
                $.getJSON(data.callback_url, function(data) {
                    if(data.success) {
                        $('.mainContent').html(data.html);
                    }
                });
            }
            $('.loginLinkWrapper .loginFormBubble').toggle('fast');
        });
    };
    
    var reloadUserBar = function() {
        var url = $('#loadUserBarUrl').val();
        $.getJSON(url, function(data) {
            if(data.success) {
                if(data.success) {
                    $('.header .userBarWrapper').html(data.html);
                }
            }
        });
    };
    
    var reloadLeftBar = function() {
        var url = $('#loadLeftBarUrl').val();
        $.getJSON(url, function(data) {
            if(data.success) {
                if(data.success) {
                    $('.sidebarLeft .optionsMenu').replaceWith(data.html);
                }
            }
        });
    };
    
});

