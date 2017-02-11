<?php

class Service_Locator
{
    /**
     * All services.
     *
     * @var array
     */
    private $services;

    /**
     * The services which have an instance.
     *
     * @var array
     */
    private $instantiated;

    /**
     * True if a service can be shared.
     *
     * @var array
     */
    private $shared;
	
	/**
	 * Config array
	 * 
	 * @var array
	 */
	private $config;

    public function __construct()
    {
        $this->services = array();
        $this->instantiated = array();
		$this->variables = array();
        $this->shared = array();
		$this->options = new Videe_Options();
    }
	
	public function setConfig($config) 
	{
		$this->config = $config;
	}
	
	public function getConfig() 
	{
		return $this->config;
	}	

    /**
     * Registers a service with specific interface.
     *
     * @param string        $interface
     * @param string|object $service
     * @param bool          $share
     */
    public function add($interface, $service, $share = true)
    {
        /*
         * When you add a service, you should register it
         * with its interface or with a string that you can use
         * in the future even if you will change the service implementation.
         */

		if (is_array($service)) {
			$this->variables[$interface] = $service;
		}
		
        if (is_object($service) && $share) {
			$this->injectLocator($service);
            $this->instantiated[$interface] = $service;
        }
		
        $this->services[$interface] = is_object($service) ? get_class($service) : $service;
        $this->shared[$interface] = $share;
    }
	
	private function injectLocator($service)
	{
		if (is_object($service) && is_subclass_of($service, 'Videe_Abstract')) {
			//var_dump($service);
			//exit();
			$service->setServiceLocator($this);
		}
	}

    /**
     * Checks if a service is registered.
     *
     * @param string $interface
     *
     * @return bool
     */
    public function has($interface)
    {
        return isset($this->services[$interface]) || isset($this->instantiated[$interface]);
    }

    /**
     * Gets the service registered for the interface.
     *
     * @param string $interface
     *
     * @return mixed
     */
    public function get($interface)
    {
		if(isset($this->variables[$interface])) {
			return $this->variables[$interface];
		}
		
        // Retrieves the instance if it exists and it is shared
        if (isset($this->instantiated[$interface]) && $this->shared[$interface]) {
            return $this->instantiated[$interface];
        }
		
		if(!isset($this->services[$interface])) {
			return false;
		}

        // otherwise gets the service registered.
        $service = $this->services[$interface];

        // You should check if the service class exists and
        // the class is instantiable.

        // This example is a simple implementation, but
        // when you create a service, you can decide
        // if $service is a factory or a class.
        // By registering a factory you can create your services
        // using the DependencyInjection pattern.

        // ...

        // Creates the service object
        $object = new $service();
		$this->injectLocator($object);

        // and saves it if the service must be shared.
        if ($this->shared[$interface]) {
            $this->instantiated[$interface] = $object;
        }

        return $object;
    }
	
	public function getOption($name) {
		return $this->options->{$name};
	}
	
	public function deleteOption($name) {
		return $this->options->delete($name);
	}
	
	public function setOption($name, $value) {
		$this->options->{$name} = $value;
	}
	
	public function __get($interface) {
		return $this->get($interface);
	}
}