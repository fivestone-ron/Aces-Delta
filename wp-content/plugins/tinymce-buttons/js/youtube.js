ytVideoTitle = '';
function onYouTubeIframeAPIReady() {
  if(jQuery('.yt-player-autoload').length > 0) {
    jQuery('.yt-player-autoload').each(function(){
      ytVideoId = jQuery(this).attr('id');
      ytVideoTitle = jQuery(this).attr('id');
      loadYT(ytVideoId, 0, ytVideoTitle);
    });
  }
}

function loadYT(id, autoplay, ytVideoTitle) {
  //id is the videoID for YouTube as well as the ID for the container we are targeting
  embeddedPlayer = new YT.Player(id, {
      playerVars: { 'autoplay': autoplay, 'controls': 2,'autohide':1,'wmode':'opaque','rel':0 },
      videoId: id,
      events: {
          'onStateChange': embeddedOnPlayerStateChange
      }
  });
}

function embeddedOnPlayerStateChange(event) {
  videoInfo = event.target.getVideoData();
  switch (event.data){
    case YT.PlayerState.PLAYING:
        if(analytics.type == 'ga') {
          ga('send', 'event', 'Video', 'Play', videoInfo.title);
        }else if(analytics.type == 'gtm'){
          dataLayer.push({'category':'Video',' action':'Play', 'label':videoInfo.title});
        }else{
          //houston we have a problem!
        }
      break;
    case YT.PlayerState.ENDED:
        if(analytics.type == 'ga') {
          ga('send', 'event', 'Video', 'Complete', videoInfo.title);
        }else if(analytics.type == 'gtm'){
          dataLayer.push({'category':'Video',' action':'Complete', 'label':videoInfo.title});
        }else{
          //houston we have a problem!
        }
      break;
  }
}