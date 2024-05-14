
<div class="woocommerce">

	<div class="icon32" id="icon-woocommerce-importer"><br></div>
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
                <a href="<?php echo admin_url('admin.php?page=wf_pr_rev_csv_im_ex') ?>" class="nav-tab <?php echo ($tab == 'export') ? 'nav-tab-active' : ''; ?>"><?php _e('Product Reviews Export', 'product-reviews-import-export-for-woocommerce'); ?></a>

        <a href="<?php echo admin_url('admin.php?import=product_reviews_csv') ?>" class="nav-tab <?php echo ($tab == 'import') ? 'nav-tab-active' : ''; ?>"><?php _e('Product Reviews Import', 'product-reviews-import-export-for-woocommerce'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=wf_pr_rev_csv_im_ex&tab=help'); ?>" class="nav-tab <?php echo ('help' == $tab) ? 'nav-tab-active' : ''; ?>"><?php _e('Help', 'product-reviews-import-export-for-woocommerce'); ?></a>
        <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank" class="nav-tab nav-tab-premium"><?php _e('Upgrade to Premium for More Features', 'product-reviews-import-export-for-woocommerce'); ?></a>

    </h2>
        <?php include(WF_ROOT_FILE_PATH . 'includes/views/market.php'); ?>
	<?php
		switch ($tab) {
                        case "help" :
				$this->admin_help_page();
			break;                    
			case "import" :
                            $this->admin_import_page();
				
			break;
			default :
                            $this->admin_export_page();
				
			break;
		}
	?>
</div>
