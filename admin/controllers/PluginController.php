<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Common;

class PluginController extends Controller{
	private $folder = ROOT . 'content/plugins/';
	private $activePlugins;
	
	public function __construct(){
		$this->activePlugins = unserialize(Common::getOption('plugins_activated'));
	}
	
	/**
	 *  Show plugins
	 */
	public function actionIndex()
	{
		$needles = ['Plugin Name', 'Plugin URI', 'Description', 'Version', 'Author', 'Author URI', 'License'];
		$plugins = plugins($this->activePlugins);
		
		foreach($plugins as $plugin)
		{
			$fileData = file_get_contents($plugin['src']);
			
			foreach($needles as $needle)
			{
				$plugin[$needle] = preg_match(str_replace('needle', $needle, PLUGIN), $fileData, $matches) ? 
					trim($matches[1]):
					'none';
			}
			
			if($plugin['Plugin Name'] == 'none') continue;
			
			$data['plugins'][] 	= $plugin;
		}
		
		$data['title'] = 'Плагины';
		return $data;
	}
	
	/**
	 *  Plugins toggle
	 *  
	 *  @param type $pluginFolder
	 *  @param type $pluginFile
	 */
	public function actionToggle($pluginFolder, $pluginFile){
		if(isset($this->activePlugins[$pluginFolder])){
			unset($this->activePlugins[$pluginFolder]);
		}else{
			$this->activePlugins[$pluginFolder] = $pluginFolder . '/' . $pluginFile;
		}
		
		Common::setOption('plugins_activated', serialize($this->activePlugins));
		redirect('admin/plugins');
	}
}