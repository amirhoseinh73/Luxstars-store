<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class CustomPaymentUpgrades{

	public static $instance;
	/**
	 * CustomPaymentUpgrades constructor.
	 */
	public function __construct() {
		add_action('init', array($this, 'perform_upgrades'));
		self::$instance = $this;
	}

	public function perform_upgrades(){
		if ( get_option( 'wc_custom_payment_139_version_upgrade' ) != 'upgraded' ) {
			$this->perform_139_upgrades();
		}
	}

	public function perform_139_upgrades() {

		$gateways = json_decode(get_option('wpruby_generated_custom_gatwayes'));
		if($gateways) {
			foreach ( $gateways as $gateway ) {
				$class_name =  'custom_' . md5($gateway->name);
				$old_id = $class_name;
				$new_id = substr($class_name, 0, 22);
				$old_gateway_settings = get_option('woocommerce_'.$old_id.'_settings');
				add_option('woocommerce_'.$new_id.'_settings', $old_gateway_settings);
				delete_option('woocommerce_'.$old_id.'_settings');
			}
		}
		add_option('wc_custom_payment_139_version_upgrade', 'upgraded');
	}

	/**
	 * @return CustomPaymentUpgrades
	 */
	public static function get_instance(){
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

CustomPaymentUpgrades::get_instance();