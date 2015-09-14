<?php namespace WebArticleExtractor\Filters;
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *  This class is based on the whitepaper 'Boilerplate detection using Shallow Text Features'
	 *  By Christian Kohlschuetter, Peter Fankhauser, Wolfgang Nejdl
	 *
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */

	/**
	 * Marks blocks between the 'title' and 'largest block' as content. Will not mark title itself as content
	 */
	class BetweenTitleAndContentFilter
	{
		public static function filter(&$textDocument)
		{
			$pastTitle = false;
			foreach ($textDocument->textBlocks as $key => $textBlock) 
			{
				if(in_array(PHPWAE_TITLE_LABEL,$textBlock->labels))
				{
					// Start when hitting title
					$pastTitle = true;
					continue;
				}
				
				if($pastTitle)
				{
					$textBlock->isContent = true;
				}
				
				if($textBlock->isContent)
				{
					// End once hit content
					return;
				}
			}
		}
	}
?>  