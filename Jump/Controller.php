<?php

namespace Jump;

use Jump\DI\DI;

class Controller{
	
	protected $di;
	
	protected $model;  
	
	protected $db;

    protected $view;

    protected $config;

    protected $request;

    protected $load;
	
    protected $options;
	
	
	public function __construct(DI $di, $model){
		$this->di = $di;
		
		$this->initVars();
		$this->options = $this->config->getCurrentPageOptions();
		
		$calledClass = static::class;
		$defaultModel = isset($calledClass::$modelName) ? $calledClass::$modelName : false;
		
		$model = '\\' . ENV . '\models\\' . ($defaultModel ? ucfirst($defaultModel) : ucfirst($model));
		
		
		if(!class_exists($model)){
			throw new \Exception('Model '.$model.' not found');
		}
		//var_dump($this->options);exit;
		$this->model = new $model($di, $this->options);
	}
	
	public function initVars()
    {
        $vars = array_keys(get_object_vars($this));
		
        foreach ($vars as $var) {
            if ($this->di->has($var)) {
                $this->{$var} = $this->di->get($var);
            }
        }
    }
}