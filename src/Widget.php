<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

use WP_Widget;

class Widget extends WP_Widget {

	/**
	 * Widget constructor.
	 */
	public function __construct() {

		parent::__construct(
			'dsubscribers_widget',
			__( 'DSubscribers', 'dsubscribers' ),
			array( 'description' => __( 'DSubscribers', 'dsubscribers' ) )
		);
	}

	/**
	 * @param array $args Widget arguments.
	 * @param $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo do_shortcode( '[dsubscribers type="widget"]' );

		echo $args['after_widget'];
	}

	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {

			$title = $instance['title'];

		} else {

			$title = __( 'Subscribe', 'dsubscribers' );

		} ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<?php
	}


	public function update( $new_instance, $old_instance ) {

		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}
