<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wt-import-export-upsell-wrapper-review market-box table-box-main">

    <div class="pipe-review-widget">
        <?php
        echo sprintf(__('<div class=""><p><i>If you like the plugin please leave us a %1$s review!</i><p></div>', 'product-reviews-import-export-for-woocommerce'), '<a href="https://wordpress.org/support/plugin/product-reviews-import-export-for-woocommerce/reviews?rate=5#new-post" target="_blank" class="xa-pipe-rating-link" data-reviewed="' . esc_attr__('Thanks for the review.', 'product-reviews-import-export-for-woocommerce') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>');
        ?>
    </div>
    <div class="ier-premium-upgrade wt-ierpro-sidebar">

        <div class="wt-ierpro-header">
            <div class="wt-ierpro-name">
                <img src="<?php echo plugins_url('images/product-ie.svg', WF_PrRevImpExpCsv_FILE); ?>" alt="featured img" width="36" height="36">
                <h4 class="wt-ier-product-name"><?php _e('Product Import Export Plugin For WooCommerce'); ?></h4>
            </div>
            <div class="wt-ierpro-mainfeatures">
                <ul>
                    <li class="money-back"><?php _e('30 Day Money Back Guarantee'); ?></li>
                    <li class="support"><?php _e('Fast and Superior Support'); ?></li>
                </ul>
                <div class="wt-ierpro-btn-wrapper">
                    <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_sidebar&utm_medium=Review_imp_exp_basic&utm_campaign=Product_Import_Export&utm_content=<?php echo WF_PR_REV_IMP_EXP_VERSION; ?>" class="wt-ierpro-blue-btn" target="_blank"><?php _e('UPGRADE TO PREMIUM'); ?></a>
                </div>                
            </div>
        </div>
                <div class="wt-ier-coupon wt-ier-order wt-ier-gopro-cta wt-ierpro-features">
                    <ul class="ticked-list wt-ierpro-allfeat">
						<li><?php _e('All free version features'); ?></li>
						<li><?php _e('Import and export in XLS and XLSX formats'); ?></li>
						<li><?php _e('XML file type support'); ?></li>							
                        <li><?php _e('Export and import variable products, subscription products and custom product types'); ?></li>
                        <li><?php _e('Export and import custom fields and third-party plugin fields'); ?></li>            
                        <li><?php _e('Run scheduled automatic import and export'); ?></li>
                        <li><?php _e('Import from URL, FTP/SFTP'); ?></li>
                        <li><?php _e('Export to FTP/SFTP'); ?></li>
                        <li><?php _e('Option to export product images as a separate zip file'); ?></li>
                        <li><?php _e('Tested compatibility with major third-party plugins'); ?></li>
                    </ul> 
                </div>  
		
    </div>
    
    </div>
