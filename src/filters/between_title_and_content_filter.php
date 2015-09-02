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
	* Marks blocks between the 'title' and 'largest block' as content. Will not mark title itself as content
	*/
	
	class BetweenTitleAndContentFilter
	{
		public static function filter(&$textDocument)
		{
			$pastTitle = false;
			foreach ($textDocument->textBlocks as $key => $textBlock) 
			{
				if(in_array("TITLE",$textBlock->labels))
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