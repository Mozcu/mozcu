$(function() {

    // Pestañas del album (lista de temas, informacion, comentarios, similares)
    $('.mainContent').on('click', '.headerDisco .navDisco button', function(e) {
      e.preventDefault();
      var me = $(this);
      
      var url = me.data('url');
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
        var div = me.parent();
        var loader = div.find('.ajaxLoader');
        var cancel = div.find('.btn-default');
        var inProcess = div.find('.in-process');
        var ready = div.find('.ready');
        
        var amount = parseFloat($.trim($('#valor').val()));
        if (isNaN(amount)) {
            amount = 0;
        }
        
        me.addClass('hidden');
        cancel.addClass('hidden');
        inProcess.removeClass('hidden');
        loader.removeClass('hidden');
        
        if (amount === 0) {
            var url = me.data('url');
            $.getJSON(url, {}, function(data){
                if(data.success) {
                    var zipUrl = data.zipUrl;

                    inProcess.addClass('hidden');
                    ready.removeClass('hidden');
                    initDownlaod(zipUrl);

                    setTimeout(function() {
                        $('.mozcu-descarga-modal').modal('hide');
                    }, 3000);
                }
            });
        } else {
            initPaymentCheckout(amount);
        }            
    });
    
    var initPaymentCheckout = function(amount) {
        window.onbeforeunload = null;
        
        if($('#mercadopago').length > 0) {
            var mp = $('#mercadopago');
            $.post(mp.val(), {albumId: mp.data('album-id'), checkoutId: mp.data('checkout-id'), price: amount}, function(data) {
                if(data.success) {
                    window.location.href = data.checkout_url;
                }
            });
        } else if($('#paypalForm').length > 0) {
            $('#amount').val(amount);
            $('#paypalForm').submit();
        }
    };
    
    
    // Submit de paypal form
    $('body').on('submit', '#paypalForm', function(e) {
        var url = $(this).data('price-url');
        $.post(url, {'price': $('#amount').val()}, function(data) {
            return data.success;
        }, 'json');
    });
    
    var initDownlaod = function(url) {
        var iframe = $('<iframe>', {width:'1', height:'1', frameborder:'0', src:url, id:'downloadAlbumIframe'});
        $('#downloadAlbumIframe').remove();
        $('.row.headerDisco').append(iframe);
    };
    
    // Modal compartir
    $('.mainContent').on('click', '#shareAlbum', function(e) {
        e.preventDefault();
        var me = $(this);

        var url = me.attr('href');
        $.getJSON(url, {}, function(data){
            $(data.html).modal();
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
    
    // Ir al disco
    $('.mainContent').on('click', '.album .albumLink', function(e) {
      e.preventDefault();
      changeMainContent($(this).attr('href'));
    });
    
    // Ir al perfil del usuario
    $('.mainContent').on('click', '.album .profileLink', function(e) {
      e.preventDefault();
      changeMainContent($(this).attr('href'));
    });
    
    // Subido por
    $('.mainContent').on('click', '.headerDisco .nombreAlbumUser a', function(e) {
      e.preventDefault();
      changeMainContent($(this).attr('href'));
    });
    
    // click en tags de informacion
    $('.mainContent').on('click', '.infoDisco .tag', function(e) {
      e.preventDefault();
      changeMainContent($(this).attr('href'));
    });
    
    // Album favorito
    $('.mainContent').on('click', '#likeAlbum', function(e) {
        e.preventDefault();
        var me = $(this);

        $.post(me.attr('href'), function(data) {
            if(data.success) {
                if(data.action == 'unlike') {
                    me.addClass('unlike');
                } else {
                    me.removeClass('unlike');
                }
            }
        }, 'json');
    });
    
});