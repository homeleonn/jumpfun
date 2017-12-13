<?php

namespace Jump\services;

use Jump\DI\DI;

abstract class AbstractProvider{
	
	protected $di;
	
	public function __construct(DI $di){
		$this->di = $di;
	}
	
	abstract public function init();
}