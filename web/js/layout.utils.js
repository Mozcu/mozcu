$(function() {
   
    // Click en el logo superior izquirdo
    $('.navbar.navbar-fixed-top').on('click', '.navbar-header a', function(e){
        e.preventDefault();
        changeMainContent($(this).attr('href'));
    });
    
    // Link login
    $('.navbar.navbar-fixed-top').on('click', '.userBar .loginLink', function(e) {
        e.preventDefault();
        changeMainContent($(this).attr('href'));
    });
    
    // Link login home
    $('.mainContent').on('click', '.btnIngresar', function(e) {
        e.preventDefault();
        changeMainContent($(this).data('url'));
    });
    
    // Links footer
    $('.mainContent').on('click', 'footer a', function(e) {
        e.preventDefault();
        
        changeMainContent($(this).attr('href'));
    });
    
    // Link registrarse en home y login
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
        changeMainContent($(this).data('url'));
    });
    
    // Link recuperar password
    $('.mainContent').on('click', '.forgotPasswordLink', function(e) {
        e.preventDefault();
        changeMainContent($(this).attr('href'));
    });
    
    // Enviar mail para recuperar password
    $('.mainContent').on('click', '.forgotPassword .btnRecPass', function(e) {
        e.preventDefault();
        var me = $(this);
        var email = me.parent().find('.email').val();
        var errorMsg = $('.alert-danger');
        
        errorMsg.hide();
        errorMsg.find('.error').remove();
        
        if(!validateEmail(email)) {
            errorMsg.append('<p class="error"> - Formato de email invalido </p>');
            errorMsg.show();
            return;
        }
        
        me.prop('disabled', true);
        $.post(me.data('url'), {email: email}, function(data) {
            if(data.success) {
                me.parent().replaceWith('<p class="text-success text-center">' + data.message + '</p>')
            } else {
                errorMsg.append('<p class="error"> - ' + data.message +' </p>');
                errorMsg.show();
            }
            me.prop('disabled', false);
        }, 'json');
    });
    
    // Enviar formulario para recuperar password
    $('.mainContent').on('click', '.passwordRecovery .btnRecPass', function(e) {
        e.preventDefault();
        var me = $(this);
        var errorMsg = $('.alert-danger');
        var passwd = me.parent().find('.password').val();
        var confirm = me.parent().find('.confirm-password').val();
        
        errorMsg.hide();
        errorMsg.find('.error').remove();
        
        if(passwd.length === 0) {
            errorMsg.append('<p class="error"> - Contraseña vacia </p>');
            errorMsg.show();
            return;
        }
        if(passwd !== confirm) {
            errorMsg.append('<p class="error"> - Las contraseñas no coinciden </p>');
            errorMsg.show();
            return;
        }
        
        me.prop('disabled', true);
        $.post(me.data('url'), {password: passwd, confirm_password: confirm}, function(data) {
            if(data.success) {
                me.parent().replaceWith('<p class="text-success text-center">' + data.message + '</p>')
            } else {
                errorMsg.append('<p class="error"> - ' + data.message +' </p>');
                errorMsg.show();
            }
            me.prop('disabled', false);
        }, 'json');
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
                    changeMainContent(data.callback_url);
                } else {
                    $('.mainContent').html(data.html);
                    $('html,body').animate({scrollTop: 0}, 800);
                    historyPushState(url, 'mainContent', 'inner');
                }
            } 
        });
    });
    
    // Opciones del sidebar
    $('.container-fluid').on('click', '.sidebar .nav-sidebar a', function(e){
        e.preventDefault();
        changeMainContent($(this).attr('href'));
    });
    
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
        changeMainContent(callbackUrl);
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
    
    // Registro
    $('.mainContent').on('click', '.btn-success.btnRegCont', function(e){
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
        $.post(url, {data: account}, function(data) {
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
        changeMainContent($(this).attr('href'));
    });
    
});

