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
	
	require 'text_block.php';

	class TextDocument 
	{
		public $title; // The title of the article
		public $articleText; // The text of the article
		public $textBlocks;
		
		public function GetText ($includeContent, $includeNonContent)
		{
			$result = "";
			foreach ($textBlocks as $block) 
			{
				if ($block->isContent) 
				{
					if (!$includeContent)
					{
						continue;
					}
				}
				else 
				{
					if (!$includeNonContent)
					{
						continue;
					}
				}
				$result .= $block->text;
				$result .= ' ';;
			}
			return $result;
		}
	    
    }
?>  