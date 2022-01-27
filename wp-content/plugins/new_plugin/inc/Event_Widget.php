<?php

class Event_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'event_widget',
			'description' => 'Виджет событий',
		);
		parent::__construct( 'event_widget', 'Event Widget', $widget_ops );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance[ 'title' ] );
		$event_title = get_bloginfo( 'name' );
		$date = get_bloginfo( 'description' );

		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; ?>
		<p><strong>Event_Name:</strong> <?php echo $event_title ?></p>
		<p><strong>Event_Date:</strong> <?php echo $date ?></p>
		<?php echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'event_date' ); ?>"><?php _e( 'Date of events to show'); ?></label>
<select id="<?php echo $this->get_field_id( 'event_date' ); ?>" name="<?php echo $this->get_field_name( 'event_date' ); ?>" class="widefat">
	<?php for ( $i = 1; $i <= 10; $i++ ): ?>
		<option value="<?php echo $i; ?>" <?php selected( $i, $instance['event_date'], true ); ?>><?php echo $i; ?></option>
	<?php endfor; ?>
		</p><?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		$instance['date'] = strip_tags($new_instance['date']);
		return $instance;
	}
}

function event_register_widget() {
	register_widget( 'Event_Widget' );
}
add_action( 'widgets_init', 'Event_Widget' );

//add_action( 'widgets_init', function(){
//	register_widget( 'Event_Widget' );
//});
