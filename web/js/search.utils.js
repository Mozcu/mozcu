$(function() {
    
    // Modifica el contenido principal
    var changeMainContent = function(url) {
         $.getJSON(url, function(data) {
            if(data.success) {
                $('.mainContent').html(data.html);
            }
        });
    };
    
    // Search
    $('.mainContent').on('click', '.searchResults .result a', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        changeMainContent(url);
    });
    
    // Livesearch
    var lastSearchAjax = null;
    $('#liveSearchInput').on('keyup', function(e){
        var me = $(this);
        if(e.keyCode == 40) {
            e.preventDefault();
            crawlDown($('.navbarBuscador').find('.autoResult.selected'));
            return;
        }
        
        if(e.keyCode == 38) {
            e.preventDefault();
            crawlUp($('.navbarBuscador').find('.autoResult.selected'));
            return;
        }
        
        if(e.keyCode == 13) {
            e.preventDefault();
            e.stopPropagation();
            var result = $('.navbarBuscador').find('.autoResult.selected');
            if(result.length > 0) {
                var url = result.data('url');
                removeLiveSearchResult();
                changeMainContent(url);
                me.blur();
            } else {
                var url = $('.navbarBuscador').find('.autoVerResult a').attr('href');
                removeLiveSearchResult();
                changeMainContent(url);
                me.blur();
            }
            return;
        }
        
        if(e.keyCode == 27) {
            e.preventDefault();
            removeLiveSearchResult();
            me.blur();
            return;
        }
        
        executeSearch(me);
    });
    
    $('#liveSearchInput').on('click', function(e){
        var me = $(this);
        if(me.val().length >= 3) {
            me.select();
            executeSearch(me);
        }
    });
    
    var resultMouseDown = false;
    $('#liveSearchInput').on('focusout', function(e){
        if(!resultMouseDown) {
            removeLiveSearchResult();
        }
        resultMouseDown = false;
    });
    
     $('.navbarBuscador').on('mousedown', '.autocompleteResult', function(e) {
        resultMouseDown = true;
    });
    
    $('.navbarBuscador').on('click', '.autoResult', function(e) {
        var me = $(this);
        var url = me.data('url');
        removeLiveSearchResult();
        changeMainContent(url);
    });
    
    $('.navbarBuscador').on('mouseover', '.autoResult', function(e) {
        addSelected($(this));
    });
    
    $('.navbarBuscador').on('click', '.autoVerResult a', function(e) {
        e.preventDefault();
        var me = $(this);
        var url = me.attr('href');
        removeLiveSearchResult();
        changeMainContent(url);
    });
    
    var addSelected = function(result) {
        $('.navbarBuscador').find('.autoResult.selected').removeClass('selected');
        result.addClass('selected');
    };
    
    var removeLiveSearchResult = function() {
        $('.autocompleteResult').remove();
    };
    
    var executeSearch = function(input) {
        if(lastSearchAjax !== null) {
            lastSearchAjax.abort();
        }
        
        if(input.val().length < 3) {
            removeLiveSearchResult();
            return false;
        }
        lastSearchAjax = $.getJSON(input.data('url'), { query: input.val() }, function (data) {
            if(data.success) {
                removeLiveSearchResult();
                input.parent().after(data.html);
            }
            lastSearchAjax = null;
        });
    };
   
    var crawlDown = function(current) {
        var next = [];
        if(current.length != 0) {
            next = current.nextAll('.autoResult').first();
        }
        
        if(next.length == 0) {
            next = $('.navbarBuscador').find('.autoResult').first();
        }
        
        var container = $('.autocompleteResult');
        container.animate({
            scrollTop: next.offset().top - container.offset().top + container.scrollTop()
        }, 200);
        addSelected(next);
    };
    
    var crawlUp = function(current) {
        var prev = [];
        if(current.length != 0) {
            prev = current.prevAll('.autoResult').first();
        }
        
        if(prev.length == 0) {
            prev = $('.navbarBuscador').find('.autoResult').last();
        }
        
        var container = $('.autocompleteResult');
        container.animate({
            scrollTop: prev.offset().top - container.offset().top + container.scrollTop()
        }, 200);
        addSelected(prev);
    };
});


