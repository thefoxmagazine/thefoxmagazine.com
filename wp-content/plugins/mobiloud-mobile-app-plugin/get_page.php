<?php if ( ! defined( 'ABSPATH' ) ) {
	include( "../../../wp-blog-header.php" );
}


$page_ID = sanitize_text_field( $_GET["page_ID"] );


$page = get_page( $page_ID );


if ( isset( $_GET["full"] ) ) {
	//redirect
	$link = get_permalink( $page_ID );
	header( "Location: $link" );
	exit;
}

$post    = $page;
$post_id = $page_ID;
include( "post/post.php" );

?>