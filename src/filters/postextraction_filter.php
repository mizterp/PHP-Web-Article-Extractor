<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		By Christian Kohlschuetter, Peter Fankhauser, Wolfgang Nejdl

		Code author: Luke Hines
		Licence: PHP Web Article Extractor is made available under the MIT License.
	*/
	
	/*
	* Removes now irrelevant 'non-content' blocks.
	* Sets 'full title' to title block text, if no text is found falls back to standard title.
	*/
	
	class PostExtractionFilter
	{
		public static function filter(&$textDocument)
		{
			$pastTitle = false;
			$textDocument->articleText = '';
			foreach ($textDocument->textBlocks as $key => $textBlock) 
			{
				if(in_array("TITLE",$textBlock->labels))
				{
					// Mark the title block as the documents 'full title'
					$textDocument->fullTitle = $textBlock->text;
				}

				if(!$textBlock->isContent)
				{
					// Remove blocks that remain not marked as content
					unset($textDocument->textBlocks[$key]);
				}
				else
				{
					$textDocument->articleText .= $textBlock->text;
					$textDocument->articleText .= ' ';
				}
			}
			
			if(!isset($textDocument->fullTitle))
			{
				$textDocument->fullTitle = $textDocument->title; 
			}
		}
	}
?>  