<?php

namespace Jump;

use Jump\helpers\HelperDI;

class Controller{
	protected $di;
	protected $model;
	protected $db;
    protected $view;
    protected $config;
    protected $request;
    protected $load;
	
	public function __construct(){
		$model = mb_strtolower(preg_replace('~.*\\\\(.*)Controller$~', '$1', get_called_class()));
		//dd($model, get_called_class());
		$this->di = HelperDI::get();
		$this->initVars($this->di);
		$model = $this->defineModel($model);
		
		// gets model dependencies
		if(!isset($this->config->getOption('frontend_deps')['models'][$model])){
			$modelArguments = [];
			//throw new \Exception("Dependencies not defined for model {$model}");
		}else{
			$modelDependencies = $this->config->getOption('frontend_deps')['models'][$model];
			// create model dependencies
			$modelArguments = $this->createModelArguments($this->di, $model, $modelDependencies);
		}
		
		// create model with created dependencies
		$this->model = (new \ReflectionClass($model))->newInstanceArgs(array_merge([$this->di], $modelArguments));
		$this->di->set('model', $this->model);
	}
	
	private function initVars($di){
        $vars = array_keys(get_object_vars($this));
        foreach ($vars as $var)
            if ($di->has($var))
                $this->{$var} = $di->get($var);
	}
	
	private function defineModel($model){
		$calledClass 	= static::class;
		$defaultModel 	= isset($calledClass::$modelName) ? $calledClass::$modelName : false;
		$modelName 		= $defaultModel ? ucfirst($defaultModel) : ucfirst($model);
		$model			= '\\' . ENV . "\models\\{$modelName}\\{$modelName}";
		
		if(!class_exists($model)){
			throw new \Exception('Model '.$model.' not found');
		}
		
		return $model;
	}
	
	/**
	 *  create model dependencies
	 *  
	 *  @param mixed $di
	 *  @param array $modelDependencies
	 *  
	 *  @return $arguments model dependencies
	 */
	private function createModelArguments($di, $model, $modelDependencies){
		$arguments = [];
		foreach($modelDependencies as $model => $dependency){
			if($dependency == 'di'){
				$arguments[] = $di;
			}elseif($model == 'di'){
				foreach($dependency as $d){
					$arguments[] = $di->get($d);
				}
			}else{
				$dep = new \ReflectionClass($model);
				$arguments[] = $dep->newInstanceArgs($this->createModelArguments($di, $model, $dependency));
			}
		}
		return $arguments;
	}
}