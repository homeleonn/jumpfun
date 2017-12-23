<?php

namespace cms\controllers;

use Jump\Controller;
use Jump\helpers\Common;

class CategoryController extends Controller{
	use \Jump\traits\PostControllerTrait;
	public function actionSingle($url, $id, $filters = NULL){
		$this->filtersProcessed($filters);
		if(!$category = $this->model->getSingleCategory($id, $url)) return 0;
		
		if($this->filters){
			
			$products = $this->model->getProductsByFilters($this->filters, $category['id']);
			
			$this->view->render('product/list', array_merge($category, ['products' => $products, 'filters' => $this->model->getFiltersHTML($category)]));
			return true;
		}
		
		if($subCategories = $this->model->getCategoriesByParent($id)){
			return array_merge($category, ['subCategories' => $subCategories]);
		}else{
			$products = $this->db->getAll('Select * from products where cat_id = ?i', $category['id']);
			$this->view->render('product/list', array_merge($category, ['products' => $products, 'filters' => $this->model->getFiltersHTML($category)]));
		}
			
		return true;
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
}