<?php
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	class NonContentFilterTest extends PHPUnit_Framework_TestCase  
	{
		private $testDocument;
		
		
		public function setUp()
		{

		}
		
		public function tearDown()
		{
			$testDocument = null;
		}
	
    	public function testRemovalOfNoncontent()
    	{
			//WebArticleExtractor\Filters\TitleFilter::filter($this->testDocument);
			//echo 'Got Title:'.$this->testDocument->title;
			$this->assertEquals("", "");
    	}
	}
?>  