<?php

if (!function_exists('add_action')) {
    die('Unauthorized access.');
}

class Videe_Widget extends WP_Widget
{
	/**
	 *
	 * @var array
	 */
    private static $config = array();
	
	/**
	 *
	 * @var ServiceLocator 
	 */
	private static $locator;
    

    private $defaultPlayerOptions = array(
        'volume' => 100,
        'autoplay' => 0,
        'loop' => 0,
        'mute' => 0,
        'async' => 1,
        'background' => 1,
        'playlist_id' => null
    );

    /**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'videe_widget', // Base ID
			__('Videe.TV', 'videe'), // Name
			array('description' => __('Embeds Videe.TV player into sidebars', 'videe'),) // Args
		);
	}

	public static function setConfig($config) {
		self::$config = array_merge(self::$config, $config);
	}
	
	private function getConfig() {
		return self::$config;
	}
	
	public static function setServiceLocator($locator) {
		self::$locator = $locator;
	}
    
	private function getServiceLocator() {
		return self::$locator;
	}

	/**
	 * Register resources
	 */
	public function loadAdminResources() {

		$config = $this->getConfig();

		wp_register_script('rangeslider', $config['videePluginUrl'] . '_inc/libs/rangeslider.min.js', array(), $config['videeVersion']);
		wp_enqueue_script('rangeslider');

		wp_register_script('select2', $config['videePluginUrl'] . '_inc/libs/select2.min.js', array('jquery'), $config['videeVersion']);
		wp_enqueue_script('select2');

		wp_register_style('select2', $config['videePluginUrl'] . '_inc/css/select2.min.css', array(), $config['videeVersion']);
		wp_enqueue_style('select2');

		wp_register_script('videe_widget_inputs', $config['videePluginUrl'] . '_inc/libs/videe_widget_inputs.js', array(), $config['videeVersion']);
		wp_enqueue_script('videe_widget_inputs', array('jquery', 'select2'));
		
		wp_register_style('videe_widget_inputs', $config['videePluginUrl'] . '_inc/css/videe_widget_inputs.css', array(), $config['videeVersion']);
        wp_enqueue_style('videe_widget_inputs');


		$params['videeToken'] = $this->getServiceLocator()->getOption('token');
		$params['videePluginUrl'] = $config['videePluginUrl'];
		$params['apiUrl'] = $config['videeApiUrl'];
		wp_localize_script('videe_widget_inputs', 'params', $params);
	}

