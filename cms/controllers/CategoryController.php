<?php

namespace cms\controllers;

use Jump\Controller;
use Jump\helpers\Common;

class CategoryController extends Controller{
	use \Jump\traits\PostControllerTrait;
	public function actionSingle($url, $id, $filters = NULL){
		if($filters){
			$filename = md5(FULL_URL) . '--' . implode('-', $this->getFilterGroups($filters));
			$cacheFileName = ROOT . 'content/uploads/cache/shop/' . $filename . '.php';
			//var_dump($cacheFileName);exit;
			if($this->getCache($cacheFileName, 1)) return true;
		}
		
		$this->filtersProcessed($filters);
		if(!$category = $this->model->getSingleCategory($id, $url)) return 0;
		$this->config->addBreadCrumbs("{$category['url']}-c{$category['id']}" , $category['title']);
		if($category['parent']){
			$parent = $this->model->getParentCategory($category['parent']);
			$this->config->addBreadCrumbs("{$parent['url']}-c{$parent['id']}" , $parent['title']);
			if($parent['parent']){
				$parent = $this->model->getParentCategory($parent['parent']);
				$this->config->addBreadCrumbs("{$parent['url']}-c{$parent['id']}" , $parent['title']);
			}
		}
		$this->config->breadcrumbsType = true;
		
		if($this->filters){
			
			$products = $this->model->getProductsByFilters($this->filters, $category['id']);
			$filters = $this->model->getFiltersHTML($category, $products);
			$selectedFilters = $this->model->getSelectedFiltersHTML();
			$this->view->render('product/list', array_merge($category, ['products' => $products, 'filters' => $filters, 'selectedFilters' => $selectedFilters]));
			$data = true;
		}else{
			if($subCategories = $this->model->getCategoriesByParent($id)){
				$data = array_merge($category, ['subCategories' => $subCategories]);
			}else{
				$products = $this->db->getAll('Select * from products where cat_id = ?i', $category['id']);
				$filters = $this->model->getFiltersHTML($category, $products);
				$selectedFilters = $this->model->getSelectedFiltersHTML();
				$this->view->render('product/list', array_merge($category, ['products' => $products, 'filters' => $filters, 'selectedFilters' => $selectedFilters]));
				$data = true;
			}
		}
		
		if(isset($cacheFileName)){
			echo $this->setCache($cacheFileName);
		}
		
		return $data ?: true;
	}
	
	private function getHeadCategoryAndSub($categories, $idHeadCategory){
		foreach($categories as $key => $category){
			if($category['id'] == $idHeadCategory){
				$headCategory = $category;
				unset($categories[$key]);
				return [$headCategory, $categories];
			}
		}
	}
	
	private function getFilterGroups($filters){
		$groups = [];
		foreach(explode(';', $filters) as $filter){
			$groups[] = explode('=', $filter)[0];
		}
		return $groups;
	}
	
	private function getCache($cacheFileName, $delayHours = 24, $template = 'product/list', $outNow = true){
		if(file_exists($cacheFileName) && filemtime($cacheFileName) > time() - $delayHours * 3600){
			if(!$data = file_get_contents($cacheFileName)) return false;
			$this->view->rendered = true;
			if($outNow){
				//$path = $this->view->getPath($template);
				//include $path . 'header.php';
				echo $data;
				//include $path . 'footer.php';
			}
			else 	  return $data;
			return true;
		}
		ob_start();
		$this->view->cache = true;
		return false;
	}

	private function setCache($cacheFileName, $data = false){
		if(!$data) $data = ob_get_clean();
		//if(!$data) $data = $this->view->cache;
		file_put_contents($cacheFileName, $data, LOCK_EX);
		return $data;
	}
}