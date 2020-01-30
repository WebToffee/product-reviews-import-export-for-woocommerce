<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Reserved column names
return array(
                
                'comment_ID'                    => 'Comments ID | ID of the comments',
                'comment_post_ID'               => 'Comment Post ID | ID of the product, on which the comment is done',
                'comment_author'                => 'Comments Author Name | The author name, who made comments',
                'comment_author_url'            => 'Comments Author URL | The author URL, who made comments',
                'comment_author_email'          => 'Comments Author Email | The author email, who made comments',
                'comment_date'                  => 'Comments Date | The date, when comments is done',
                'comment_date_gmt'              => 'Comments Date(GMT) | The date, when comments is done',
                'comment_content'               => 'Comments Content | The content of the comments',
                //'comment_karma'                       => 'comment_karma',
                'comment_approved'              => 'Comments Approved or Not? | 1, for YES and 0, for NO',
                'comment_parent'                => 'Comments Parent | The parent comments id',
                'user_id'                       => 'User ID | The user id who comments, if the user is GUEST USER then it is 0',

                //Meta

                'rating'                        => 'Rating | 1: for 1 star, 2: for 2 star,...'
                

    
);