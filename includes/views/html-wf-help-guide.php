<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
    .help-guide .cols {
        display: flex;
    }
    .help-guide .inner-panel {
        padding: 40px;
        background-color: #FFF;
        margin: 15px 10px;
        box-shadow: 1px 1px 5px 1px rgba(0,0,0,.1);
        text-align: center;
    }
    .help-guide .inner-panel p{
        margin-bottom: 20px;
    }
    .help-guide .inner-panel img{
        margin:30px 15px 0;
    }

</style>
<div class="pipe-main-box">
    <div class="tool-box bg-white p-20p pipe-view">
        <div id="tab-help" class="coltwo-col panel help-guide">
            <div class="cols">
                <div class="inner-panel" style="">
                    <img src="<?php echo  plugins_url(basename((WF_ROOT_FILE_PATH))) . '/images/video.png'; ?>"/>
                    <h3><?php _e('How-to-setup', 'product-reviews-import-export-for-woocommerce'); ?></h3>
                    <p style=""><?php _e('Get to know about our product in 3 minutes with this video', 'product-reviews-import-export-for-woocommerce'); ?></p>
                    <a href="https://www.webtoffee.com/how-to-import-export-woocommerce-product-reviews/" target="_blank" class="button button-primary">
                        <?php _e('Setup Guide', 'product-reviews-import-export-for-woocommerce'); ?></a>
                </div>

                <div class="inner-panel" style="">
                    <img src="<?php echo plugins_url(basename((WF_ROOT_FILE_PATH))) . '/images/documentation.png'; ?>"/>
                    <h3><?php _e('Documentation', 'product-reviews-import-export-for-woocommerce'); ?></h3>
                    <p style=""><?php _e('Refer to our documentation to set and get started', 'product-reviews-import-export-for-woocommerce'); ?></p>
                    <a target="_blank" href="https://www.webtoffee.com/how-to-import-export-woocommerce-product-reviews/" class="button-primary"><?php _e('Documentation', 'product-reviews-import-export-for-woocommerce'); ?></a> 
                </div>

                <div class="inner-panel" style="">
                    <img src="<?php echo plugins_url(basename((WF_ROOT_FILE_PATH))) . '/images/support.png'; ?>"/>
                    <h3><?php _e('Support', 'product-reviews-import-export-for-woocommerce'); ?></h3>
                    <p style=""><?php _e('We would love to help you on any queries or issues.', 'product-reviews-import-export-for-woocommerce'); ?></p>
                    <a href="https://www.webtoffee.com/support/" target="_blank" class="button button-primary">
                        <?php _e('Contact Us', 'product-reviews-import-export-for-woocommerce'); ?></a>
                </div>
            </div>
            <div class="cols">  
                <div class="inner-panel" style="width:23%">
                    <img src="<?php echo plugins_url(basename((WF_ROOT_FILE_PATH))) . '/images/csv.png'; ?>"/>
                    <h3><?php _e('Sample-CSV', 'product-reviews-import-export-for-woocommerce'); ?></h3>
                    <p style=""><?php _e('Familiarize yourself with the CSV format', 'product-reviews-import-export-for-woocommerce'); ?></p>
                    <a href="<?php echo plugins_url('Product_Review_Sample.csv', WF_PrRevImpExpCsv_FILE); ?>" target="_blank" class="button-primary">
                        <?php _e('Get Sample CSV', 'product-reviews-import-export-for-woocommerce'); ?></a>
                </div> 
            </div>
        </div>
    </div>
    
</div>