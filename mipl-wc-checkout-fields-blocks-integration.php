<?php
use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;


    class Custom_Fields_Blocks_Integration implements IntegrationInterface {

	
        public function get_name() {
            return 'mipl-wc-checkout-fields';
        }


        private function extend_store_api() {
            MIPL_WC_Checkout_Fields_Extend_Store_Endpoint::init();
        }   
    
    
        public function get_script_handles() {
            return [ 'mipl-wc-checkout-fields-blocks-integration', 'mipl-wc-checkout-fields-group-frontend' ];
        }

	
        public function get_editor_script_handles() {
            return [ 'mipl-wc-checkout-fields-blocks-integration', 'mipl-wc-checkout-fields-group-editor' ];
        }

	
        public function get_script_data() {
            $data = [
                'mipl-wc-checkout-fields-active' => true,
            ];
        
            return $data;

        }    

	
        public function initialize() {
            require_once __DIR__ . '/mipl-wc-checkout-fields-extend-store-endpoint.php';
            $this->register_custom_fields_block_frontend_scripts();
            $this->register_custom_fields_block_editor_scripts();
            $this->register_custom_fields_block_editor_styles();
            $this->register_main_integration();
            $this->extend_store_api();
            $this->save_custom_fields_instructions();
            $this->show_custom_fields_instructions_in_order();
            
        }

	

		private function save_custom_fields_instructions() {
       
        // add_action(
        //     'woocommerce_store_api_checkout_update_order_from_request',
        //     function( \WC_Order $order, \WP_REST_Request $request ) {
        //         $custom_fields_request_data = $request['extensions'][$this->get_name()];
        //         $order->update_meta_data( 'mipl_custom_fields', $custom_fields_request_data );
        //     },
        //     10,
        //     2
        // );
   		 }

   
    private function show_custom_fields_instructions_in_order() {
        // add_action(
        //     'woocommerce_admin_order_data_after_shipping_address',
        //     function( \WC_Order $order ) {
               

        //     }
        // );
    }

	
	private function show_custom_fields_instructions_in_order_confirmation() {
		
	}

	
	public function show_custom_fields_instructions_in_order_email() {
	
	
	}

	/**
	 * Registers the main JS file required to add filters and Slot/Fills.
	 */
	private function register_main_integration() {
		$script_path = '/build/index.js';
		$style_path  = '/build/style-index.css';

		$script_url = plugins_url( $script_path, __FILE__ );
		$style_url  = plugins_url( $style_path, __FILE__ );

		$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: [
				'dependencies' => [],
				'version'      => $this->get_file_version( $script_path ),
			];

		wp_enqueue_style(
			'mipl-wc-checkout-fields-blocks-integration',
			$style_url,
			[],
			$this->get_file_version( $style_path )
		);

		wp_register_script(
			'mipl-wc-checkout-fields-blocks-integration',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
		wp_set_script_translations(
			'mipl-wc-checkout-fields-blocks-integration',
			'mipl-wc-checkout-fields',
			dirname( __FILE__ ) . '/languages'
		);
	}


	public function register_custom_fields_block_editor_styles() {
		$style_path = '/build/style-mipl-wc-checkout-fields-group.css';

		$style_url = plugins_url( $style_path, __FILE__ );
		wp_enqueue_style(
			'mipl-wc-checkout-fields-group',
			$style_url,
			[],
			$this->get_file_version( $style_path )
		);
	}

	public function register_custom_fields_block_editor_scripts() {
		$script_path       = '/build/mipl-wc-checkout-fields-group.js';
		$script_url        = plugins_url( $script_path, __FILE__ );
		$script_asset_path = dirname( __FILE__ ) . '/build/mipl-wc-checkout-fields-group.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: [
				'dependencies' => [],
				'version'      => $this->get_file_version( $script_asset_path ),
			];

		wp_register_script(
			'mipl-wc-checkout-fields-group-editor',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'mipl-wc-checkout-fields-group-editor',
			'mipl-wc-checkout-fields',
			dirname( __FILE__ ) . '/languages'
		);
	}

	public function register_custom_fields_block_frontend_scripts() {
		$script_path       = '/build/mipl-wc-checkout-fields-group-frontend.js';
		$script_url        = plugins_url( $script_path, __FILE__ );
		$script_asset_path = dirname( __FILE__ ) . '/build/mipl-wc-checkout-fields-group-frontend.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: [
				'dependencies' => [],
				'version'      => $this->get_file_version( $script_asset_path ),
			];

		wp_register_script(
			'mipl-wc-checkout-fields-group-frontend',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'mipl-wc-checkout-fields-group-frontend',
			'mipl-wc-checkout-fields',
			dirname( __FILE__ ) . '/languages'
		);
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return MIPL_WC_CHECKOUT_FIELDS_VERSION;
	}
}
