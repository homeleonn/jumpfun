<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\helpers\Common;

class CategoryController extends Controller{
	use \Jump\traits\PostControllerTrait;
	public function actionSingle($url, $id, $filters = NULL){
		if($this->goCache($filters, 0.001)) return true;
		// Обрабатываем пришедшие фильтры(анализ, валидация)
		$this->filtersProcessed($filters);
		
		if(!$category = $this->model->getSingleCategory($id, $url)) return 0;
		
		$this->breadcrumbs($category['url'], $category['id'], $category['title'], $category['parent']);
		
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
		if(strpos($filters, '=') === false) return 'groups---all';
		$groups = [];
		foreach(explode(';', $filters) as $filter){
			$groups[] = explode('=', $filter)[0];
		}
		return implode('-', $groups);
	}
	
	private function getCache($cacheFileName, $outNow = true, $delayHours = 24, $template = 'product/list'){
		if(file_exists($cacheFileName) && filemtime($cacheFileName) > time() - $delayHours * 3600){
			$data = file_get_contents($cacheFileName);
			if($data === false) return false;
			if($outNow){
				$path = $this->view->getPath($template);
				extract($this->getCacheMeta($data));
				include $path . 'header.php';
				echo $data;
				include $path . 'footer.php';
				$this->view->rendered = true;
			}
			else return $data;
			return true;
		}
		//ob_start();
		$this->view->cacheOn($cacheFileName);
		return false;
	}

	private function setCache($cacheFileName, $data = false){
		if($data === false) $data = ob_get_clean();
		file_put_contents($cacheFileName, (string)$data, LOCK_EX);
		return $data;
	}
	
	private function getCacheMeta($data){
		preg_match('/var postData = ({.*})/', $data, $matches);
		return (array)json_decode($matches[1]);
	}
	
	private function goCache($filters, $delayHours = 24){
		if($this->view->cache) return false;
		$filename = md5(FULL_URL_WITHOUT_PARAMS) . '--' . $this->getFilterGroups($filters);
		$cacheFileName = ROOT . 'content/uploads/cache/shop/' . $filename . '.php';
		return $this->getCache($cacheFileName, true, $delayHours) ? true : false;
	}
	
	private function breadcrumbs($url, $id, $title, $parent){
		$this->config->addBreadCrumbs("{$url}-c{$id}" , $title);
		if($parent){
			do{
				$child = $this->model->getCategoryById($parent);
				$this->config->addBreadCrumbs("{$child['url']}-c{$child['id']}" , $child['title']);
			}while($parent = $child['parent']);
		}
		$this->config->breadcrumbsType = true;
	}
}