<?php

class WPBakeryShortCode_Tana_Woo extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        $atts = shortcode_atts( array(
            'list_type' => 'sale_products',
            'list_style' => 'default',
            'per_page' => '12',
            'columns'  => '4',
            'orderby'  => '',
            'order'    => '',
            'attribute' => '',
            'filter'    => '',
            'category'  => '',
            'ids'       => '',
            'skus'      => ''
        ), $atts, 'tana_woo' );

        if( class_exists('WC_Shortcodes') ){
            $result = '';

            switch($atts['list_type']){
                case 'sale_products':
                    $result = WC_Shortcodes::sale_products( $atts );
                    break;
                case 'best_selling_products':
                    $result = WC_Shortcodes::best_selling_products( $atts );
                    break;
                case 'top_rated_products':
                    $result = WC_Shortcodes::top_rated_products( $atts );
                    break;
                case 'featured_products':
                    $result = WC_Shortcodes::featured_products( $atts );
                    break;
                case 'product_attribute':
                    $result = WC_Shortcodes::product_attribute( $atts );
                    break;
                case 'product_category':
                    $result = WC_Shortcodes::product_category( $atts );
                    break;
                case 'products':
                    $result = WC_Shortcodes::products( $atts );
                    break;
                case 'recent_products':
                    $result = WC_Shortcodes::recent_products( $atts );
                    break;
            }

            if( $atts['list_style']=='card' ){
                $result = preg_replace('/ class="/', ' class="woo-card-style ', $result, 1);
            }

            return $result;
        }
        else{
            return '';
        }

    }

}

