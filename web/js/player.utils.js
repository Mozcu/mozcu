$(function() {
    
    var currentPlayList = null;
	
    var getAlbumForPlaylist = function(id) {
        var url = $('#urlAlbumForPlayer').val();
        var album = null;
        $.ajaxSetup({async: false});
        $.getJSON(url, {id: id}, function(data){
            if (data.success) {
                album = data.album;
            }
        });
        $.ajaxSetup({async: true});
        
        return album;
    };
    
    var getSongsForPlaylist = function(id) {
        var album = getAlbumForPlaylist(id);
        return album.songs;
    };
	
    $('.mainContent').on('click', '.headerDisco .playPause', function(e) {
        e.preventDefault();
        var me = $(this);
        var album = getAlbumForPlaylist(me.attr('id'));
        var songs = album.songs;
        var image = album.image;
        currentPlayList = me.attr('id');
        mozcuPlaylist.setPlaylist(songs);
        $('.jp-playlist').css('background-image', 'url('+ image +')');
    });
	
    $('.mainContent').on('click', '.playList .songName a', function(e) {
        e.preventDefault();
        var me = $(this);
		var data = me.attr('id').split('-');
        var songs = getSongsForPlaylist(data[0]);
        if (currentPlayList != data[0]) {
            mozcuPlaylist.setPlaylist(songs);
            currentPlayList = data[0];
        }
        mozcuPlaylist.play(data[1] - 1);
    });
    
    var mozcuPlaylist = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_1",
            cssSelectorAncestor: "#jp_container_1"
    }, [],
    {
        playlistOptions: {
            autoPlay: true,
        },
        swfPath: "/js/jplayer",
        supplied: "mp3",
        smoothPlayBar: true,
        wmode: "window",
        keyEnabled: true,
        solution: "flash, html"
    });
    
});