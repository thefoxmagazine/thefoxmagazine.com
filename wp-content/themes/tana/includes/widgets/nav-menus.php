<?php
class Themeton_Nav_Menu_Widget extends WP_Widget {


	public function __construct() {
		$widget_ops = array(
			'description' => esc_html__( 'Add a custom menu to your sidebar.' , 'tana'),
			'customize_selective_refresh' => true,
		);
		parent::__construct( false, esc_html__(': Menu for Push Sidebar', 'tana'), $widget_ops );
	}

	

	public function widget( $args, $instance ) {
		// Get menu
		$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;
		$menu_style = ! empty( $instance['menu_style'] ) ? $instance['menu_style'] : 'big';
		$menu_style = $menu_style=='big' ? 'big-menu' : $menu_style;
		$menu_style = $menu_style=='medium' ? 'medium-menu' : $menu_style;
		$menu_style = $menu_style=='small' ? 'small-menu' : $menu_style;

		if ( !$nav_menu ){
			return;
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		print $args['before_widget'];

		if ( !empty($instance['title']) ){
			print $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		$nav_menu_args = array(
			'fallback_cb' => '',
			'menu'        => $nav_menu,
			'container_class' => $menu_style,
			'container' => 'nav'
		);

		wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance ) );

		print $args['after_widget'];
	}



	public function update( $new_instance, $old_instance ) {
		$instance = array();
		if ( ! empty( $new_instance['title'] ) ) {
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
		}
		if ( ! empty( $new_instance['nav_menu'] ) ) {
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		}
		if ( ! empty( $new_instance['menu_style'] ) ) {
			$instance['menu_style'] = sanitize_text_field( $new_instance['menu_style'] );
		}
		return $instance;
	}



	public function form( $instance ) {
		global $wp_customize;
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		$menu_style = isset( $instance['menu_style'] ) ? $instance['menu_style'] : 'big';

		// Get menus
		$menus = wp_get_nav_menus();

		// If no menus exists, direct the user to go and create some.
		?>
		<p class="nav-menu-widget-no-menus-message" <?php if ( ! empty( $menus ) ) { print ' style="display:none" '; } ?>>
			<?php
			if ( $wp_customize instanceof WP_Customize_Manager ) {
				$url = 'javascript: wp.customize.panel( "nav_menus" ).focus();';
			} else {
				$url = admin_url('nav-menus.php');
			}
			?>
			<?php printf('%s <a href="%s">%s</a>', esc_html__('No menus have been created yet.', 'tana'), esc_attr($url), esc_html__('Create some', 'tana')); ?>
		</p>
		<div class="nav-menu-widget-form-controls" <?php if ( empty( $menus ) ) { print ' style="display:none" '; } ?>>
			<p>
				<label for="<?php print $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'tana' ) ?></label>
				<input type="text" class="widefat" id="<?php print $this->get_field_id( 'title' ); ?>" name="<?php print $this->get_field_name( 'title' ); ?>" value="<?php print esc_attr( $title ); ?>"/>
			</p>
			<p>
				<label for="<?php print $this->get_field_id( 'nav_menu' ); ?>"><?php esc_html_e( 'Select Menu:', 'tana' ); ?></label>
				<select id="<?php print $this->get_field_id( 'nav_menu' ); ?>" name="<?php print $this->get_field_name( 'nav_menu' ); ?>">
					<option value="0"><?php esc_html_e( '&mdash; Select &mdash;', 'tana' ); ?></option>
					<?php foreach ( $menus as $menu ) : ?>
						<option value="<?php print esc_attr( $menu->term_id ); ?>" <?php selected( $nav_menu, $menu->term_id ); ?>>
							<?php print esc_html( $menu->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php print $this->get_field_id( 'menu_style' ); ?>"><?php esc_html_e('Menu output style:', 'tana'); ?></label>
				<select id="<?php print $this->get_field_id( 'menu_style' ); ?>" name="<?php print $this->get_field_name( 'menu_style' ); ?>">
					<option value="small" <?php print esc_attr($menu_style=='small' ? 'selected' : ''); ?>><?php esc_html_e('Small Menu', 'tana'); ?></option>
					<option value="medium" <?php print esc_attr($menu_style=='medium' ? 'selected' : ''); ?>><?php esc_html_e('Medium Menu + Icon', 'tana'); ?></option>
					<option value="big" <?php print esc_attr($menu_style=='big' ? 'selected' : ''); ?>><?php esc_html_e('Big Menu', 'tana'); ?></option>
				</select>
			</p>
			<?php if ( $wp_customize instanceof WP_Customize_Manager ) : ?>
				<p class="edit-selected-nav-menu" style="<?php if ( ! $nav_menu ) { print 'display: none;'; } ?>">
					<button type="button" class="button"><?php esc_html_e('Edit Menu', 'tana') ?></button>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}
}


add_action('widgets_init', create_function('', 'return register_widget("Themeton_Nav_Menu_Widget");'));
