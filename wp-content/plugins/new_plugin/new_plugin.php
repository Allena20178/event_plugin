<?php

/*
 * Plugin name: Новый плагин
 * Description: Описание супер-плагина
 * Author: Alena
  */

function event_post_type() {

	$labels = array(
			'name' => 'event',
			'start_day'=> 'event-start-date',
			'status' => 'open_closed_status',
		);
	$args = array(
		'public' => true,
	);
	register_post_type( 'events', $args );
}
add_action( 'init', 'event_post_type' );


function create_taxonomies() {
	$args = array(
		'labels' => array(
			'menu_name' => 'Событие'
		),
	);
	register_taxonomy( 'event', 'events', $args);
}
add_action( 'init', 'create_taxonomies');


include(‘inc/widget-events.php’);