// Element options
if( class_exists('WooCommerce') ):

    function themeton_woo_getCategoryChildsFull( $parent_id, $pos, $array, $level, &$dropdown ) {
        for ( $i = $pos; $i < count( $array ); $i ++ ) {
            if ( $array[ $i ]->category_parent == $parent_id ) {
                $name = str_repeat( '- ', $level ) . $array[ $i ]->name;
                $value = $array[ $i ]->slug;
                $dropdown[] = array(
                    'label' => $name,
                    'value' => $value,
                );
                themeton_woo_getCategoryChildsFull( $array[ $i ]->term_id, $i, $array, $level + 1, $dropdown );
            }
        }
    }
    
    $attributes_tax = wc_get_attribute_taxonomies();
    $attributes = array();
    foreach ( $attributes_tax as $attribute ) {
        $attributes[ $attribute->attribute_label ] = $attribute->attribute_name;
    }

    $woo_categories = get_categories( array(
        'type' => 'post',
        'child_of' => 0,
        'parent' => '',
        'orderby' => 'id',
        'order' => 'ASC',
        'hide_empty' => false,
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'number' => '',
        'taxonomy' => 'product_cat',
        'pad_counts' => false,

    ));
    $product_categories_dropdown = array();
    themeton_woo_getCategoryChildsFull( 0, 0, $woo_categories, 0, $product_categories_dropdown );

    vc_map( array(
        "name" => esc_html__('Tana Woocommerce', 'tana'),
        "description" => esc_html__("List of products", 'tana'),
        "base" => 'tana_woo',
        "icon" => "icon-wpb-woocommerce",
        "content_element" => true,
        "category" => 'Tana',
        "class" => 'tana-vc-element',
        'params' => array(
            
            array(
                "type" => "dropdown",
                "param_name" => "list_type",
                "heading" => esc_html__("List type", 'tana'),
                "value" => array(
                    esc_html__('Sale products', 'tana')         => 'sale_products',
                    esc_html__('Best selling products', 'tana') => 'best_selling_products',
                    esc_html__('Top rated products', 'tana')    => 'top_rated_products',
                    esc_html__('Featured products', 'tana')     => 'featured_products',
                    esc_html__('Product attribute', 'tana')     => 'product_attribute',
                    esc_html__('Product category', 'tana')      => 'product_category',
                    esc_html__('Products', 'tana')              => 'products',
                    esc_html__('Recent products', 'tana')       => 'recent_products'
                ),
                "std" => "products",
                "holder" => "div"
            ),


            array(
                "type" => "dropdown",
                "param_name" => "list_style",
                "heading" => esc_html__("List Style", 'tana'),
                "value" => array(
                    esc_html__('Default', 'tana')    => 'default',
                    esc_html__('Card Style', 'tana') => 'card'
                ),
                "std" => "default"
            ),

            array(
                'type' => 'textfield',
                'heading' => esc_html__('Per page', 'tana'),
                'value' => 12,
                'save_always' => true,
                'param_name' => 'per_page',
                'description' => esc_html__('How much items per page to show', 'tana'),
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'sale_products',
                        'top_rated_products',
                        'featured_products',
                        'product_attribute',
                        'product_category',
                        'recent_products',
                        'best_selling_products'
                    )
                )
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Columns', 'tana'),
                'value' => 4,
                'save_always' => true,
                'param_name' => 'columns',
                'description' => esc_html__('How much columns grid', 'tana'),
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'sale_products',
                        'top_rated_products',
                        'featured_products',
                        'product_attribute',
                        'product_category',
                        'recent_products',
                        'best_selling_products',
                        'products'
                    )
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Order by', 'tana'),
                'param_name' => 'orderby',
                'value' => array(
                    '',
                    esc_html__('Date', 'tana') => 'date',
                    esc_html__('ID', 'tana') => 'ID',
                    esc_html__('Author', 'tana') => 'author',
                    esc_html__('Title', 'tana') => 'title',
                    esc_html__('Modified', 'tana') => 'modified',
                    esc_html__('Random', 'tana') => 'rand',
                    esc_html__('Comment count', 'tana') => 'comment_count',
                    esc_html__('Menu order', 'tana') => 'menu_order',
                ),
                'save_always' => true,
                'description' => sprintf( esc_html__('Select how to sort retrieved products. More at %s.', 'tana'), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
                
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'sale_products',
                        'top_rated_products',
                        'featured_products',
                        'product_attribute',
                        'product_category',
                        'recent_products',
                        'products'
                    )
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Sort order', 'tana'),
                'param_name' => 'order',
                'value' => array(
                    '',
                    esc_html__('Descending', 'tana') => 'DESC',
                    esc_html__('Ascending', 'tana') => 'ASC',
                ),
                'save_always' => true,
                'description' => sprintf( esc_html__('Designates the ascending or descending order. More at %s.', 'tana'), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),

                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'sale_products',
                        'top_rated_products',
                        'featured_products',
                        'product_attribute',
                        'product_category',
                        'recent_products',
                        'products'
                    )
                )
            ),


            array(
                'type' => 'dropdown',
                'heading' => esc_html__( 'Attribute', 'tana' ),
                'param_name' => 'attribute',
                'value' => $attributes,
                'save_always' => true,
                'description' => esc_html__( 'List of product taxonomy attribute', 'tana' ),
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'product_attribute'
                    )
                )
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__('Filter', 'tana'),
                'param_name' => 'filter',
                'value' => array( 'empty' => 'empty' ),
                'save_always' => true,
                'description' => esc_html__('Taxonomy values', 'tana'),
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'product_attribute'
                    ),
                    'callback' => 'vcWoocommerceProductAttributeFilterDependencyCallback'
                )
            ),



            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Category', 'tana'),
                'value' => $product_categories_dropdown,
                'param_name' => 'category',
                'save_always' => true,
                'description' => esc_html__( 'Product category list', 'tana' ),
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'product_category'
                    )
                )
            ),


            array(
                'type' => 'autocomplete',
                'heading' => esc_html__( 'Products', 'tana' ),
                'param_name' => 'ids',
                'settings' => array(
                    'multiple' => true,
                    'sortable' => true,
                    'unique_values' => true
                ),
                'save_always' => true,
                'description' => esc_html__( 'Enter List of Products', 'tana' ),
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'products'
                    )
                )
            ),
            array(
                'type' => 'hidden',
                'param_name' => 'skus',
                'dependency' => array(
                    'element' => 'list_type',
                    'value' => array(
                        'products'
                    )
                )
            )
            
        )
    ));
endif;