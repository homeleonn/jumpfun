<?php

namespace Jump\core\responce;

class Responce
{
	const HTTP_OK = 200;
	const HTTP_NOT_FOUND = 404;
	const HTTP_MOVED_PERMANENTLY = 301;
	
	private $status;
	private $content;
	private $headers;
	private $version;
	
	public static $statusText = [
		200 => 'OK',
		404 => 'Not Found',
		301 => 'Moved Permanently',
	];
	
	public function __construct(string $content = '', int $status = 200, array $headers = [])
	{
		$this->setContent($content);
		$this->setStatusCode($status);
		$this->setHeaders($headers);
		$this->version = $_SERVER['SERVER_PROTOCOL'];
	}
	
	public function setStatusCode(int $status): self
	{
		if (!isset(self::$statusText[$status])) {
			throw new \UnexpectedValueException('Unknow status code: ' . $status);
		}
		
		$this->status = $status;
		
		return $this;
	}
	
	public function getStatusCode(): int
	{
		return $this->status;
	}
	
	public function setContent(string $content): self
	{
		$this->content = $content;
		
		return $this;
	}
	
	public function getContent(): self
	{
		return $this->content;
	}
	
	public function setHeaders(array $headers): self
	{
		$this->headers = $headers;
		
		return $this;
	}
	
	public function send(): void
	{
		$this->sendHeaders();
		$this->sendContent();
	}
	
	public function sendHeaders(): void
	{
		$statusCode = $this->getStatusCode();
		header("{$this->version} {$statusCode} " . self::$statusText[$statusCode]);
	}
	
	public function sendContent(): self
	{
		echo $this->content;
		
		return $this;
	}
	
	public function view(string $filename, int $status = 200, bool $exit = false): void
	{
		$this->setStatusCode($status);
		$this->sendHeaders();
		
		$file = THEME_DIR . $filename . '.php';
		if (file_exists($file)) {
			include_once $file;
		}
		
		if($exit) exit;
	}
	
	public function notFound(): void
	{
		
	}
}