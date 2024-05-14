<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Reserved column names
return array(
	'comment_ID',
	'comment_post_ID',
        'comment_author',
        'comment_author_url',
	'comment_author_email',
	'comment_date',
	'comment_date_gmt',
	'comment_content',
	//'comment_karma',
	'comment_approved',
	'comment_parent',
	'user_id',
	'rating',
	'verified'
);