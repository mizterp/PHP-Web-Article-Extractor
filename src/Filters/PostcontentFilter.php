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
	 
	use \WebArticleExtractor\BlockLabels as Labels;
	
	class PostcontentFilter
	{
		const WORD_COUNT_THRESHOLD = 60; 
	
		public static function filter(&$textDocument)
		{
			$numberOfWords = 0;
			$foundEndOfText = false;
			
			foreach ($textDocument->textBlocks as $textBlock) 
			{
				$endBlock = in_array(Labels::END_BLOCK_LABEL, $textBlock->labels);

				if($textBlock->isContent)
				{
					$numberOfWords += $textBlock->numFullTextWords;
				}
				
				if($endBlock && $numberOfWords >= self::WORD_COUNT_THRESHOLD)
				{
					$foundEndOfText = true;
				}
				
				if($foundEndOfText)
				{
					$textBlock->isContent = false;
				}
			}
		}
	}
?>  