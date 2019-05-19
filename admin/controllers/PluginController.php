<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Common;
use Jump\helpers\HelperDI;

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
		$data['plugins'] = [];
		$data['title'] = 'Плагины';
		
		if(!$plugins) return $data;
		
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
	
	public function actionSettings($pluginFolder)
	{
		ob_start();
		doAction('plugin_settings');
		//HelperDI::get('view')->rendered = true;
		$content = ob_get_clean();
		
		return ['content' => $content];
	}
	
	public function actionSettingsSave()
	{
		doAction('plugin_settings_save');
	}
}