<?php

namespace cms\controllers;

use Jump\Controller;

class ProductController extends Controller{
	public function actionSingle($url, $id){
		if(!$product = $this->db->getRow('Select * from products where id = ?i and url = ?s', $id, $url)) return 0;
		//var_dump($product);
		return $product;
	}
}