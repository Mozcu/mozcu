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
    
    // Pestañas del album (lista de temas, informacion, comentarios, similares)
    $('.mainContent').on('click', '.headerDisco .navDisco a', function(e) {
      e.preventDefault();
      var me = $(this);
      
      var url = me.attr('href');
      if(url == '#') {
          return;
      }
      $.getJSON(url, {}, function(data){
        if (data.success) {
          me.parents('.navDisco').find('.discoActive').removeClass('discoActive');
          me.parent().addClass('discoActive');
          $('.mainContent .albumContent').replaceWith(data.html);
        }
      });
    });
    
    // Download album modal
    $('.mainContent').on('click', '.headerDisco .btnDescarga', function(e) {
        e.preventDefault();
        var me = $(this);

        var url = me.data('url');
        $.getJSON(url, {}, function(data){
            if (data.success) {
                var content = $(data.html);
                content.modal();
            }
        });
    });
    
    // On modal close
    $('body').on('hidden.bs.modal', '.modal.fade', function (e) {
        $(this).remove();
    });
    
    // Checkout album
    $('body').on('click', '.mozcu-descarga-modal .btn-success', function(e) {
        e.preventDefault();
        var me = $(this);
        var loader = me.next('.ajaxLoader');
        
        me.addClass('hidden');
        loader.removeClass('hidden');
        
        var url = me.data('url');
        $.getJSON(url, {}, function(data){
            if(data.success) {
                var zipUrl = data.zipUrl;
                var amount = parseFloat($.trim($('#valor').val()));
                
                if(isNaN(amount)) {
                    amount = 0;
                }
                
                if (amount == 0) {
                    location.href = zipUrl;
                    $('.mozcu-descarga-modal').modal('hide');
                } else {
                    $('#amount').val(amount);
                    $('#checkoutUrl').val(zipUrl);
                    $('#paypalForm').submit();
                }
            }
        });
    });
    
    // Modal denunciar obra
    $('.mainContent').on('click', '#reportAlbum', function(e) {
        e.preventDefault();
        var me = $(this);

        var url = me.attr('href');
        $.getJSON(url, {}, function(data){
            $(data.html).modal();
        });
    });
    
    // Enviar formulario de denuncia
    $('body').on('click', '.modal-report-album .btn-success', function(e) {
        e.preventDefault();
        var me = $(this);

        var url = me.data('url');
        $.post(url, {}, function(data) {
            // TODO
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
    
    // Ir al disco
    $('.mainContent').on('click', '.album .albumLink', function(e) {
      e.preventDefault();
      var me = $(this);
      
      $.getJSON(me.attr('href'), {}, function(data) {
        if(data.success) {
          $('.mainContent').html(data.html);
        }
      });
    });
    
    // Ir al perfil del usuario
    $('.mainContent').on('click', '.album .profileLink', function(e) {
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