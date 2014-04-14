$(function() {
    
    $('.mainContent').on('click', '.profileMenu a', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        $.getJSON(url, function(data) {
            if(data.success) {
                $('.profileMenu').find('.selected').removeClass('selected');
                me.addClass('selected');
                $('.profileContent').replaceWith(data.html);
            }
        });
    });
    
    /* Open Album */
    $('.mainContent').on('click', '.contentInner.discos .disco a', function(e) {
        e.preventDefault();
        var me = $(this);
        var url = me.attr('href');
        
        $.getJSON(url, function(data) {
            if(data.success) {
                $('.mainContent').html(data.html);
            }
        });
    });
    
    /* Delete album */
    $('.mainContent').on('click', '.contentInner.discos .disco .action.delete', function(e) {
        if(!confirm('Esta seguro que desea eliminar el album?')) {
            return;
        }
        
        var me = $(this);
        var url = $('#deleteAlbumUrl').val();
        var album = me.parents('.disco');
        
        album.find('.actions').hide();
        album.find('.actionsLoader').show();
        $.post(url, {id: album.find('.albumId').val()}, function(data) {
            if(data.success) {
                album.hide('slow', function(){ album.remove(); });
            }
        }, 'json');
    });
    
    /***** Account Configuration *****/
    $('.mainContent').on('click', '#saveUserSettings', function(e){
        var url = $('#saveUserSettingsUrl').val();
        var wrapper = $('.profileContent.config');
        
        $.post(url, {userData: prepareUserData()}, function(data) {
            var message = wrapper.find('.message');
            if (data.success) {
                message.removeClass('error');
                message.find('.messageInner p').html(data.message);
                message.show();
            } else {
                message.addClass('error');
                message.find('.messageInner p').html(data.message);
                message.show();
            }
        }, 'json');
    });
    
    $('.mainContent').on('click', '#saveProfileSettings', function(e){
        var url = $('#saveProfileSettingsUrl').val();
        var wrapper = $('.profileContent.config');
        
        $.post(url, {profileData: prepareProfileData()}, function(data) {
            var message = wrapper.find('.message');
            if (data.success) {
                message.removeClass('error');
                message.find('.messageInner p').html(data.message);
                message.show();
                if(data.image) {
                    $('.wrapperProfile.account .imgProfile img').attr('src', 'http://' + data.image);    
                }
            } else {
                message.addClass('error');
                message.find('.messageInner p').html(data.message);
                message.show();
            }
        }, 'json');
    });
    
    var prepareUserData = function() {
        var user = {};
        user.email = $('#email').val();
        user.oldPassword = $('#oldPassword').val();
        user.newPassword = $('#newPassword').val();
        user.newPasswordConfirm = $('#newPasswordConfirm').val();
        
        return user;
    }
    
    var prepareProfileData = function() {
        var profile = {};
        profile.imageFileName = $('#imageFileName').val();
        profile.name = $('#name').val();
        profile.slogan = $('#slogan').val();
        profile.country = $('#country').val();
        profile.city = $('#city').val();
        profile.webSiteUrl = $('#webSiteUrl').val();
        profile.paypalEmail = $('#paypalEmail').val();
        profile.description = $('#description').val();
        
        return profile;
    }
    
});

