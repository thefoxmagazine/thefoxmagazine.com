<?php

class WPBakeryShortCode_Tana_Ads_Box extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'image' => '',
            'title' => esc_html__("Title", 'tana'),
            'desc' => esc_html__("Description", 'tana'),
            'descbottom' => '0',
            'link' => '#',
            'extra_class' => ''
        ), $atts));


        $thumb = wp_get_attachment_image_src($image, 'tana-blog-square');
        $thumb = $thumb[0];

        $titledesc = $descbottom == '1' ? "<span class='font28'>$title</span><br>$desc" : "$desc<br><span class='font28'>$title</span>";


        $result = sprintf('<div class="post first hover-dark cart-hover">
                                <a href="%1$s">
                                    <div class="image" data-src="%2$s">
                                        <img src="%3$s" alt="%4$s">
                                    </div>
                                </a>
                                <h4 class="post-title"><a href="%1$s">%5$s</a></h4>
                            </div>', $link, $thumb, get_template_directory_uri().'/images/1x1.png', $title, $titledesc);

        // return result
        return $result;

    }

}


// Element options
vc_map( array(
    "name" => esc_html__('Ads Box', 'tana'),
    "description" => esc_html__("Image Ads Style", 'tana'),
    "base" => 'tana_ads_box',
    "icon" => "tana-vc-icon tana-vc-icon10",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            'type' => 'attach_image',
            "param_name" => "image",
            "heading" => esc_html__("Image", 'tana'),
            "value" => ''
        ),
        array(
            "type" => "textfield",
            "param_name" => "desc",
            "heading" => esc_html__("Description", 'tana'),
            "value" => esc_html__("Description", 'tana')
        ),
        array(
            "type" => 'textfield',
            "param_name" => "title",
            "heading" => esc_html__("Title", 'tana'),
            "value" => esc_html__("Title", 'tana'),
            "holder" => 'div'
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "descbottom",
            "heading" => esc_html__("Description at bottom of title", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
        ),
        array(
            "type" => "textfield",
            "param_name" => "link",
            "heading" => esc_html__("Link", 'tana'),
            "value" => '#'
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