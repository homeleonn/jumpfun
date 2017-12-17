<?php

namespace Jump;

use Jump\DI\DI;
use Jump\helpers\Common;

abstract class Model{
	
	protected $di;
	protected $db;
	protected $config;
	protected $request;
	
    protected $options;
    protected $post;
	
	public function __construct(DI $di, $postOptions){
		global $post;
		$this->di = $di;
		$this->options = $post = $postOptions;
		$this->db = $this->di->get('db');
		$this->config = $this->di->get('config');
		$this->request = $this->di->get('request');
		Common::loadCurrentPostOptions();
		
	}
}