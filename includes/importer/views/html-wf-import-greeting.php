<div class="tool-box bg-white p-20p pipe-view">
    <p><?php _e('You can import product reviews (in CSV format) in to the shop using any of below methods.', 'wf_pr_rev_import_export'); ?></p>

<?php if (!empty($upload_dir['error'])) : ?>
        <div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:', 'wf_pr_rev_import_export'); ?></p>
            <p><strong><?php echo $upload_dir['error']; ?></strong></p></div>
<?php else : ?>
        <form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="upload"><?php _e('Method 1: Select a file from your computer', 'wf_pr_rev_import_export'); ?></label>
                        </th>
                        <td>
                            <input type="file" id="upload" name="import" size="25" />
                            <input type="hidden" name="action" value="save" />
                            <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
                            <small><?php printf(__('Maximum size: %s', 'wf_pr_rev_import_export'), $size); ?></small>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e('Upload file and import', 'wf_pr_rev_import_export'); ?>" />
            </p>
        </form>
<?php endif; ?>
</div>
<?php include(WF_ROOT_FILE_PATH . 'includes/views/market.php'); ?>