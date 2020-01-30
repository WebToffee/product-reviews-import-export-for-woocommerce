<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_PrRevImpExpCsv_Settings {

	/**
	 * Product Exporter Tool
	 */
	public static function save_settings( ) {
		//update_option( 'woocommerce_'.WF_PR_REV_IMP_EXP_ID.'_settings', $settings );
                wp_redirect( admin_url( '/admin.php?page='.wf_pr_rev_csv_im_ex.'&tab=settings' ) );
		exit;
	}
}