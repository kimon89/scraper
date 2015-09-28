<?php 
namespace Sainsburys;
include "vendor/autoload.php";
use Sunra\PhpSimple\HtmlDomParser;

/**
 * The scraper class
 */
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
		curl_setopt($ch, CURLOPT_COOKIESESSION, true );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,   'cookie');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie' );
		curl_setopt($ch, CURLOPT_URL,$url);

		$res = curl_exec($ch);
		if (!$res) {
			throw new Exception('Curl failed to execute');
		}
		return $res;
	}

	/**
	 * Create a dom instance from html string
	 * @param  string $html Html string
	 * @return object      Dom instance
	 */
	static function getDom($html){
		$dom = HtmlDomParser::str_get_html($html);
		if (!$dom) {
			throw new Exception('Failed to initialise DOM');
		}
		//add the lenght of the page as an objet attribute
		$dom->size = strlen($html);
		return $dom;
	}

	/**
	 * Extract the data (title,size,unit_price,description) from the DOM object
	 * @param  object $dom Dom object
	 * @return array      Data exracted from the DOM
	 */
	static function getItemData($dom){
		$itemData = [];
		$title = $dom->find('.productTitleDescriptionContainer h1');
		$description = $dom->find('#information .productText p');
		$price = $dom->find('.pricePerUnit text');
		$itemData['title'] = $title[0]->innertext;
		//divide by 1024 to get the size of the page in kilobytes and round it 
		$itemData['size'] = round($dom->size/1024,2) . 'kb';
		//remove the currency sign and trim the string to get the numeric price of the product
		$itemData['unit_price'] = trim(str_replace('£', '', $price[0]->innertext));
		$itemData['description'] = $description[0]->innertext;
		return $itemData;
	}
}




