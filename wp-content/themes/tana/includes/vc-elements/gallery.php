<?php

class WPBakeryShortCode_Tana_Gallery extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'images' => '',
            'extra_class' => ''
        ), $atts));

		if ( '' === $images ) {
			$images = '-1,-2,-3';
		}
        
        // return result
        return Tana_Tpl::gallery_slideshow( array( 'ids'=>$images ) );

    }

}




// Element options
vc_map( array(
    "name" => esc_html__('Gallery Slider', 'tana'),
    "description" => esc_html__("Image with Titles", 'tana'),
    "base" => 'tana_gallery',
    "icon" => "tana-vc-icon tana-vc-icon10",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
		
		array(
			'type' => 'attach_images',
			'heading' => __( 'Images', 'tana' ),
			'param_name' => 'images',
			'value' => '',
			'description' => __( 'Select images from media library.', 'tana' ),
			'holder' => 'div'
		),
        array(
            "type" => "textfield",
            "param_name" => "extra_class",
            "heading" => esc_html__("Extra Class", 'tana'),
            "value" => "",
            "description" => esc_html__("If you wish text to white. If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'tana'),
        )
    )
));