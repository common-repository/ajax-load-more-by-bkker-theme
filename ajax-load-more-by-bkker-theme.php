<?php
/**
 * Plugin Name: AJAX Load More By BKKER Theme
 * Plugin URI: http://bkkertheme.com/wordpress-plugins/
 * Description: Load the next page of posts with AJAX.
 * Version: 1.0.0
 * Author: BKKER Theme
 * Author URI: http://bkkertheme.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bkker
**/

class Ajax_Load_More_By_Bkker_Theme {
	private $bkker_get_option;

	public function __construct() {
		add_action('template_redirect', array( $this, 'ajax_load_more_by_bkker_theme_init' ) );
		add_action( 'admin_menu', array( $this, 'ajax_load_more_by_bkker_theme_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'ajax_load_more_by_bkker_theme_page_init' ) );
	}

	function ajax_load_more_by_bkker_theme_init() {
		$default_option = array( 
			'enable_ajax_load_more'	=> 'enable',
			'content_selector'		=> '#main',
			'post_class_selector'	=> '.post',
			'navigation_selector'	=> '.posts-navigation',
			'button_label'			=> 'Load More.',
			'loading_message'		=> 'Loading...',
			'finished_message'		=> 'No more posts available.'
		);

		$bkker_get_option = get_option( 'ajax_load_more_by_bkker_theme_option_name', $default_option );
		$bkker_get_option = wp_parse_args( $bkker_get_option , $default_option  );
		$enable_ajax_load_more = $bkker_get_option['enable_ajax_load_more'];

		global $wp_query;

		if( !is_singular() && $enable_ajax_load_more === 'enable' ) {
			wp_enqueue_style(
				'ajax-load-more-by-bkker-theme',
				plugin_dir_url( __FILE__ )  . 'css/ajax-load-more-by-bkker-theme.css',
				array(), 
				'1.0.0', 
				'all'
			);

			wp_enqueue_script(
				'ajax-load-more-by-bkker-theme',
				plugin_dir_url( __FILE__ )  . 'js/ajax-load-more-by-bkker-theme.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);

			$max_num_pages = $wp_query->max_num_pages;
			$page_next = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) + 1 : 2;
			$content_selector = $bkker_get_option['content_selector'];
			$post_class_selector = $bkker_get_option['post_class_selector'];
			$navigation_selector = $bkker_get_option['navigation_selector'];
			$button_label = $bkker_get_option['button_label'];
			$loading_message = $bkker_get_option['loading_message'];
			$finished_message = $bkker_get_option['finished_message'];

			wp_localize_script( 'ajax-load-more-by-bkker-theme', 'ajax_load_more', array(
				'pageLink'			=> get_pagenum_link( PHP_INT_MAX ),
				'pageMax'			=> $max_num_pages,
				'pageNext'			=> $page_next,
				'contentSelector'	=> $content_selector,
				'postClassSelector'	=> $post_class_selector,
				'navigationSelector'=> $navigation_selector,
				'buttonLabel'		=> $button_label,
				'loadingMessage'	=> $loading_message,
				'finishedMessage'	=> $finished_message
			) );
		}
	}

	public function ajax_load_more_by_bkker_theme_add_plugin_page() {
		add_menu_page(
			__( 'AJAX Load More By BKKER Theme', 'bkker' ),
			__( 'AJAX Load More', 'bkker' ),
			'manage_options',
			'ajax-load-more-by-bkker-theme',
			array( $this, 'ajax_load_more_by_bkker_theme_create_admin_page' ),
			'dashicons-schedule',
			100
		);
	}

	public function ajax_load_more_by_bkker_theme_create_admin_page() {
		$this->bkker_get_option = get_option( 'ajax_load_more_by_bkker_theme_option_name' ); ?>
		<div class="wrap ajax-load-more-by-bkker-theme">
			<h2><?php echo esc_html__( 'AJAX Load More By BKKER Theme', 'bkker' ) ?></h2>
			<?php settings_errors(); ?>
			<form method="post" action="options.php"> <?php
				settings_fields( 'ajax_load_more_by_bkker_theme_option_group' );
				do_settings_sections( 'ajax-load-more-by-bkker-theme-admin' );
				submit_button(); ?>
			</form>
		</div><?php 
	}

	public function ajax_load_more_by_bkker_theme_page_init() {
		register_setting(
			'ajax_load_more_by_bkker_theme_option_group',
			'ajax_load_more_by_bkker_theme_option_name',
			array( $this, 'ajax_load_more_by_bkker_theme_sanitize' )
		);

		add_settings_section(
			'ajax_load_more_by_bkker_theme_setting_section',
			'Plugin Settings',
			array( $this, 'ajax_load_more_by_bkker_theme_section_info' ),
			'ajax-load-more-by-bkker-theme-admin'
		);

		add_settings_field(
			'enable_ajax_load_more',
			'Enable Ajax Load More',
			array( $this, 'enable_ajax_load_more_callback' ),
			'ajax-load-more-by-bkker-theme-admin',
			'ajax_load_more_by_bkker_theme_setting_section'
		);

		add_settings_field(
			'content_selector',
			'Content Selector',
			array( $this, 'content_selector_callback' ),
			'ajax-load-more-by-bkker-theme-admin',
			'ajax_load_more_by_bkker_theme_setting_section'
		);

		add_settings_field(
			'post_class_selector',
			'Post Class Selector',
			array( $this, 'post_class_selector_callback' ),
			'ajax-load-more-by-bkker-theme-admin',
			'ajax_load_more_by_bkker_theme_setting_section'
		);

		add_settings_field(
			'navigation_selector',
			'Navigation Selector',
			array( $this, 'navigation_selector_callback' ),
			'ajax-load-more-by-bkker-theme-admin',
			'ajax_load_more_by_bkker_theme_setting_section'
		);

		add_settings_field(
			'button_label',
			'Button label',
			array( $this, 'button_label_callback' ),
			'ajax-load-more-by-bkker-theme-admin',
			'ajax_load_more_by_bkker_theme_setting_section'
		);

		add_settings_field(
			'loading_message',
			'Loading Message',
			array( $this, 'loading_message_callback' ),
			'ajax-load-more-by-bkker-theme-admin',
			'ajax_load_more_by_bkker_theme_setting_section'
		);

		add_settings_field(
			'finished_message',
			'Finished Message',
			array( $this, 'finished_message_callback' ),
			'ajax-load-more-by-bkker-theme-admin',
			'ajax_load_more_by_bkker_theme_setting_section'
		);
	}

	public function ajax_load_more_by_bkker_theme_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['enable_ajax_load_more'] ) ) {
			$sanitary_values['enable_ajax_load_more'] = $input['enable_ajax_load_more'];
		}

