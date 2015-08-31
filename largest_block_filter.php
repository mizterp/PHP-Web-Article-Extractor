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
	
	/*
	* Filters all blocks asside from the largest.
	* Largest is determined by number of words.
	* If there is more than one 'largest' block only the 1st is kept.
	* non largest blocks are marked as 'possibly content'
	*/
	
	class LargestBlockFilter
	{
		public static function Filter(&$textDocument)
		{
			if(count($textDocument->textBlocks) < 2)
			{
				return;
			}
			
			$largestCount = -1;
			$largestCountKey = NULL;
			
			foreach ($textDocument->textBlocks as $key => $textBlock) 
			{
				if($textBlock->numWords > $largestCount)
				{
					$largestCountKey = $key;
					$largestCount = $textBlock->numWords;
				}
			}
			
			foreach ($textDocument->textBlocks as $key => $textBlock) 
			{
				if($key != $largestCountKey)
				{
					$textBlock->isContent = false;
					$textBlock->labels[] = 'POSSIBLY CONTENT';
				}
			}
		}
	}
?>  