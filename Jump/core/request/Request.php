<?php

namespace Jump\core\request;

use Jump\helpers\HelperDI;

class Request{
	
	public $server;
	
	public $post;
	public $get;
	
	private $rootUri;
	private $siteUrl;
	private $uri = '/';
	
	private $siteInRoot = NULL;
	
	public function __construct(){
		
		$this->post = $_POST;
		$this->get = $_GET;
		
		$this->server = $_SERVER;
		$this->rootUri = str_replace($this->server['DOCUMENT_ROOT'], '', str_replace('\\', '/', ROOT));
		
		if($this->server['PHP_SELF'] == '/index.php'){
			$this->uri = $this->server['REQUEST_URI'][0] != '/' ? $this->server['REQUEST_URI'] : substr($this->server['REQUEST_URI'], 1);
		}else{
			if($this->server['REQUEST_URI'] != $this->rootUri){
				$this->uri = str_replace($this->rootUri, '', $this->server['REQUEST_URI']);
			}
		}
		
		$this->uri = explode('?', $this->uri)[0];
		
		$this->siteUrl = $this->getScheme() . '://' . $this->server['HTTP_HOST'] . $this->rootUri;
		
		$this->siteInRoot();
		
		
	}
	
	public function parseUri($needController = NULL, $uri = NULL){
		$uri = $uri ?: URI;//var_dump($uri, explode(':', 'aa/a'));
		$result = array();
		
		// Определим есть ли тип поста
		$params = explode(':', $uri);
		$sep = ENV == 'admin' ? '/' : '|';
		// Если есть тип поста, скажем что это PostController и запишем тип в массив конфигурации
		if(isset($params[1])){
			HelperDI::get('config')->setOption('postType', $params[0]);
			$uri = 'post' . $sep . $params[1];
		}
			
		$params = explode($sep, trim($uri, '/'));
		//var_dump($params,ENV, $sep, $uri);
		if($needController) $result[] = $params[0] ?: 'index';
		$result[] = isset($params[1]) ? $params[1] : 'index'; // method
		$result[] = array_slice($params, 2); // args
		//var_dump($result);exit;
		return $result;
	}
	
	public function alternateParseUrl($string, $matches){
		eval('$urlString = "'.$string.'";');
		parse_str(parse_url($urlString, PHP_URL_QUERY), $params);
		return ['post', 'router', $params];
	}
	
	public function siteInRoot(){
		if($this->siteInRoot == NULL){
			$this->siteInRoot = $this->server['PHP_SELF'] == '/index.php';
		}
		
		return $this->siteInRoot;
	}
	
	public function getScheme(){
		return $this->server['REQUEST_SCHEME'];
	}
	
	public function rootUri(){
		return $this->rootUri;
	}
	
	public function siteUrl(){
		return $this->siteUrl;
	}
	
	public function uri(){
		return $this->uri;
	}
	
	public function checkRequestMethod($permissibleMethod){
		return $permissibleMethod == $_SERVER['REQUEST_METHOD'];
	}
	
	public function location($url = NULL, $code = NULL){
		$codes = [
			404 => 'Not Found',
			301 => 'Moved Permanently',
		];
		if($code)
			header("HTTP/1.1 {$code} {$codes[$code]}");
		if($code == 404){
			exit('Page not found');
		}
			
		
		header('Location:' . $url);
		exit;
	}
	
	public function get404(){
		$this->location(NULL, 404);
	}
}