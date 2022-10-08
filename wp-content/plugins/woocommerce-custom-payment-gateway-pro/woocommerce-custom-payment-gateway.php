<?php
/* @wordpress-plugin
 * Plugin Name:       WooCommerce Custom Payment Gateway Pro
 * Plugin URI:        https://wpruby.com/plugin/woocommerce-custom-payment-gateway-pro/
 * Description:       Make your own custom payment gateway.
 * Version:           2.0.4
 * WC requires at least: 3.0
 * WC tested up to: 3.7
 * Author:            WPRuby
 * Author URI:        https://wpruby.com
 * Text Domain:       woocommerce-custom-payment-gateway
 * Domain Path: /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

class Custom_Payment_Pro{

	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;
	/**
	 * @return Custom_Payment_Pro
	 */
	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * Custom_Payment_Pro constructor.
	 */
	public function __construct() {
	    if(!$this->is_woocommerce_active()){
	        return;
        }

	    $this->load_dependencies();
	    $this->handle_license_and_updates();

		spl_autoload_register(array($this, 'load_fields'));

		add_filter('woocommerce_payment_gateways', array($this,  'add_custom_payment_gateway'));
		add_action( 'plugins_loaded', array($this,  'activate_old_license') );
		add_action( 'plugins_loaded', array($this, 'load_plugin_textdomain') );
		add_action('plugins_loaded', array($this, 'init_custom_payment_gateway'));
		add_action( 'admin_init', array($this, 'admin_css'));
		add_action( 'admin_enqueue_scripts', array($this, 'admin_js'));
		add_action( 'wp_enqueue_scripts', array($this, 'frontend_css') );
		add_action( 'add_meta_boxes_shop_order', array($this, 'woocommerce_payment_information'), 1, 2 );
		add_action('woocommerce_thankyou', array($this, 'add_customer_note_to_thank_you_page'));
		add_action('woocommerce_email_order_details', array($this, 'add_payment_information_to_emails'), 10, 2);



	}



	public function add_payment_information_to_emails($order, $sent_to_admin){
		if(get_option('show_payment_data_in_email') === 'yes'){
			/** @var WC_Order $order */
			$order_id = $order->get_id();
			$customer_note =	get_post_meta((int)$order_id, 'woocommerce_customized_customer_note', true);
			$data = get_post_meta($order_id, 'woocommerce_customized_payment_data', true);
			if($data){ ?>
                <h2><?php _e('Submitted Payment Information', 'woocommerce-custom-payment-gateway'); ?>:</h2>
                <table>
                    <tbody>
                    <?php if(isset($data['data'])):
	                    $this->display_data($data);
                    else:
	                    $this->display_legacy_data($data);
                    endif; ?>
                    </tbody>
                </table>
			<?php	}
			if($customer_note){  echo '<br>'.$customer_note; }
		}
	}

	public function add_customer_note_to_thank_you_page($order_id){
		$customer_note =	get_post_meta((int)$order_id, 'woocommerce_customized_customer_note', true);
		if($customer_note){
			echo '<p>'	. $customer_note . '</p>';
		}
	}

	public function admin_js($hook){
		// if( 'woocommerce_page_wc-settings' === $hook){
			wp_enqueue_script( 'custompayment', plugins_url( "includes/assets/js/custompayment.js", __FILE__ ) , array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-accordion' ), '1.3.5', true );
		// }
	}

	public function frontend_css() {
		if(is_checkout()){
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('custom_payment_front_js',plugins_url('includes/assets/js/custom-payment-front.js', __FILE__), array('jquery-ui-datepicker') );
			wp_enqueue_script('signature_pad',plugins_url('includes/assets/js/signature_pad.min.js', __FILE__) );
			wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/flick/jquery-ui.css');
			wp_enqueue_style( 'custom_payment_front_css', plugins_url('includes/assets/css/front.css', __FILE__) );
			wp_enqueue_style( 'hint-css', plugins_url('includes/assets/css/hint.min.css', __FILE__) );
		}
	}


    public function init_custom_payment_gateway(){
		require_once 'class-woocommerce-custom-payment-gateway.php';
		require_once plugin_dir_path(__FILE__). 'includes/CustomPaymentUpgrades.php';
		require_once plugin_dir_path(__FILE__). 'includes/gateway-classes-generator.php';
		require_once plugin_dir_path(__FILE__). 'includes/fields/class-custom-payment-field.php';
		require_once plugin_dir_path(__FILE__). 'includes/fields/class-signature-field.php';
	}

    public function admin_css() {
		wp_enqueue_style( 'custom_payment_admin_css', plugins_url('includes/assets/css/admin.css', __FILE__) );
	}
    public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-custom-payment-gateway', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	public function add_custom_payment_gateway( $gateways ){
		$gateways['wpruby_wc_custom'] = 'WC_Custom_Payment_Gateway';
		$stored_gateways = json_decode(get_option('wpruby_generated_custom_gatwayes'));

		if($stored_gateways){
			foreach($stored_gateways as $gateway){
				$gateway->name =  'custom_' . md5($gateway->name);
				$gateways[ $gateway->name ] =  $gateway->name;
			}
		}
		return $gateways;
	}

	public function activate_old_license(){
		if ( get_option( 'custom_payment_103_version' ) != 'upgraded' ) {
			// 1.0.3 activate the license for old customers
			if(get_option('wc_custompayment_license_key') != false){
				add_option('wc_custompayment_license_key_license_status', 'valid') or update_option('wc_custompayment_license_key_license_status', 'valid');
			}
			add_option( "custom_payment_103_version", 'upgraded' );
		}
	}

	private function is_woocommerce_active(){
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() )
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
    }

	public function woocommerce_payment_information( $post ) {
		$order_id = $post->ID;
		if(isset($_GET['delete_payment']) && $_GET['delete_payment'] == 'true'){
			delete_post_meta($order_id, 'woocommerce_customized_payment_data');
		}
		$data = get_post_meta($order_id, 'woocommerce_customized_payment_data', true);
		if($data){
			add_meta_box(
				'woocommerce_customized_payment_gateway',
				__( 'Payment Information' , 'woocommerce-custom-payment-gateway'),
				array($this, 'render_woocommerce_payment_information_metabox'),
				'shop_order',
				'normal',
				'high'
			);
		}
	}

	public function render_woocommerce_payment_information_metabox( $post ){
		$order_id = $post->ID;
		$data = get_post_meta($order_id, 'woocommerce_customized_payment_data', true);
		if($data){ ?>
            <h2>Order #<?php echo $order_id; ?> <?php _e('Submitted Payment Information', 'woocommerce-custom-payment-gateway'); ?>.</h2>
            <table class="wp-list-table widefat fixed striped posts">
                <tbody>
		            <?php if(isset($data['data'])):
                        $this->display_data($data);
                     else:
                        $this->display_legacy_data($data);
                   endif; ?>
                </tbody>
            </table><br>

            <a style="color:#a00;" onclick="if(!confirm('Are you sure that you want to delete payment information?')){return false;}" href="<?php echo admin_url('post.php?post='. $order_id .'&action=edit&delete_payment=true') ?>">Delete Information</a>

		<?php	}
	}

	private function load_dependencies() {
		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
		}
		if(!class_exists('WPRuby_Licence_Handler')){
			include( dirname( __FILE__ ) . '/includes/WPRuby_Licence_Handler.php' );
		}
		if(!class_exists('Generate_Custom_Payment_Gateways')){
			add_filter('woocommerce_get_settings_pages', 'wpruby_add_custom_payment_settings_tab');
			function wpruby_add_custom_payment_settings_tab($pages){
				$pages[] = include( dirname( __FILE__ ) . '/includes/classes/class-generate-custom-payment-gateways.php' );
				return $pages;
			}
		}
	}

	private function handle_license_and_updates() {

		$item_name = 'WooCommerce Custom Payment Gateway Pro';
        // Licence Handler
		$license_handler = new WPRuby_Licence_Handler('wc_custompayment_license_key');
		$license_handler->setPage('wc-settings');
		$license_handler->setSection('custom_payment');
		$license_handler->setReturnUrl(admin_url('admin.php?page=wc-settings&tab=checkout&section=custom_payment'));
		$license_handler->setPluginName($item_name);

        // Update Handler
		$license_key = trim( get_option( 'wc_custompayment_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater( 'https://wpruby.com', __FILE__, array(
			'version' 	=> '2.0.4',		// current version number
			'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
			'item_name' => $item_name,	// name of this plugin
			'author' 	=> 'Waseem Senjer',	// author of this plugin
			'url'       => home_url()
		));
	}

	public function load_fields($class_name){
		$class_name = str_replace( '_', '-', strtolower( $class_name ) );
		$class_path = plugin_dir_path( __FILE__ ) . 'includes/fields/class-' . $class_name . '.php';
		$interface_path = plugin_dir_path( __FILE__ ) . 'includes/fields/interface-' . $class_name . '.php';
		if ( file_exists( $class_path ) ) {
			require_once( $class_path );
		}
		if ( file_exists( $interface_path ) ) {
			require_once( $interface_path );
		}
	}

	private function display_legacy_data($data) {
         foreach ($data as $key => $value) { ?>
            <tr>
                <th style="width:150px; !important;"><strong><?php echo $key; ?></strong></th>
                <td>
                    <?php if($key == "Card Number"){
                        echo str_replace(" ", "", $value);
                    }else{
                        if (strpos($value, 'data:image/png') === 0){
                            echo '<img style="width:100%; background:#ffffff; border:1px dashed #000000;" alt="signature" src="'. $value .'" />';
                        }else {
                            echo $value;
                        }
                    }
                    ?>
                </td>
            </tr>
        <?php }
	}

	/**
	 * @param array $array
	 *
	 * @return mixed|null
	 */
	private function get_label($array){
	    if(function_exists('array_key_first')){
		    return array_key_first($array);
	    }else {
		    return count($array) ? array_keys($array)[0] : null;
	    }
    }

	private function display_data( $data ) {

		foreach ($data['data'] as $row) {

		    $label = $this->get_label($row);
			$value = $row[$label];
		    ?>
            <tr>
                <th style="width:150px; !important;"><strong><?php echo $label; ?></strong></th>
                <td>
					<?php if($label == "Card Number"){
						echo str_replace(" ", "", $value);
					}else{
						if (strpos($value, 'data:image/png') === 0){
							echo '<img style="width:60%; background:#ffffff; border:1px dashed #000000;" alt="signature" src="'. $value .'" />';
						}else {
							echo $value;
						}
					}
					?>
                </td>
            </tr>
		<?php }
	}
}

Custom_Payment_Pro::get_instance();