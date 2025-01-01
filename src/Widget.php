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
	 * Echoes the widget content.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		echo do_shortcode( '[dsubscribers type="widget"]' );

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 */
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {

			$title = $instance['title'];

		} else {

			$title = __( 'Subscribe', 'dsubscribers' );

		} ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
					type="text"
					value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>

		<?php
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}
