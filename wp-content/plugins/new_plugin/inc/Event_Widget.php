<?php

/**
 * Class Event_Widget
 */
class Event_Widget extends WP_Widget {

	
	public function __construct() {
		$widget_ops = array(
			'class'			=>	'widget_events',
			'description'	=>	__( 'A widget to display a list of events', 'ewp' )
		);

		parent::__construct(
			'widget_events',			//base id
			__( 'Event Widget', 'ewp' ),	//title
			$widget_ops
		);
	}


	
	public function form( $instance ) {
		$widget_defaults = array(
			'title'			=>	'Event Widget',
			'number_events'	=>	5
		);

		$instance  = wp_parse_args( (array) $instance, $widget_defaults );
		?>
		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'ewp' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number_events' ); ?>"><?php _e( 'Number of events to show', 'ewp' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'number_events' ); ?>" name="<?php echo $this->get_field_name( 'number_events' ); ?>" class="widefat">
				<?php for ( $i = 1; $i <= 10; $i++ ): ?>
					<option value="<?php echo $i; ?>" <?php selected( $i, $instance['number_events'], true ); ?>><?php echo $i; ?></option>
				<?php endfor; ?>
			</select>
		</p>

		<?php
	}


	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];
		$instance['number_events'] = $new_instance['number_events'];

		return $instance;
	}


	public function widget( $args, $instance ) {

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		
		$meta_quer_args = array(
			'relation'	=>	'AND',
			array(
				'key'		=>	'event-end-date',
				'value'		=>	time(),
				'compare'	=>	'>='
			)
		);

		$query_args = array(
			'post_type'				=>	'event',
			'posts_per_page'		=>	$instance['number_events'],
			'post_status'			=>	'publish',
			'ignore_sticky_posts'	=>	true,
			'meta_key'				=>	'event-start-date',
			'orderby'				=>	'meta_value_num',
			'order'					=>	'ASC',
			'meta_query'			=>	$meta_quer_args
		);

		$widget_events = new WP_Query( $query_args );

		
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		?>
		
		<ul class="ewp_event_entries">
			<?php
            while( $widget_events->have_posts() ): $widget_events->the_post();
				$event_start_date = get_post_meta( get_the_ID(), 'event-start-date', true );
				$event_end_date = get_post_meta( get_the_ID(), 'event-end-date', true );
				$event_venue = get_post_meta( get_the_ID(), 'event-venue', true );

			?>
					<h2><a href="<?php the_permalink(); ?>" class="ewp_event_title"><?php the_title(); ?></a> <span class="event_venue">at <?php echo $event_venue; ?></span></h2>
					<?php the_excerpt(); ?>
					<time class="ewp_event_date"><?php echo date( 'F d, Y', $event_start_date ); ?> &ndash; <?php echo date( 'F d, Y', $event_end_date ); ?></time>
			<?php endwhile; ?>
		</ul>
<?php
//        if ( have_posts() ) :


		wp_reset_query();

		echo $after_widget;

	}
}

function ewp_register_widget() {
	register_widget( 'Event_Widget' );
}
add_action( 'widgets_init', 'ewp_register_widget' );
