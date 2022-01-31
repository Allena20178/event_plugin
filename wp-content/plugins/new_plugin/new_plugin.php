<?php
/*
 * Plugin name: Новый плагин
 * Description: Описание супер-плагина
 * Author: Alena
  */


define( 'ROOT', plugins_url( '', __FILE__ ) );
define( 'IMAGES', ROOT . '/img/' );
define( 'STYLES', ROOT . '/css/' );
define( 'SCRIPTS', ROOT . '/js/' );



function ewp_custom_post_type() {
	$labels = array(
		'name' =>	__( 'Events', 'ewp' ),
		'singular_name' =>	__( 'Event', 'ewp' ),
		'add_new_item' =>	__( 'Add New Event', 'ewp' ),
		'all_items' =>	__( 'All Events', 'ewp' ),
		'edit_item' =>	__( 'Edit Event', 'ewp' ),
		'new_item' =>	__( 'New Event', 'ewp' ),
		'view_item' =>	__( 'View Event', 'ewp' ),
		'not_found' =>	__( 'No Events Found', 'ewp' ),
		'not_found_in_trash' =>	__( 'No Events Found in Trash', 'ewp' )
	);

	$supports = array(
		'title',
		'editor',
		'excerpt'
	);

	$args = array(
		'label' =>	__( 'Events', 'ewp' ),
		'labels' =>	$labels,
		'description' =>	__( 'A list of events', 'ewp' ),
		'public' =>	true,
		'show_in_menu' =>	true,
		'menu_icon' =>	IMAGES . 'event.svg',
		'has_archive' =>	true,
		'rewrite' =>	true,
		'supports' =>	$supports
	);

	register_post_type( 'event', $args );
}
add_action( 'init', 'ewp_custom_post_type' );


function ewp_activation_deactivation() {
	ewp_custom_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ewp_activation_deactivation' );


function ewp_add_event_info_metabox() {
	add_meta_box(
		'ewp-event-info-metabox',
		__( 'Event Info', 'ewp' ),
		'ewp_render_event_info_metabox',
		'event',
		'side',
		'core'
	);
}
add_action( 'add_meta_boxes', 'ewp_add_event_info_metabox' );



function ewp_render_event_info_metabox( $post ) {
	
	wp_nonce_field( basename( __FILE__ ), 'ewp-event-info-nonce' );

	
	$event_start_date = get_post_meta( $post->ID, 'event-start-date', true );
	$event_end_date = get_post_meta( $post->ID, 'event-end-date', true );
	$event_venue = get_post_meta( $post->ID, 'event-venue', true );

	
	$event_start_date = ! empty( $event_start_date ) ? $event_start_date : time();

	
	$event_end_date = ! empty( $event_end_date ) ? $event_end_date : $event_start_date;

	?>
	<p> 
		<label for="ewp-event-start-date"><?php _e( 'Event Start Date:', 'ewp' ); ?></label>
		<input type="text" id="ewp-event-start-date" name="ewp-event-start-date" class="widefat ewp-event-date-input" value="<?php echo date( 'F d, Y', $event_start_date ); ?>" placeholder="Format: February 18, 2021">
	</p>
	<p>
		<label for="ewp-event-end-date"><?php _e( 'Event End Date:', 'ewp' ); ?></label>
		<input type="text" id="ewp-event-end-date" name="ewp-event-end-date" class="widefat ewp-event-date-input" value="<?php echo date( 'F d, Y', $event_end_date ); ?>" placeholder="Format: February 18, 2021">
	</p>
	<p>
		<label for="ewp-event-venue"><?php _e( 'Event Venue:', 'ewp' ); ?></label>
		<input type="text" id="ewp-event-venue" name="ewp-event-venue" class="widefat" value="<?php echo $event_venue; ?>" placeholder="eg. Times Square">
	</p>
	<?php
}


function ewp_admin_script_style( $hook ) {
	global $post_type;

	if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && ( 'event' == $post_type ) ) {
		wp_enqueue_script(
			'event_widget',
			SCRIPTS . 'script.js',
			array( 'jquery', 'jquery-ui-datepicker' ),
			'1.0',
			true
		);

		wp_enqueue_style(
			'jquery-ui-calendar',
			STYLES . 'jquery-ui-1.10.4.custom.min.css',
			false,
			'1.10.4',
			'all'
		);
	}
}
add_action( 'admin_enqueue_scripts', 'ewp_admin_script_style' );


function ewp_widget_style() {
	if ( is_active_widget( '', '', 'ewp_upcoming_events', true ) ) {
		wp_enqueue_style(
			'event_widget',
			STYLES . 'event_widget.css',
			false,
			'1.0',
			'all'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ewp_widget_style' );


function ewp_save_event_info( $post_id ) {
	
	if ( 'event' != $_POST['post_type'] ) {
		return;
	}

	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = isset( $_POST['ewp-event-info-nonce'] ) && ( wp_verify_nonce( $_POST['ewp-event-info-nonce'], basename( __FILE__ ) ) );

	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}


	if ( isset( $_POST['ewp-event-start-date'] ) ) {
		update_post_meta( $post_id, 'event-start-date', strtotime( $_POST['ewp-event-start-date'] ) );
	}

	if ( isset( $_POST['ewp-event-end-date'] ) ) {
		update_post_meta( $post_id, 'event-end-date', strtotime( $_POST['ewp-event-end-date'] ) );
	}

	if ( isset( $_POST['ewp-event-venue'] ) ) {
		update_post_meta( $post_id, 'event-venue', sanitize_text_field( $_POST['ewp-event-venue'] ) );
	}
}
add_action( 'save_post', 'ewp_save_event_info' );


function ewp_custom_columns_head( $defaults ) {
	unset( $defaults['date'] );

	$defaults['event_start_date'] = __( 'Start Date', 'ewp' );
	$defaults['event_end_date'] = __( 'End Date', 'ewp' );
	$defaults['event_venue'] = __( 'Venue', 'ewp' );

	return $defaults;
}
add_filter( 'manage_edit-event_columns', 'ewp_custom_columns_head', 10 );


function ewp_custom_columns_content( $column_name, $post_id ) {
	if ( 'event_start_date' == $column_name ) {
		$start_date = get_post_meta( $post_id, 'event-start-date', true );
		echo date( 'F d, Y', $start_date );
	}

	if ( 'event_end_date' == $column_name ) {
		$end_date = get_post_meta( $post_id, 'event-end-date', true );
		echo date( 'F d, Y', $end_date );
	}

	if ( 'event_venue' == $column_name ) {
		$venue = get_post_meta( $post_id, 'event-venue', true );
		echo $venue;
	}
}
add_action( 'manage_event_posts_custom_column', 'ewp_custom_columns_content', 10, 2 );

//add_shortcode('event', 'event_plugin');
//
//function event_plugin($atts){
//    $atts = '';
//
//
//}

include 'inc/Event_Widget.php';
