<?php

namespace Jump;

use Jump\DI\DI;
use Jump\helpers\Common;

abstract class Model{
	
	protected $di;
	protected $db;
	protected $config;
	
    protected $postType;
    protected $options;
	
	public function __construct(DI $di){
		$this->di = $di;
		$this->db = $this->di->get('db');
		$this->config = $this->di->get('config');
		$this->postType = $this->di->get('config')->postType;
		$this->options = $this->di->get('config')->getCurrentPageOptions();
		Common::loadCurrentPostOptions();
	}
}