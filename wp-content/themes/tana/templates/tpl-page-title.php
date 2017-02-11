<?php
$page_title = Tana_Tpl::get_page_title();
$page_desc = $page_title;
if( is_page() ){
	$page_meta_desc = Tana_Std::getmeta('page_desc');
	if( !empty($page_meta_desc) ){
		$page_desc = $page_meta_desc;
	}
}
else if( is_category() ){
	$term = get_queried_object();
	$cat_desc = category_description($term->term_id);
	if( !empty($cat_desc) ){
		$page_desc = $cat_desc;
	}
}

?>
<h2 class="title-block mt3 mb5" data-title="<?php echo esc_attr($page_desc); ?>"><?php printf('%s', $page_title); ?></h2>