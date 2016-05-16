<?php
/**
 * Plugin widgets.
 *
 * @package Really_Simple_Image_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Really_Simple_Image_Widget.
 *
 * @since 1.0.0
 */
class Really_Simple_Image_Widget extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// Widget options.
		$opts = array(
			'classname'                   => 'really_simple_image_widget',
			'description'                 => __( 'Easiest way to add image in your sidebar', 'really-simple-image-widget' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'really-simple-image-widget', __( 'Really Simple Image Widget', 'really-simple-image-widget' ), $opts );

	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Display arguments.
	 * @param array $instance Settings for the current widget instance.
	 */
	function widget( $args, $instance ) {

		$title                        = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$rsiw_image_url               = ! empty( $instance['rsiw_image_url'] ) ? $instance['rsiw_image_url'] : '';
		$rsiw_image_width             = ! empty( $instance['rsiw_image_width'] ) ? $instance['rsiw_image_width'] : '';
		$rsiw_image_height            = ! empty( $instance['rsiw_image_height'] ) ? $instance['rsiw_image_height'] : '';
		$rsiw_link                    = ! empty( $instance['rsiw_link'] ) ? $instance['rsiw_link'] : '';
		$rsiw_alt_text                = ! empty( $instance['rsiw_alt_text'] ) ? $instance['rsiw_alt_text'] : '';
		$rsiw_open_link               = ! empty( $instance['rsiw_open_link'] ) ? $instance['rsiw_open_link'] : false;
		$rsiw_image_caption           = ! empty( $instance['rsiw_image_caption'] ) ? $instance['rsiw_image_caption'] : '';
		$rsiw_disable_link_in_title   = ! empty( $instance['rsiw_disable_link_in_title'] ) ? $instance['rsiw_disable_link_in_title'] : false;
		$rsiw_disable_link_in_caption = ! empty( $instance['rsiw_disable_link_in_caption'] ) ? $instance['rsiw_disable_link_in_caption'] : false;

		$instance['link_open']  = '';
		$instance['link_close'] = '';
		if ( ! empty( $rsiw_link ) ) {
			$target                 = ( empty( $rsiw_open_link ) ) ? '' : ' target="_blank" ';
			$instance['link_open']  = '<a href="' . esc_url( $rsiw_link ) . '"' . $target . '>';
			$instance['link_close'] = '</a>';
		}

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'];
			if ( $rsiw_disable_link_in_title ) {
				echo sprintf( '%s', $title );
			} else {
				echo sprintf( '%s%s%s',
					$instance['link_open'],
					$title,
					$instance['link_close']
				);
			}
			echo $args['after_title'];
		}

		if ( ! empty( $rsiw_image_url ) ) {

			$sizes = array();

			$alt_text = ( ! empty( $rsiw_alt_text ) ) ? $rsiw_alt_text : basename( $rsiw_image_url );
			$dimension_text = '';
			if ( ! empty( $rsiw_image_width ) ) {
				$dimension_text .= ' width="' . esc_attr( $rsiw_image_width ) . '" ';
			}
			if ( ! empty( $rsiw_image_height ) ) {
				$dimension_text .= ' height="' . esc_attr( $rsiw_image_height ) . '" ';
			}

			$imgtag = '<img src="' . esc_url( $rsiw_image_url ) . '" alt="' . esc_attr( $alt_text ) . '" ' . $dimension_text . ' />';

			echo '<div class="image-wrapper">';
			echo sprintf( '<div class="rsiw-image" %s>%s%s%s</div>',
				' style="max-width:100%;"',
				$instance['link_open'],
				$imgtag,
				$instance['link_close']
			);
			if ( ! empty( $rsiw_image_caption ) ) {
				if ( $rsiw_disable_link_in_caption ) {
					echo sprintf( '<div class="rsiw-image-caption">%s</div>',
						wp_kses_post( $rsiw_image_caption )
					);
				} else {
					echo sprintf( '<div class="rsiw-image-caption">%s%s%s</div>',
						$instance['link_open'],
						wp_kses_post( $rsiw_image_caption ),
						$instance['link_close']
					);
				}
			}
			echo '</div>';

		} // End if : image is there.

		echo $args['after_widget'];

	}

	/**
	 * Handles updating settings for the current widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user.
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']                        = sanitize_text_field( $new_instance['title'] );
		$instance['rsiw_image_url']               = esc_url_raw( $new_instance['rsiw_image_url'] );
		$instance['rsiw_image_width']             = esc_attr( $new_instance['rsiw_image_width'] );
		$instance['rsiw_image_height']            = esc_attr( $new_instance['rsiw_image_height'] );
		$instance['rsiw_link']                    = esc_url_raw( $new_instance['rsiw_link'] );
		$instance['rsiw_alt_text']                = sanitize_text_field( $new_instance['rsiw_alt_text'] );
		$instance['rsiw_open_link']               = isset( $new_instance['rsiw_open_link'] );
		$instance['rsiw_disable_link_in_title']   = isset( $new_instance['rsiw_disable_link_in_title'] );
		$instance['rsiw_disable_link_in_caption'] = isset( $new_instance['rsiw_disable_link_in_caption'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['rsiw_image_caption'] = $new_instance['rsiw_image_caption'];
		} else {
			$instance['rsiw_image_caption'] = wp_kses_post( $new_instance['rsiw_image_caption'] );
		}

		return $instance;

	}

	/**
	 * Outputs the widget settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 */
	function form( $instance ) {

		// Defaults.
		$instance = wp_parse_args( (array) $instance, array(
			'title'                        => '',
			'rsiw_image_url'               => '',
			'rsiw_image_width'             => '',
			'rsiw_image_height'            => '',
			'rsiw_link'                    => '',
			'rsiw_alt_text'                => '',
			'rsiw_open_link'               => 0,
			'rsiw_image_caption'           => '',
			'rsiw_disable_link_in_title'   => 0,
			'rsiw_disable_link_in_caption' => 0,
			) );
		?>
	    <p>
	        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'really-simple-image-widget' ); ?>:</label>
	        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	    </p>

	    <div>
		      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_url' ) ); ?>"><?php _e( 'Image URL', 'really-simple-image-widget' ); ?></label>:<br />
		      <input type="text" class="img widefat" name="<?php echo esc_attr( $this->get_field_name( 'rsiw_image_url' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_url' ) ); ?>" value="<?php echo esc_url( $instance['rsiw_image_url'] ); ?>" /><br />
		      <input type="button" class="select-img button button-primary" value="<?php _e( 'Upload', 'really-simple-image-widget' ); ?>" data-uploader_title="<?php _e( 'Select Image', 'really-simple-image-widget' ); ?>" data-uploader_button_text="<?php _e( 'Choose Image', 'really-simple-image-widget' ); ?>" style="margin-top:5px;" />

				<?php
		        $full_image_url = '';
		        if ( ! empty( $instance['rsiw_image_url'] ) ) {
					$full_image_url = $instance['rsiw_image_url'];
		        }
		        $wrap_style = '';
		        if ( empty( $full_image_url ) ) {
					$wrap_style = ' style="display:none;" ';
		        }
				?>
		      <div class="rsiw-preview-wrap" <?php echo $wrap_style; ?>>
		        <img src="<?php echo esc_url( $full_image_url ); ?>" alt="<?php _e( 'Preview', 'really-simple-image-widget' ); ?>" style="max-width: 100%;"  />
		      </div><!-- .rsiw-preview-wrap -->

	    </div>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_width' ) ); ?>"><?php _e( 'Image Width', 'really-simple-image-widget' ); ?>:</label>
	        <input id="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_width' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'rsiw_image_width' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['rsiw_image_width'] ); ?>" style="max-width:60px;"/>&nbsp;<em class="small"><?php _e( 'in pixel', 'really-simple-image-widget' ); ?></em>
	    </p>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_height' ) ); ?>"><?php _e( 'Image Height', 'really-simple-image-widget' ); ?>:</label>
	        <input id="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_height' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'rsiw_image_height' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['rsiw_image_height'] ); ?>" style="max-width:60px;"/>&nbsp;<em class="small"><?php _e( 'in pixel', 'really-simple-image-widget' ); ?></em>
	    </p>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_alt_text' ) ); ?>"><?php _e( 'Alt Text', 'really-simple-image-widget' ); ?>:</label>
	        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rsiw_alt_text' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'rsiw_alt_text' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['rsiw_alt_text'] ); ?>" />
	    </p>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_link' ) ); ?>"><?php _e( 'Link', 'really-simple-image-widget' ); ?>:</label>
	        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rsiw_link' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'rsiw_link' ) ); ?>" type="text" value="<?php echo esc_url( $instance['rsiw_link'] ); ?>" />
	    </p>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_open_link' ) ); ?>"><?php _e( 'Open in New Window', 'really-simple-image-widget' ); ?>:</label>
	      <input id="<?php echo esc_attr( $this->get_field_id( 'rsiw_open_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rsiw_open_link' ) ); ?>" type="checkbox" <?php checked( isset( $instance['rsiw_open_link'] ) ? $instance['rsiw_open_link'] : 0 ); ?> />
	    </p>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_disable_link_in_title' ) ); ?>"><?php _e( 'Disable Link in Title', 'really-simple-image-widget' ); ?>:</label>
	      <input id="<?php echo esc_attr( $this->get_field_id( 'rsiw_disable_link_in_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rsiw_disable_link_in_title' ) ); ?>" type="checkbox" <?php checked( isset( $instance['rsiw_disable_link_in_title'] ) ? $instance['rsiw_disable_link_in_title'] : 0 ); ?> />
	    </p>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_disable_link_in_caption' ) ); ?>"><?php _e( 'Disable Link in Caption', 'really-simple-image-widget' ); ?>:</label>
	      <input id="<?php echo esc_attr( $this->get_field_id( 'rsiw_disable_link_in_caption' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rsiw_disable_link_in_caption' ) ); ?>" type="checkbox" <?php checked( isset( $instance['rsiw_disable_link_in_caption'] ) ? $instance['rsiw_disable_link_in_caption'] : 0 ); ?> />
	    </p>

	    <p>
	      <label for="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_caption' ) ); ?>"><?php _e( 'Caption', 'really-simple-image-widget' ); ?>:</label>
	      <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'rsiw_image_caption' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rsiw_image_caption' ) ); ?>"><?php echo esc_textarea( $instance['rsiw_image_caption'] ); ?></textarea>
	    </p>
		<?php
	}
}
