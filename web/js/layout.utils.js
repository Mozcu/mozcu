$(function() {
   
    // Abandonar pagina
    function closePageWarning(){
        return 'Si te vas no vas a poder escuchar mas musica. Estas seguro?';
    }
    window.onbeforeunload = closePageWarning;
   
   // Click en el logo superior izquirdo
   $('.navbar.navbar-fixed-top').on('click', '.navbar-header a', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        changeMainContent(url);
    });
    
    // Link login
    $('.navbar.navbar-fixed-top').on('click', '.userBar .loginLink', function(e) {
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        changeMainContent(url);
    });
    
    // Link login home
    $('.mainContent').on('click', '.btnIngresar', function(e) {
        e.preventDefault();
        
        var me = $(this);
        var url = me.data('url');
        
        changeMainContent(url);
    });
    
    // Check login
    $('.mainContent').on('click', '.loginCheckButton', function(e){
        e.preventDefault();

        var me = $(this);
        var username = me.parent().find('#username').val();
        var password = me.parent().find('#password').val();
        var rememberMe = me.parent().find('#remember_me');
        //var loader = me.parents('.loginLinkWrapper').find('.ajaxLoader');
        
        //loader.show();
        if(rememberMe.is(':checked')) {
            loginCheck(username, password, true);
        } else {
            loginCheck(username, password);
        }
    });
    
    // Link Registrarse
    $('.navbar.navbar-fixed-top').on('click', '.userBar .registration', function(e) {
        e.preventDefault();
        
        var me = $(this);
        var url = me.data('url');
        
        changeMainContent(url);
    });
   
    // Links del menu de usuario
    $('.navbar.navbar-fixed-top').on('click', '.userBar .dropdown-menu a', function(e) {
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        $.getJSON(url, function(data){
            if(data.success) {
                if(me.hasClass('logoutLink')) {
                    reloadUserBar();
                    reloadLeftBar();
                    $.getJSON(data.callback_url, function(data) {
                        if(data.success) {
                            $('.mainContent').html(data.html);
                        }
                    });
                } else {
                    $('.mainContent').html(data.html);
                    $('html,body').animate({scrollTop: 0}, 800);
                }
            } 
        });
    });
    
    // Opciones del sidebar
    $('.container-fluid').on('click', '.sidebar .nav-sidebar a', function(e){
        e.preventDefault();
        
        var me = $(this);
        var url = me.attr('href');
        
        changeMainContent(url);
    });
    
    // Modifica el contenido principal
    var changeMainContent = function(url) {
         $.getJSON(url, function(data) {
            if(data.success) {
                $('.mainContent').html(data.html);
            }
        });
    };
    
    // Ejecuta el login 
    var loginCheck = function(username, password, rememberMe) {
        var url = $('#old_login_check_url').val();
        var postData = {_username: username, _password: password};
        
        if(rememberMe) {
            postData._remember_me = true;
        }
        
        $.post(url, postData, function(data) {
            if(data.success) {
                if(data.login_check) {
                    validLogin(data.callback_url);
                } else {
                    currentLogin(postData);
                }
            } else {
                throw data.message;
            }
        }, 'json');
    };
    
    var currentLogin = function(postData) {
        var url = $('#login_check_url').val();
        
        $.post(url, postData, function(data) {
            if(data.success) {
                validLogin(data.callback_url);
            } else {
                throw data.message;
            }
        }, 'json');
    };
    
    var validLogin = function(callbackUrl) {
        reloadUserBar();
        reloadLeftBar();
        $.getJSON(callbackUrl, function(data) {
            if(data.success) {
                $('.mainContent').html(data.html);
            }
        });
    };
    
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

