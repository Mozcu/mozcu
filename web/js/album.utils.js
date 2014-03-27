$(function() {


    $( ".pageDiscListen #searchTags" ).autocomplete({
      source: $('#getTagListUrl').val(),
      minLength: 2,
      select: function( event, ui ) {
        var span = $('<span>', {id: ui.item.id, class: 'category'}).html(ui.item.value);
        var plus = $('<span>',{class: 'plus'}).html(' + ');
        if ($( ".pageDiscListen .wrapperCategories .plus").length > 0) {
            $( ".pageDiscListen .wrapperCategories .plus").last().after(span);
            $( ".pageDiscListen .wrapperCategories .category").last().after(plus);
        } else {
            $( ".pageDiscListen .wrapperCategories").prepend(plus);
            $( ".pageDiscListen .wrapperCategories").prepend(span);
        }
        
        $( ".pageDiscListen #searchTags" ).val('');
        
        $( document ).ajaxStop();
        searchAlbums();
        
        event.preventDefault();
      }
    });
    

    $( ".pageDiscListen .wrapperTags" ).on('click', '.category', function(e) {
        e.preventDefault();
        var me = $(this);
        var aux = me.attr('id').split('tag-');
        var id = aux[1];
        
        if ($(".pageDiscListen .wrapperCategories #" + parseInt(id)).length > 0) {
            return;
        }
        
        var span = $('<span>', {id: id, class: 'category'}).html(me.html());
        var plus = $('<span>',{class: 'plus'}).html(' + ');
        
        if ($( ".pageDiscListen .wrapperCategories .plus").length > 0) {
            $( ".pageDiscListen .wrapperCategories .plus").last().after(span);
            $( ".pageDiscListen .wrapperCategories .category").last().after(plus);
        } else {
            $( ".pageDiscListen .wrapperCategories").prepend(plus);
            $( ".pageDiscListen .wrapperCategories").prepend(span);
        }
        
        $( document ).ajaxStop();
        searchAlbums();
    });
    
    $('.mainContent').on('click', '.wrapperProfile.breadC .profileOptions a', function(e) {
      e.preventDefault();
      var me = $(this);
      
      var url = me.attr('href');
      $.getJSON(url, {}, function(data){
        if (data.success) {
          me.parents('.profileOptions').find('.selected').removeClass('selected');
          me.addClass('selected');
          $('.mainContent .content').replaceWith(data.html);
        }
      });
    });
    
    $('.mainContent').on('click', '.breadCrumb .allAlbums', function(e) {
      e.preventDefault();
      var me = $(this);
      
      var url = me.attr('href');
      $.getJSON(url, {}, function(data){
        if (data.success) {
          $('.mainContent').html(data.html);
        }
      });
    });
    
    /* Download option */
    $('.mainContent').on('click', '.wrapperProfile.breadC .download a', function(e) {
      e.preventDefault();
      var me = $(this);
      
      var url = me.attr('href');
      $.getJSON(url, {}, function(data){
        if (data.success) {
          me.parents('.profileLeft').find('.profileOptions .selected').removeClass('selected');
          $('.mainContent .content').replaceWith(data.html);
        }
      });
    });
    
    /* Go to Profile option */
    $('.mainContent').on('click', '.wrapperProfile.breadC .albumProfile', function(e) {
      e.preventDefault();
      var me = $(this);
      
      var url = me.attr('href');
      $.getJSON(url, {}, function(data){
        if (data.success) {
          $('.mainContent').html(data.html);
        }
      });
    });
    
    $('.mainContent').on('click', '.pageDiscListen .discListen a', function(e) {
      e.preventDefault();
      var me = $(this);
      
      $.getJSON(me.attr('href'), {}, function(data) {
        if(data.success) {
          $('.mainContent').html(data.html);
        }
      });
    });
    
    /* To Disc Page from Related Page */
    $('.mainContent').on('click', '.related .discListen a', function(e) {
      e.preventDefault();
      var me = $(this);
      
      $.getJSON(me.attr('href'), {}, function(data) {
        if(data.success) {
          $('.mainContent').html(data.html);
        }
      });
    });
    
    var searchAlbums = function() {
        var container = $('.pageDiscListen .wrapperDiscListen');
        var tags = new Array();
        $( ".pageDiscListen .wrapperCategories .category").each(function(key, value) {
            tags.push($(value).attr('id'));
        });
        
        var url = $('#findAlbumsByTagUrl').val();
        $.post(url, {tags: tags}, function(data){
            if (data.success) {
                container.html('');
                for(key in data.html) {
                    container.append($(data.html[key]));
                }
            }
        }, 'json');
    }
    
});