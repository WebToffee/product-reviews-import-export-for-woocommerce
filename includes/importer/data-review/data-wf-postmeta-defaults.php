<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// New postmeta defaults
return apply_filters( 'product_reviews_csv_product_postmeta_defaults', array(
	'rating'				=> '',
	'verified'				=> ''
) );