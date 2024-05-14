<div class="tool-box bg-white p-20p pipe-view">
    <h3 class="title aw-title"><?php _e('Import Product Reviews in CSV Format:', 'product-reviews-import-export-for-woocommerce'); ?></h3>
    <p><?php _e('Import product reviews in CSV format', 'product-reviews-import-export-for-woocommerce'); ?></p>
    <p class="submit">
        <?php
        $merge_url = admin_url('admin.php?import=product_reviews_csv&merge=1');
        $import_url = admin_url('admin.php?import=product_reviews_csv');
        ?>
        <a class="button button-primary" id="mylink" href="<?php echo admin_url('admin.php?import=product_reviews_csv'); ?>"><?php _e('Import Product Reviews', 'product-reviews-import-export-for-woocommerce'); ?></a>
        &nbsp;
        <!--<input type="checkbox" id="merge" value="0"><?php _e('Merge product reviews if exists', 'product-reviews-import-export-for-woocommerce'); ?> <br>-->
    </p>
</div>
<script type="text/javascript">
    jQuery('#merge').click(function () {
        if (this.checked) {
            jQuery("#mylink").attr("href", '<?php echo $merge_url ?>');
        } else {
            jQuery("#mylink").attr("href", '<?php echo $import_url ?>');
        }
    });
</script>