<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\traits\PostControllerTrait;

class PluginController extends Controller{
	private $folder = ROOT . 'content/plugins/';
	
	public function actionIndex()
	{
		//return;
		// $pluginFolders = glob($this->folder . '*');
		$needles = ['Plugin Name', 'Plugin URI', 'Description', 'Version', 'Author', 'Author URI', 'License'];
		
		// if(!$pluginFolders) return ['empty' => 'Плагинов нет'];
		
		// foreach($pluginFolders as $folder)
		// {
			// $basename = basename($folder);
			// $mainFile = $folder . '/' . $basename . '.php';
			
			// if(file_exists($mainFile))
			// {
				// $fileData = file_get_contents($mainFile);
				
				// foreach($needles as $needle)
				// {
					// $plugin[$needle] = preg_match(str_replace('needle', $needle, PLUGIN), $fileData, $matches) ? 
						// trim($matches[1]):
						// 'none';
				// }
				
				// if($plugin['Plugin Name'] == 'none') continue;
				
				// $data['plugins'][] = $plugin;
			// }
		// }
		// return $data;
		$plugins = plugins();
		foreach($plugins as $plugin)
		{
			$fileData = file_get_contents($plugin);
			
			foreach($needles as $needle)
			{
				$plugin1[$needle] = preg_match(str_replace('needle', $needle, PLUGIN), $fileData, $matches) ? 
					trim($matches[1]):
					'none';
			}
			
			if($plugin1['Plugin Name'] == 'none') continue;
			
			$data['plugins'][] = $plugin1;
		}
		$data['title'] = 'Плагины';
		return $data;
		dd($this->folder);
	}
}