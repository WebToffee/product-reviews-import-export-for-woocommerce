<?php
/**
 * WooCommerce CSV Importer class for managing parsing of CSV files.
 */
class WF_CSV_Parser {

	var $row;
	var $post_type;
	var $reserved_fields;		// Fields we map/handle (not custom fields)
	var $post_defaults;			// Default post data
	var $postmeta_defaults;		// default post meta
	var $postmeta_allowed;		// post meta validation
	var $allowed_product_types;	// Allowed product types

	/**
	 * Constructor
	 */
	public function __construct( $post_type = 'product' ) {
		$this->post_type         = $post_type;
		$this->reserved_fields   = include( 'data-review/data-wf-reserved-fields.php' );
		$this->post_defaults     = include( 'data-review/data-wf-post-defaults.php' );
		$this->postmeta_defaults = include( 'data-review/data-wf-postmeta-defaults.php' );
		$this->postmeta_allowed  = include( 'data-review/data-wf-postmeta-allowed.php' );

	}

	/**
	 * Format data from the csv file
	 * @param  string $data
	 * @param  string $enc
	 * @return string
	 */
	public function format_data_from_csv( $data, $enc ) {
		return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
	}

	/**
	 * Parse the data
	 * @param  string  $file      [description]
	 * @param  string  $delimiter [description]
	 * @param  array  $mapping   [description]
	 * @param  integer $start_pos [description]
	 * @param  integer  $end_pos   [description]
	 * @return array
	 */
	public function parse_data( $file, $delimiter, $mapping, $start_pos = 0, $end_pos = null, $eval_field ) {
// Set locale
		$enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
		if ( $enc )
			setlocale( LC_ALL, 'en_US.' . $enc );
		@ini_set( 'auto_detect_line_endings', true );

		$parsed_data = array();
		$raw_headers = array();

		// Put all CSV data into an associative array
		if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) {

			$header   = fgetcsv( $handle, 0, $delimiter , '"', '"'  );
			if ( $start_pos != 0 )
				fseek( $handle, $start_pos );

		    while ( ( $postmeta = fgetcsv( $handle, 0, $delimiter , '"', '"' ) ) !== FALSE ) {
	            $row = array();
				
	            foreach ( $header as $key => $heading ) {
					$s_heading = $heading;

            		if ( $s_heading == '' )
            			continue;

	            	// Add the heading to the parsed data
					$row[$s_heading] = ( isset( $postmeta[$key] ) ) ? $this->format_data_from_csv( $postmeta[$key], $enc ) : '';

	               	// Raw Headers stores the actual column name in the CSV
					$raw_headers[ $s_heading ] = $heading;
	            }
	            $parsed_data[] = $row;

	            unset( $postmeta, $row );

	            $position = ftell( $handle );

	            if ( $end_pos && $position >= $end_pos )
	            	break;
		    }
		    fclose( $handle );
		}
		return array( $parsed_data, $raw_headers, $position );
	}
	
	
	/**
	 * Parse product review
	 * @param  array  $item
	 * @param  integer $merge_empty_cells
	 * @return array
	 */
	public function parse_product_review( $item, $merge_empty_cells = 0, $use_sku = 0 ) {
          
		global $WF_CSV_Product_Review_Import, $wpdb;
		$this->row++;

		$terms_array = $postmeta = $product = array();
		$attributes = $default_attributes = $gpf_data = null;
		// Merging
		$merging = ( ! empty( $_GET['merge'] ) && $_GET['merge'] ) ? true : false;
		$post_id = ( ! empty( $item['comment_ID'] ) ) ? $item['comment_ID'] : 0;
		$post_id = ( ! empty( $item['post_id'] ) ) ? $item['post_id'] : $post_id;
		if ( $merging ) {

			$product['merging'] = true;

			$WF_CSV_Product_Review_Import->hf_log_data_change( 'csv-import', sprintf( __('> Row %s - preparing for merge.', 'product-reviews-import-export-for-woocommerce'), $this->row ) );

			// Required fields
			if ( ! $post_id ) {

				$WF_CSV_Product_Review_Import->hf_log_data_change( 'csv-import', __( '> > Cannot merge without id. Importing instead.', 'product-reviews-import-export-for-woocommerce') );

				$merging = false;
			} else {

				// Check product exists
				if ( ! $post_id ) 
				{
                    $post_db_type = $this->post_defaults['post_type'];
                    $post_pass_type = '"'.$post_db_type.'"';
                    // Check product to merge exists
                    $db_query = $wpdb->prepare("
						SELECT comment_ID
					    FROM $wpdb->comments
					    WHERE $wpdb->comments = %d",$post_id);
					$found_product_id = $wpdb->get_var($db_query);
					if ( ! $found_product_id ) {
						$WF_CSV_Product_Review_Import->hf_log_data_change( 'csv-import', sprintf(__( '> > Skipped. Cannot find product reviews with ID %s. Importing instead.', 'product-reviews-import-export-for-woocommerce'), $item['ID']) );
						$merging = false;

					} else {

						$post_id = $found_product_id;

						$WF_CSV_Product_Review_Import->hf_log_data_change( 'csv-import', sprintf(__( '> > Found product reviews with ID %s.', 'product-reviews-import-export-for-woocommerce'), $post_id) );
					}
				}
				$product['merging'] = true;
			}
		}

		if ( ! $merging ) {

			$product['merging'] = false;
			$WF_CSV_Product_Review_Import->hf_log_data_change( 'csv-import', sprintf( __('> Row %s - preparing for import.', 'product-reviews-import-export-for-woocommerce'), $this->row ) );

			// Required fields
                        if ( !isset($item['comment_content']) || $item['comment_content'] === '')
			{
				$WF_CSV_Product_Review_Import->hf_log_data_change( 'csv-import', __( '> > Skipped. No comment content set for new product reviews.', 'product-reviews-import-export-for-woocommerce') );
				return new WP_Error( 'parse-error', __( 'No comment content set for new product reviews.', 'product-reviews-import-export-for-woocommerce' ) );
			}

                        if($use_sku == 1 && (!isset($item['product_SKU']) || $item['product_SKU'] === ''))
			{
				$WF_CSV_Product_Review_Import->hf_log_data_change( 'csv-import', __( '> > Skipped. No Product SKU given, for which new comment is to be imported', 'wf_csv_import_export') );
			        return new WP_Error( 'parse-error', __( 'Product SKU is empty, Skipped the review.', 'product-reviews-import-export-for-woocommerce' ) );
			}
                        elseif ( $use_sku == 0 && (!isset($item['comment_post_ID']) || $item['comment_post_ID'] === '' )){                       
                            $WF_CSV_Product_Review_Import->hf_log_data_change('csv-import', __('> > Skipped. No post(product) id found, for which new comment is to be imported', 'product-reviews-import-export-for-woocommerce'));
                            return new WP_Error('parse-error', __('No post(product) id found, for which new comment is to be imported.', 'product-reviews-import-export-for-woocommerce'));
                        }
                  }
                  
                  
		if($use_sku == 1 && $item['product_SKU'])
		{
			$temp_product_id = wc_get_product_id_by_sku( $item['product_SKU'] );
			if(! $temp_product_id)
			{
				$WF_CSV_Product_Review_Import->hf_log_data_change( 'review-csv-import', __( '> > Skipped. No Product found for given SKU, for which new comment is to be imported', 'wf_csv_import_export') );
				return new WP_Error( 'parse-error', __( 'No Product found for given SKU, Skipped the review.', 'wf_csv_import_export' ) );
			}
		}
		

		$product['post_id'] = $post_id;


		// Get post fields
		foreach ( $this->post_defaults as $column => $default ) {
			if ( isset( $item[ $column ] ) ) 
                            $product[ $column ] = $item[ $column ];
                        if($column == 'comment_post_ID' && $use_sku == 1)
				$product[ $column ] = !empty($temp_product_id) ? $temp_product_id : null;
                        
		}

		// Get custom fields
		foreach ( $this->postmeta_defaults as $column => $default ) {
			if ( isset( $item[$column] ) )
				$postmeta[$column] = (string) $item[$column];
			elseif ( isset( $item[$column] ) )
				$postmeta[$column] = (string) $item[$column];

			// Check custom fields are valid
			if ( isset( $postmeta[$column] ) && isset( $this->postmeta_allowed[$column] ) && ! in_array( $postmeta[$column], $this->postmeta_allowed[$column] ) ) {
				$postmeta[$column] = $this->postmeta_defaults[$column];
			}
		}

		if ( ! $merging ) {
			// Merge post meta with defaults
			$product  = wp_parse_args( $product, $this->post_defaults );
			$postmeta = wp_parse_args( $postmeta, $this->postmeta_defaults );
		}
		
		// Put set core product postmeta into product array
		foreach ( $postmeta as $key => $value ) {
			$product['postmeta'][] = array( 'key' 	=> esc_attr($key), 'value' => $value );
		}

		/**
		 * Handle other columns
		 */
		foreach ( $item as $key => $value ) 
		{

			if ( empty($item['post_parent']) && ! $merge_empty_cells && $value == "" )
				continue;


			/**
			 * Handle meta: columns - import as custom fields
			 */
			elseif ( strstr( $key, 'meta:' ) ) {

				// Get meta key name
				$meta_key = ( isset( $WF_CSV_Product_Review_Import->raw_headers[$key] ) ) ? $WF_CSV_Product_Review_Import->raw_headers[$key] : $key;
				$meta_key = trim( str_replace( 'meta:', '', $meta_key ) );

                                if($meta_key !== 'wcpb_bundle_products'){
				// Decode JSON
				$json = json_decode( $value, true );

				if ( is_array( $json ) || is_object( $json ) )
					$value = (array) $json;
                                
                                }
				// Add to postmeta array
				$product['postmeta'][] = array(
					'key' 	=> esc_attr( $meta_key ),
					'value' => $value
				);
			}

			

		}

		// Remove empty attribues
                if(!empty($attributes))
		foreach ( $attributes as $key => $value ) {
			if ( ! isset($value['name']) ) unset( $attributes[$key] );
		}

		
		$product['comment_content'] = ( ! empty( $item['comment_content'] ) ) ? $item['comment_content'] : '';
                $product['comment_alter_id'] = ( ! empty( $item['comment_alter_id'] ) ) ? $item['comment_alter_id'] : '';

		unset( $item, $terms_array, $postmeta, $attributes, $gpf_data, $images );
		return $product;
	}
    
}