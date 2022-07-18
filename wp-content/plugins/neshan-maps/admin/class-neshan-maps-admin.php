<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://platform.neshan.org
 * @since      1.0.0
 *
 * @package    Neshan_Maps
 * @subpackage Neshan_Maps/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Neshan_Maps
 * @subpackage Neshan_Maps/admin
 * @author     Neshan Platform <platform@neshan.org>
 */
class Neshan_Maps_Admin {

	/**
	 * Create page hook.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $create_page_hook Create page hook.
	 */
	private $create_page_hook;

	/**
	 * My maps page hook.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $list_page_hook My maps page hook.
	 */
	private $list_page_hook;

	/**
	 * Help page hook.
	 *
	 * @since    1.1.1
	 * @access   private
	 * @var      string $list_page_hook Help page hook.
	 */
	private $help_page_hook;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Is wordpress loaded in a RTL language mode.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $is_rtl Is current Wordpress language RTL?
	 */
	private $is_rtl;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$lang = get_locale();

		if ( $lang === 'fa_IR' ) {
			$this->is_rtl = true;
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param $hook
	 *
	 * @since    1.0.0
	 *
	 */
	public function enqueue_styles( $hook ) {
		if ( $hook === $this->create_page_hook ) {
			$dir = $this->is_rtl ? '-rtl' : '';

			wp_enqueue_style( 'neshan-web-sdk', 'https://static.neshan.org/api/web/v1/openlayers/v4.6.5.css', [],
				$this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '_bootstrap',
				plugin_dir_url( __FILE__ ) . "css/bootstrap{$dir}.min.css", [], $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '_maker', plugin_dir_url( __FILE__ ) . 'css/MapMaker.css', [],
				$this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/neshan-maps-admin.css', [],
				$this->version, 'all' );
		} elseif ( $hook === $this->list_page_hook ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/my-maps.css', [], $this->version,
				'all' );
		} elseif ( $hook === $this->help_page_hook ) {
			/**
			 * @since    1.1.1
			 */
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/help.css', [], $this->version,
				'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param $hook
	 *
	 * @since    1.0.0
	 *
	 */
	public function enqueue_scripts( $hook ) {
		if ( $hook === $this->create_page_hook ) {
			wp_enqueue_script( 'neshan-polyfill-check',
				'https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL',
				[], null, true );

			wp_enqueue_script( 'neshan-web-sdk', 'https://static.neshan.org/api/web/v1/openlayers/v4.6.5.js',
				[ 'neshan-polyfill-check' ], $this->version . '111', true );

			wp_enqueue_script( $this->plugin_name . '_bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js',
				[ 'jquery' ], $this->version, true );

			wp_enqueue_script( $this->plugin_name . '_maker', plugin_dir_url( __FILE__ ) . 'js/MapMaker.js',
				[ 'jquery', $this->plugin_name . '_bootstrap' ], $this->version, true );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/neshan-maps-admin.js',
				[ $this->plugin_name . '_maker' ], $this->version, true );

			$scripts = json_encode( [
				'translate' => [
					'api_key_help' => __( 'Please fill Api Key field by the appropriate key (WebSite) you received from Neshan Developers Panel',
						'neshan-maps' ),
				]
			] );

			wp_add_inline_script( $this->plugin_name, "var neshan_options = {$scripts};", 'before' );
		} elseif ( $hook === $this->list_page_hook ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/my-maps.js', [ 'jquery' ],
				$this->version, true );

			$scripts = json_encode( [
				'translate' => [
					'delete_confirm_message' => __( 'Are you sure about deleting this map?', 'neshan-maps' )
				]
			] );

			wp_add_inline_script( $this->plugin_name, "var neshan_options = {$scripts};", 'before' );
		}
	}

	/**
	 * Register admin menu.
	 *
	 * @since    1.0.0
	 *
	 */
	public function menu() {
		add_menu_page( __( 'Neshan Maps', 'neshan-maps' ), __( 'Neshan Map', 'neshan-maps' ), 'manage_options',
			'neshan_maps', [ $this, 'page_my_maps' ], 'dashicons-location-alt', null );

		$this->list_page_hook = add_submenu_page( 'neshan_maps', __( 'My Maps', 'neshan-maps' ),
			__( 'My Maps', 'neshan-maps' ), 'manage_options', 'neshan_maps', [ $this, 'page_my_maps' ] );

		$this->create_page_hook = add_submenu_page( 'neshan_maps', __( 'Create New Map', 'neshan-maps' ),
			__( 'Create New Map', 'neshan-maps' ), 'manage_options', 'neshan_maps_create', [ $this, 'page_create' ] );

		/**
		 * @since    1.1.1
		 */
		$this->help_page_hook = add_submenu_page( 'neshan_maps', __( 'Neshan Maps Help', 'neshan-maps' ),
			__( 'Usage Help', 'neshan-maps' ), 'manage_options', 'neshan_maps_help', [ $this, 'page_help' ] );
	}

