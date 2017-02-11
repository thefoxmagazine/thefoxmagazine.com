<?php

class WPBakeryShortCode_Tt_Movie_Testimonial extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        extract(shortcode_atts(array(
            'list' => '',
            'autoplay' => '0',
            'autoplay_seconds' => '5',
            'imagetop' => '0',
            'extra_class' => ''
        ), $atts));

        // Auto play
        $autoplay_seconds = $autoplay == '1' ? $autoplay_seconds : '0';

        $list = vc_param_group_parse_atts($list);
        $slides = '';


        if( is_array($list) ){
            foreach ($list as $item) {
                $image = isset($item['image']) ? $item['image'] : "";
                $tname = isset($item['tname']) ? $item['tname'] : "";
                $tcontent = isset($item['tcontent']) ? $item['tcontent'] : "";

                $tcontent = wpb_js_remove_wpautop( $tcontent, true );

                if( !empty($image) ){
                    $image = wp_get_attachment_image_src($image, 'full');
                    $image = "<img src='".$image[0]."' alt='".esc_attr__('Image', 'tana')."' style='margin-top:-".abs($imagetop)."px;'/>";
                }

                $slides .= "<div class='swiper-slide'>
                            <div class='swiper-holder' style='margin-top:".abs($imagetop)."px;'>

                                $image
                                <blockquote>
                                    <cite>$tname</cite>
                                    <p>$tcontent</p>
                                </blockquote>
                            </div>
                            <!-- /.swiper-holder -->

                        </div>
                        <!-- /.swiper-slide -->";

            }
        }


        $extra_class = esc_attr($extra_class);

        return "<div class='testimonial-slider fs-blog-carousel $extra_class' data-col='1' data-row='1' data-responsive='1,1,1' data-autoplay='".(abs($autoplay_seconds)*1000)."'>
            <div class='swiper-container'>
                <div class='swiper-wrapper'>

                    $slides

                </div>
                <!-- /.swiper-wrapper -->

                <div class='fs-pager'>
                    <span>
                        <i class='fs-current-index'>1</i> / <i class='fs-current-total'>1</i>
                    </span>
                </div>

            </div>
            <!-- /.swiper-container -->

            <div class='swiper-button-prev swiper-prev'>
                <i class='fa fa-angle-left'></i> <span>".esc_html__('Prev', 'tana')."</span>
            </div>
            <div class='swiper-button-next swiper-next'>
                <span>".esc_html__('Next', 'tana')."</span> <i class='fa fa-angle-right'></i>
            </div>
        </div>
        <!-- /.testimonial-slider -->";

    }

}

vc_map( array(
    "name" => esc_html__('Testimonial Slider', 'tana'),
    "description" => esc_html__("Movie Quote Carousel Slider", 'tana'),
    "base" => 'tt_movie_testimonial',
    "icon" => "tana-vc-icon tana-vc-icon05",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
        
        array(
            'type' => 'param_group',
            'heading' => esc_html__('Testimonials Content', 'tana'),
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
                    'heading' => esc_html__('Name', 'tana'),
                    'param_name' => 'tname',
                    'admin_label' => true,
                    'holder' => 'div'
                ),
                array(
                    'type' => 'textarea',
                    'heading' => esc_html__('Testimonial', 'tana'),
                    'param_name' => 'tcontent'
                )
            )
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "autoplay",
            "heading" => esc_html__("Autoplay", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0",
        ),
        array(
            'type' => 'textfield',
            "param_name" => "autoplay_seconds",
            "heading" => esc_html__("Autoplay", 'tana'),
            'value' => "5",
            "std" => "5",
            "dependency" => Array("element" => "autoplay", "value" => array('1'))
        ),
        array(
            'type' => 'textfield',
            "param_name" => "imagetop",
            "heading" => esc_html__("Image top overflow", 'tana'),
            'value' => "0",
            "std" => "0",
            "description" => esc_html__("Like movie demo example you can overflow at top user image. Number value but performs in PX.", 'tana'),
        ),

        array(
            "type" => "textfield",
            "param_name" => "extra_class",
            "heading" => esc_html__("Extra Class", 'tana'),
            "value" => "",
            "description" => esc_html__("If you wish text to white, you should add class \"text-light\". If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'tana'),
        )
    )
));