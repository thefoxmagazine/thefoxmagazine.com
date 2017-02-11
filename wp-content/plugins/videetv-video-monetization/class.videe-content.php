<?php

class Videe_Content extends Videe_Abstract
{
	
	/**
	 *
	 * @var array
	 */
	public $options = array();
	
	private $defaultAttributes = array(
		'autoplay' => 'false',
		'loop' => 'false',
		'volume' => '100',
		'mute' => 'false',
		'load-settings' => 'false',
		'load-playlist' => 'true'
	);
	
	private $defaultAdAttributes = array(
		'async' => 'true',
		'background' => 'true',
	);
	
	public function setServiceLocator($locator) {
		parent::setServiceLocator($locator);
		$this->setOptions();
	}
	


	public function setOptions() {		
		$this->options['user-aid'] = $this->locator->getOption('aid');
		$this->options['user-id'] = $this->locator->getOption('userId');
		$this->options['token'] = $this->locator->getOption('token');
		$this->options['substitute-player'] = (boolean) $this->locator->getOption('settingSubstitutePlayer');
		$this->options['enable-monetization'] = (boolean) $this->locator->getOption('settingEnableMonetization') 
			&& !empty($this->options['user-id']) ? true : false;
	}


	public function getConfig() {
		if (!$this->config) {
			$this->config = $this->defaultConfig;
		}
		return $this->config;
	}

	public function contentMonitor($content) {
		$this->registerVideeScripts();

		$content = $this->parseVideeShorttag($content);
		if ($this->options['substitute-player']) {
			$content = $this->parseStandardPlaylistShorttag($content);
			$content = $this->parseStandardVideoShorttag($content);
		}

		return $content;
	}

	private function parseStandardPlaylistShorttag($content) {
		$onlyAttributes = array('ids');

		preg_match_all('/\[playlist(?<paramStrings>[^\]]+video[^\]]+)\]/Uis', $content, $finded);

		if (!isset($finded[0])) {
			return $content;
		}


		foreach ($finded[0] as $key => $videoString) {

			$attributes = $this->parseAttributes($finded['paramStrings'][$key], $onlyAttributes);

			if (isset($attributes['ids']) && !empty($attributes['ids'])) {
				$ids = explode(',', $attributes['ids']);
				unset($attributes['ids']);
			} else {
				continue;
			}

			$urls = array();

			foreach ($ids as $id) {
				if ($url = wp_get_attachment_url($id)) {
					$urls[] = $url;
				}
			}

			$attributes['wp-playlist-videos'] = implode(',', $urls);
			$replaceStr = $this->createTag($attributes, 'wordpress');

			$num = 1;
			$content = str_replace($videoString, $replaceStr, $content, $num);
		}


		return $content;
	}

	private function parseStandardVideoShorttag($content) {
		/**
		 * Flag to track that user
		 * added wordpress videos
		 * and use our player
		 */
		$addedWordpressVideo = false;
		
		$onlyAttributes = array('mp4', 'ogv', 'webm', 'src', 'autoplay', 'loop');

		preg_match_all('/\[video(?<paramStrings>[^\]]+)(\/)?\](\s+)?(\[\/video\])?/is', $content, $finded);

		if (!isset($finded[0])) {
			return $content;
		}

		foreach ($finded[0] as $key => $videoString) {

			$attributes = $this->parseAttributes(rtrim($finded['paramStrings'][$key], '/'), $onlyAttributes);

			if (!isset($attributes['wp-playlist-videos']) || empty($attributes['wp-playlist-videos'])) {
				continue;
			}

			$replaceStr = $this->createTag($attributes, 'wordpress');
			$addedWordpressVideo = true;

			$num = 1;
			$content = str_replace($videoString, $replaceStr, $content, $num);
		}
		
		if ($addedWordpressVideo) {
			$this->locator->piwik->trackWordpressVideoAdded();
		}


		return $content;
	}

	private function parseVideeShorttag($content, $autoSize = false) {
		/**
		 * Flag to track that user
		 * added videe video
		 */
		$addedVideeVideo = false;
		
		$onlyAttributes = array('volume', 'mute', 'autoplay','loop', 'autosize',
			'playlistid', 'videoid', 'async', 'background', 'width', 'height');

		preg_match_all('/\[videe_widget(?<paramStrings>[^\]]+)\]/i', $content, $finded);

		if (!isset($finded[0])) {
			return $content;
		}

		foreach ($finded[0] as $key => $videoString) {


			if (!$this->options['token']) {
				$replaceStr = "<span>" . __('[Videe] Connect your Videe.TV account', 'videe') . "</span>";
				$content = str_replace($videoString, $replaceStr, $content);
				continue;
			}

			$attributes = $this->parseAttributes($finded['paramStrings'][$key], $onlyAttributes);
	

			if ((!isset($attributes['playlistid']) || empty($attributes['playlistid'])) 
				&& (!isset($attributes['videoid']) || empty($attributes['videoid'])) ) {
				continue;
			}
			
			if (isset($attributes['autosize']) && $attributes['autosize'] == "true") {
				$attributes['size'] = 'autoxauto';
			}
			
			unset($attributes['autosize']);

			if ($autoSize) {
				$attributes['size'] = 'autoxauto';
			}

			$replaceStr = $this->createTag($attributes, 'videe');

			$addedVideeVideo = true;
			$num = 1;
			$content = str_replace($videoString, $replaceStr, $content, $num);
		}

		if ($addedVideeVideo) {
			$this->locator->piwik->trackVideeVideoAdded();
		}

		return $content;
	}
	
