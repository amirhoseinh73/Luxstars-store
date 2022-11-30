<?php 

/**
 * Welcome Page Class
 *
 * Displays on plugin activation or updation
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * orddd_ts_welcome Class
 *
 * A general class for About page.
 *
 * @since 7.7
 */

class orddd_ts_welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';
	
	/**
	 * @var string The name of the plugin
	 * @access public
	 */
	public static $plugin_name = "";

	/**
	 * @var string Unique prefix of the plugin
	 * @access public 
	 */

	public static $plugin_prefix = '';

	/**
	 * @var Plugin Context
	 * @access public
	 */

	public static $plugin_context = '';

	/**
	 * @var string Folder of the plugin
	 * @access public
	 */
	public static $plugin_folder = '';

	/**
	 * @var string Plugin live version
	 * @access public
	 */

	 public static $plugin_version = '';

	 /**
	  * @var string Plugin previous version
	  * @access public
	  */
	public static $previous_plugin_version = '';
	/**
	 * @var string Plugin Url
	 * @access public
	 */
	public static $plugin_url = '';
	/**
	 * @var string Template base path
	 * @access public
	 */
	public static $template_base = '';
	/**
	 * @var string Plugin dir name with plugin file name
	 * @access public
	 */
	public static $plugin_file_path = '';

	/**
	 * @var tring Page Header text
	 * @access public
	 */
	public static $ts_welcome_header_text = '';

	/**
	 * @var string Plugin License Key Name in Options table
	 * @access public
	 */
	public static $plugin_license_key = '';

	/**
	 * @var string Plugin License Status name in Options table
	 * @access public
	 */
	public static $plugin_license_status = '';

	/**
	 * Get things started
	 *
	 * @since 7.7
	 */
	public function __construct( $ts_plugin_name = '', $ts_plugin_prefix = '', $ts_plugin_context = '', $ts_plugin_folder_name = '', $ts_plugin_dir_name = '' , $ts_previous_version = '', $welcome_header_text = '', $ts_license_key_name = '', $ts_license_status_name = ''  ) {
		self::$plugin_name    	= $ts_plugin_name;
		self::$plugin_prefix  	= $ts_plugin_prefix;
		self::$plugin_context 	= $ts_plugin_context;
		self::$plugin_folder    = $ts_plugin_folder_name;
		self::$plugin_file_path = $ts_plugin_dir_name;
		self::$ts_welcome_header_text = $welcome_header_text;
		self::$plugin_license_key     = $ts_license_key_name;
		self::$plugin_license_status  = $ts_license_status_name;

		//Update plugin
		add_action( 'admin_init', array( &$this, 'ts_update_db_check' ) );

		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );

		if ( !isset( $_GET[ 'page' ] ) || 
		( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] != self::$plugin_prefix . '-pro-about' ) ) {
			add_action( 'admin_init', array( $this, 'ts_pro_welcome' ) );
		}

		self::$plugin_version 		   = $this->ts_get_version();
		
		self::$previous_plugin_version = $ts_previous_version;
		self::$plugin_url     		   = $this->ts_get_plugin_url();
		self::$template_base  		   = $this->ts_get_template_path();
		self::$ts_welcome_header_text  = sprintf( esc_html__( 'Welcome to %s %s', self::$plugin_context ), self::$plugin_name, self::$plugin_version );
	}

	/**
     * This function returns the plugin version number.
     *
     * @access public 
     * @since 7.7
     * @return $plugin_version
     */
    public function ts_get_version() {
        $plugin_version = '';
        
		$plugin_data = get_file_data( self::$plugin_file_path, array( 'Version' => 'Version' ) );
        if ( ! empty( $plugin_data['Version'] ) ) {
            $plugin_version = $plugin_data[ 'Version' ];
        }
        return $plugin_version;;
    }

    /**
     * This function returns the plugin url 
     *
     * @access public 
     * @since 7.7
     * @return string
     */
    public function ts_get_plugin_url() {
        return plugins_url() . '/' . self::$plugin_folder . '/';
    }

    /**
    * This function returns the template directory path
    *
    * @access public 
    * @since 7.7
    * @return string
    */
    public function ts_get_template_path() {
		
    	return untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/';
    } 

	/**
	 * Register the Dashboard Page which is later hidden but this pages
	 * is used to render the Welcome page.
	 *
	 * @access public
	 * @since  7.7
	 * @return void
	 */
	public function admin_menus() {
		$display_version = self::$plugin_version;

		// About Page
		add_dashboard_page(
			sprintf( self::$ts_welcome_header_text ),
			esc_html__( 'Welcome to ' . self::$plugin_name, self::$plugin_context ),
			$this->minimum_capability,
			self::$plugin_prefix . '-pro-about',
			array( $this, 'about_screen' )
		);

	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since  7.7
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', self::$plugin_prefix . '-pro-about' );
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since  7.7
	 * @return void
	 */
	public function about_screen() {
		$gmt = false;
		if( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );
		
		$display_version = self::$plugin_version;
		$ts_file_path    = plugin_dir_url( __FILE__ ) ; 
		// Badge for welcome page
		$badge_url = $ts_file_path . '/assets/images/icon-256x256.png';		
		
		$license_key_name = self::$plugin_license_key;
		$license_status_name = self::$plugin_license_status;
		
		$existing_license = get_option( $license_key_name ) ? get_option( $license_key_name ) : '';
		$license_status = get_option( $license_status_name ) ? get_option( $license_status_name ) : '';
		
		$plugin_name = self::$plugin_name;
		$plugin_context = self::$plugin_context;
		$plugin_prefix = self::$plugin_prefix;
		
		$site_name = "<a href='https://www.tychesoftwares.com/' target='_blank'>Tyche Softwares</a>";
		$purchase_history = "<a href='https://www.tychesoftwares.com/checkout/purchase-history' target='_blank'>Account->Purchase History</a>";
		
		$display_failed = false;

		ob_start();

		$accept = false;
		if( isset( $_POST[ $plugin_prefix . '_license_display' ] ) &&  $_POST[ $plugin_prefix . '_license_display' ] == '2' ) { // the license activation failed the first time round
		
		    $insert = false;
		    $license_key = '';
		    // check if a license key is entered
		    if( isset( $_POST[ 'license_key' ] ) && '' != $_POST[ 'license_key' ] ) {
		        $license_key = $_POST[ 'license_key' ];
		        update_option( $license_key_name, $license_key );
		        orddd_license::orddd_edd_sample_activate_license(); // call the respective plugin's license activation function
		    }
		
		    $license_details = array( 'license_key' => $license_key );
		
		    if( isset( $_POST[ $plugin_prefix . '_accept_terms' ] ) && '1' == $_POST[ $plugin_prefix . '_accept_terms' ] ) {
		        $license_details[ $plugin_prefix . '_accept_terms' ] = '1';
		        $accept = true;
		        $insert = true;
		    }
		
		    if( get_option( $license_status_name ) == 'valid' ) {
		        $license_details[ 'is_valid' ] = true;
		        $license_status = get_option( $license_status_name );
		        $insert = true;
		    }
		
		    // if accept terms is enabled or the license was valid, save and move on to the welcome page
		    if( $insert ) {
		        add_option( $plugin_prefix . '_installation_wizard_license_key', json_encode( $license_details ) );
		    }
		
		} else if( isset( $_POST[ $plugin_prefix . '_license_display' ] ) && $_POST[ $plugin_prefix . '_license_display' ] == '1' ) { // only for first time
		
		    update_option( 'edd_sample_license_key_odd_woo', $_POST[ 'license_key' ] );
		   	orddd_license::orddd_edd_sample_activate_license(); // call the respective plugin's license activation function
		    
		    if( get_option( $license_status_name ) == 'valid' ) { // license key validation was successful
		        $license_status = get_option( $license_status_name );
		        add_option( $plugin_prefix . '_installation_wizard_license_key', json_encode( array( 'license_key' => $_POST[ 'license_key' ], 'is_valid' => true ) ) );
		    } else { // license key validation failed
		        $display_failed = true;
		
		       // load scripts on the page
		        wp_enqueue_style( 'orddd-font-awesome',  esc_url( plugins_url() . '/order-delivery-date/css/font-awesome.css' ) );
		        wp_enqueue_style( 'orddd-font-awesome-min', esc_url( plugins_url() . '/order-delivery-date/css/font-awesome.min.css' ) );
		
		        // display the template that allows them to proceed without the license key
		        wc_get_template( 'welcome/license-activation-failed.php', array(
			        'plugin_name'         => $plugin_name,
			        'plugin_context'      => $plugin_context,
			        'get_welcome_header'  => $this->get_welcome_header(),
			        'site_name'           => $site_name,
			        'purchase_history'    => $purchase_history,
			        'plugin_prefix'       => $plugin_prefix,
		        ), self::$plugin_folder, self::$template_base );
		    }
		}
		
		if( isset( $license_status ) && $license_status != 'valid' && $accept == false && ! $display_failed ) {
		    wc_get_template( 'welcome/license-activation.php', array(
			    'plugin_name'         => $plugin_name,
			    'plugin_context'      => $plugin_context,
			    'get_welcome_header'  => $this->get_welcome_header(),
			    'site_name'           => $site_name,
			    'purchase_history'    => $purchase_history,
			    'plugin_prefix'       => $plugin_prefix,
		    ), self::$plugin_folder, self::$template_base );
		} else if( ! $display_failed ) {
			wc_get_template( 'welcome/welcome-page.php', array(
	        	'plugin_name'        => self::$plugin_name,
	        	'plugin_url'         => self::$plugin_url, 
	            'display_version'    => $display_version,
				'badge_url'          => $badge_url,
				'ts_dir_image_path'  => $ts_file_path . '/assets/images/',
				'plugin_context'     => self::$plugin_context,
	            'get_welcome_header' => $this->get_welcome_header()
	        ),  self::$plugin_folder, self::$template_base );
		}
        
        echo ob_get_clean();

		add_option( self::$plugin_prefix . '_pro_welcome_page_shown', 'yes' );
		add_option( self::$plugin_prefix . '_pro_welcome_page_shown_time', $current_time );
	}

	/**
	 * The header section for the welcome screen.
	 *
	 * @since 7.7
	 */
	public function get_welcome_header() {
		// Badge for welcome page
		$ts_file_path    = plugin_dir_url( __FILE__ ) ;
		
		// Badge for welcome page
		$badge_url = $ts_file_path . '/assets/images/icon-256x256.png';
		?>
        <h1 class="welcome-h1"><?php echo get_admin_page_title(); ?></h1>
		<?php $this->social_media_elements();
	}

	/**
	 * Social Media Like Buttons
	 *
	 * Various social media elements to Tyche Softwares
	 */
	public function social_media_elements() { 
		ob_start();
		wc_get_template( '/social-media-elements.php', 
						 array(), 
						 self::$plugin_folder, 
						 self::$template_base );
        echo ob_get_clean();
	}
	/**
	 * Sends user to the Welcome page on first activation of the plugin as well as each
	 * time the plugin is updated is upgraded to a new version
	 *
	 * @access public
	 * @since  7.7
	 *
	 * @return void
	 */
	public function ts_pro_welcome() {

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET[ 'activate-multi' ] ) ) {
			return;
		}

		if ( ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == "do-plugin-upgrade" ) ) {
			return;
		}

		if( !get_option( self::$plugin_prefix . '_pro_welcome_page_shown' ) ) {
			wp_safe_redirect( admin_url( 'index.php?page=' . self::$plugin_prefix . '-pro-about' ) );
			exit;
		}
	}

	/**
	 *  Executed when the plugin is updated using the Automatic Updater. 
	 */
	public function ts_update_db_check() {
		if( self::$previous_plugin_version > self::$plugin_version ) {
		   return;
		}

		if ( ( false === self::$plugin_version || false === self::$previous_plugin_version ) &&
		     'yes' == get_option( self::$plugin_prefix . '_pro_welcome_page_shown' )
		) {
		   return;
		}

		if ( '' == self::$plugin_version || '' == self::$previous_plugin_version ) {
		   return;
		}

		if ( self::$plugin_version != self::$previous_plugin_version ) {
		   delete_option( self::$plugin_prefix . '_pro_welcome_page_shown' );
		   delete_option( self::$plugin_prefix . '_pro_welcome_page_shown_time' );
		}
	}
}