jQuery(document).ready(function(){
   if(jQuery('.vimeo-player-autoload').length > 0) {
    jQuery('.vimeo-player-autoload').each(function(){
      vimeoVideoId = jQuery(this).attr('id');
      tinyMCELoadVimeo(vimeoVideoId, false);
    });
  }
});

function tinyMCELoadVimeo(id, autoplay) {
  //id is the videoID for Vimeo as well as the ID for the container we are targeting
  var options = {
        id: id,
        width: 640,
        loop: false
    };

    var player = new Vimeo.Player(id, options);

    player.getVideoTitle().then(function(title) {
      video_title = title
    }).catch(function(error) {
        // an error occurred
    });
    
    // player.on('play', function() {
    //     sendVimeoEvent(video_title,'Play');
    // });

    // player.on('ended', function() {
    //     sendVimeoEvent(video_title,'Complete');
    // });

    if(autoplay) {
      player.ready().then(function(title) {
        player.play();
      });
    }
}

function sendTinyMCEVimeoEvent(title, action) {
  if(analytics.type == 'ga') {
    ga('send', 'event', 'Video', status, title);
  }else if(analytics.type == 'gtm'){
    dataLayer.push({event:'video', 'eventCategory': 'Video', 'eventAction':action, 'eventLabel': title});
  }else{
    //houston we have a problem!
  }
}