	public function loadAsync( $tag, $handle, $src ) {

		$config = $this->getConfig();
		
		if (strpos($src, $config['playerScriptSrc']) > -1 ) {
			return '<script type="text/javascript" src="' . $src . '" async="async" defer></script>' . "\n";
		}

		return $tag;
	}


	private function registerVideeScripts() {
		
		add_filter( 'script_loader_tag', array($this, 'loadAsync'), 10, 3);
		
		$config = $this->getConfig();
		
		wp_enqueue_script('videe_player', $config['playerScriptSrc'] /* . '?_=' . time() */, array(), $this->config['videeVersion'], true);
		
	}

	private function parseAttributes($string, $only = null) {
		$attributes = array();

		// parse params from string like
		// width=640 height=480 autoplay=true loop=true volume=50 videoId=366 thumbnail=http://video1source1.videe.tv/pcovers/1412da267b5003917b7f2e516d883510.jpg"
		// width="1280" height="720" mp4="http://videe.tv/wp-content/uploads/2015/10/SampleVideo_1080x720_2mb.mp4"
		preg_match_all("/(?<attrName>[a-zA-Z0-9_.-]+)=(?<attrValue>[^\s]+?)(\s|$)/is", $string, $params);

		if (!isset($params[0])) {
			return $attributes;
		}


		$videoFormats = array('mp4', 'ogv', 'webm');

		foreach ($params[0] as $paramKey => $paramString) {

			$attrName = str_replace(array('\'', '"'), '', strtolower($params['attrName'][$paramKey]));
			$attrValue = str_replace(array('\'', '"'), '', $params['attrValue'][$paramKey]);


			if (is_array($only) && !in_array($attrName, $only)) {
				continue;
			}

			if ($attrName == 'src') {
				$ext = pathinfo($attrValue, PATHINFO_EXTENSION);
				if (in_array($ext, $videoFormats)) {
					$attributes['wp-playlist-videos'] = $attrValue;
					continue;
				}
			}

			if (in_array($attrName, $videoFormats)) {
				$attributes['wp-playlist-videos'] = $attrValue;
				continue;
			}
			
			$attributes[$attrName] = $attrValue;
		}

		$attributes['size'] = (isset($attributes['height']) && isset($attributes['width']) 
			&& filter_var($attributes['height'], FILTER_VALIDATE_INT) && filter_var($attributes['width'], FILTER_VALIDATE_INT)) ?
			$attributes['height'] . 'x' . $attributes['width'] : 'autoxauto';

		unset($attributes['height']);
		unset($attributes['width']);

		return $attributes;
	}

	private function preAttribute($attributeName) {
		$config = $this->getConfig();

		if (is_string($attributeName)) {
			return $config['preAttribute'] . strtolower($attributeName);
		} else {
			return $attributeName;
		}
	}

	private function createTag($attributes, $type) {
		$attributesString = '';
		// videe video
		if ($type === 'videe') {
			$attributes['user-id'] = $this->options['user-id'];
			$attributes['user-aid'] = $this->options['user-aid'];
			$attributes = array_merge($this->defaultAttributes, $this->defaultAdAttributes, $attributes);
		} else { // wordpress video
			if ($this->options['enable-monetization']) {
				$attributes['monetization'] = 'true';
				$attributes['user-id'] = $this->options['user-id'];
				$attributes['user-aid'] = $this->options['user-aid'];
				$attributes = array_merge($this->defaultAttributes, $this->defaultAdAttributes, $attributes);
			} else { // monetization disabled. Use player only as wrapper without ads.
				$attributes['monetization'] = 'false';
				$attributes = array_merge($this->defaultAttributes, $attributes);
			}
		}

		foreach ($attributes as $key => $value) {
			$attributesString .= sprintf(' %s="%s"', $this->preAttribute($key), $value);
		}

		$replaceStr = "<div><videe-player " . $attributesString . "> </videe-player></div>";
		return $replaceStr;
	}

}