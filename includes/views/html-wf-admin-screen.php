
<div class="woocommerce">

	<div class="icon32" id="icon-woocommerce-importer"><br></div>
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
   
        <a href="<?php echo admin_url('admin.php?page=wf_pr_rev_csv_im_ex') ?>" class="nav-tab <?php echo ($tab == 'import') ? 'nav-tab-active' : ''; ?>"><?php _e('Product Reviews Import / Export', 'wf_pr_rev_import_export'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=wf_pr_rev_csv_im_ex&tab=help'); ?>" class="nav-tab <?php echo ('help' == $tab) ? 'nav-tab-active' : ''; ?>"><?php _e('Help', 'wf_pr_rev_import_export'); ?></a>
        <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank" class="nav-tab nav-tab-premium"><?php _e('Upgrade to Premium for More Features', 'wf_pr_rev_import_export'); ?></a>

    </h2>
        <?php include(WF_ROOT_FILE_PATH . 'includes/views/market.php'); ?>
	<?php
		switch ($tab) {
                        case "help" :
				$this->admin_help_page();
			break;                    
//			case "export" :
//				$this->admin_export_page();
//			break;
			default :
				$this->admin_import_page();
			break;
		}
	?>
</div>
