<?php

namespace Jump\core\router;

use Jump\core\request\Request;
use Jump\DI\DI;
use Jump\helpers\HelperDI;


class Router{
	private $request;
	
	public $routes = [];
	
	private $controller;
	private $action;
	private $params = [];
	private $alternate = false;
	
	public function __construct($routes = NULL){
		$this->request = HelperDI::get('request');
		$this->fillRoutes($routes);
	}
	
	public function run(){
		$controllerName = '\\' . ENV . '\controllers\\' . ucfirst($this->controller) . 'Controller';
		$action = 'action' . ucfirst($this->action);
		$controller = new $controllerName();
		
		if(!method_exists($controller, $action))
			throw new \Exception("class '{$controllerName}' does not have a method '{$action}'");
		
		// $params = [];
		// foreach(array_slice((new \ReflectionMethod($controller, $action))->getParameters(), count($this->params)) as $param){
			// if($param->getClass() && $paramNamespace = $param->getClass()->name){
				// foreach(HelperDI::get()->container as $service){
					// if(is_object($service) && $paramNamespace == get_class($service))
						// $params[] = $service;
				// }
			// }
		// }
		// if(!empty($params))
			// $this->params = array_merge($this->params, $params);
		
		if(!$this->alternate)
			return call_user_func_array([$controller, $action], $this->params);
		else
			return call_user_func([$controller, $action], $this->params);
	}
	
	public function searchController(){
		$uri = urldecode(URI);
		$uri = $this->replaceUriIfAdmin($uri);
		
		$this->routesReverse();
		//var_dump($this->routes);exit;
		
		foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $pattern => $replacement)
		{
			if(isset($replacement['method']) && !$this->request->checkRequestMethod($replacement['method']))
				continue;
				
			$pattern = '~^'.$pattern.'/$~u';
			
			//var_dump($pattern . ' - ' . $uri . ' - ' . preg_match($pattern, $uri));
			
			if(preg_match($pattern, $uri, $matches))
			{
				if(strpos($replacement['controller'], '*') === 0){
					list($this->controller, $this->action, $this->params) = $this->request->alternateParseUrl($replacement['controller'], $matches);
					$this->alternate = true;
					return true;
				}
					
				$convertedUri = preg_replace($pattern, $replacement['controller'], $uri);
				list($this->controller, $this->action, $this->params) = $this->request->parseUri(true, $convertedUri);
				return true;
			}
		}
		
		return false;
	}
	
	private function fillRoutes($routes){
		if(is_array($routes) && !empty($routes)){
			foreach($routes as $route){
				if(!is_array($route)) 
					throw new \Exception('Invalid route');
				
				call_user_func_array([$this, 'add'], $route);
			}
		}
	}
	
	public function add($pattern, $controller, $method = 'GET'){
		if(!in_array($method, ['GET', 'POST']))
			throw new \Exception('Invalid method');
		
		$this->routes[$method][$pattern] = [
			'controller' 	=> $controller
		];
		
		return $this;
	}
	
	public function getController(){
		return $this->controller;
	}
	
	public function getAction(){
		return $this->action;
	}
	
	
	private function routesReverse(){
		foreach($this->routes as $method => $rules){
			$this->routes[$method] = array_reverse($rules);
		}
	}
	
	private function replaceUriIfAdmin($uri){
		if(strpos($uri, 'admin/') === 0) {
			if(!isAdmin()) $this->request->notfound();
			if($uri == 'admin/') $uri = '/';
			else{
				$replaceCount = 1;
				$uri = str_replace('admin/', '', $uri, $replaceCount);
			}
		}
		return $uri;
	}
}