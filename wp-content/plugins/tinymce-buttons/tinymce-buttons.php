<?php
/**
 * Plugin Name: TinyMCE Buttons
 * Plugin URI: https://www.mower.com
 * Version: 1.0
 * Author: Eric Mower + Associates
 * Author URI: https://www.mower.com
 * Description: A TinyMCE plugin to allow for custom TinyMCE buttons.
 */

require_once('tinymce-shortcodes.php');
class TinyMCE_Buttons extends TinyMCE_Shortcodes {
	
	private $buttons;
	private $shortcodes;

	/**
	* Constructor. Called when the plugin is initialised.
	*/
	public function __construct() {
		if ( is_admin() ) :

			$this->buttons = array(
				'youtube'		=>'YouTube',
				'vimeo'			=>'Vimeo',
				'ema_button'	=>'Button',
				'pull_quote'	=>'Pull Quote',
				//'color_block'	=>'Color Block',
				//'script_embed'	=>'Script Embed'
			);
			

			add_action( 'init', array(  $this, 'setup_tinymce_plugin' ) );
			add_action( 'admin_menu', array(  $this, 'button_settings_menu' ) );
		endif;

		if ( !is_admin() ) :
			$this->shortcodes = array(
	        'button'        =>'button',
	        'youtubeEmbed'  =>'youtube_embed',
	        'vimeoEmbed'    =>'vimeo_embed',
	        'pull_quote'    =>'pull_quote',
	        //'color_block'   =>'color_block',
        	);

            foreach($this->shortcodes as $tag=>$func) :
                add_shortcode( $tag, array( $this, $func ));
            endforeach;

            //register scripts needed for shortcodes (YouTube, Vimeo, etc...)
            wp_register_script('tinyMCEYouTubeAPI','https://www.youtube.com/player_api', array(), false, true);
            wp_register_script('tinyMCEVimeoAPI','https://player.vimeo.com/api/player.js', array(), false, true);

            wp_register_script('tinyMCEYouTube', plugin_dir_url( __FILE__ ).'js/youtube.js' , array('tinyMCEYouTubeAPI'), false, true);
            wp_register_script('tinyMCEVimeo', plugin_dir_url( __FILE__ ).'js/vimeo.js' , array('tinyMCEVimeoAPI'), false, true);

        endif;
	}

	/**
	* Check if the current user can edit Posts or Pages, and is using the Visual Editor
	* If so, add some filters so we can register our plugin
	*/
	public function setup_tinymce_plugin() {

		// Check if the logged in WordPress User can edit Posts or Pages
		// If not, don't register our TinyMCE plugin
			
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) :
			return;
		endif;

		// Check if the logged in WordPress User has the Visual Editor enabled
		// If not, don't register our TinyMCE plugin
		if ( get_user_option( 'rich_editing' ) !== 'true' ) :
			return;
		endif;

		//save settings data
		
		if(isset($_POST['submit'])) :
			unset($_POST['submit']);

			$settings = array();
			foreach($_POST as $k=>$v) :
				$settings[$k] = $v[0];
			endforeach;

   	 		update_option( 'tinymce_button_settings', json_encode($settings) );
   	 	endif;

		// Setup some filters
		add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ) );
		add_filter( 'mce_buttons', array( &$this, 'add_tinymce_toolbar_button' ) );
			
	}

	/**
	* Adds a TinyMCE plugin compatible JS file to the TinyMCE / Visual Editor instance
	*
	* @param array $plugin_array Array of registered TinyMCE Plugins
	* @return array Modified array of registered TinyMCE Plugins
	*/
	public function add_tinymce_plugin( $plugin_array ) {
		$saved_settings = $this->get_saved_settings();
		foreach($this->buttons as $k=>$v) :
			if(empty($saved_settings) || $saved_settings[$k] === 'yes' || $saved_settings[$k] === '') :
				$plugin_array[$k] = plugin_dir_url( __FILE__ ) . '/tinymce-buttons.js';
			endif;
		endforeach;
		return $plugin_array;
	}

	/**
	* Adds a button to the TinyMCE / Visual Editor which the user can click
	* to insert a link with a custom CSS class.
	*
	* @param array $buttons Array of registered TinyMCE Buttons
	* @return array Modified array of registered TinyMCE Buttons
	*/
	public function add_tinymce_toolbar_button( $buttons ) {
		$saved_settings = $this->get_saved_settings();
		foreach($this->buttons as $k=>$v) :
			if(empty($saved_settings) || $saved_settings[$k] === 'yes') :
				array_push( $buttons, '|', $k );
			endif;
		endforeach;
		return $buttons;
	}

	public function button_settings_menu() {
		//create new top-level menu
		//add_options_page('TinyMCE Button Settings','TinyMCE Button Settings','administrator','tinymce-button-settings');
		
		 add_options_page(
            __( 'TinyMCE Button Settings', 'textdomain' ),
            __( 'TinyMCE Button Settings', 'textdomain' ),
            'manage_options',
            'tinymce_button_settings',
            array(
                $this,
                'tinymce_button_settings_page'
            )
        );

		//call register settings function
		//add_action( 'admin_init', 'button_settings_settings' );
	}


	public function tinymce_button_settings_page() { 
		$saved_settings = $this->get_saved_settings();
	?>
		<div class="wrap">
		<h1>TinyMCE Settings</h1>
		<p>This page will allow you to toggle the custom EMA TinyMCE buttons.</p>
		<form method="post" action="options-general.php?page=tinymce_button_settings">
		    <table class="form-table">
		    	<?php 
		    	foreach($this->buttons as $k=>$v) : 
		    		$checked = (array_key_exists($k, $saved_settings) && $saved_settings[$k] === 'yes') ? (' checked') : ('');
		    	?>
			        <tr valign="top">
			        <th scope="row">Show <?php echo $v; ?></th>
			        <td>
			        	<label for="<?php echo $k; ?>_yes"><input type="radio" id="<?php echo $k; ?>_yes" name="<?php echo $k; ?>[]" value="yes"<?php if( array_key_exists($k, $saved_settings) && $saved_settings[$k] === 'yes'){ echo ' checked'; } ?> /> Yes</label><br />
			        	<label for="<?php echo $k; ?>_no"><input type="radio" id="<?php echo $k; ?>_no" name="<?php echo $k; ?>[]" value="no"<?php if(array_key_exists($k, $saved_settings) && $saved_settings[$k] === 'no'){ echo ' checked'; } ?> /> No</label>
			        </tr>
			    <?php endforeach; ?>
		         
		    </table>
		    
		    <?php submit_button(); ?>

		</form>
		</div>
	<?php 
	}

	private function get_saved_settings() {
		$saved_settings = json_decode(get_option('tinymce_button_settings'), true);
		if(!is_array($saved_settings)) :
			$saved_settings = array();
		endif;

		return $saved_settings;
	}
    
}

$tinymce_Buttons = new TinyMCE_Buttons;
