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
    
    var movePlaylist = function() {
        if ($('.sidebar').css('display') === 'none' && $('.sidebar .player').length > 0) {
            var player = $('.sidebar .player');
            player.css('position', 'absolute');
            player.css('left', '-500px');
            $('body').append(player);
        }
        //$('.albumContent.playList').hide(800);
        //$("html, body").animate({ scrollTop: $(document).height() }, 1000);
    };
	
    $('.mainContent').on('click', '.headerDisco .playPause', function(e) {
        e.preventDefault();
        var me = $(this);
        
        var toActivate = me.find('.glyphicon.hidden');
        if(toActivate.hasClass('glyphicon-play')) {
            mozcuPlaylist.pause();
            me.find('.glyphicon-pause').addClass('hidden');
            toActivate.removeClass('hidden');
            return;
        } else {
            me.find('.glyphicon-play').addClass('hidden');
            toActivate.removeClass('hidden');
            if (currentPlayList == me.attr('id')) {
                mozcuPlaylist.play();
                return;
            }
        }
        
        //movePlaylist();
        
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