	/**
	 * Save standard map form via ajax.
	 *
	 * @since    1.0.0
	 *
	 */
	public function save() {
		global $wpdb;

		$id = null;

		if ( isset( $_POST['id'] ) ) {
			$id = sanitize_text_field( $_POST["id"] );
			check_ajax_referer( 'update_neshan_map_' . $id, 'token' );
		} else {
			check_ajax_referer( 'create_neshan_map', 'token' );
		}

		$date = current_time( 'mysql', false );

		$title   = sanitize_text_field( $_POST["title"] );
		$api_key = sanitize_text_field( $_POST["key"] );
		$lat     = sanitize_text_field( $_POST["lat"] );
		$lng     = sanitize_text_field( $_POST["lng"] );
		$width   = sanitize_text_field( $_POST["width"] );
		$height  = sanitize_text_field( $_POST["height"] );
		$zoom    = sanitize_text_field( $_POST["zoom"] );
		$maptype = sanitize_text_field( $_POST["maptype"] );

		$data = [
			"api_key" => "{$api_key}",
			"lat"     => "{$lat}",
			"lng"     => "{$lng}",
			"width"   => "{$width}",
			"height"  => "{$height}",
			"zoom"    => "{$zoom}",
			"maptype" => "{$maptype}",
		];

		if ( $id ) {
			$result = $wpdb->update( 'neshan_maps', [
				'title'      => $title,
				'updated_at' => $date,
				'data'       => json_encode( $data )
			], [ 'id' => $id ], [ '%s', '%s', '%s' ], [ '%d' ] );
		} else {
			$result = $wpdb->insert( 'neshan_maps', [
				'title'      => $title,
				'created_at' => $date,
				'updated_at' => $date,
				'data'       => json_encode( $data )
			], [ '%s', '%s', '%s' ] );
		}

		wp_send_json( [
			'id' => $result !== false ? ( $id ? $id : $wpdb->insert_id ) : 0
		] );
	}

	/**
	 * Delete map via ajax.
	 *
	 * @since    1.0.0
	 *
	 */
	public function delete() {
		global $wpdb;

		$id = sanitize_text_field( $_POST["id"] );

		check_ajax_referer( 'delete_neshan_map_' . $id, 'token' );

		$result = $wpdb->delete( 'neshan_maps', [ 'id' => $id ], [ '%d' ] );

		wp_send_json( [
			'status' => $result ? 'OK' : 'ERROR',
		] );
	}

	/**
	 * Manage admin notices
	 *
	 * @since    1.1.1
	 *
	 */
	public function notices() {
		if ( isset( $_GET['page'] ) ) {
			if ( $_GET['page'] === 'neshan_maps' ) {
				if ( get_locale() === 'fa_IR' ) {
					include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/my-maps-help-fa.php';
				}
			}
		}
	}

	/**
	 * Show help page
	 *
	 * @since    1.1.1
	 *
	 */
	public function page_help() {
		if ( get_locale() === 'fa_IR' ) {
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-fa.php';
		} else {
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-en.php';
		}
	}

	/**
	 * Show My Maps page.
	 *
	 * @since    1.0.0
	 *
	 */
	public function page_my_maps() {
		global $wpdb;

		$maps = $wpdb->get_results( "SELECT * FROM neshan_maps ORDER BY updated_at DESC" );

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/my-maps.php';
	}

	/**
	 * Show Create New Map or Edit Map page.
	 *
	 * @since    1.0.0
	 *
	 */
	public function page_create() {
		$current_map = null;

		if ( isset( $_GET["id"] ) && isset( $_GET["action"] ) && $_GET["action"] === 'edit' ) {
			global $wpdb;

			$id = sanitize_text_field( $_GET["id"] );

			$maps = $wpdb->get_results( "SELECT * FROM neshan_maps WHERE id = {$id}" );

			if ( count( $maps ) === 1 ) {
				$current_map       = $maps[0];
				$current_map->data = json_decode( $current_map->data );

				$current_map->style = '';

				if ( strpos( $current_map->data->width, '%' ) !== false ) {
					$current_map->style = 'width: ' . $current_map->data->width . ';';
				}
				if ( strpos( $current_map->data->height, '%' ) !== false ) {
					$current_map->style = 'height: ' . $current_map->data->height . ';';
				}
			}
		}

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/create-simple-map.php';
	}

}