		if ( isset( $input['content_selector'] ) ) {
			$sanitary_values['content_selector'] = sanitize_text_field( $input['content_selector'] );
		}

		if ( isset( $input['post_class_selector'] ) ) {
			$sanitary_values['post_class_selector'] = sanitize_text_field( $input['post_class_selector'] );
		}

		if ( isset( $input['navigation_selector'] ) ) {
			$sanitary_values['navigation_selector'] = sanitize_text_field( $input['navigation_selector'] );
		}

		if ( isset( $input['button_label'] ) ) {
			$sanitary_values['button_label'] = sanitize_text_field( $input['button_label'] );
		}

		if ( isset( $input['loading_message'] ) ) {
			$sanitary_values['loading_message'] = sanitize_text_field( $input['loading_message'] );
		}

		if ( isset( $input['finished_message'] ) ) {
			$sanitary_values['finished_message'] = sanitize_text_field( $input['finished_message'] );
		}

		return $sanitary_values;
	}

	public function ajax_load_more_by_bkker_theme_section_info() { ?>
		<p><?php echo esc_html__( 'Version 1.0.0 by', 'bkker' ); ?> <a href="http://bkkertheme.com/" target="_blank">The BKKER Theme</a>.</p> <?php
	}

	public function enable_ajax_load_more_callback() { ?> 
		<fieldset>
			<?php $checked = ( isset( $this->bkker_get_option['enable_ajax_load_more'] ) && $this->bkker_get_option['enable_ajax_load_more'] === 'enable' ) ? 'checked' : 'checked' ; ?>
			<label for="enable_ajax_load_more-0">
				<input type="radio" name="ajax_load_more_by_bkker_theme_option_name[enable_ajax_load_more]" id="enable_ajax_load_more-0" value="enable" <?php echo $checked; ?>> Enable
				</label><br>
			<?php $checked = ( isset( $this->bkker_get_option['enable_ajax_load_more'] ) && $this->bkker_get_option['enable_ajax_load_more'] === 'disable' ) ? 'checked' : '' ; ?>
			<label for="enable_ajax_load_more-1">
				<input type="radio" name="ajax_load_more_by_bkker_theme_option_name[enable_ajax_load_more]" id="enable_ajax_load_more-1" value="disable" <?php echo $checked; ?>> Disable
			</label>
		</fieldset> <?php
	}

	public function content_selector_callback() {
		printf(
			'<input class="regular-text" type="text" name="ajax_load_more_by_bkker_theme_option_name[content_selector]" id="content_selector" value="%s">',
			isset( $this->bkker_get_option['content_selector'] ) ? esc_attr( $this->bkker_get_option['content_selector']) : '#main'
		);
	}

	public function post_class_selector_callback() {
		printf(
			'<input class="regular-text" type="text" name="ajax_load_more_by_bkker_theme_option_name[post_class_selector]" id="post_class_selector" value="%s">',
			isset( $this->bkker_get_option['post_class_selector'] ) ? esc_attr( $this->bkker_get_option['post_class_selector']) : '.post'
		);
	}

	public function navigation_selector_callback() {
		printf(
			'<input class="regular-text" type="text" name="ajax_load_more_by_bkker_theme_option_name[navigation_selector]" id="navigation_selector" value="%s">',
			isset( $this->bkker_get_option['navigation_selector'] ) ? esc_attr( $this->bkker_get_option['navigation_selector']) : '.posts-navigation'
		);
	}

	public function button_label_callback() {
		printf(
			'<input class="regular-text" type="text" name="ajax_load_more_by_bkker_theme_option_name[button_label]" id="button_label" value="%s">',
			isset( $this->bkker_get_option['button_label'] ) ? esc_attr( $this->bkker_get_option['button_label']) : esc_html__( 'Load More.', 'bkker' )
		);
	}

	public function loading_message_callback() {
		printf(
			'<input class="regular-text" type="text" name="ajax_load_more_by_bkker_theme_option_name[loading_message]" id="loading_message" value="%s">',
			isset( $this->bkker_get_option['loading_message'] ) ? esc_attr( $this->bkker_get_option['loading_message']) : esc_html__( 'Loading...', 'bkker' )
		);
	}

	public function finished_message_callback() {
		printf(
			'<input class="regular-text" type="text" name="ajax_load_more_by_bkker_theme_option_name[finished_message]" id="finished_message" value="%s">',
			isset( $this->bkker_get_option['finished_message'] ) ? esc_attr( $this->bkker_get_option['finished_message']) : esc_html__( 'No more posts available.', 'bkker' )
		);
	}

}

new Ajax_Load_More_By_Bkker_Theme();
