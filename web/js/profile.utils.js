$(function() {
    
    // Menu del perfil
    $('.mainContent').on('click', '.headerPerfil .profileMenu .btn', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.data('url');
        
        $.getJSON(url, function(data) {
            if(data.success) {
                $('.headerPerfil .navDisco').find('.discoActive').removeClass('discoActive');
                $('.headerPerfil .navDiscoMobile').find('.navDiscoMobileActive').removeClass('navDiscoMobileActive');
                
                var idx = me.parent().prevAll().length;
                
                var option = $('.headerPerfil .navDisco li').get(idx);
                $(option).addClass('discoActive');
                
                var mobileOption = $('.headerPerfil .navDiscoMobile li').get(idx);
                $(mobileOption).addClass('navDiscoMobileActive');
                
                $('.profileContent').replaceWith(data.html);
            }
        });
    });
    
    // Seguir
    $('.mainContent').on('click', '.headerPerfil .btnSeguir', function(e) {
        e.preventDefault();
        var me = $(this);
        var counter = me.parents('.headerPerfil').find('.followCounter');
        
        $.post(me.data('url'), {profileId: me.data('id')}, function(data) {
            if(data.success) {
                if(data.action == 'following') {
                    me.addClass('following');
                    me.find('.text').html(me.data('following'));
                } else {
                    me.removeClass('following');
                    me.find('.text').html(me.data('follow'));
                }
                counter.html(data.followers_count)
            }
        }, 'json');
    });
    
    $('.mainContent').on('mouseover', '.headerPerfil .btnSeguir', function(e) {
        var me = $(this);
        if(me.hasClass('following')) {
            me.find('.text').html(me.data('unfollow'));
        }
    });
    
    $('.mainContent').on('mouseout', '.headerPerfil .btnSeguir', function(e) {
        var me = $(this);
        if(me.hasClass('following')) {
            me.find('.text').html(me.data('following'));
        }
    });
    
    // Pagina de seguidores
    $('.mainContent').on('click', '.headerPerfil .followersPerfil .link', function(e) {
        e.preventDefault()
        var me = $(this);
        
        $.getJSON(me.data('url'), function(data) {
            $('.profileContent').replaceWith(data.html);
            $('.headerPerfil .navDisco').find('.discoActive').removeClass('discoActive');
            $('.headerPerfil .navPerfilMobile').find('.navDiscoMobileActive').removeClass('navDiscoMobileActive');
        });
    });
    
    $('.mainContent').on('click', '.paginaSeguidores .btn-seguidores button', function(e) {
        var me = $(this);
        if (me.hasClass('btnPerfilActive')) {
            return;
        }
        
        var other = me.parent().find('.btnPerfilActive');
        other.removeClass('btnPerfilActive');
        me.addClass('btnPerfilActive');
        $('.paginaSeguidores .' + other.data('type')).addClass('hidden');
        $('.paginaSeguidores .' + me.data('type')).removeClass('hidden');
    });
    
    $('.mainContent').on('click', '.paginaSeguidores a', function(e) {
        e.preventDefault();
        changeMainContent($(this).attr('href'));
    });
    
    $('.mainContent').on('click', '.paginaSeguidores .btnSeguirFollow', function(e) {
        var me = $(this);
        $.post(me.data('url'), {profileId: me.data('id')}, function(data) {
            if(data.success) {
                if(data.action == 'following') {
                    me.addClass('disabled');
                    me.find('.text').html(me.data('following'));
                }
            }
        }, 'json');
    });
    
    // Toggle mis albumes y favoritos
    $('.mainContent').on('click', '.profileContent .headerAlbumes .btn-default', function(e) {
        e.preventDefault();
        
        var me = $(this);
        if(me.hasClass('btnPerfilActive')) {
            return;
        }
        
        var parent = me.parents('.profileContent');
        var option = me.data('option');
        var currentOption = parent.find('.btnPerfilActive').data('option');
        
        parent.find('.btnPerfilActive').removeClass('btnPerfilActive');
        me.addClass('btnPerfilActive');
        parent.find('.'+option).removeClass('hidden');
        parent.find('.'+currentOption).addClass('hidden');
        
    });
    
    // Ir al album
    $('.mainContent').on('click', '.profileContent .album a', function(e) {
        e.preventDefault();
        changeMainContent($(this).attr('href'));
    });
    
    // Eliminar album
    $('.mainContent').on('click', '.profileContent .album .delete', function(e) {
        if(!confirm('Esta seguro que desea eliminar el album?')) {
            return;
        }
        
        var me = $(this);
        var url = me.data('url');
        var album = me.parents('.album');
        
        album.find('.btnAlbumManager').hide();
        album.find('.loader').show();
        $.post(url, {id: album.data('id')}, function(data) {
            if(data.success) {
                album.hide('slow', function(){ album.remove(); });
            }
        }, 'json');
    });
    
    // Editar album
    $('.mainContent').on('click', '.profileContent .album .edit', function(e) {
        var me = $(this);
        var url = me.data('url');
        var album = me.parents('.album');
        
        album.find('.btnAlbumManager').hide();
        album.find('.loader').show();
        changeMainContent(url)
    });
    
    /***** Account Configuration *****/
    $('.mainContent').on('click', '.botonesPerfil .btn-success', function(e){
        var me = $(this);
        var url = me.data('url');
        
        var account = prepareAccountData();
        
        var errorMsg = $('.alert-danger');
        errorMsg.hide();
        errorMsg.find('.error').remove();
        
        var validation = validateAccountData(account);
        if (!validation.success) {
            $.each(validation.errors, function(key, value) {
                errorMsg.append('<p class="error"> - ' + value.message + '</p>');
            });
            errorMsg.show();
            $('html,body').animate({scrollTop: 0}, 800);
            return;
        }
        
        me.prop('disabled', true);
        $.post(url, {account: account}, function(data) {
            if(data.success) {
                var callbackUrl = data.callback_url;
                $.getJSON(callbackUrl, {}, function(data) {
                    if(data.success) {
                        $('.mainContent').html(data.html);
                        reloadUserBar();
                        reloadLeftBar();
                        $('html,body').animate({scrollTop: 0}, 800);
                        historyPushState(callbackUrl, 'mainContent', 'inner');
                    }
                });
            } else {
                errorMsg.append('<p class="error"> - ' + data.message + '</p>');
                errorMsg.show();
                $('html,body').animate({scrollTop: 0}, 800);
                me.prop('disabled', false);
            }
        }, 'json');
    });
    
    var prepareAccountData = function() {
        var account = {};
        account.name = $.trim($('#name').val());
        account.slogan = $.trim($('#slogan').val());
        account.slug = $.trim($('#slug input').val());
        account.city = $.trim($('#city').val());
        account.country = $('#country').data('id');
        account.description = $.trim($('#description').val());
        account.email = $.trim($('#email').val());
        account.paypalEmail = $.trim($('#paypalEmail').val());
        account.password = $('#password').val();
        account.passwordRepeat = $('#passwordRepeat').val();
        account.image = $('#imageFileName').val();
        
        account.links = [];
        $('.vinculos .form-group').each(function(key, value) {
            var me = $(value);
            
            var link = {};
            var url = $.trim(me.find('.link-url').val());
            var name = $.trim(me.find('.link-name').val());
            if(url.length === 0 && name.length === 0) {
                return true;
            }
            link.name = name;
            link.url = url;
            account.links[key] = link;
        });
        
        return account;
        
    };
    
    var validateAccountData = function(account) {
        var response = {};
        response.success = true;
        response.errors = new Array();
        
        if (account.name.length === 0) {
            response.success = false;
            response.errors.push({message: "El campo nombre de usuario esta vacio"});
        }
        
        if (account.slug.length === 0) {
            response.success = false;
            response.errors.push({message: "El campo permalink esta vacio"});
        }
        
        if (account.city.length === 0) {
            response.success = false;
            response.errors.push({message: "El campo ciudad esta vacio"});
        }
        
        if (account.email.length === 0) {
            response.success = false;
            response.errors.push({message: "El campo email esta vacio"});
        } else if (!validateEmail(account.email)) {
            response.success = false;
            response.errors.push({message: "El formato del email es invalido: " + account.email});
        }
        
        if (account.paypalEmail.length > 0 && !validateEmail(account.paypalEmail)) {
            response.success = false;
            response.errors.push({message: "El formato del email es invalido: " + account.paypalEmail});
        }
        
        if((account.password.length > 0 || account.passwordRepeat.length > 0)
                && (account.password !== account.passwordRepeat)) {
            response.success = false;
            response.errors.push({message: "Los passwords no coinciden"});
        }
        
        for(var key in account.links) {
            var link = account.links[key];
            
            if(link.url.length > 0 && !validateUrl(link.url)) {
                response.success = false;
                response.errors.push({message: "Formato de link invalido: " + link.url});
                continue;
            }
            
            if(link.url.length > 0 && link.name.length === 0 ) {
                response.success = false;
                response.errors.push({message: "Falta un nombre para el link " + link.url});
                continue;
            }
            
            if(link.url.length === 0 && link.name.length > 0 ) {
                response.success = false;
                response.errors.push({message: "Falta un link para " + link.name});
                continue;
            }
        }
        
        return response;
    };
    
    var validateEmail = function(email) { 
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };
    
    var validateUrl = function(url) {
        var re = /^(ht|f)tps?:\/\/[a-z0-9-\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?$/;
        return re.test(url);
    }
    
    // Recarga la barra superior derecha
    var reloadUserBar = function() {
        var url = $('#loadUserBarUrl').val();
        $.getJSON(url, function(data) {
            if(data.success) {
                if(data.success) {
                    $('.navbar.navbar-fixed-top .userBar').replaceWith(data.html);
                }
            }
        });
    };
    
    // Recarga el menu izquierdo
    var reloadLeftBar = function() {
        var url = $('#loadLeftBarUrl').val();
        $.getJSON(url, function(data) {
            if(data.success) {
                if(data.success) {
                    $('.sidebar .nav-sidebar').replaceWith(data.html);
                }
            }
        });
    };
    
});

