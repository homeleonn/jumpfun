<?php

namespace Jump\core\router;

use Jump\core\request\Request;
use Jump\DI\DI;
use Jump\helpers\HelperDI;

class Router{
	private $di;
	private $request;
	
	public $routes = [];
	
	private $controller;
	private $action;
	private $params = [];
	private $alternate = false;
	
	public function __construct(DI $di, $routes = NULL){
		$this->di = $di;
		$this->request = $this->di->get('request');
		$this->fillRoutes($routes);
	}
	
	public function run(){
		$controller = '\\' . ENV . '\controllers\\' . ucfirst($this->controller) . 'Controller';
		$action = 'action' . ucfirst($this->action);
		if(!$this->alternate)
			return call_user_func_array([new $controller($this->di, $this->controller), $action], $this->params);
		else
			return call_user_func([new $controller($this->di, $this->controller), $action], $this->params);
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
	
	private function fillRoutes($routes)
	{
		if(is_array($routes) && !empty($routes))
		{
			foreach($routes as $route)
			{
				if(!is_array($route))
				{
					throw new \Exception('Invalid route');
				}
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
			if($uri == 'admin/') $uri = '/';
			else{
				$replaceCount = 1;
				$uri = str_replace('admin/', '', $uri, $replaceCount);
			}
		}
		return $uri;
	}
	
	public static function add1($pattern, $controller){
		$Router = DI::getD('router');
		$Router->add($pattern, $controller);
		var_dump($Router->routes);
	}
	
}