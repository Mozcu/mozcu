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
    
    // Link registrarse home
    $('.mainContent').on('click', '.crearRegistro', function(e) {
        e.preventDefault();
        
        changeMainContent($(this).attr('href'));
    });
    
    // Check login
    $('.mainContent').on('click', '.loginCheckButton', function(e){
        e.preventDefault();

        var me = $(this);
        var username = me.parent().find('#username').val();
        var password = me.parent().find('#password').val();
        var rememberMe = me.parent().find('#remember_me');
        //var loader = me.parents('.loginLinkWrapper').find('.ajaxLoader');
        
        if(username.length > 0 && password.length > 0) {
            //loader.show();
            if(rememberMe.is(':checked')) {
                loginCheck(username, password, true);
            } else {
                loginCheck(username, password);
            }
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
                $('html,body').animate({scrollTop: 0}, 800);
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
                $('.form-signin .alert').hide();
                $('.form-signin .alert').show('300');
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
                $('.form-signin .alert').hide();
                $('.form-signin .alert').show('300');
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
    
    
    $('.mainContent').on('click', '.btn-success.btnRegCont', function(e){
        var url = $(this).data('url');
        
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
        
        $(this).prop('disabled', true);
        $.post(url, {data: account}, function(data) {
            if(data.success) {
                $.getJSON(data.callback_url, {}, function(data) {
                    if(data.success) {
                        $('.mainContent').html(data.html);
                        reloadUserBar();
                        reloadLeftBar();
                        $('html,body').animate({scrollTop: 0}, 800);
                    }
                });
            } else {
                errorMsg.append('<p class="error"> - ' + data.message + '</p>');
                errorMsg.show();
                $('html,body').animate({scrollTop: 0}, 800);
                $(this).prop('disabled', false);
            }
        }, 'json');
    });
    
    var prepareAccountData = function() {
        var account = {};
        account.username = $.trim($('#username').val());
        account.city = $.trim($('#city').val());
        account.country = $('#country').val();
        account.email = $.trim($('#email').val());
        account.password = $('#password').val();
        account.passwordRepeat = $('#repeat_password').val();
        account.terms = $('#terms').is(':checked');
        
        return account;
    };
    
    var validateAccountData = function(account) {
        var response = {};
        response.success = true;
        response.errors = new Array();
        
        if (account.username.length === 0) {
            response.success = false;
            response.errors.push({message: "El campo nombre de usuario esta vacio"});
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
        
        if((account.password.length > 0 || account.passwordRepeat.length > 0)
                && (account.password !== account.passwordRepeat)) {
            response.success = false;
            response.errors.push({message: "Los passwords no coinciden"});
        } else if(account.password.length === 0) {
            response.success = false;
            response.errors.push({message: "El password esta vacio"});
        }
        
        if(!account.terms) {
            response.success = false;
            response.errors.push({message: "Debes aceptar los terminos y condiciones"})
        }
        
        return response;
    };
    
    var validateEmail = function(email) { 
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };
    
    // Links de terminos y condiciones
    $('.mainContent').on('click', '.terms footer a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        changeMainContent(url);
    });
    
});