	/**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
		$addedWidgetVideeVideo = false;
		
        echo $args['before_widget'];


        if ($this->getServiceLocator()->getOption('token')) {

            if (($playlist = $this->getWorkingPlaylist($instance))) {

                $this->registerVideeScripts();
                
                $params = array();

				$instance = array_merge($this->defaultPlayerOptions, $instance);
                
                $params["playlistid"] = $playlist['id'];
                $params["user-aid"] = (int)$this->getServiceLocator()->getOption('aid');
                $params["user-id"] = (int)$this->getServiceLocator()->getOption('userId');
                $params["size"] = "autoxauto";
                $params["load-settings"] = "false";
                $params["load-playlist"] = "true";

                $params["autoplay"] = $instance['autoplay']? "true": "false";
                $params["loop"] = $instance['loop']? "true": "false";
                $params["async"] = $instance['async']? "true": "false";
                $params["background"] = $instance['background']? "true": "false";
                $params["mute"] = $instance['mute']? "true": "false";
                $params["volume"] = $instance['volume'];


                $attributes = '';
                foreach ($params as $key => $value) {
                    $attributes .= sprintf(' data-videe-%s=%s', $key, var_export($value, true));
                }
				
				$addedWidgetVideeVideo = true;

                $content = "<div id='videe_widget_container'><videe-player " . $attributes . "></videe-player></div>";

            } else {
                $content = "<span>" . __('[Videe] Please customize videe widget.', 'videe') . "</span>";
            }
        } else {
            $content = "<span>" . __('[Videe] Connect your Videe.TV account.', 'videe') . "</span>";
        }
		
		if ($addedWidgetVideeVideo) {
			$this->getServiceLocator()->piwik->trackWidgetVideeVideoAdded();
		}

        echo apply_filters('widget_videe', $content);
        echo $args['after_widget'];
    }

    private function getWorkingPlaylist($instance)
    {
        if ( isset($instance['playlist_id']) && $playlist = $this->checkPlaylist($instance['playlist_id'])) {
            return $playlist;
        }
        
        // if selected playlist not exists
        // return first playlist(requirement)
        $playlists = $this->getPlaylists($limit = 1);

        
        if ($playlists) {
            return $playlists[0];
        }
        
        return false;
    }
	
	
	public function loadAsync( $tag, $handle, $src ) {

		$config = $this->getConfig();
		
		if (strpos($src, $config['playerScriptSrc']) > -1 ) {
			return '<script type="text/javascript" src="' . $src . '" async="async" defer></script>' . "\n";
		}

		return $tag;
	}


    public function registerVideeScripts()
    {
		add_filter( 'script_loader_tag', array($this, 'loadAsync'), 10, 3);
		
        $config = $this->getConfig();
		
        wp_enqueue_script('videe_player', $config['playerScriptSrc'], array(), $config['videeVersion'], true);	
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {

        $params = array_merge($this->defaultPlayerOptions, $instance);
		$playlist = $this->getWorkingPlaylist($params);
        $params['playlist_id'] = $playlist['id'];
        
		$playlists = $this->getPlaylists($limit = 100);


        if ($this->getServiceLocator()->getOption('token')):
            $this->loadAdminResources();
		
            ?>

            <p>
				<p class="videe-settings-notice" > Please note the widget will be adjusted to the sidebar size automatically. If the size is less than 300x250px the advertisements may not be shown.</p>

                <label for="videe_widget_playlist_id"><?php _e('Playlist:', 'videe'); ?></label>
                <select name="<?php echo $this->get_field_name('playlist_id'); ?> " id="videe_widget_playlist_id">
					<?php foreach ($playlists as $item): ?>
                        <option value="<?php echo $item['id']; ?>" <?php if ($item['id'] == $params['playlist_id'] && !is_null($params['playlist_id'])): ?> selected <?php endif; ?> ><?php echo esc_attr($item['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <br/>

            <ul class="accordion" style="display:<?php echo $playlist ? 'block' : 'none'; ?>;">
                <li>
                    <a class="toggle videe-setting-name" href="javascript:void(0);">Playback settings</a>

                    <p class="inner">

                        <label for="videe_widget_volume">
                            <?php _e('Volume:', 'videe'); ?>
                            <br/>
                            <i id="volume-icon"
                               class="settings-icon dashicons <?php echo ((int)$params['volume'] === 0 || $params['mute'] === true) ? 'dashicons-controls-volumeoff' : 'dashicons-controls-volumeon'; ?>"></i>
                            <input type="range" name="volume_display" id="videe_widget_volume_display" min="0" max="100"
                                   step="5" value="<?php echo esc_attr($params['volume']); ?>"
                                   data-orientation="vertical">
                            <span class="volume-percents"><?php echo esc_attr($params['volume']); ?>%</span>
                            <input type="hidden" name="<?php echo $this->get_field_name('volume'); ?>"
                                   id="videe_widget_volume" value="<?php echo esc_attr($params['volume']); ?>">

                        </label>
                        <label for="videe_widget_mute">
                            <input type="checkbox" name="<?php echo $this->get_field_name('mute'); ?>"
                                   id="videe_widget_mute"
                                   value="1"  <?php if (1 == $params['mute']) echo "checked"; ?> />
                            <?php _e('Mute', 'videe'); ?>
                        </label>
                        <label for="videe_widget_autoplay">
                            <input type="checkbox" name="<?php echo $this->get_field_name('autoplay'); ?>"
                                   id="videe_widget_autoplay"
                                   value="1"  <?php if (1 == $params['autoplay']) echo "checked"; ?> />
                            <?php _e('Autoplay', 'videe'); ?>
                        </label>
                        <label for="videe_widget_loop">
                            <input type="checkbox" name="<?php echo $this->get_field_name('loop'); ?>"
                                   id="videe_widget_loop"
                                   value="1"   <?php if (1 == $params['loop']) echo "checked"; ?>  />
                            <?php _e('Loop', 'videe'); ?>
                        </label>


                    </p>
                </li>

                <li>
                    <a class="toggle videe-setting-name" href="javascript:void(0);">Monetization settings</a>

                    <p class="inner">
                        <label>
                            <input type="checkbox" class="async" name="<?php echo $this->get_field_name('async'); ?>"
                                   id="videe_widget_async"
                                   value="1"  <?php if (1 == $params['async']) echo "checked"; ?> />
                            <?php _e('VPAID Async', 'videe'); ?>
                            <i class="settings-icon dashicons dashicons-editor-help"></i>
                            <span class="tooltip">Recommended when autoplay is disabled. The attribute allows to load ads independently of the video loading, that dicreases the site’s page load time.</span>
                        </label>
                        <label>
                            <input type="checkbox" class="background"
                                   name="<?php echo $this->get_field_name('background'); ?>"
                                   id="videe_widget_background"
                                   value="1" <?php if (1 == $params['background']) echo "checked"; ?> />
                            <?php _e('VPAID Background', 'videe'); ?>
                            <i class="settings-icon dashicons dashicons-editor-help"></i>
                            <span class="tooltip">Recommended for usage together with async attribute. In case the user clicks play button before the ad has loaded, it will keep loading in the background mode. The video will be paused only when the ad starts</span>

                        </label>
                    </p>
                </li>

            </ul>

            </p>
        <?php else: ?>
            <h4 class="unauthorized-error">
                <p>Connect your Videe.tv account to get access to 10,000+ monetizable videos instantly</p>

                <p>
                    <a class="button button-orange" href="<?php echo $this->getServiceLocator()->admin->getPageUrl('login'); ?>">Log In</a>
                </p>
            </h4>
        <?php endif; ?>
    <?php
    }

    /**
     * Retrive list of playlists
     *
     * @return array
     * @throws Exception
     */
    private function getPlaylists($limit=5)
    {
        if (!$this->getServiceLocator()->getOption('token')) {
            return array();
        }
        
        $config = $this->getConfig();
        $url = sprintf('%splaylists?auth_token=%s&limit=%d', 
			$config['videeApiUrl'], $this->getServiceLocator()->getOption('token'), $limit);


        try {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $jsonResponse = curl_exec($ch);

            curl_close($ch);

            $data = json_decode($jsonResponse, true);
            if (!isset($data['items']) || (isset($data['items']) && count($data['items']) === 0 ) ) {
                throw new Exception('Can\'t retrive playlists');
            }

            return $data['items'];

        } catch (Exception $e) {
            return array();
        }
    }
    
