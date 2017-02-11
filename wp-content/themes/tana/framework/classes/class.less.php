<?php

class TTLess{

    public static function get_less_variables(){
        $instanse = new TTLess();
        return $instanse->get_variables();
    }

    // call from customizer
    public static function get_compiled_css(){
        $instanse = new TTLess();
        return $instanse->generate_css(true);
    }


    // call from customizer
    public static function build_css(){
        $instanse = new TTLess();
        return $instanse->create_css(true);
    }

    // call from customizer
    public static function reset_css(){
        $instanse = new TTLess();
        return $instanse->create_css();
    }

    // return created css file path
    public static function get_less_path(){
        return array(
                    'path' => get_theme_mod('less_css_path', ''),
                    'url'  => get_theme_mod('less_css_url', '')
                );
    }

    // build less
    public function create_css( $modify_vars=false ){
        $upload_dir = wp_upload_dir();
        $css_file = array(
            'path' => trailingslashit($upload_dir['path']) . wp_get_theme()->template . '.css',
            'url'  => trailingslashit($upload_dir['url']) . wp_get_theme()->template . '.css'
        );

        global $wp_filesystem;
        if( empty($wp_filesystem) ){
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        $css_content = $this->generate_css($modify_vars);
        $wp_filesystem->put_contents( $css_file['path'], $css_content, 0644);

        set_theme_mod('less_css_path', $css_file['path']);
        set_theme_mod('less_css_url', $css_file['url']);

        return false;
    }



    public function get_variables(){
        $less_variables = array();
        $variables = ThemetonStd::fs_get_contents_array(get_template_directory() . "/less/variables.less");
        foreach ($variables as $str) {
            $line = trim($str . '');
            if( substr($line, 0, 2)!="//" && strlen($line)>3 && substr($line, 0, 1)=="@" ){
                $splits = explode(':', $line);
                $variable = trim( str_replace('@', '', $splits[0]) );
                $value = trim($splits[1]);
                if( strpos($value, '//')!==false ){
                    $pos = explode('//', $value);
                    $value = trim($pos[0]);
                }
                $value = str_replace(';', '', $value);
                $value = str_replace('"', '', $value);
                $value = str_replace("'", "", $value);

                $less_variables[$variable] = $value;
            }
        }

        return $less_variables;
    }



    public function generate_css( $modify_vars=false ){
        require_once get_template_directory() . '/framework/classes/lib.lessc.inc.php';
        $css = '';
        try{
            $less_file = get_template_directory() . '/less/style.less';
            $theme_uri = trailingslashit(get_template_directory_uri());
            
            $parser = new Less_Parser();
            $parser->parseFile( $less_file, $theme_uri );

            if($modify_vars){
                $modified_vars = array();
                $variables = $this->get_variables();
                foreach ($variables as $key => $value) {
                    $mod_value = ThemetonStd::get_mod($key);
                    if( strpos($mod_value, "darken(")!==false && strpos($mod_value, "%")===false )
                        $mod_value .= "%)";
                    if( !empty($mod_value) && $mod_value!=$value && $mod_value!='default' ){
                        $mod_value = trim($mod_value);
                        $modified_vars = array_merge( $modified_vars, array( $key=>$mod_value ) );
                    }
                }

                // change fonts
                $controls = get_customizer_controls();
                $font_controls = array();
                foreach ($controls as $control) {
                    if( isset($control['type']) && $control['type']=='font_set' ){
                        $v = ThemetonStd::get_mod( $control['id'], $control['default'] );
                        $vals = ThemetonStd::get_option_array($v);
                        foreach ($vals as $key => $value) {
                            $font_controls[$key] = $value;
                        }
                    }
                }
                foreach ($font_controls as $key => $value) {
                    if( isset($variables[$key]) && !empty($value) && $variables[$key]!=$value )
                        $modified_vars = array_merge( $modified_vars, array( $key=>$value ) );
                }
                
                if( !empty($modified_vars) )
                    $parser->ModifyVars($modified_vars);
            }

            $css = $parser->getCss();

            $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
            $css = str_replace(': ', ':', $css);
            $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

        }
        catch(Exception $e){
            error_log($e->getMessage());
        }

        return $css;
    }

}



class Themeton_Init_Custom_CSS{
    function __construct(){
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script') );
    }

    public function enqueue_script(){
        // theme customized style
        $less_css_path = TTLess::get_less_path();
        if( file_exists($less_css_path['path']) ){
            wp_enqueue_style( 'themeton-custom-stylesheet', $less_css_path['url'] );
        }
        else if( file_exists(get_template_directory()."/css/default.css") ){
            wp_enqueue_style( 'themeton-custom-stylesheet', trailingslashit(get_template_directory_uri()) . "css/default.css" );
        }
    }
}

new Themeton_Init_Custom_CSS();
