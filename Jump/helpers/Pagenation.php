<?php

/**
 * Pagenation
 *
 * Created by Victor Puhnyuk
 *
 */

namespace Jump\helpers;
 
class Pagenation
{
	/**
	 * Create and show pagenation block
	 *
	 * @param int $currentPageNumber number of current page
	 * @param int $all count of all items
	 * @param int $perPage count items on one page
	 * @param string $idStyle pagenation block style class
	 *
	 * @return void
	 * 
	 */
	public function run($currentPageNumber, $all, $perPage, $filters, $idStyle = 'pagenation')
	{
		$this->currentPageNumber = $currentPageNumber;
		$this->filters = $filters;
		$html = '';
		// Узнаем сколько всего страниц
		$countPages = ($perPage > $all) ? 1 : ceil($all / $perPage);
		
		if($countPages == 1) return false;
		
		if($countPages > 10){
			$start = $currentPageNumber <= 4 ? 2 : $currentPageNumber - 2;
			$end = $currentPageNumber >= $countPages - 3 ? $countPages - 1 : $currentPageNumber + 2;
		}
		
		$html = "<div class=\"clearfix\"></div><ul id=\"{$idStyle}\">";
		if(!isset($start))
		{
			foreach(range(1, $countPages) as $i){
				$html .= $this->getLink($i);
			}
		}
		else
		{
			$html .= $this->getLink(1) . ($currentPageNumber > 4 ? $this->getLink($start - 1, true) : ' ');
			
			foreach(range($start, $end) as $i){
				$html .= $this->getLink($i);
			}
			
			$html .= ($currentPageNumber > $countPages - 4 ? '' : $this->getLink($end + 1, true)); 
			$html .= $this->getLink($countPages);
		}
		$html .= '</ul>';//exit;
		return $html;
	}
	
	
	/**
	 * Helper for show links
	 *
	 * @param int $i number in itepation pages
	 * @param int $currentPageNumber number of current page
	 * @param int $ellipsis substitute for ellipsis link name
	 *
	 * @return void
	 * 
	 */
	private function getLink($i, $ellipsis = NULL){
		$activePageLink = 'javascript:void(0);" class="active-page-link"';
		$link = $activePageLink;
		if($this->filters){//var_dump($this->filters);
			$currentPage = preg_match('~page=(\d+)~', FULL_URL, $matches) ? $matches[1] : null;
			if($this->currentPageNumber == 1){
				$link = $i == 1 ? $activePageLink : str_replace($this->filters, 'page=' . $i . ';' .$this->filters , FULL_URL);
			}else{
				if($currentPage != $i){
					$firstPage = $i == 1;
					$moreFilters = (strpos($this->filters , ';') !== false);
					$search  = 'page=' . $currentPage . ($firstPage ? ($moreFilters ? ';' : '/') : '');
					$replace = ($firstPage ? '' : 'page=' . $i);
					//var_dump(strpos($this->filters , ';'), $this->filters, $moreFilters, $search, $replace);
					$link = str_replace($search, $replace, FULL_URL);
				}
			}
		}else{
			if($i != 1)
				$link = FULL_URL . 'page=' . $i . '/';
		}
		
		$link .= '"';
			
		return '<li><a href="'.$link.'>'.($ellipsis ? ' ... ' : $i).'</a></li>';
	}
}

