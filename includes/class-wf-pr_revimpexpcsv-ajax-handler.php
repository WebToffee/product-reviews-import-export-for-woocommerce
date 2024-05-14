<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_ProdReviewImpExpCsv_AJAX_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_product_reviews_csv_import_request', array( $this, 'csv_import_request' ) );
	}
	
	/**
	 * Ajax event for importing a CSV
	 */
	public function csv_import_request() {            
		define( 'WP_LOAD_IMPORTERS', true );
                WF_PrRevImpExpCsv_Importer::product_importer();
	}

}

new WF_ProdReviewImpExpCsv_AJAX_Handler();