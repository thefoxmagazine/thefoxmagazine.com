<?php

if (!function_exists('add_action')) {
    die('Unauthorized access.');
}

class Videe_Settings extends Videe_Abstract
{

    const PAGE_NAME = 'account-settings';
    const SETTINGS_SECTION_NAME = 'account_setting_section';
    const PAYMENTINFO_SECTION_NAME = 'paymentinfo_setting_section';
    const PAYMENTINFO_EMAIL = 'paymentinfo_email';

    private  $initiated = false;
	
    public  function init() {
        if (!$this->initiated) {
            $this->initHooks();
        }	
    }
	
    public  function initHooks() {
        
        $this->initiated = true;

        wp_register_style('videe_settings',  $this->config['videePluginUrl'] 
			. '_inc/css/videe_settings.css', array(),  $this->config['videeVersion']);
        wp_enqueue_style('videe_settings');

        wp_register_script('videe_settings',  $this->config['videePluginUrl'] 
			. '_inc/libs/videe_settings.js', array(),  $this->config['videeVersion']);
        wp_enqueue_script('videe_settings', array('jquery'));

        add_settings_section(
            self::SETTINGS_SECTION_NAME, 
            '', 
            array($this, 
            'accountSettingSectionCallbackFunction'), 
            self::PAGE_NAME
        );

        add_settings_field(
            Videe_Options::getDbOption('settingSubstitutePlayer'), 
            __('Replace default video with Videe.TV player', 'videe'), 
            array($this, 'substituteVideoSettingCallbackFunction'), 
            self::PAGE_NAME, 
            self::SETTINGS_SECTION_NAME
        );

        register_setting(self::SETTINGS_SECTION_NAME, Videe_Options::getDbOption('settingSubstitutePlayer'));


        add_settings_field(
            Videe_Options::getDbOption('settingEnableMonetization'), 
            __('Activate monetization for videos uploaded from Wordpress media library', 'videe'), 
            array($this, 'enableMonitarizationSettingCallbackFunction'), 
            self::PAGE_NAME, 
            self::SETTINGS_SECTION_NAME
        );

        register_setting(self::SETTINGS_SECTION_NAME, Videe_Options::getDbOption('settingEnableMonetization'));

        add_settings_section(
            self::PAYMENTINFO_SECTION_NAME, 
            '', 
            array($this, 'paymentInfoCallbackFunction'), 
            self::PAGE_NAME
        );

        add_settings_field(
            self::PAYMENTINFO_EMAIL, 
            __('', 'videe'), 
            array($this, 'paymentEmailCallbackFunction'), 
            self::PAGE_NAME, 
            self::PAYMENTINFO_SECTION_NAME
        );

        register_setting(self::PAYMENTINFO_SECTION_NAME, self::PAYMENTINFO_EMAIL);
    }

    public  function accountSettingSectionCallbackFunction() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_settings'])) {

            $messageUpdated = __('Your changes have been saved', 'videe');
            $messageMonetizationDeactivated = __('Video monetization deactivated. You can activate it back at any time.', 'videe');
            $messageMonetizationActivated = __('Video monetization activated', 'videe');

            $message = $messageUpdated;

            $substitute = isset($_POST[Videe_Options::getDbOption('settingSubstitutePlayer')])?
				$_POST[Videe_Options::getDbOption('settingSubstitutePlayer')] : 0;
			
			$this->locator->setOption('settingSubstitutePlayer', $substitute);


            $oldMonetizationValue = (int) $this->locator->getOption('settingEnableMonetization');
			$newMonetizationValue = isset($_POST[Videe_Options::getDbOption('settingEnableMonetization')]) && (bool) $substitute ?
				$_POST[Videe_Options::getDbOption('settingEnableMonetization')] : 0;
			
			$this->locator->setOption('settingEnableMonetization', $newMonetizationValue);

            if ($oldMonetizationValue != $newMonetizationValue && $newMonetizationValue === 1) {
                $message = $messageMonetizationActivated;
				$this->locator->piwik->trackMonetizationActivated();
            } elseif ($oldMonetizationValue != $newMonetizationValue && $newMonetizationValue === 0) {
                $message = $messageMonetizationDeactivated;
                $this->locator->piwik->trackMonetizationDeactivated();
            }