    private  function checkPlaylist($id) {
        
        if (!$this->getServiceLocator()->getOption('token')) {
            return false;
        }
        
        $config = $this->getConfig();
        $url = sprintf('%splaylists/%d?auth_token=%s', $config['videeApiUrl'], 
			$id, $this->getServiceLocator()->getOption('token'));


        try {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $jsonResponse = curl_exec($ch);

            curl_close($ch);

            $data = json_decode($jsonResponse, true);
            
            if (!isset($data['items'])) {
                throw new Exception('No playlists found');
            }

            return $data['items'];

        } catch (Exception $e) {
            return false;
        }        
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['volume'] = (isset($new_instance['volume'])) ? $new_instance['volume'] : 100;
        $instance['autoplay'] = (isset($new_instance['autoplay'])) ? 1 : 0;
        $instance['mute'] = (isset($new_instance['mute'])) ? 1 : 0;
        $instance['loop'] = (isset($new_instance['loop'])) ? 1 : 0;
        $instance['async'] = (isset($new_instance['async'])) ? 1 : 0;
        $instance['background'] = (isset($new_instance['background'])) ? 1 : 0;
        $instance['playlist_id'] = (isset($new_instance['playlist_id'])) ? $new_instance['playlist_id'] : null;
        return $instance;
    }

}