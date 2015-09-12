<?php
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	/**
	 *	Removes blocks where 'isContent' is false
	 */
	class NonContentFilter
	{
		public static function filter(&$textDocument)
		{
			foreach ($textDocument->textBlocks as $key => $textBlock) 
			{
				if(!$textBlock->isContent)
				{
					unset($textDocument->textBlocks[$key]);	
				}
			}
		}
	}
?>  