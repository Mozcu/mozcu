$(function() {
    
    currentPlayList = null;
	
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
        
        var toActivate = me.find('.glyphicon.hidden');
        if(toActivate.hasClass('glyphicon-play')) {
            mozcuPlaylist.pause();
            me.find('.glyphicon-pause').addClass('hidden');
            toActivate.removeClass('hidden');
            
            $('#playerControlBottom .jp-pause').hide();
            $('#playerControlBottom .jp-play').show();
            
            return;
        } else {
            me.find('.glyphicon-play').addClass('hidden');
            toActivate.removeClass('hidden');
            
            $('#playerControlBottom .jp-play').hide();
            $('#playerControlBottom .jp-pause').show();
            
            if (currentPlayList == me.attr('id')) {
                mozcuPlaylist.play();
                return;
            }
        }
        
        var album = getAlbumForPlaylist(me.attr('id'));
        var songs = album.songs;
        var image = album.image;
        currentPlayList = me.attr('id');
        mozcuPlaylist.setPlaylist(songs);
        $('.jp-playlist').css('background-image', 'url('+ image +')');
    });
	
    $('.mainContent').on('click', '.playList .songName a', function(e) {
        e.preventDefault();
        //movePlaylist();
        
        var me = $(this);
        var data = me.attr('id').split('-');
        var songs = getSongsForPlaylist(data[0]);
        
        $('.headerDisco .playPause .glyphicon-play').addClass('hidden');
        $('.headerDisco .playPause .glyphicon-pause').removeClass('hidden');
        
        if (currentPlayList != data[0]) {
            mozcuPlaylist.setPlaylist(songs);
            currentPlayList = data[0];
        }
        mozcuPlaylist.play(data[1] - 1);
    });
    
    /** Villereadas para player de mobile **/
    $('body').on('click', '#playerControlBottom .jp-previous', function(e) {
        e.preventDefault();
        mozcuPlaylist.previous();
    });
    
    $('body').on('click', '#playerControlBottom .jp-next', function(e) {
        e.preventDefault();
        mozcuPlaylist.next();
    });
    
    $('body').on('click', '#playerControlBottom .jp-play', function(e) {
        e.preventDefault();
        if(!currentPlayList) {
            return;
        }
        
        mozcuPlaylist.play();
        $(this).hide();
        $('#playerControlBottom .jp-pause').show();
    });
    
    $('body').on('click', '#playerControlBottom .jp-pause', function(e) {
        e.preventDefault();
        mozcuPlaylist.pause();
        $(this).hide();
        $('#playerControlBottom .jp-play').show();
        
    });
    
    $('body').on('click', '#musicPlayer .jp-play', function(e) {
        e.preventDefault();
        $('#musicPlayer .jp-play').hide();
        $('#musicPlayer .jp-pause').show();
    });
    
    $('body').on('click', '#musicPlayer .jp-pause', function(e) {
        e.preventDefault();
        $('#musicPlayer .jp-pause').hide();
        $('#musicPlayer .jp-play').show();
    });
    /** fin de las villereadas **/
    
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

// Log de reproduccion
$(function() {
    var currentAlbumId = null;
    var currentSongId = null;
    var paused = false;
    
    var logAlbum = function (albumId, ownerId) {
        var url = $('#urlLogAlbum').val();
        var params = {
            album_id: albumId,
            album_owner_id: ownerId
        };
        
        $.post(url, params, function (response) {
            
        }, 'json');
    };
    
    $("#jquery_jplayer_1").bind($.jPlayer.event.play, function(e) {
        var song = e.jPlayer.status.media;
        
        if (paused && currentSongId !== null && currentSongId === song.id) {
            paused = false;
            return;
        }
        
        paused = false;
        currentSongId = song.id;
        console.log('Log de cancion ' + song.title + ' - Owner Id: ' + song.owner_id);
        
        if (currentAlbumId !== null && currentAlbumId === song.album_id) {
            return;
        }
        currentAlbumId = song.album_id;
        console.log('Log de Album ' + song.album_id + ' - Owner Id: ' + song.owner_id);
        logAlbum(song.album_id, song.owner_id);
    });
    
    $("#jquery_jplayer_1").bind($.jPlayer.event.pause, function(e) {
        paused = true;
    });
    
});