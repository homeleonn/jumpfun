<?php

namespace Jump\core\router;

use Jump\core\request\Request;
use Jump\DI\DI;

class Router{
	private $di;
	private $request;
	
	private $routes = [];
	
	private $controller;
	private $action;
	private $params = [];
	
	public function __construct(DI $di, $routes = NULL){
		$this->di = $di;
		$this->request = $this->di->get('request');
		$this->fillRoutes($routes);
	}
	
	public function run(){
		$controller = '\\' . ENV . '\controllers\\' . ucfirst($this->controller) . 'Controller';
		$action = 'action' . ucfirst($this->action);

		//var_dump($controller,$action);exit;
		return call_user_func_array([new $controller($this->di, $this->controller), $action], $this->params);
	}
	
	public function searchController(){
		$uri = urldecode($this->request->uri());
		
		if(strpos($uri, 'admin/') === 0) {
			if($uri == 'admin/') $uri = '/';
			else{
				$replaceCount = 1;
				$uri = str_replace('admin/', '', $uri, $replaceCount);
			}
		}
		
		$this->routesReverse();
		//var_dump($this->routes);exit;
		
		foreach($this->routes[$this->request->server['REQUEST_METHOD']] as $pattern => $replacement)
		{
			if(isset($replacement['method']) && !$this->request->checkRequestMethod($replacement['method']))
				continue;
				
			$pattern = '~^'.$pattern.'/$~u';
			
			//var_dump($pattern . ' - ' . $uri . ' - ' . preg_match($pattern, $uri));
			
			if(preg_match($pattern, $uri))
			{
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
	
}