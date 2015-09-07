<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.

		Code author: Luke Hines
		Licence: PHP Web Article Extractor is made available under the MIT License.
	*/
	
	/*
	* Determins the language of the article by filtering out stop words
	* the language that has the most stop words filtered from the article is
	* the most likely language of the text.
	*/
	
	class LanguageFilter
	{
		public static function filter(&$textDocument)
		{
			$StopWordLanguageMap = new ResourceProvider("stop_words");
			
			foreach ($StopWordLanguageMap->resourceContent as $value) 
			{
				//echo json_encode($value);
				
				//Generate regex on a per language basis
			}
		}
	}
?>  