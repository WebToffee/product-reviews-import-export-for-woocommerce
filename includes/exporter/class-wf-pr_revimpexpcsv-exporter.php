<?php

if (!defined('ABSPATH')) {
    exit;
}

class WF_PrRevImpExpCsv_Exporter {

    /**
     * Product Reviews Exporter Tool
     */
    public static function do_export() {
        global $wpdb;
        $export_limit = 99999;
        $delimiter = ',';
        $products = !empty($_POST['products']) ? array_map('intval',$_POST['products']) : '';
        $csv_columns = include( 'data/data-wf-post-columns.php' );
        $user_columns_name = $csv_columns;
        $export_columns = '';
        $export_reply = !empty($_POST['v_replycolumn']) ? '1' : '';


        $wpdb->hide_errors();
        @set_time_limit(0);
        if (function_exists('apache_setenv'))
            @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 0);
        @ob_clean();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=woocommerce-product-reviews-export-' . date('Y_m_d_H_i_s', current_time('timestamp')) . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        $fp = fopen('php://output', 'w');


        // Headers
        $all_meta_keys = array('rating');

        // Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.
        foreach ($all_meta_keys as $meta) {
            if (in_array($meta, array_keys($csv_columns)))
                continue;
        }

        // Variable to hold the CSV data we're exporting
        $row = array();

        // Export header rows
        foreach ($csv_columns as $column => $value) {

            $temp_head = esc_attr($user_columns_name[$column]);
            if (!$export_columns)
                $row[] = $temp_head;
        }

        $row = array_map('WF_PrRevImpExpCsv_Exporter::wrap_column', $row);
        fwrite($fp, implode($delimiter, $row) . "\n");
        unset($row);
        $args = apply_filters('product_reviews_csv_product_export_args', array(
            'status' => 'all',
            'orderby' => 'comment_ID',
            'order' => 'ASC',
            'post_type' => 'product',
            'number' => $export_limit,
            'parent' => 0,
        ));
        
        if ($export_reply === '1') {
            unset($args['parent']);
        }

        if (!empty($products)) {
            $args['post__in'] = implode(',', $products);
        }
            $comments_query = new WP_Comment_Query;
            $comments = $comments_query->query($args);
            foreach ($comments as $comment) {
                self::wt_import_to_csv($comment, $csv_columns, $export_columns, $delimiter, $fp, $comments);

            }

        fclose($fp);
        exit;
    }
    
    
        public static function wt_import_to_csv($comment, $csv_columns, $export_columns, $delimiter, $fp, $comments) {
            
             $row = array();
                $comment_ID = $comment->comment_ID;
                $obj = new WF_PrRevImpExpCsv_Exporter();
                $meta_data = $obj->get_all_meta_data($comment_ID);
                $comment->meta = new stdClass;
                $comment->meta->rating = get_comment_meta($comment_ID, 'rating', true);

                // Meta data
                foreach ($meta_data as $meta => $value) {
                    if (!$meta) {
                        continue;
                    }
                    $meta_value = maybe_unserialize(maybe_unserialize($value));

                    if (is_array($meta_value)) {
                        $meta_value = json_encode($meta_value);
                    }

                    $comment->meta->$meta = self::format_export_meta($meta_value, $meta);
                }
               
                foreach ($csv_columns as $column => $value) {
                    if (!$export_columns) {
                        if ($column === 'comment_alter_id') {
                            $row[] = self::format_data($comment_ID);
                            continue;
                        }
                      
                        if ($column === 'comment_post_ID') {
                        $temp_product_id = sanitize_text_field($comment->$column);
                        }
                        if (isset($comment->meta->$column)) {
                            $row[] = self::format_data($comment->meta->$column);
                        } elseif (isset($comment->$column) && !is_array($comments[0]->$column)) {
                            $row[] = self::format_data($comment->$column);
                        }
                        if ($column === 'product_SKU' && !empty($temp_product_id)) {
                        $row[] = (string) get_post_meta($temp_product_id, '_sku', true);
                        continue;
                    }
                     
                  }
                     
                }

                $row = array_map('WF_PrRevImpExpCsv_Exporter::wrap_column', $row);
                fwrite($fp, implode($delimiter, $row) . "\n");
                unset($row);
                return;
            
        }

    /*
     * Format the data if required
     * @param  string $meta_value
     * @param  string $meta name of meta key
     * @return string
     */

    public static function format_export_meta($meta_value, $meta) {
        return $meta_value;
    }

    public static function format_data($data) {
        if (!is_array($data))
            ;
        $data = (string) urldecode($data);
        $enc = mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true);
        $data = ( $enc == 'UTF-8' ) ? $data : utf8_encode($data);
        return self::escape_data( $data );
    }

	/**
	 * Escape a string to be used in a CSV context
	 *
	 * Malicious input can inject formulas into CSV files, opening up the possibility
	 * for phishing attacks and disclosure of sensitive information.
	 *
	 * Additionally, Excel exposes the ability to launch arbitrary commands through
	 * the DDE protocol.
	 *
	 * @see http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
	 * @see https://patchstack.com/database/report-preview/53c7a97e-2de1-4ff2-b837-d69e45f6f97c
	 *
	 * @param string $data CSV field to escape.
	 * @return string
	 */
	public static function escape_data( $data )
	{
		$active_content_triggers = array( '=', '+', '-', '@' );

		if ( in_array( mb_substr( $data, 0, 1 ), $active_content_triggers, true ) ) {
			$data = "'" . $data;
		}

		return $data;
	}	
	
    /**
     * Wrap a column in quotes for the CSV
     * @param  string data to wrap
     * @return string wrapped data
     */
    public static function wrap_column($data) {
        return '"' . str_replace('"', '""', $data) . '"';
    }

    public static function get_all_meta_data($id) {
        $meta_data = array();
        $meta_data[] = array('key' => 'rating',
            'value' => get_comment_meta($id, 'rating', true));
        return $meta_data;
    }
    


}
