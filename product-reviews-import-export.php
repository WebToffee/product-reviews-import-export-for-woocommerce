<?php

/*
  Plugin Name: Product Reviews Import Export for WooCommerce
  Plugin URI: https://wordpress.org/plugins/product-reviews-import-export-for-woocommerce/
  Description: Import and Export Products Reviews From and To your WooCommerce Store.
  Author: WebToffee
  Author URI: https://www.webtoffee.com/product/product-import-export-woocommerce/
  Version: 1.4.9
  WC tested up to: 7.1
  Text Domain: product-reviews-import-export-for-woocommerce
  License: GPLv3
  License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH') || !is_admin()) {
    return;
}

define("WF_PR_REV_IMP_EXP_ID", "wf_pr_rev_imp_exp");
define("WF_PR_REV_CSV_IM_EX", "wf_pr_rev_csv_im_ex");
define("WF_ROOT_FILE_PATH", plugin_dir_path(__FILE__));
define( 'WF_PR_REV_IMP_EXP_VERSION', '1.4.9' );
/**
 * Check if WooCommerce is active
 */
if ( ! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) &&  !array_key_exists( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins', array() ) ) )) { // deactive if woocommerce in not active
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    deactivate_plugins( plugin_basename(__FILE__) );
}

    if (!class_exists('WF_Product_Review_Import_Export_CSV')) :

        /**
         * Main CSV Import class
         */
        class WF_Product_Review_Import_Export_CSV {

            /**
             * Constructor
             */
            public function __construct() {

                if (!defined('WF_PrRevImpExpCsv_FILE')) {
                    define('WF_PrRevImpExpCsv_FILE', __FILE__);
                }
                if (is_admin()) {
                    add_action('admin_notices', array($this, 'wf_product_review_ie_admin_notice'), 15);
                }

                add_filter('woocommerce_screen_ids', array($this, 'woocommerce_screen_ids'));
                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'wf_plugin_action_links'));
                add_action('init', array($this, 'load_plugin_textdomain'));
                add_action('init', array($this, 'catch_export_request'), 20);
                add_action('admin_init', array($this, 'register_importers'));

                add_filter('admin_footer_text', array($this, 'WT_admin_footer_text'), 100);
                add_action('wp_ajax_pripe_wt_review_plugin', array($this, "review_plugin"));
                
                include_once( 'includes/class-wf-pr_revimpexpcsv-admin-screen.php' );
                include_once( 'includes/importer/class-wf-pr_revimpexpcsv-importer.php' );

                if (defined('DOING_AJAX')) {
                    include_once( 'includes/class-wf-pr_revimpexpcsv-ajax-handler.php' );
                }
                 // review request
                include_once 'includes/class-wf-revimpexpcsv-plugin-review-request.php';
            }
            

        public function wf_plugin_action_links($links) {
                $plugin_links = array(
                    '<a href="' . admin_url('admin.php?page=wf_pr_rev_csv_im_ex') . '">' . __('Import Export', 'product-reviews-import-export-for-woocommerce') . '</a>',
                    '<a target="_blank" href="https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_listing&utm_medium=Review_imp_exp_basic&utm_campaign=Product_Import_Export&utm_content='.WF_PR_REV_IMP_EXP_VERSION.'" style="color:#3db634;"> ' . __('Premium Upgrade', 'product-reviews-import-export-for-woocommerce') . '</a>',
                    '<a target="_blank" href="https://www.webtoffee.com/support/">' . __('Support', 'product-reviews-import-export-for-woocommerce') . '</a>',
                    '<a target="_blank" href="https://wordpress.org/support/plugin/product-reviews-import-export-for-woocommerce/reviews?rate=5#new-post">' . __('Review', 'product-reviews-import-export-for-woocommerce') . '</a>',
                );
                return array_merge($plugin_links, $links);
            }

            function wf_product_review_ie_admin_notice() {
                global $pagenow;
                global $post;

                if (!isset($_GET["wf_product_review_ie_msg"]) && empty($_GET["wf_product_review_ie_msg"])) {
                    return;
                }

                $wf_product_review_ie_msg = $_GET["wf_product_review_ie_msg"];

                switch ($wf_product_review_ie_msg) {
                    case "1":
                        echo '<div class="update"><p>' . __('Successfully uploaded via FTP.', 'product-reviews-import-export-for-woocommerce') . '</p></div>';
                        break;
                    case "2":
                        echo '<div class="error"><p>' . __('Error while uploading via FTP.', 'product-reviews-import-export-for-woocommerce') . '</p></div>';
                        break;
                    case "3":
                        echo '<div class="error"><p>' . __('Please choose the file in CSV format using Method 1.', 'product-reviews-import-export-for-woocommerce') . '</p></div>';
                        break;
                }
            }

            /**
             * Add screen ID
             */
            public function woocommerce_screen_ids($ids) {
                $ids[] = 'admin'; // For import screen
                return $ids;
            }

            /**
             * Handle localisation
             */
            public function load_plugin_textdomain() {
                load_plugin_textdomain('product-reviews-import-export-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/lang/');
            }

            /**
             * Catches an export request and exports the data. This class is only loaded in admin.
             */
            public function catch_export_request() {
                if (!empty($_GET['action']) && !empty($_GET['page']) && $_GET['page'] == 'wf_pr_rev_csv_im_ex') {
                    switch ($_GET['action']) {
                        case "export" :
                            $user_ok = self::hf_user_permission();
                            if ($user_ok) {
                                include_once( 'includes/exporter/class-wf-pr_revimpexpcsv-exporter.php' );
                                WF_PrRevImpExpCsv_Exporter::do_export();
                            } else {
                                wp_redirect(wp_login_url());
                            }
                            break;
                    }
                }
            }

            /**
             * Register importers for use
             */
            public function register_importers() {
                register_importer('product_reviews_csv', 'WooCommerce Product Reviews (CSV)', __('Import <strong>product reviews</strong> to your store via a csv file.', 'product-reviews-import-export-for-woocommerce'), 'WF_PrRevImpExpCsv_Importer::product_importer');
            }

            public static function hf_user_permission() {
                // Check if user has rights to export
                $current_user = wp_get_current_user();
                $current_user->roles = apply_filters('hf_add_user_roles', $current_user->roles);
                $current_user->roles = array_unique($current_user->roles);
                $user_ok = false;
                $wf_roles = apply_filters('hf_user_permission_roles', array('administrator', 'shop_manager'));
                if ($current_user instanceof WP_User) {
                    $can_users = array_intersect($wf_roles, $current_user->roles);
                    if (!empty($can_users) || is_super_admin($current_user->ID)) {
                        $user_ok = true;
                    }
                }
                return $user_ok;
            }
            
            function webtoffee_storefrog_admin_notices() {

                if (apply_filters('webtoffee_storefrog_suppress_admin_notices', false) || !self::hf_user_permission()) {
                    return;
                }
                $screen = get_current_screen();

                $allowed_screen_ids = array('woocommerce_page_wf_pr_rev_csv_im_ex');
                if (in_array($screen->id, $allowed_screen_ids) || (isset($_GET['import']) && $_GET['import'] == 'product_reviews_csv')) {

                    $notice = __('<h3>Save Time, Money & Hassle on Your WooCommerce Data Migration?</h3>', 'product-reviews-import-export-for-woocommerce');
                    $notice .= __('<h3>Use StoreFrog Migration Services.</h3>', 'product-reviews-import-export-for-woocommerce');

                    $content = '<style>.webtoffee-storefrog-nav-tab.updated {z-index:2; display: flex;align-items: center;margin: 18px 20px 10px 0;padding:23px;border-left-color: #2c85d7!important}.webtoffee-storefrog-nav-tab ul {margin: 0;}.webtoffee-storefrog-nav-tab h3 {margin-top: 0;margin-bottom: 9px;font-weight: 500;font-size: 16px;color: #2880d3;}.webtoffee-storefrog-nav-tab h3:last-child {margin-bottom: 0;}.webtoffee-storefrog-banner {flex-basis: 20%;padding: 0 15px;margin-left: auto;} .webtoffee-storefrog-banner a:focus{box-shadow: none;}</style>';
                    $content .= '<div class="updated woocommerce-message webtoffee-storefrog-nav-tab notice is-dismissible"><ul>' . $notice . '</ul><div class="webtoffee-storefrog-banner"><a href="http://www.storefrog.com/" target="_blank"> <img src="' . plugins_url(basename(plugin_dir_path(WF_PrRevImpExpCsv_FILE))) . '/images/storefrog.png"/></a></div><div style="position: absolute;top: 0;right: 1px;z-index: 10000;" ><button type="button" id="webtoffee-storefrog-notice-dismiss" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'wf_order_import_export') . '</span></button></div></div>';
                    echo $content;


                    wc_enqueue_js("jQuery( '#webtoffee-storefrog-notice-dismiss' ).click( function() {
                                            jQuery.post( '" . admin_url("admin-ajax.php") . "', { action: 'PREIPF_webtoffee_storefrog_notice_dismiss' } );
                                            jQuery('.webtoffee-storefrog-nav-tab').fadeOut();
                                        });
                                    ");
                }
            }

            function webtoffee_storefrog_notice_dismiss() {

                if (!self::hf_user_permission()) {
                    wp_die(-1);
                }
                update_option('PREIPF_Webtoffee_storefrog_admin_notices_dismissed', 1);
                wp_die();
            }

            public function WT_admin_footer_text($footer_text) {
                if (!self::hf_user_permission()) {
                    return $footer_text;
                }
                $screen = get_current_screen();
                $allowed_screen_ids = array('woocommerce_page_wf_pr_rev_csv_im_ex');
                if (in_array($screen->id, $allowed_screen_ids) || (isset($_GET['import']) && $_GET['import'] == 'product_reviews_csv')) {
                    if (!get_option('pripe_wt_plugin_reviewed')) {
                        $footer_text = sprintf(
                                __('If you like the plugin please leave us a %1$s review.', 'product-reviews-import-export-for-woocommerce'), '<a href="https://wordpress.org/support/plugin/product-reviews-import-export-for-woocommerce/reviews/?rate=5#new-post" target="_blank" class="wt-review-link" data-rated="' . esc_attr__('Thanks :)', 'product-reviews-import-export-for-woocommerce') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
                        );
                        wc_enqueue_js(
                                "jQuery( 'a.wt-review-link' ).click( function() {
                                                   jQuery.post( '" . WC()->ajax_url() . "', { action: 'pripe_wt_review_plugin' } );
                                                   jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
                                           });"
                        );
                    } else {
                        $footer_text = __('Thank you for your review.', 'product-reviews-import-export-for-woocommerce');
                    }
                }

                return '<i>' . $footer_text . '</i>';
            }

            public function review_plugin() {
                if (!self::hf_user_permission()) {
                    wp_die(-1);
                }
                update_option('pripe_wt_plugin_reviewed', 1);
                wp_die();
            }                 

        }

        endif;

    new WF_Product_Review_Import_Export_CSV();
    


