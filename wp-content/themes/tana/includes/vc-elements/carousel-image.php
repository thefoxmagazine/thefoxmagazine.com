<?php

class WPBakeryShortCode_Tana_Image_Carousel extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        extract(shortcode_atts(array(
            'list' => '',
            'extra_class' => ''
        ), $atts));


        $list = vc_param_group_parse_atts($list);
        $slides = '';

        if( is_array($list) ){
            foreach ($list as $item) {
                $image = isset($item['image']) ? $item['image'] : "";
                $title = isset($item['title']) ? $item['title'] : "";
                $link = isset($item['link']) ? $item['link'] : "";

                if( !empty($image) ){
                    $image = wp_get_attachment_image($image, 'full');
                }

                $slides .= sprintf('<div class="swiper-slide">
                                        <div class="ws-item">
                                            <a href="%s">%s</a>
                                            <h3><a href="%s">%s</a></h3>
                                        </div>
                                    </div>', $link, $image, $link, $title);

            }
        }

        return sprintf('<div class="wslider %s">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">%s</div>
                            </div>
                            <div class="ws-arrows">
                                <a href="javascript:;" class="ws-arrow-prev"><i class="fa fa-angle-left"></i></a>
                                <a href="javascript:;" class="ws-arrow-next"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>', esc_attr($extra_class), $slides);

    }

}

vc_map( array(
    "name" => esc_html__('Image Carousel', 'tana'),
    "description" => esc_html__("Image fullwidth carousel", 'tana'),
    "base" => 'tana_image_carousel',
    "icon" => "tana-vc-icon tana-vc-icon04",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
        
        array(
            'type' => 'param_group',
            'heading' => esc_html__('Carousel Items', 'tana'),
            'param_name' => 'list',
            'params' => array(

                array(
                    'type' => 'attach_image',
                    "param_name" => "image",
                    "heading" => esc_html__("Image", 'tana'),
                    "value" => ''
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Title', 'tana'),
                    'param_name' => 'title',
                    "value" => esc_html__('Title', 'tana'),
                    'admin_label' => true,
                    'holder' => 'div'
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Link', 'tana'),
                    'param_name' => 'link',
                    'value' => '#'
                )
            )
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