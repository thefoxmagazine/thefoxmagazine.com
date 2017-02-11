<?php

class Videe_Abstract
{
	/**
	 *
	 * @var array
	 */
	protected $config;

	/**
	 *
	 * @var Service_Locator 
	 */
	protected $locator;
	
	
	public function setServiceLocator($locator) {
		$this->locator = $locator;
		$this->config = $locator->getConfig();
	}
}