register_activation_hook(__FILE__, 'hf_welcome_screen_activate_basic_review');

function hf_welcome_screen_activate_basic_review() {
    if(!class_exists( 'WooCommerce' )){
        deactivate_plugins(basename(__FILE__));
        wp_die(__("WooCommerce is not installed/actived. it is required for this plugin to work properly. Please activate WooCommerce.", "product-reviews-import-export-for-woocommerce"), "", array('back_link' => 1));
    }
    if (is_plugin_active('product-csv-import-export-for-woocommerce/product-csv-import-export.php')) {
        deactivate_plugins(basename(__FILE__));
        wp_die(__("Is everything fine? You already have the Premium version installed in your website. For any issues, kindly raise a ticket via <a target='_blank' href='https://www.webtoffee.com/support/'>support</a>", "product-reviews-import-export-for-woocommerce"), "", array('back_link' => 1));
    }
}



if ( !function_exists( 'wt_product_review_imex_basic_plugin_screen_update_js' ) ) {

	function wt_product_review_imex_basic_plugin_screen_update_js() {
		?>
		<script>
		( function ( $ ) {
		var update_dv = $( '#product-reviews-import-export-for-woocommerce-update' );
		update_dv.find( '.wt-product-review-update-message' ).next( 'p' ).remove();
		update_dv.find( 'a.update-link:eq(0)' ).click( function () {
			$( '.wt-product-review-update-message' ).remove();
		} );
		} )( jQuery );
		</script>
		<?php
	}

}

/*
 *  Displays update information for a plugin. 
 */
function wt_product_reviews_import_export_for_woocommerce_update_message( $data, $response )
{
    if(isset( $data['upgrade_notice']))
    {
		add_action( 'admin_print_footer_scripts', 'wt_product_review_imex_basic_plugin_screen_update_js' );
        printf(
        '<div class="update-message wt-product-review-update-message">%s</div>',
           $data['upgrade_notice']
        );
    }
}
add_action( 'in_plugin_update_message-product-reviews-import-export-for-woocommerce/product-reviews-import-export.php', 'wt_product_reviews_import_export_for_woocommerce_update_message', 10, 2 );