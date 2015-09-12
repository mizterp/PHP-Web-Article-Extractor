<?php
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
    /*
  	 * Identifies the language of the article by filtering out stop words
	 * the language that has the most stop words filtered from the article is
	 * the most likely language of the text.
	 */
	class LanguageFilter
	{
		public static function filter(&$textDocument)
		{
			$StopWordLanguageMap = new ResourceProvider("stop_words");
			
			$topLang = '';
			$topScore = 0;
			foreach ($StopWordLanguageMap->resourceContent as $value) 
			{
				//echo json_encode($value);
				$regex = LanguageFilter::regexForWordList($value[1]);
				$languageScore = preg_match_all($regex,$textDocument->articleText);
				
				// Uncomment for debug
				//echo $value[0].'-'.$languageScore;
				//echo '<br/>';
				
				if($languageScore > $topScore)
				{
					$topLang = $value[0];
					$topScore = $languageScore;
				}
				
				$textDocument->language = $topLang;
			}
		}
		
		private static function regexForWordList($WordList)
		{	
			$result = '/(';
			foreach ($WordList as $word) 
			{
				$result .= '\b'.preg_quote($word).'\b|'; //Requires unicode support
			}
			
			$result = rtrim($result, "|");
			$result .= ')/iu';
			return $result;
		}
	}
?>  