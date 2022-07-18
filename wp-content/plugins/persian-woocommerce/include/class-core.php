<?php

defined( 'ABSPATH' ) || exit;

class Persian_Woocommerce_Core {

	protected $options;

	// sub classes
	public $tools, $translate, $address, $gateways;

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {

		$this->activated_plugin();

		$this->options = get_option( 'PW_Options' );

		//add_filter( 'woocommerce_show_addons_page', '__return_false', 100 );
		add_action( 'admin_menu', [ $this, 'admin_menus' ], 59 );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 10 );
	}

	public function plugins_loaded() {
		require_once( 'class-gateways.php' );
	}

	public function admin_menus() {

		add_menu_page( 'ووکامرس فارسی', 'ووکامرس فارسی', 'manage_options', 'persian-wc', [
			$this->translate,
			'translate_page',
		], $this->plugin_url( 'assets/images/logo.png' ), '55.6' );

		add_submenu_page( 'persian-wc', 'حلقه های ترجمه', 'حلقه های ترجمه', 'manage_options', 'persian-wc', [
			$this->translate,
			'translate_page',
		] );

		add_submenu_page( 'persian-wc', 'ابزار ها', 'ابزار ها', 'manage_options', 'persian-wc-tools', [
			$this->tools,
			'tools_page',
		] );

		do_action( "PW_Menu" );

		add_submenu_page( 'persian-wc', 'افزونه ها', 'افزونه ها', 'manage_woocommerce', 'persian-wc-plugins', [
			$this,
			'plugins_page',
		] );

		add_submenu_page( 'persian-wc', 'پوسته ها', 'پوسته ها', 'manage_woocommerce', 'persian-wc-themes', [
			$this,
			'themes_page',
		] );

		add_submenu_page( 'persian-wc', 'پیشخوان پست تاپین', 'پیشخوان پست تاپین', 'manage_woocommerce', 'https://yun.ir/pwtm' );

		add_submenu_page( 'woocommerce', 'افزونه های پارسی', 'افزونه های پارسی', 'manage_woocommerce', 'wc-persian-plugins', [
			$this,
			'plugins_page',
		] );

		add_submenu_page( 'woocommerce', 'پوسته های پارسی', 'پوسته های پارسی', 'manage_woocommerce', 'wc-persian-themes', [
			$this,
			'themes_page',
		] );

		add_submenu_page( 'persian-wc', 'درباره ما', 'درباره ما', 'manage_options', 'persian-wc-about', [
			$this,
			'about_page',
		] );
	}

	public function admin_head() {
		?>
		<script type="text/javascript">
            jQuery(document).ready(function ($) {
                $("ul#adminmenu a[href$='https://yun.ir/pwtm']").attr('target', '_blank');
            });
		</script>
		<?php
	}

	public function themes_page() {
		wp_enqueue_style( 'woocommerce_admin_styles' );
		include( 'view/html-admin-page-themes.php' );
	}

	public function plugins_page() {
		wp_enqueue_style( 'woocommerce_admin_styles' );
		include( 'view/html-admin-page-plugins.php' );
	}

	public function about_page() {
		include( 'view/html-admin-page-about.php' );
	}

	public function activated_plugin() {
		global $wpdb;

		if ( ! file_exists( PW_DIR . '/.activated' ) ) {
			return false;
		}

		$woocommerce_ir_sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}woocommerce_ir` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`text1` text CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
			`text2` text CHARACTER SET utf8 COLLATE utf8_persian_ci,
			PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $woocommerce_ir_sql );

		//delete deprecated tables-----------------------------
		$deprecated_tables = [
			'woocommerce_ir_cities',
			'Woo_Iran_Cities_By_HANNANStd',
		];

		foreach ( $deprecated_tables as $deprecated_table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$deprecated_table}" );
		}

		//delete deprecated Options-----------------------------
		$deprecated_options = [
			'is_cities_installed',
			'pw_delete_city_table_2_5',
			'woocommerce_persian_feed',
			'redirect_to_woo_persian_about_page',
			'enable_woocommerce_notice_dismissed',
			'Persian_Woocommerce_rename_old_table',
		];

		foreach ( $deprecated_options as $deprecated_option ) {
			delete_option( $deprecated_option );
		}

		for ( $i = 0; $i < 10; $i ++ ) {
			delete_option( 'persian_woo_notice_number_' . $i );
		}

		unlink( PW_DIR . '/.activated' );

		if ( ! headers_sent() ) {
			wp_redirect( admin_url( 'admin.php?page=persian-wc-about' ) );
			die();
		}
	}

	public function enqueue_scripts() {
		$pages = [
			'persian-wc-about',
			'persian-wc-plugins',
			'wc-persian-plugins',
			'persian-wc-themes',
			'wc-persian-themes',
		];

		$sanitize_text_field = sanitize_text_field( $_GET['page'] ?? null );

		if ( in_array( $sanitize_text_field, $pages ) ) {
			wp_register_style( 'pw-admin-fonts', $this->plugin_url( 'assets/css/admin.font.css' ) );
			wp_enqueue_style( 'pw-admin-fonts' );
		}
	}

	public function plugin_url( $path = null ) {
		return untrailingslashit( plugins_url( is_null( $path ) ? '/' : $path, PW_FILE ) );
	}

	public function get_options( $option_name = null, $default = false ) {

		if ( is_null( $option_name ) ) {
			return $this->options;
		}

		$default_options = [];

		if ( ! empty( $this->tools ) && method_exists( $this->tools, 'get_tools_default' ) ) {
			$default_options = $this->tools->get_tools_default();
		}

		if ( isset( $this->options[ $option_name ] ) ) {
			return $this->options[ $option_name ];
		} elseif ( isset( $default_options["PW_Options[$option_name]"] ) ) {
			return $default_options["PW_Options[$option_name]"];
		} else {
			return $default;
		}
	}

}

if ( ! class_exists( 'Persian_Woocommerce_Plugin' ) ) {
	class Persian_Woocommerce_Plugin extends Persian_Woocommerce_Core {

	}
}
