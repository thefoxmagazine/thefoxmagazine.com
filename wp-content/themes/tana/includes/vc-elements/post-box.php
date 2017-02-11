<?php

class WPBakeryShortCode_Tana_Post_Box extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'image' => '',
            'title' => esc_html__("Title", 'tana'),
            'desc' => esc_html__("Description", 'tana'),
            'link' => '#',
            'label_color' => '',
            'text_color' => 'fn-light',
            'extra_class' => ''
        ), $atts));


        $thumb = wp_get_attachment_image($image, 'tana-blog-square');
        $thumb = !empty($thumb) ? $thumb : sprintf( '<img src="%s/images/1x1.png" alt="'.esc_attr__('Box', 'tana').'">', get_template_directory_uri() );

        $label_color = !empty($label_color) ? sprintf( ' style="background-color:%s;"', esc_attr($label_color) ) : '';

        // result
        $result = sprintf('<div class="fn-postbox %5$s">
                                <a href="%2$s">%1$s</a>
                                <div class="postbox-meta">
                                    <h6><a href="%2$s"%6$s><span data-title="%3$s">%3$s</span></a></h6>
                                    <h3><a href="%2$s"><span data-title="%4$s">%4$s</span></a></h3>
                                </div>
                            </div>', $thumb, $link, esc_attr($title), esc_attr($desc), esc_attr($text_color), $label_color );

        // return result
        return $result;

    }

}




// Element options
vc_map( array(
    "name" => esc_html__('Fashion Box', 'tana'),
    "description" => esc_html__("Image with Titles", 'tana'),
    "base" => 'tana_post_box',
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
            "type" => 'textfield',
            "param_name" => "title",
            "heading" => esc_html__("Title", 'tana'),
            "value" => esc_html__("Title", 'tana'),
            "holder" => 'div'
        ),

        array(
            "type" => "textfield",
            "param_name" => "desc",
            "heading" => esc_html__("Description", 'tana'),
            "value" => esc_html__("Description", 'tana')
        ),

        array(
            "type" => "textfield",
            "param_name" => "link",
            "heading" => esc_html__("Link", 'tana'),
            "value" => '#'
        ),

        array(
            'type' => 'colorpicker',
            "param_name" => "label_color",
            "heading" => esc_html__("Title Label Color", 'tana'),
            "value" => '',
        ),

        array(
            "type" => "dropdown",
            "param_name" => "text_color",
            "heading" => esc_html__("Text Color", 'tana'),
            "value" => array(
                "Light" => "fn-light",
                "Dark" => "fn-dark"
            ),
            "std" => "fn-light"
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