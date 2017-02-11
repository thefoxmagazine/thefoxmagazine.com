<?php

class WPBakeryShortCode_Google_Map extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        extract( shortcode_atts( array(
            "lat" => '40.7797115',
            "lng" => '-74.1755574',
            "color" => '',
            "saturation" => "-100",
            "zoom" => '16',
            "map_height" => '550',
            "marker" => ''
        ), $atts ) );

        $content = wpb_js_remove_wpautop( $content, true );

        wp_enqueue_script( 'google-map-config', get_template_directory_uri() . '/js/google-maps.js');
        wp_enqueue_script( 'google-map', '//maps.googleapis.com/maps/api/js?callback=initMap');

        $image_src = !empty($marker) ? wp_get_attachment_image_src($marker, 'thumbnail') : '';
        $marker = !empty($image_src) ? $image_src[0] : '';

        $result = '<div id="tt-google-map" style="height:'.abs($map_height).'px;" class="tt-google-map" data-lat="'.esc_attr($lat).'" data-lng="'.esc_attr($lng).'" data-zoom="'.abs($zoom).'" data-saturation="'.esc_attr($saturation).'" data-color="'.esc_attr($color).'" data-marker="'.esc_attr($marker).'">
                        <div id="gmap_content">
                            <div class="entry-content">'.do_shortcode($content).'</div>
                        </div>
                    </div>';

        return $result;
    }
}

vc_map( array(
    "name" => esc_html__("Google Map", 'tana'),
    "description" => esc_html__("Google Maps Latitude, Longitude", 'tana'),
    "base" => "google_map",
    "class" => "",
    "icon" => "tana-vc-icon tana-vc-icon18",
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    "show_settings_on_create" => true,
    "params" => array(
        array(
            'type' => 'textfield',
            "param_name" => "lat",
            "heading" => esc_html__("Latitude", 'tana'),
            "value" => '',
            'description' => '-37.831208'
        ),
        array(
            'type' => 'textfield',
            "param_name" => "lng",
            "heading" => esc_html__("Longitude", 'tana'),
            "value" => '',
            "description" => '144.998499'
        ),
        
        array(
            'type' => 'colorpicker',
            "param_name" => "color",
            "heading" => esc_html__("Hue Color", 'tana'),
            "value" => '',
        ),
        array(
            'type' => 'textfield',
            "param_name" => "saturation",
            "heading" => esc_html__("Saturation", 'tana'),
            "value" => '-100',
            "description" => '(a floating point value between -100 and 100)'
        ),
        
        array(
            'type' => 'textfield',
            "param_name" => "zoom",
            "heading" => esc_html__("Zoom", 'tana'),
            "value" => '16',
            "desc"  => 'Zoom levels 0 to 18'
        ),
        array(
            'type' => 'textfield',
            "param_name" => "map_height",
            "heading" => esc_html__("Height", 'tana'),
            "value" => ''
        ),
        array(
            'type' => 'attach_image',
            "param_name" => "marker",
            "heading" => esc_html__("Marker Image", 'tana'),
            "value" => ''
        ),

        array(
            'type' => 'textarea_html',
            "param_name" => "content",
            "heading" => esc_html__("Address Content", 'tana'),
        )
        
    )
) );