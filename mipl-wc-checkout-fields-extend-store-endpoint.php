<?php
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema;

class MIPL_WC_Checkout_Fields_Extend_Store_Endpoint {
	/**
	 * Stores Rest Extending instance.
	 *
	 * @var ExtendRestApi
	 */
	private static $extend;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'mipl-wc-checkout-fields';

	/**
	 * Bootstraps the class and hooks required data.
	 *
	 */
    
	public static function init() {

		self::$extend = Automattic\WooCommerce\StoreApi\StoreApi::container()->get( Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::class );

		self::extend_store();

	}



	/**
	 * Registers the actual data into each endpoint.
	 */

	public static function extend_store() {
		
		if ( is_callable( [ self::$extend, 'register_endpoint_data' ] ) ) {


			self::$extend->register_endpoint_data(
				[
					'endpoint'        => CheckoutSchema::IDENTIFIER,
					'namespace'       => self::IDENTIFIER,
					'schema_callback' => [ 'MIPL_WC_Checkout_Fields_Extend_Store_Endpoint', 'extend_checkout_schema' ],
					'schema_type'     => ARRAY_A,
				]
			);

		}
		
        
	}

	/**
	 * Register schema into the Checkout endpoint.
	 *
	 * @return array Registered schema.
	 *
	 */

	public static function extend_checkout_schema() {
        
        
        return [];
        
    }
}
