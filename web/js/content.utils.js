// Modifica el contenido principal
function changeMainContent(url, parameters, preventPush) {
    if (!parameters) {
       parameters = {}; 
    }
    $.getJSON(url, parameters, function(data) {
        if(data.success) {
            $('.mainContent').html(data.html);
            $('html,body').animate({scrollTop: 0}, 800);
            
            if(!preventPush) {
                historyPushState(url, 'mainContent', 'inner');
            }
        }
    });
};

function historyPushState(url, targetClass, action, options, text) {
    options = !options ? {} : options;
    text = !text ? '' : text;
    history.pushState({url: url, targetClass: targetClass, action: action, options: options}, text, url);
}

$(window).on("popstate", function(e) {
    //console.log(e.originalEvent.state);
    var state = e.originalEvent.state;
    if (state !== null) {
        $.getJSON(state.url, function(data) {
            if (state.action == 'replace') {
                $('.' + state.targetClass).replaceWith(data.html);
            } else if(state.action == 'inner') {
                $('.' + state.targetClass).html(data.html);
            }
            $('html,body').animate({scrollTop: 0}, 800);
        });
    }
});

// Guardando el primer estado
history.replaceState({
    url: window.location.pathname, 
    targetClass: 'mainContent', 
    action: 'inner', 
    options: {}
}, document.title, window.location.pathname);
  