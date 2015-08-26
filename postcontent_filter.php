<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		whitepaper http://www.l3s.de/~kohlschuetter/publications/wsdm187-kohlschuetter.pdf
		and 'boilerpipe' by Dr. Christian Kohlschütter
	
		Code author: Luke Hines
		Licence: PHP Web Article Extractor is licensed under the MIT License.
	*/
	
	class PostcontentFilter
	{
		const WORD_COUNT_THRESHOLD = 60; 
	
		public static function Filter(&$textDocument)
		{
			$numberOfWords = 0;
			$foundEndOfText = false;
			
			foreach ($textDocument->textBlocks as $textBlock) 
			{
				$endBlock = in_array("END BLOCK", $textBlock->labels); //TODO: Split labels into seperate area

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