(function() {
	tinymce.PluginManager.add( 'youtube', function( editor, url ) {
		// Add Button to Visual Editor Toolbar
		editor.addButton('youtube', {
			title: 'Insert YouTube Video',
			image: url + '/images/icon-youtube.png',
			cmd: 'youtube'
		});	

		// Add Command when Button Clicked
		editor.addCommand('youtube', function() {
			// Check we have selected some text that we want to link
			editor.windowManager.open({
                title: 'Insert YouTube Video',
                body: [
                    {
                    type: 'textbox',
                    name: 'youtube_url',
                    label: 'Enter the YouTube URL',
                    minWidth: 350
                	}
                ],
                onsubmit: function(e) {
                	if(!validateYouTubeUrl(e.data.youtube_url)) {
						/*alert('The URL entered is not a valid YouTube URL');*/
						e.preventDefault();
						editor.windowManager.alert('The URL entered is not a valid YouTube URL', function(){});

						//return;
					}else{
	                    editor.focus();
	                    // Insert selected callout back into editor, wrapping it in a shortcode
	                    editor.execCommand('mceInsertContent', false, '[youtubeEmbed url="' + e.data.youtube_url + '"][/youtubeEmbed]');
	                }
                }
            });
		});
	});

    tinymce.PluginManager.add( 'vimeo', function( editor, url ) {
        // Add Button to Visual Editor Toolbar
        editor.addButton('vimeo', {
            title: 'Insert Vimeo Video',
            image: url + '/images/icon-vimeo.png',
            cmd: 'vimeo'
        }); 

        // Add Command when Button Clicked
        editor.addCommand('vimeo', function() {
            // Check we have selected some text that we want to link
            editor.windowManager.open({
                title: 'Insert Vimeo Video',
                body: [
                    {
                    type: 'textbox',
                    name: 'vimeo_url',
                    label: 'Enter the Vimeo URL',
                    minWidth: 350
                    }
                ],
                onsubmit: function(e) {
                    if(!validateVimeoUrl(e.data.vimeo_url)) {
                        /*alert('The URL entered is not a valid YouTube URL');*/
                        e.preventDefault();
                        editor.windowManager.alert('The URL entered is not a valid Vimeo URL', function(){});

                        //return;
                    }else{
                        editor.focus();
                        // Insert selected callout back into editor, wrapping it in a shortcode
                        editor.execCommand('mceInsertContent', false, '[vimeoEmbed url="' + e.data.vimeo_url + '"][/vimeoEmbed]');
                    }
                }
            });
        });
    });

	tinymce.PluginManager.add( 'ema_button', function( editor, url ) {
		// Add Button to Visual Editor Toolbar
		editor.addButton('ema_button', {
			title: 'Insert a button',
			image: url + '/images/icon-button.png',
			cmd: 'ema_button'
		});	

		// Add Command when Button Clicked
		editor.addCommand('ema_button', function() {
			// Check we have selected some text that we want to link		
			editor.windowManager.open({
                title: 'Add a button',
                body: [
                    {
                    type: 'textbox',
                    name: 'button_text',
                    label: 'Button Text',
                    minWidth: 350
                	},
					{
                    type: 'textbox',
                    name: 'button_url',
                    label: 'URL',
                    minWidth: 350
                	},
                	{
                    type: 'checkbox',
                    name: 'button_target',
                    label: 'Open in new window/tab?',
                    checked : false
                	}
                ],
                onsubmit: function(e) {
                    editor.focus();
                    // Insert selected callout back into editor, wrapping it in a shortcode
                    var target = (e.data.button_target) ? (' target="blank"') : ('');
                    editor.execCommand('mceInsertContent', false, '[button url="' + e.data.button_url + '"'+target+']'+e.data.button_text+'[/button]');
                }
            });
		});
	});

	tinymce.PluginManager.add( 'pull_quote', function( editor, url ) {
		// Add Button to Visual Editor Toolbar
		editor.addButton('pull_quote', {
			title: 'Insert a pull quote',
			image: url + '/images/icon-pull-quote.png',
			cmd: 'pull_quote'
		});	

		// Add Command when Button Clicked
		editor.addCommand('pull_quote', function() {
			// Check we have selected some text that we want to link		
			editor.windowManager.open({
                title: 'Add a pull quote',
                body: [
                    {
                    type: 'textbox',
                    multiline: true,
                    name: 'pull_quote',
                    label: 'Quote',
                    minWidth: 350,
                    minHeight: 150
                	}
                ],
                onsubmit: function(e) {
                    editor.focus();
                    // Insert selected callout back into editor, wrapping it in a shortcode
                    editor.execCommand('mceInsertContent', false, '[pull_quote]'+e.data.pull_quote+'[/pull_quote]');
                }
            });
		});
	});

    tinymce.PluginManager.add( 'color_block', function( editor, url ) {
        // Add Button to Visual Editor Toolbar
        editor.addButton('color_block', {
            title: 'Insert a color block',
            image: url + '/images/icon-color-block.png',
            cmd: 'color_block'
        }); 

        // Add Command when Button Clicked
        editor.addCommand('color_block', function() {
            // Check we have selected some text that we want to link        
            editor.windowManager.open({
                title: 'Add a color block',
                body: [
                    {
                    type   : 'listbox',
                    name   : 'color',
                    label  : 'Colors',
                    values : [
                        { text: 'Moss', value: 'moss-lt' },
                        { text: 'Beach', value: 'beach-lt' },
                        { text: 'Slate', value: 'slate-lt' },
                        { text: 'Loam', value: 'loam-lt' },
                        { text: 'Clay', value: 'clay-lt' }
                    ],
                    minWidth: 350
                    }

                ],
                onsubmit: function(e) {
                    editor.focus();
                    // Insert selected callout back into editor, wrapping it in a shortcode
                    var micetype = (e.data.micetype) ? (' micetype="'+e.data.micetype+'"') : ('');
                    editor.execCommand('mceInsertContent', false, '[color_block color="'+e.data.color+'"]Enter Content Here[/color_block]');
                }
            });
        });
    });

    tinymce.PluginManager.add( 'script_embed', function( editor, url ) {
        // Add Button to Visual Editor Toolbar
        editor.addButton('script_embed', {
            title: 'Insert a JavaScript Snippet',
            image: url + '/images/icon-pull-quote.png',
            cmd: 'script_embed'
        }); 

        // Add Command when Button Clicked
        editor.addCommand('script_embed', function() {
            // Check we have selected some text that we want to link        
            editor.windowManager.open({
                title: 'Add a JavaScript Snippet',
                body: [
                    {
                    type: 'textbox',
                    multiline: true,
                    name: 'script_embed',
                    label: 'Script',
                    minWidth: 350,
                    minHeight: 150
                    }
                ],
                onsubmit: function(e) {
                    editor.focus();
                    // Insert selected callout back into editor, wrapping it in a shortcode
                    /*string = e.data.script_embed;
                    string = string.replace(/(\r\n|\n|\r)/gm,"");
                    string = string.replace('<script>','<script>// <![CDATA[');
                    string = string.replace('<script type="text/javascript">','<script type="text/javascript">// <![CDATA[');
                    string = string.replace('</script>','// ]]</script>');*/

                    editor.execCommand('mceInsertRawHTML', false, '[script_embed]'+e.data.script_embed+'[/script_embed]');
                    //tinymce.activeEditor.setContent('[script_embed]'+e.data.script_embed+'[/script_embed]', {format: 'raw'});
                }
            });
        });
    });
})();




function validateYouTubeUrl(url)
{
    if (url != undefined || url != '') {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
        var match = url.match(regExp);
        if (match && match[2].length == 11) {
            // Do anything for being valid
            return true;
        }
        else {
            return false;
        }
    }
}

function validateVimeoUrl(url){
    var regExp = /(?:https?:\/\/(?:www\.)?)?vimeo.com\/(?:channels\/|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/,
    match = url.match(regExp);
    if(match) {
        return true;
    }else{
        return false;
    }
}