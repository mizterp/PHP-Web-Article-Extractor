<?php namespace WebArticleExtractor;
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	class Article
	{
		/**
		* The title of the article
		*
		* @var string
		*/
		public $title;
		
		/**
		* The resulting article text
		*
		* @var string
		*/
		public $text;
		
		/**
		* The raw text blocks of article
		*
		* @var array
		*/
		public $textBlocks;
		
		/**
		* The language of the article
		*
		* @var string
		*/
		public $language;
		
		/**
		* The key words of the article
		*
		* @var array
		*/
		public $keywords;
	}
?>  