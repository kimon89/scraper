<?php
include "Scraper.php";

use Sainsburys\Scraper;

$url = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?listView=true&orderBy=FAVOURITES_FIRST&parent_category_rn=12518&top_category=12518&langId=44&beginIndex=0&pageSize=20&catalogId=10137&searchTerm=&categoryId=185749&listId=&storeId=10151&promotionId=#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";
//get the html
$curlData = Scraper::curlPage($url);
//create a dom object out of the html
$dom = Scraper::getDom($curlData);

if ($dom) {
	$data = [
		'results'	=> [],
		'total'		=> 0
	];
	//get all the product links
	$elems = $dom->find('.productInfo h3 a');
	//we don't need that anymore so clear the memory
	$dom->clear();
	//iterate through each link in order to get the data
	foreach($elems as $k => $el){
		$itemData = [];
		try {
			$curlData = Scraper::curlPage($el->href);
			$dom = Scraper::getDom($curlData);
			$itemData = Scraper::getItemData($dom);
			$dom->clear();
			$data['results'][] = $itemData;
			//add up to  the total price
			$data['total'] += $itemData['unit_price'];
		} catch (Exception $e){
			print 'Url: ' . $el->href . ' - ' . $e->getMessage();
		}
	}
	print json_encode($data);
} else {
	throw new Exception('Dom failed to load');
}