<?php

class WPBakeryShortCode_Tt_Title extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        extract(shortcode_atts(array(
            'title' => 'Title',
            'style' => 'middle',
            'background' => '',
            'align' => 'left',
            'border' => '1',
            'borderwidth' => '0',
            'link' => '0',
            'linkurl' => 'http://',
            'linktext' => esc_attr__('Continue to the category', 'tana'),
            'extra_class' => ''
        ), $atts));


        $class = esc_attr($extra_class);
        if( $border == '1' ) { 
            $class = 'title-border ' . $class;
            if( $borderwidth == '1' ) { $class = 'width-auto ' . $class; }
        }
        
        if( $align != 'left' ) { $class = "text-$align " . $class; }

        if(  $link == '1' ) {
            $title .= " <a href='".esc_url($linkurl)."' class='category-more text-right'>$linktext <img src='".get_template_directory_uri()."/images/arrow-right.png' alt='".esc_attr__('Arrow','tana')."'></a>";
        }


        if( $style == 'block' ) {
            $result = "<h2 class='title-block mt5 mb2 $class' data-title='$background'>$title</h2>";
        } else if ( $style == 'middle' ) {
            $result = "<h3 class='title-middle $class'>$title</h3>";
        } else if ( $style == 'small' ) {
            $result = "<h4 class='title-small $class'>$title</h4>";
        }


        return $result;
    }
}

vc_map( array(
    "name" => esc_html__('Title element', 'tana'),
    "description" => esc_html__("Heading variations", 'tana'),
    "base" => 'tt_title',
    "icon" => "tana-vc-icon tana-vc-icon07",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
        array(
            "type" => 'textfield',
            "param_name" => "title",
            "heading" => esc_html__("Title", 'tana'),
            "holder" => 'div'
        ),

        array(
            "type" => "dropdown",
            "param_name" => "style",
            "heading" => esc_html__("Layout options", 'tana'),
            "value" => array(
                "Big block title" => "block",
                "Middle title" => "middle",
                "Small title" => "small"
            ),
            "std" => "middle",
            "holder" => "div"
        ),

        array(
            "type" => "textfield",
            "param_name" => "background",
            "heading" => esc_html__("Background text (optional)", 'tana'),
            "value" => "",
            "dependency" => Array("element" => "style", "value" => array("block"))
        ),

        array(
            'type' => 'dropdown',
            "param_name" => "align",
            "heading" => esc_html__("Text Align", 'tana'),
            "value" => array(
                "Left" => "left",
                "Center" => "center",
                "Right" => "right"
            ),
            "std" => "left"
        ),


        array(
            'type' => 'checkbox',
            "param_name" => "border",
            "heading" => esc_html__("Border", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1",
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "borderwidth",
            "heading" => esc_html__("Border width auto", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0",
            "dependency" => Array("element" => "border", "value" => array("1"))
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "link",
            "heading" => esc_html__("Link at right", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
        ),
        array(
            'type' => 'textfield',
            "param_name" => "linkurl",
            "heading" => esc_html__("Link url", 'tana'),
            'value' => 'http://',
            "dependency" => Array("element" => "link", "value" => array("1"))
        ),
        array(
            'type' => 'textfield',
            "param_name" => "linktext",
            "heading" => esc_html__("Link text", 'tana'),
            'value' => esc_html__( 'Continue to the category', 'tana' ),
            "dependency" => Array("element" => "link", "value" => array("1"))
        ),


        array(
            "type" => "textfield",
            "param_name" => "extra_class",
            "heading" => esc_html__("Extra Class", 'tana'),
            "value" => "",
            "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'tana'),
        )
    )
));