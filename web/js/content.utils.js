// Modifica el contenido principal
function changeMainContent(url) {
     $.getJSON(url, function(data) {
        if(data.success) {
            $('.mainContent').html(data.html);
            $('html,body').animate({scrollTop: 0}, 800);
        }
    });
};