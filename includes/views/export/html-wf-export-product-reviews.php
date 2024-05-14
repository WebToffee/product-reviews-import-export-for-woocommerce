<div class="tool-box bg-white p-20p pipe-view">
    <h3 class="title aw-title"><?php _e('Export Product Reviews in CSV Format:', 'product-reviews-import-export-for-woocommerce'); ?></h3>
    <p><?php _e('Export and download your product reviews in CSV format. This file can be used to import product reviews back into your Woocommerce shop.', 'product-reviews-import-export-for-woocommerce'); ?></p>
    <form action="<?php echo admin_url('admin.php?page=wf_pr_rev_csv_im_ex&action=export'); ?>" method="post">

        <table class="form-table">
            <tr>
                <th>
                    <label for="v_prods"><?php _e('Products', 'product-reviews-import-export-for-woocommerce'); ?></label>
                </th>
                <td>
                    <select id="v_prods" name="products[]" data-placeholder="<?php _e('All product', 'product-reviews-import-export-for-woocommerce'); ?>" class="wc-enhanced-select" multiple="multiple">
                        <?php
                        $args = array(
                            'posts_per_page' => -1,
                            'post_type' => 'product',
                            'post_status' => 'publish',
                            'suppress_filters' => true
                        );
                        $products = get_posts($args);
                        foreach ($products as $product) {
                            echo '<option value="' . $product->ID . '">' . $product->post_title . '</option>';
                        }
                        ?>
                    </select>

                    <p style="font-size: 12px"><?php _e('Selected product/s will be exported. If left blank, all reviews will be exported.', 'product-reviews-import-export-for-woocommerce'); ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="v_replycolumn"><?php _e('Review with replies', 'wf_csv_import_export'); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="v_replycolumn" id="v_replycolumn" class="input-text">
                    <p style="font-size: 12px"><?php _e('Check to export reviews with replies', 'wf_csv_import_export'); ?></p>
                </td>
            </tr>  
        </table>
        <p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Export Product Reviews', 'product-reviews-import-export-for-woocommerce'); ?>" /></p>
    </form>
</div>