            echo '<div class="updated"><p><strong>' . $message . '</strong></p></div>';
        }
    }

    public  function do_settings_sections($page, $section_id) {
        global $wp_settings_sections, $wp_settings_fields;

        if (!isset($wp_settings_sections[$page]))
            return;

        foreach ((array) $wp_settings_sections[$page] as $section) {
            if ($section['id'] != $section_id) {
                continue;
            }

            echo "<h2>{$section['title']}</h2>\n";

            if ($section['callback'])
                call_user_func($section['callback'], $section);

            if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) 
				|| !isset($wp_settings_fields[$page][$section['id']]))
                continue;
			
            echo '<div class="settings-container">';
            $this->do_settings_fields($page, $section['id']);
            echo '</div>';
        }
    }

    public  function do_settings_fields($page, $section) {
        global $wp_settings_fields;

        if (!isset($wp_settings_fields[$page][$section]))
            return;

        foreach ((array) $wp_settings_fields[$page][$section] as $field) {
            $class = '';

            if (!empty($field['args']['class'])) {
                $class = ' class="' . esc_attr($field['args']['class']) . '"';
            }
            if (!is_array($field['args'])) {
                $field['args'] = array();
            }
            $field['args']['title'] = $field['title'];


            echo "<div{$class}>";
            call_user_func($field['callback'], $field['args']);
            echo '</div>';
        }
    }

    // creates a checkbox true/false option. 
    public function substituteVideoSettingCallbackFunction($args) {

        $notice = null;
        $classNotice = null;
        $classTitle = null;
		
		$settingName = Videe_Options::getDbOption('settingSubstitutePlayer');
		$settingValue = $this->locator->getOption('settingSubstitutePlayer');

        if ((int) $settingValue === 0) {
            $classTitle = 'title-active';
            $classNotice = 'notice-disabled';
            $title = __('Replace default video player with Videe.TV player', 'videe');
            $notice = __('Default player will be replaced throughout the website. '
                    . 'To restore initial settings just uncheck the checkbox.', 'videe');
        } else {
            $classTitle = 'title-disabled';
            $classNotice = 'notice-active';
            $title = __('Default player has been replaced with Videe.TV player throughout your website.', 'videe');
            $notice = __('To restore initial settings just untick the checkbox.', 'videe');
        }

        echo '<label for="' . $settingName . '" class="label">'
        . '<input type="checkbox" name="' . $settingName . '" value="1" '
        . 'id="' . $settingName . '"'
        . checked(1, $settingValue, false) . '/> '
        . $title . '</label><p class="option-notice">' . $notice . '</p>';
    }

    public function enableMonitarizationSettingCallbackFunction($args) {

        $disable = null;
        $notice = null;
        $activateNotice = null;
        $classNotice = null;
        $classTitle = null;
		
		$settingName = Videe_Options::getDbOption('settingEnableMonetization');
		$settingValue = $this->locator->getOption('settingEnableMonetization');

        if ((int) $settingValue === 0) {
            $classTitle = 'title-active';
            $classNotice = 'notice-disabled';
            $title = __('Activate monetization for videos uploaded from Wordpress media library.', 'videe');
            $notice = __(' By  enabling this option the ads will be turned on for '
                    . 'all videos from your media library published to website. '
                    . 'Default player will be replaced with Videe.TV player. ', 'videe');
        } else {
            $classTitle = 'title-disabled';
            $classNotice = 'notice-active';
            $title = __('Monetization for videos uploaded from your Wordpress media library is activated. ', 'videe');
            $notice = __('To deactivate monetization please untick the checkbox.', 'videe');
        }

        if (!$this->locator->getOption('userId')) {
            $disable = 'disabled readonly';
            $activateNotice =  sprintf(__('To activate monetization, please <a alt="" href="%s">connect your Videe.tv account</a>.', 'videe'),
				$this->locator->admin->getPageUrl('login'));
        }
        
        echo '<label for="' . $settingName . '" class="label">' 
             . '<input name="' . $settingName . '" id="' 
             . $settingName .'" type="checkbox" value="1"  ' 
             . $disable . checked(1, $settingValue, false) 
             . ' /> ' .  $title .  '</label>  <p class="option-notice">' . $notice . '<br>' . $activateNotice . '</p>';

    }

    public  function logoutButton() {
        if ($this->locator->getOption('userId')) {
            echo '<a href="#" class="button button-primary" id="disconnect-videe">Disconnect Videe.TV account</a>';
        } else {
            echo '<a href="' . $this->locator->admin->getPageUrl('login') 
				. '" class="button button-primary">Connect Videe.TV account</a>';
        }
    }


    public  function paymentInfoCallbackFunction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' 
			&& isset($_POST['submit_paymentinfo']) 
			&& filter_var($_POST[self::PAYMENTINFO_EMAIL], FILTER_VALIDATE_EMAIL)) {
            $info = $this->locator->user->getBillingPaypalInfo();
            $this->locator->user->setBillingPaypalInfo(strtolower($_POST[self::PAYMENTINFO_EMAIL]));
        }
    }

    public  function paymentEmailCallbackFunction() {

        $info = $this->locator->user->getBillingPaypalInfo();

        if(isset($info['paypal_email'])) {
            $email = $info['paypal_email'];
            $editButton = '<a href="#" class="button" id="editmail">Edit Email</a>';
            $disabled = 'disabled';
        } else {
            $email = '';
            $editButton = '';
            $disabled = '';
        }


        echo '<p>We only support PayPal Payment processor<br/>'
        . 'Please <a href="https://www.paypal.com/signup/account" target="_blank"> register a PayPal account</a>, if you do not have one.</p>'
        . '<label for="' . self::PAYMENTINFO_EMAIL . '" class="label">PayPal E-mail:'
        . '<input ' . $disabled . ' required  name="' . self::PAYMENTINFO_EMAIL . '" value="' . $email . '"'
        . 'oninvalid="this.setCustomValidity(\'Please enter valid e-mail\')" oninput="setCustomValidity(\'\')" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,5}$">'
        . '</label>' . $editButton;
    }

}