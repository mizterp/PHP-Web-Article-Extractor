<?php
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	class KeywordFilter
	{
		public static function filter(&$textDocument)
		{
			$textDocument->keywords = array();
			
			//array_push($textDocument->keywords,"Test","Two");
			//$StopWordsForLanguage = new ResourceProvider("stop_words");
		}
	}
?>  