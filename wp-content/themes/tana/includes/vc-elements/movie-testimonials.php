<?php

class WPBakeryShortCode_Tt_Movie_Quote extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'name' => 'Dave Clar',
            'time' => 'in 1985',
            'quote' => 'The exit will be a humble and start swift fall from power for a man.',
            'image' => '',
            'position' => 'Inventor',
            'link' => 'http://themeforest.net/user/themeton',
            'linktext' => 'Continue to the news',
            'color' => 'color-1',
            'extra_class' => ''
        ), $atts));


        // Preparing qute contents
        $tcontent = wpb_js_remove_wpautop( $quote, true );

        // Avatar image
        if( !empty($image) ){
            $image = wp_get_attachment_image_src($image, 'thumbnail');
            $image = "<a href='".get_permalink()."'><img class='image image-thumb border-radius' src='".$image[0]."' alt='".esc_attr__('Image','tana')."'></a>";
        }

        // Meta link
        $metamarkup = $name !== '' ? "<div class='meta bullet-style'><span class='author'>$name</span><span class='date'>".$time."</span></div>" : "";
        $linkmarkup = "<a href='".esc_attr($link)."' class='category-more'>$linktext <img src='".get_template_directory_uri()."/images/arrow-right.png' alt='".esc_attr__('Arrow', 'tana')."'></a>";

        // Label
        $labelmarkup = sprintf( "<a href='%s' class='label'>%s</a>", esc_attr($link), $position );

        // Extra class
        $extra_class = esc_attr($extra_class);


        // Final result
        $result = "<div class='post en-block quote $color $extra_class'>

                $metamarkup
                <blockquote>$tcontent</blockquote>
                <div class='author clearfix'>
                    $image
                    $labelmarkup
                </div>
                <!-- /.meta -->
                $linkmarkup
                
            </div>
            <!-- .post -->";

        return $result;

    }

}

// Element options
vc_map( array(
    "name" => esc_html__('Testimonial', 'tana'),
    "description" => esc_html__("Quote Testimo Design", 'tana'),
    "base" => 'tt_movie_quote',
    "icon" => "tana-vc-icon tana-vc-icon15",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
        

        array(
            'type' => 'textfield',
            'heading' => esc_html__('Title', 'tana'),
            'param_name' => 'name',
            'std' => 'Steve Jobs',
            'holder' => 'div'
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Time / sub text (optional)', 'tana'),
            'param_name' => 'time',
            'std' => 'in 1985'
        ),
        array(
            'type' => 'textarea',
            'heading' => esc_html__('Testimonial', 'tana'),
            'param_name' => 'quote',
            'std' => 'The exit will be a humble and start swift fall from power for a man.'
        ),
        array(
            'type' => 'attach_image',
            "param_name" => "image",
            "heading" => esc_html__("Image", 'tana'),
            "value" => ''
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Position / Label', 'tana'),
            'param_name' => 'position',
            'std' => 'Inventor'
        ),
        array(
            'type' => 'vc_link',
            'heading' => esc_html__('Link address', 'tana'),
            'param_name' => 'link',
            'std' => 'http://themeforest.net/user/themeton',
            'description' => 'http://themeforest.net'
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Link text', 'tana'),
            'param_name' => 'linktext',
            'std' => 'Continue to the news'
        ),

        array(
            "type" => "dropdown",
            "param_name" => "color",
            "heading" => esc_html__("Layout options", 'tana'),
            "value" => array(
                "Color 1" => "color-1",
                "Color 2" => "color-2",
                "Color 3" => "color-3",
                "Color 4" => "color-4",
                "Color 5" => "color-5",
                "Color 6" => "color-6"
            ),
            "std" => 'color-1',
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