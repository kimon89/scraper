<?php
include "Scraper.php";

use Sainsburys\Scraper;

class ScrapeTest extends PHPUnit_Framework_TestCase
{
	public $html = '';

	public function setUp()
	{
		$this->html = file_get_contents('tests'.DIRECTORY_SEPARATOR.'test_data.html');
	}

	public function testgetDom()
	{
		$dom = Scraper::getDom($this->html);

		$this->assertInstanceOf('simple_html_dom', $dom);
		$this->assertTrue(isset($dom->size));
	}

	public function testGetItemData()
	{
		$dom = Scraper::getDom($this->html);
		$itemData = Scraper::getItemData($dom);

		$this->assertTrue(is_array($itemData));
		$this->assertArrayHasKey('title',$itemData);
		$this->assertArrayHasKey('size',$itemData);
		$this->assertArrayHasKey('unit_price',$itemData);
		$this->assertArrayHasKey('description',$itemData);
		$this->assertEquals($itemData['title'],'Sainsbury\'s Apricot Ripe & Ready x5');
		$this->assertEquals($itemData['unit_price'],'3.00');
		$this->assertEquals($itemData['description'],'Apricots');
	}
}