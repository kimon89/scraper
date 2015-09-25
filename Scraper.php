<?php 
include "vendor/autoload.php";
use Sunra\PhpSimple\HtmlDomParser;

class Scraper{
	/**
	 * Curls the requested url and returns the html in plain text
	 * @param  string $url The destination to grab the html from
	 * @return string      The html of the page
	 */
	static function curlPage($url){
		//init curl
		$ch = curl_init();
		//website needs user agent and enabled cookies to function properly
		$agent= 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt( $ch, CURLOPT_COOKIEJAR,   'cookie');
		curl_setopt( $ch, CURLOPT_COOKIEFILE, 'cookie' );
		curl_setopt($ch, CURLOPT_URL,$url);

		return curl_exec($ch);
	}

	/**
	 * Create a dom instance from html string
	 * @param  [type] $url [description]
	 * @return [type]      [description]
	 */
	static function getDom($html){
		$dom = HtmlDomParser::str_get_html($html);
		$dom->size = strlen($html);
		return $dom;
	}

	/**
	 * [getItemData description]
	 * @param  [type] $dom [description]
	 * @return [type]      [description]
	 */
	static function getItemData($dom){
		$itemData = [];
		$title = $dom->find('.productTitleDescriptionContainer h1');
		$description = $dom->find('#information .productText p');
		$price = $dom->find('.pricePerUnit text');
		$itemData['title'] = $title[0]->innertext;
		$itemData['size'] = round($dom->size/1024,2) . 'kb';
		$itemData['unit_price'] = trim(str_replace('£', '', $price[0]->innertext));
		$itemData['description'] = $description[0]->innertext;
		return $itemData;
	}

	static function generateJson($dom){
		$data = [
			'results'	=> [],
			'total'		=> 0
		];
		$elems = $dom->find('.productInfo h3 a');
		$dom->clear();
		foreach($elems as $k => $el){
			$itemData = [];
			$curlData = Scraper::curlPage($el->href);
			$dom = Scraper::getDom($curlData);
			$itemData = Scraper::getItemData($dom);
			$dom->clear();
			$data['results'][] = $itemData;
			$data['total'] += $itemData['unit_price'];
		}
		return json_encode($data);
	}
}




