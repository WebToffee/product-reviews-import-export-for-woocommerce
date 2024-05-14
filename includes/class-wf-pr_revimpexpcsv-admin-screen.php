<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_ProdReviewImpExpCsv_Admin_Screen {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}


	/**
	 * Notices in admin
	 */
	public function admin_notices() {
		if ( ! function_exists( 'mb_detect_encoding' ) ) {
			echo '<div class="error"><p>' . __( 'Product Review CSV Import Export requires the function <code>mb_detect_encoding</code> to import and export CSV files. Please ask your hosting provider to enable this function.', 'product-reviews-import-export-for-woocommerce' ) . '</p></div>';
		}
	}

	/**
	 * Admin Menu
	 */
	public function admin_menu() {
		$page = add_submenu_page( 'woocommerce', __( 'Product Reviews Im-Ex', 'product-reviews-import-export-for-woocommerce' ), __( 'Product Reviews Im-Ex', 'product-reviews-import-export-for-woocommerce' ), apply_filters( 'product_reviews_csv_product_role', 'manage_woocommerce' ), 'wf_pr_rev_csv_im_ex', array( $this, 'output' ) );
	}
         /**
	 * Get WC Plugin path without fail on any version
	 */
        public static function hf_get_wc_path(){
                if (function_exists('WC')){
                   $wc_path =  WC()->plugin_url();
                }else{
                   $wc_path = plugins_url() . '/woocommerce'; 
                }
                return $wc_path;
        }

        /**
	 * Admin Scripts
	 */
	public function admin_scripts() {
			$screen = get_current_screen();
			wp_enqueue_script('wc-enhanced-select');
            $allowed_creen_id = array('woocommerce_page_wf_pr_rev_csv_im_ex');
            if (in_array($screen->id, $allowed_creen_id) || (isset($_GET['import']) && $_GET['import'] == 'product_reviews_csv')) {
                $wc_path = self::hf_get_wc_path();
                wp_enqueue_style('woocommerce_admin_styles', $wc_path . '/assets/css/admin.css');
                wp_enqueue_style('woocommerce-product-csv-importer', plugins_url(basename(plugin_dir_path(WF_PrRevImpExpCsv_FILE)) . '/styles/wf-style.css', basename(__FILE__)), '', '1.2.0', 'screen');

            }
        }

    /**
	 * Admin Screen output
	 */
	public function output() {
                
		$tab = 'export';
            if (!empty($_GET['tab'])) {
                if ($_GET['tab'] == 'import') {
                    $tab = 'import';                
                } else if ($_GET['tab'] == 'help') {
                    $tab = 'help';
                }
            }
            include( 'views/html-wf-admin-screen.php' );
	}
        

	/**
	 * Admin page for importing
	 */
	public function admin_import_page() {
//            admin_url('admin.php?import=product_reviews_csv');
		include( 'views/import/html-wf-import-product-reviews.php' );
//		include( 'views/export/html-wf-export-product-reviews.php' );
	}

	/**
	 * Admin Page for exporting
	 */
	public function admin_export_page() {
        include( 'views/export/html-wf-export-product-reviews.php' );
	}
        /**
        * Admin Page for help
        */
       public function admin_help_page() {
           include('views/html-wf-help-guide.php');
       }

}

new WF_ProdReviewImpExpCsv_Admin_Screen();