<?php
/*
Plugin Name: Meerkat
Plugin URI: https://wordpress.org/plugins/meerkat/
Description: Embed a Meerkat video on your site
Version: 1.1
Author: David Artiss
Author URI: http://www.artiss.co.uk
*/

define( 'meerkat_version', '1.1' );

/**
* Plugin initialisation
*
* Loads the plugin's translated strings
*
* @since	1.1
*/

function meerkat_plugin_init() {

	$language_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages/';

	load_plugin_textdomain( 'meerkat', false, $language_dir );

}

add_action( 'init', 'meerkat_plugin_init' );

/**
* Add meta to plugin details
*
* Add options to plugin meta line
*
* @since	1.0
*
* @param	string  $links	Current links
* @param	string  $file	File in use
* @return   	string		Links, now with settings added
*/

function meerkat_set_plugin_meta( $links, $file ) {

	if ( strpos( $file, 'meerkat.php' ) !== false ) {
		$links = array_merge( $links, array( '<a href="http://wordpress.org/support/plugin/meerkat">' . __( 'Support', 'meerkat' ) . '</a>' ) );
		$links = array_merge( $links, array( '<a href="http://www.artiss.co.uk/donate">' . __( 'Donate', 'meerkat' ) . '</a>' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'meerkat_set_plugin_meta', 10, 2 );

/**
* Widgets
*
* Create and display widgets
*
* @since	1.1
*/

class meerkat_widget extends WP_Widget {

	/**
	* Widget Constructor
	*
	* Call WP_Widget class to define widget
	*
	* @since	1.1
	*
	* @uses		WP_Widget				Standard WP_Widget class
	*/

    //function meerkat_widget() {

    //    parent::WP_Widget( false, $name = 'Meerkat' );

    //}

	function __construct() {

		parent::__construct( 'meerkat_widget',
							__( 'Meerkat', 'meerkat' ),
							array( 'description' => __( 'Meerkat Video Widget.', 'meerkat' ), 'class' => 'my-widget-class' )
							);
	}

	/**
	* Display widget
	*
	* Display the Meerkat widget
	*
	* @since	1.1
	*
	* @uses		generate_meerkat_code		Generate the required Meerkat video code
	*
	* @param	string		$args			Arguments
	* @param	string		$instance		Instance
	*/

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		// Output the header
		echo $before_widget;

		// Extract title for heading
		$title = $instance[ 'titles' ];

		// Output title, if one exists
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }

		// Convert tick boxes to true/false
		if ( $instance[ 'participation' ] == '1' ) { $participation = 'true'; } else { $participation = 'false'; }
		if ( $instance[ 'muted' ] == '1' ) { $muted = 'true'; } else { $muted = 'false'; }
		if ( $instance[ 'debug' ] == '1' ) { $debug = 'true'; } else { $debug = 'false'; }

		// Generate the video and output it

		echo generate_meerkat_code( $muted,
									$participation,
									'',
									$instance[ 'type' ],
									$instance[ 'username' ],
									$debug );

		// Output the trailer
		echo $after_widget;
	}

	/**
	* Widget update/save function
	*
	* Update and save widget
	*
	* @since	1.1
	*
	* @param	string		$new_instance		New instance
	* @param	string		$old_instance		Old instance
	* @return	string							Instance
	*/

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance[ 'titles' ] = strip_tags( $new_instance[ 'titles' ] );
		$instance[ 'username' ] = $new_instance[ 'username' ];
		$instance[ 'type' ] = $new_instance[ 'type' ];
		$instance[ 'participation' ] = $new_instance[ 'participation' ];
		$instance[ 'muted' ] = $new_instance[ 'muted' ];
		$instance[ 'debug' ] = $new_instance[ 'debug' ];

		return $instance;
	}

	/**
	* Widget Admin control form
	*
	* Define admin file
	*
	* @since	1.1
	*
	* @param	string		$instance		Instance
	*/

	function form( $instance ) {

		// Set default options

		$default = array( 'titles' => 'Meerkat', 'username' => '', 'type' => 'portrait', 'participation' => '1', 'muted' => '', 'debug' => '1' );
		$instance = wp_parse_args( ( array ) $instance, $default );

		// Widget Title field

		$field_id = $this -> get_field_id( 'titles' );
		$field_name = $this -> get_field_name( 'titles' );
		echo "\r\n" . '<p><label for="' . $field_id . '">' . __( 'Widget Title', 'meerkat' ) . ': </label><input type="text" class="widefat" id="' . $field_id . '" name="' . $field_name . '" value="' . esc_attr( $instance[ 'titles' ] ).'" /></p>';

		// User name field

		$field_id = $this -> get_field_id( 'username' );
		$field_name = $this -> get_field_name( 'username' );
		echo "\r\n" . '<p><label for="' . $field_id . '">' . __( 'User name', 'meerkat' ) . ': </label><input type="text" class="widefat" id="' . $field_id . '" name="' . $field_name . '" value="' . esc_attr( $instance[ 'username' ] ) . '" /></p>';

		// Type

		$field_id = $this -> get_field_id( 'type' );
		$field_name = $this -> get_field_name( 'type' );
		echo "\r\n" . '<p><label for="' . $field_id . '">' . __( 'Type', 'meerkat' ) . ': </label><select name="' . $field_name . '" id="' . $field_id . '"><option value="portrait"';
		if ( esc_attr( $instance[ 'type' ] ) == 'portrait' ) { echo " selected='selected'"; }
		echo '>' . __( 'Portrait', 'meerkat' ) . '</option><option value="square"';
		if ( esc_attr( $instance[ 'type' ] ) == 'square' ) { echo " selected='selected'"; }
		echo '>' . __( 'Square', 'meerkat' ) . '</option><option value="bigsquare"';
		if ( esc_attr( $instance[ 'type' ] ) == 'bigsquare' ) { echo " selected='selected'"; }
		echo '>' . __( 'Big Square', 'meerkat' ) . '</option></select></p>';

		// Participation

		$field_id = $this -> get_field_id( 'participation' );
		$field_name = $this -> get_field_name( 'participation' );
		echo "\r\n" . '<p><input class="checkbox" type="checkbox"  id="' . $field_id . '" name="' . $field_name . '" value="1"';
		if ( esc_attr( $instance[ 'participation' ] ) == '1' ) { echo " checked='checked'"; }
		echo ' /><label for="' . $field_id . '">' . __( 'Participation', 'meerkat' ) . '</label></p>';

		// Muted

		$field_id = $this -> get_field_id( 'muted' );
		$field_name = $this -> get_field_name( 'muted' );
		echo "\r\n" . '<p><input class="checkbox" type="checkbox"  id="' . $field_id . '" name="' . $field_name . '" value="1"';
		if ( esc_attr( $instance[ 'muted' ] ) == '1' ) { echo " checked='checked'"; }
		echo ' /><label for="' . $field_id . '">' . __( 'Muted', 'meerkat' ) . '</label></p>';

		// Debug

		$field_id = $this -> get_field_id( 'debug' );
		$field_name = $this -> get_field_name( 'debug' );
		echo "\r\n" . '<p><input class="checkbox" type="checkbox"  id="' . $field_id . '" name="' . $field_name . '" value="1"';
		if ( esc_attr( $instance[ 'debug' ] ) == '1' ) { echo " checked='checked'"; }
		echo ' /><label for="' . $field_id . '">' . __( 'Debug', 'meerkat' ) . '</label></p>';

	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget( "meerkat_widget" );' ) );

/**
* Meerkat shortcode
*
* Shortcode function to output Meerkat video
*
* @since	1.0
*
* @param	string	$paras		Shortcode parameters
* @param	string	$content	Content between shortcode start and stop - ignored!
* @return	string				Output code
*/

function meerkat_shortcode( $paras = '', $content = '' ) {

	extract( shortcode_atts( array( 'username' => '', 'type' => 'portrait', 'participation' => 'true', 'cover' => '', 'muted' => 'false', 'debug' => 'true' ), $paras ) );

	// Check for any missing information

	$error = false;
	if ( $username == '' ) { $error = 'No username specified'; }
	if ( $type != 'portrait' && $type != 'square' && $type != 'bigsquare' ) { $error = 'The \'type\' should only be \'portrait\', \'square\' or \'bigsquare\''; }

	// If an error occurred output that

	if ( $error !== false ) {

		$content = '<p style="color: #f00">' . __( 'Meerkat embedding error', 'meerkat' ) . ': ' . $error . '</p>';

	} else {

		// If no errors, output the appropriate Meerkat script

		$content = generate_meerkat_code( $muted, $participation, $cover, $type, $username, $debug );

	}

    return $content;
}

add_shortcode( 'meerkat', 'meerkat_shortcode' );

/**
* Generate Meerkat code
*
* Generated the Meerkat video code based upon passed parameters
*
* @since	1.1
*
* @param	string	$muted			Muted audio
* @param	string	$participation	Include comments, likes, etc.
* @param	string	$cover			Cover image URL
* @param	string	$type			Video type
* @param	string	$username		Video user name (required)
* @param	string	$debug			Whether to show debug output or not
* @return	string					Output code
*/

function generate_meerkat_code( $muted, $participation, $cover, $type, $username, $debug ) {

	$newline = "\n";
	$code = $newline;

	if ( $debug == 'true' ) { $code .= '<!-- Meerkat v' . meerkat_version . ' -->' . $newline; }
	$code .= '<script class="meerkat" id="meerkat" name="meerkat" data-mute="' . $muted . '" data-social="' . $participation . '" class="mkEmbedPlayer" data-cover="' . $cover . '" data-type="' . $type . '" data-username="' . $username . '" src="http://meerkatapp.co/widgets/embed.js" type="text/javascript"></script>' . $newline;
	if ( $debug == 'true' ) { $code .= '<!-- End of Meerkat code -->' . $newline; }

	return $code;
}
?>