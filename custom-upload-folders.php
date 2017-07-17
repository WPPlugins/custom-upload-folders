<?php
/**
 * Plugin Name: Custom Upload Folders
 * Plugin URI: http://wordpress.org/extend/plugins/custom-upload-folders/
 * Description: Organize the uploads by Year-Month-Day, File Type, Post ID or Author display name.
 * Version: 1.2
 * Author: Rodolfo Buaiz
 * Author URI: http://rodbuaiz.com/
 * Text Domain: bfcuf
 * Domain Path: /languages/
 * License: GPLv2 or later
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */


!defined( 'ABSPATH' ) AND exit(
                "<pre>Hi there! I'm just part of a plugin, <h1>&iquest;what exactly are you looking for?"
);


add_action(
	'plugins_loaded', 
	array( BF_Custom_Upload_Folders_Class::get_instance(), 'plugin_setup' )
);


class BF_Custom_Upload_Folders_Class
{
	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 * @type object
	 */
	protected static $instance = NULL;

	
	/**
	 * URL to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_url = '';

	
	/**
	 * Path to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_path = '';

	
	/**
	 * Access this plugin’s working instance
	 *
	 * @wp-hook plugins_loaded
	 * @since   2012.09.13
	 * @return  object of this class
	 */
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}


	/**
	 * Used for regular plugin work.
	 *
	 * @wp-hook plugins_loaded
	 * @since   2012.09.10
	 * @return  void
	 */
	public function plugin_setup()
	{
		$this->plugin_url	 = plugins_url( '/', __FILE__ );
		$this->plugin_path	 = plugin_dir_path( __FILE__ );
		$this->plugin_slug   = dirname( plugin_basename( __FILE__ ) );
		$this->load_language( 'bfcuf' );

		add_filter(
			'wp_handle_upload_prefilter',
			array( $this, 'handle_upload_prefilter' )
		);
		add_filter(
			'wp_handle_upload',
			array( $this, 'handle_upload' )
		);
		
		add_filter( 
			'admin_init' , 			
			array( $this, 'register_fields' ) 
		);
		add_filter( 
			'plugin_action_links',  
			array( $this, 'settings_plugin_link' ), 
			10, 
			2 
		);

		// Dummy strings for Plugin Header translation
		$plugin_name = __( 'Custom Upload Folders', 'bfcuf' );
		$plugin_desc = __( 'Organize the uploads by Year-Month-Day, File Type, Post ID or Author display name.', 'bfcuf' );
	}


	/**
	 * Constructor. Intentionally left empty and public.
	 *
	 * @see plugin_setup()
	 * @since 2012.09.12
	 */
	public function __construct() {}


	/**
	 * CUSTOM UPLOAD DIR
	 * Change upload folder
	 * 
	 * @param type $file
	 * @return type
	 */
	public function handle_upload_prefilter( $file )
	{
	    add_filter( 'upload_dir', array($this, 'custom_upload_dir') );
	    return $file;
	}

	
	/**
	 * CUSTOM UPLOAD DIR
	 * Remove upload folder filter
	 * 
	 * @param type $fileinfo
	 * @return type
	 */
	public function handle_upload( $fileinfo )
	{
	    remove_filter('upload_dir', array($this, 'custom_upload_dir'));
	    return $fileinfo;
	}


	/**
	 * CUSTOM UPLOAD DIR
	 * Organize the Uploads Folder
	 * 
	 * @param type $path
	 * @return string
	 */
	public function custom_upload_dir( $path )
	{   
	    if( $path['error'] )
	        return $path; //error on uploading
		// Check if uploading from a plugin
		$pid = ( !isset( $_REQUEST['post_id'] ) ) ? 0 : $_REQUEST['post_id'];
		$option_value = get_option( 'custom_upload_folders' );
		if( $pid == 0 && 'by_filetype' != $option_value )
			$option_value = 'default';
		$customdir = '';
		
		switch( $option_value )
		{
			case 'by_id':
				$customdir = '/' . $pid;
				break;

			case 'by_author':
			    $the_post = get_post( $pid );
				$the_author = get_user_by('id', $the_post->post_author);
				$customdir = '/' . $the_author->data->display_name;
				break;
			
			case 'by_slug':
				$the_post = get_post( $pid );
				$customdir = '/' . $the_post->post_name;
				break;
			
			case 'by_filetype':
				$img_arr = array( 'jpg', 'jpeg', 'png', 'gif', 'svg', 'tif', 'tiff', 'ico' );
				$vid_arr = array( 'mp4', 'ogg', 'm4v', 'avi', 'mp3', 'wav', 'mov', 'mpeg', 'mpg', 'mkv', 'webm', 'flv' );
				$doc_arr = array( 'txt', 'rtf', 'doc', 'docx', 'xls', 'xlsx', 'pdf' );
				$extension = pathinfo( $_POST['name'], PATHINFO_EXTENSION );
				switch( true )
				{
				   case in_array( $extension, $img_arr ):
					   $customdir = '/images';
					   break;

				   case in_array( $extension, $vid_arr ):
					   $customdir = '/audiovisual';
					   break;

				   case in_array( $extension, $doc_arr ):
					   $customdir = '/documents';
					   break;

				   default:
					   $customdir = '/others';
					   break;
				}
				break;
			case 'by_ymd':
				$the_post = get_post( $pid );
				$y = date( 'Y', strtotime( $the_post->post_date ) );
				$m = date( 'm', strtotime( $the_post->post_date ) );
				$d = date( 'd', strtotime( $the_post->post_date ) );

				$customdir = '/' . $y . '/' . $m . '/' . $d;
				break;
			default:
				$customdir = '/general';
				break;
		}
		
	    $path['path']    = str_replace( $path['subdir'], '', $path['path'] ); //remove default subdir (year/month)
	    $path['url']     = str_replace( $path['subdir'], '', $path['url'] ); 
		
		// Add Year-Month to the subfolder -> /custom-folder/year-month/
		if ( strlen($path['subdir']) > 1 && !in_array( $option_value, array( 'by_ymd', 'by_slug', 'by_id' ) ) )
		{
			$path['subdir'] = $this->str_lreplace( '/', '-', $path['subdir'] );
			$customdir .= $path['subdir'];
		}
		
		$path['subdir']  = $customdir;
		$path['path']   .= $customdir; 
		$path['url']    .= $customdir;
		return $path;
	}

	private function str_lreplace( $search, $replace, $subject )
	{
		$pos = strrpos( $subject, $search );

		if($pos !== false)
		{
			$subject = substr_replace( $subject, $replace, $pos, strlen( $search ));
		}

		return $subject;
	}
	/**
	 * Add settings to wp-admin/options-general.php page
	 */
	public function register_fields() 
	{
		register_setting( 'media', 'custom_upload_folders', 'esc_attr' );
		add_settings_field(
			'custom_upload_folders',
			__( 'Custom Upload Folders', 'bfcuf' ),
			array( $this, 'custom_upload_folders_func' ),
			'media',
			'uploads'
		);
	}

	
	/**
	 * Settings dropdown options
	 */
	public function custom_upload_folders_func()
	{
		$option_value = get_option( 'custom_upload_folders' );
		$options = array( 
			'by_id'			=> __( 'By Post ID', 'bfcuf' ), 
			'by_slug'		=> __( 'By Post Slug', 'bfcuf' ), 
			'by_author'		=> __( 'By Author', 'bfcuf' ), 
			'by_filetype'	=> __( 'By File Type', 'bfcuf' ) ,
			'by_ymd'		=> __( 'By Year Month Day', 'bfcuf' ) ,
		);		
		$selected = selected( '', $option_value, false );
		
		echo "<select name='custom_upload_folders' id='custom_upload_folders'>";
		echo "<option value='' {$selected}>". __( '-none-', 'bfcuf' ) ."</option>";
		foreach( $options as $key => $value )
		{
			$selected = selected( $key, $option_value, false );
			echo "<option value='{$key}' {$selected}>{$value}</option>";
		}
		echo '</select>';
		
		printf( 
				'<p class="description desc">%s<br />%s</p>',
				__( "Some configurations (even not organizing by Year/Month) are not recommended if your site has thousands of images.", 'bfcuf' ),
				__( "In this case, it's better to use ID, Slug or Date structures.", 'bfcuf' )
		); 
		
		printf(
				'<div style="height:30px">&nbsp;<span id="alert-structure" class="hidden" style="font-style:italic;color:red">%s <b><code>%s</code></b></span></div>',
				__( 'Having "Organize by Month Year" enabled, will make the structure like: ', 'bfcuf' ),
				__( '/uploads/custom-folder/year-month/', 'bfcuf' )
		);
		
		?>
		<script type="text/javascript">
		jQuery(document).ready( function($) 
		{
			function show_hide()
			{
				var the_checkbox = $('#uploads_use_yearmonth_folders').attr('checked');
				var the_dropdown = $("#custom_upload_folders").val();
				
				if( the_checkbox && '' != the_dropdown )
				{
					$('#alert-structure').fadeIn();
				}
				else
				{
					$('#alert-structure').fadeOut();
				}
			}
			
			show_hide();
			
			$("#uploads_use_yearmonth_folders, #custom_upload_folders").change( show_hide );
		});
		</script>
		<?php
		
	}

	
	/**
	 * Add settings link to plugin action row
	 * 
	 * @param type $links
	 * @param type $file
	 * @return type
	 */
	public function settings_plugin_link( $links, $file ) 
	{
	    if ( $file == plugin_basename( dirname(__FILE__) . '/custom-upload-folders.php' ) ) 
	    {
	        $in = '<a href="options-media.php">' . __( 'Settings', 'bfcuf' ) . '</a>';
	        array_unshift( $links, $in );
	    }
	    return $links;
	} 


	/**
	 * Loads translation file.
	 *
	 * Accessible to other classes to load different language files (admin and
	 * front-end for example).
	 *
	 * @wp-hook init
	 * @param   string $domain
	 * @since   2012.09.11
	 * @return  void
	 */
	public function load_language( $domain )
	{
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain(
				$domain, WP_LANG_DIR . '/plugins/custom-upload-folders/' . $domain . '-' . $locale . '.mo'
		);
		
		load_plugin_textdomain(
				$domain, FALSE, $this->plugin_slug . '/languages'
		);
	}
}

