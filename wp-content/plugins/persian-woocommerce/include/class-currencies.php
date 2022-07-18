<?php

defined( 'ABSPATH' ) || exit;

class Persian_Woocommerce_Currencies extends Persian_Woocommerce_Core {

	public $currencies;

	public function __construct() {

		$this->currencies = [
			'IRR'  => __( 'ریال', 'woocommerce' ),
			'IRHR' => __( 'هزار ریال', 'woocommerce' ),
			'IRT'  => __( 'تومان', 'woocommerce' ),
			'IRHT' => __( 'هزار تومان', 'woocommerce' ),
		];

		add_filter( 'woocommerce_currencies', [ $this, 'currencies' ] );
		add_filter( 'woocommerce_currency_symbol', [ $this, 'currencies_symbol' ], 10, 2 );
	}

	public function currencies( $currencies ): array {

		foreach ( $this->currencies as $key => $value ) {
			unset( $currencies[ $key ] );
		}

		return array_merge( $currencies, $this->currencies );
	}

	public function currencies_symbol( $currency_symbol, $currency ) {

		if ( in_array( $currency, array_keys( $this->currencies ) ) ) {
			return $this->currencies[ $currency ];
		}

		return $currency_symbol;
	}
}

new Persian_Woocommerce_Currencies();