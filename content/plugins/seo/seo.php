<?php

/*
 * Plugin Name: SEO
 * Plugin URI: 
 * Description: Работаем над SEO
 * Version: 0.1
 * Author: Anonymous
 * Author URI: 
 * License: 
 */

addAction('add_post_after', 'seo');
addAction('edit_post_after', 'seo');
function seo(){
	global $post;
	$descr = isset($post['_seo_description']) ? $post['_seo_description'] : '';
	include 'view.php';
}

addFilter('extra_fields_keys', 'seo_extra_fields_keys');
function seo_extra_fields_keys($extraFieldKeys){
	$extraFieldKeys[] = '_seo_description';
	
	return $extraFieldKeys;
}


addAction('jhead', function(){
	global $post;
	//dd($post);
	if(isset($post['__list'])){
		echo seoMeta('description', $post['description']);
		return;
	}
	$meta = ['_seo_description' => 'description', '_seo_keywords' => 'keywords'];
	
	if(!isset($post['_seo_description'])){
		global $di;
		$post['_seo_description'] = $di->get('config')->getOption('description');
	}
	
	foreach($meta as $k => $m){
		if(isset($post[$k]))
			echo seoMeta($m, $post[$k]);
	}
});

function seoMeta($name, $content){
	return '<meta name="'. $name .'" content="'. $content .'">' . "\n";
}