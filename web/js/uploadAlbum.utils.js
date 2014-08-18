$(function() {
    
    $('.pageUpload').on('click', '.uploadedSong .editSongName', function(e){
        e.preventDefault();
        
        var me = $(this);
        me.hide();
        me.next('.editingSongName').val(me.html()).show();
    });
    
    $('.pageUpload').on('keypress', '.uploadedSong .editingSongName', function(e){
        var me = $(this);
        var link = me.prev('.editSongName');
        
        if(e.which == 13) {
            e.preventDefault();
            
            me.hide();
            link.html(me.val()).show();
        }
        
        if(e.which == 27) { // esc
            e.preventDefault();
            
            me.val(link.html()).hide();
            link.show();
        }
    });
    
    $('.pageUpload').on('focusout', '.uploadedSong .editingSongName', function(e){
        var me = $(this);
        var link = me.prev('.editSongName');
        
        me.val(link.html()).hide();
        link.show();
    });
    
    /*$( ".pageUpload .content .tags .tagInput" ).autocomplete({
      source: $('#getTagListUrl').val(),
      minLength: 2,
      select: function( event, ui ) {
        var tag = createTag(ui.item.value, ui.item.id);
        $( ".pageUpload .content .tags").prepend(tag);
        $( ".pageUpload .content .tags .tagInput" ).val('');
        event.preventDefault();
      }
    });*/
    
    $( ".pageUpload .content .tags .tagInput" ).on('keypress', function(e){
        var me = $(this);
        
        if(e.keyCode == 44 || e.keyCode == 13) {
            e.preventDefault();
            var tag = createTag(me.val());
            $( ".pageUpload .content .tags").prepend(tag);
            $( ".pageUpload .content .tags .tagInput" ).val('');
        }
    });
    
    
    $( ".pageUpload .content .tags").on('click', '.removeTag', function(e){
        e.preventDefault();
        $(this).parent().remove();
    });
    
    $('.pageUpload .uploadButton').on('click', function(e) {
        e.preventDefault()
        var url = $(this).attr('href');
        
        var errorMsg = $('.uploadErrorMessage');
        errorMsg.hide();
        errorMsg.find('.messageInner .error').remove();
        
        var album = prepareAlbumData();
        
        var validation = validateAlbumData(album);
        if (!validation.success) {
            $.each(validation.errors, function(key, value) {
                errorMsg.find('.messageInner').append('<p class="error"> - ' + value.message + '</p>');
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
    
    var createTag = function(tagValue, tagId) {
        var tag = $("<div>", {class: 'tag'});
        var remove = $("<a>", {class: 'removeTag', href: '#'});
        var hidden = $('<input>', {type: 'hidden', class: 'tagValue'});
        
        if (tagId) {
            var hiddenId = $('<input>', {type: 'hidden', class: 'tagId'});
            hiddenId.val(tagId);
            tag.append(hiddenId);
        }
        
        hidden.val(tagValue);        
        tag.append(tagValue);
        tag.append(remove);
        tag.append(hidden);
        
        return tag;
    };
    
    var prepareAlbumData = function() {
        var album = {};
        
        album.name = $.trim($('#albumName').val());
        album.release_date = $.trim($('#albumRecordingDate').val());
        album.description = $.trim($('#albumDescription').val());
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
            song.name = $.trim(me.find('.editingSongName').val());
            song.file_name = me.find('.realFileName').val();
            song.track_number = key + 1;
            
            if(me.find('.id').length > 0) {
                song.id = me.find('.id').val();
            }
            
            album.songs.push(song);
        });
        
        album.tags = new Array;
        $('.tag').each(function(key, value) {
            var tag = {name: $.trim($(value).find('.tagValue').val())};
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
            response.errors.push({message: "El disco no posee nombre"});
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
            
            if(me.find('.url').length === 0 && me.find('.realFileName').val().length === 0) {
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