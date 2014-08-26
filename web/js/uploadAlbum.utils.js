$(function() {
    
    /** Edicion del nombre del track **/
    
    $('.mainContent').on('click', '.uploadedSong .editSongName', function(e){
        e.preventDefault();
        
        var me = $(this);
        var parent = me.parent();
        
        parent.find('.editSongName').hide();
        parent.find('.editingSongName').val(parent.find('.filename').html()).show().focus();
        parent.find('.confirm').show();
    });
    
    $('.mainContent').on('keypress', '.uploadedSong .editingSongName', function(e){
        var me = $(this);
        var parent = me.parent();
        
        if(e.which == 13) {
            e.preventDefault();
            
            me.hide();
            parent.find('.confirm').hide();
            parent.find('.filename').html(me.val()).show();
            parent.find('.editSongName').show();
        }
        
        if(e.which == 27) { // esc
            e.preventDefault();
            
            me.val(parent.find('.filename').html()).hide();
            parent.find('.confirm').hide();
            parent.find('.editSongName').show();
        }
    });
    
    $('.mainContent').on('click', '.uploadedSong .confirm', function(e){
        e.preventDefault();
        
        var me = $(this);
        var parent = me.parent();
        
        
        me.hide();
        parent.find('.editingSongName').hide();
        parent.find('.filename').html(parent.find('.editingSongName').val()).show();
        parent.find('.editSongName').show();
        
    });
    
    $('.mainContent').on('focusout', '.uploadedSong .editingSongName', function(e){
        var me = $(this);
        var parent = me.parent();
        
        me.val(parent.find('.filename').html()).hide();
        parent.find('.confirm').hide();
        parent.find('.editSongName').show();
    });
    
    /** Licencias **/
    $('.mainContent').on('click', '.licencia .select', function(e) {
       e.preventDefault();
       
       var me = $(this);
       $('.pageUpload').find('.licenciaSelect').removeClass('licenciaSelect');
       me.parents('.licencia').addClass('licenciaSelect');
       $('#license').val(me.parents('.licencia').data('id'));
    });
    
    
    /** Click en el boton de subir **/
    $('.mainContent').on('click', '.publicarFooter .btn-success', function(e) {
        e.preventDefault()
        var url = $(this).data('url');
        
        var errorMsg = $('.alert-danger');
        errorMsg.hide();
        errorMsg.find('.error').remove();
        
        var album = prepareAlbumData();
        console.log(album);
        
        var validation = validateAlbumData(album);
        if (!validation.success) {
            $.each(validation.errors, function(key, value) {
                errorMsg.append('<p class="error"> - ' + value.message + '</p>');
            });
            errorMsg.show();
            $('html,body').animate({scrollTop: 0}, 800);
            return;
        }
        
        $(this).replaceWith('Subiendo...');
        $.post(url, {album: album}, function(data) {
            if (data.success) {
                var url = $('#albumsForProfileLink').val();
                $.getJSON(url, {}, function(data) {
                    if (data.success) {
                        $('.mainContent').html(data.html);
                    }
                })
            }
        }, 'json');
    });
    
    var prepareAlbumData = function() {
        var album = {};
        
        album.name = $.trim($('#title').val());
        album.artist = $.trim($('#artist').val());
        album.release_date = $.trim($('#date').val());
        album.description = $.trim($('#description').val());
        album.image_file_name = $.trim($('#imageFileName').val());
        album.validate_image = true;
        if($('#imageUrl').length > 0) {
            album.validate_image = false;
        }
        album.license = $("#license").val();
        
        album.songs = new Array;
        $('.uploadedSong').each(function(key, value){
            var me = $(value);
            var song = {};
            song.name = $.trim(me.find('.filename').html());
            song.file_name = me.find('.realFileName').val();
            song.track_number = key + 1;
            
            if(me.find('.id').length > 0) {
                song.id = me.find('.id').val();
            }
            
            album.songs.push(song);
        });
        
        album.tags = new Array;
        $('.tagit-choice').each(function(key, value) {
            var tag = {name: $.trim($(value).find('.tagit-label').html())};
            album.tags.push(tag);
        });
        
        return album;
    };
    
    var validateAlbumData = function(album) {
        var response = {};
        var yre = new RegExp('^[0-9]{4}$');
        response.success = true;
        response.errors = new Array();
        
        if (album.name.length === 0) {
            response.success = false;
            response.errors.push({message: "El disco no posee titulo"});
        }
        
        if (album.artist.length === 0) {
            response.success = false;
            response.errors.push({message: "El disco no posee nombre de artista"});
        }
        
        if (album.validate_image && album.image_file_name.length === 0) {
            response.success = false;
            response.errors.push({message: "El disco no posee una imagen"});
        }
        
        if (album.songs.length === 0) {
            response.success = false;
            response.errors.push({message: "El disco no posee canciones"});
        }
        
        if (album.release_date.length === 0) {
            response.success = false;
            response.errors.push({message: "El disco no posee a&ntilde;o"});
        } else {
            if (!yre.test(album.release_date)) {
                response.success = false;
                response.errors.push({message: "El A&ntilde;o es invalido"});
            }
        }
        
        if (album.tags.length === 0) {
            response.success = false;
            response.errors.push({message: "El disco no posee etiquetas"});
        }
        
        if (album.license.length === 0) {
            response.success = false;
            response.errors.push({message: "Debe seleccionar una licencia"});
        }
        
         $('.uploadedSong').each(function(key, value){
            var me = $(value);
            
            if(me.find('.id').length === 0 && me.find('.realFileName').val().length === 0) {
                response.success = false;
                response.errors.push({message: "Uno o mas temas aun estan subiendo"});
                return false;
            }
            
            if(me.hasClass('uploadify-error')) {
                response.success = false;
                response.errors.push({message: "Uno o mas temas no fueron subidos correctamente"});
                return false;
            }
        });
        
        return response;
    };
    
});