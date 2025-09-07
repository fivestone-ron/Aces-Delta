<?php
class TinyMCE_Shortcodes  {
    /**
    * Button
    **/
    public function button( $atts, $content='' ){
    	$a = shortcode_atts( array(
            'url' => '/',
            'target' => ''
        ), $atts );

        $url_protocol = parse_url($a['url']);
     	
    	if(!isset($url_protocol['scheme'])) :
    		$a['url'] = 'http://'.$a['url'];
    	endif;

    	$target = ($a['target'] === 'blank') ? (' target="_blank"') : ('');

    	return '<a href="'.$a['url'].'"'.$target.' class="button track" data-emacategory="CTA" data-emalabel="'.$content.' | '.$a['url'].'">'.$content.'</a>';
    }

    /**
    * YouTube Embed
    **/
    public function youtube_embed($atts, $content='') {
        wp_enqueue_script( 'tinyMCEYouTubeAPI' );
        wp_enqueue_script( 'tinyMCEYouTube' );
        $player = '';
        $a = shortcode_atts( array(
        'url' => ''
        ), $atts );
        $url = $a['url'];

    	$ytid = $this->getYoutubeIdFromUrl($url);

        return '<div class="video-container"><div class="yt-player-autoload" id="'.$ytid.'" yt-player-title="'.$content.'"></div></div>';
    }

    /**
    * Vimeo Embed
    **/
    public function vimeo_embed($atts, $content='') {
        wp_enqueue_script( 'tinyMCEVimeoAPI' );
        wp_enqueue_script( 'tinyMCEVimeo' );
        $player = '';
        $a = shortcode_atts( array(
        'url' => ''
        ), $atts );
        $url = $a['url'];

        $vimeoid = $this->getVimeoVideoIdFromUrl($url);

        return '<div class="video-container"><div class="vimeo-player-autoload" id="'.$vimeoid.'" vimeo-player-title="'.$content.'"></div></div>';
    }

    /**
    * Pull Quote
    **/
    public function pull_quote($atts, $content='') {
        return '<p class="pull-quote">'.$content.'</p>';
    }

    private function getYoutubeIdFromUrl($url) {
        $parts = parse_url($url);
        if(isset($parts['query'])){
            parse_str($parts['query'], $qs);
            if(isset($qs['v'])){
                return $qs['v'];
            }else if(isset($qs['vi'])){
                return $qs['vi'];
            }
        }
        if(isset($parts['path'])){
            $path = explode('/', trim($parts['path'], '/'));
            return $path[count($path)-1];
        }
        return false;
    }

    private function getVimeoVideoIdFromUrl($url = '') { 
        $regs = array();
        $id = '';
        if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
            $id = $regs[3];
        }
        return $id;
    }
}

