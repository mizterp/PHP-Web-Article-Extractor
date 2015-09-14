<?php namespace WebArticleExtractor;
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	class TextDocument 
	{
		public $title; // The title of the article
		public $articleText; // The text of the article
		public $textBlocks; // Raw text block of article
		public $language; // The language of the article
		public $keywords; // The key words of the article
    }
?>  