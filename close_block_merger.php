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
	
	class CloseBlockMerger
	{
		const BLOCK_DISTANCE = 1;
		
		public static function Merge(&$textDocument, $onlyContent)
		{
			if(sizeof($textDocument->textBlocks) < 2)
			{
				return;
			}
			
			$previousBlock;
			$offset = 0;
			
			if($onlyContent)
			{
				$previousBlock = NULL;
				$offset = 0;
				
				foreach ($textDocument->textBlocks as $textBlock) 
				{
					$offset++;
					if($textBlock->isContent)
					{
						$previousBlock = $textBlock;
						break;
					}
				}
				
				if($previousBlock == NULL)
				{
					return;
				}
			}
			else
			{
				$previousBlock = $textDocument->textBlocks[0];
				$offset = 1;
			}
			
			$count = 0;
			
			foreach ($textDocument->textBlocks as $textBlock) 
			{
				if(!$textBlock->isContent)
				{
					$previousBlock = $textBlock;
					continue;
				}
				
				$blockDiff = $textBlock->offsetBlocksStart - $previousBlock->offsetBlocksEnd - 1;
				if($blockDiff <= SELF::BLOCK_DISTANCE)
				{
					$validMerge = true;
					if($onlyContent)
					{
						if(!$previousBlock->isContent || !$textBlock->isContent)
						{
							$validMerge = false;
						}
					}
					
					if($validMerge)
					{
						//$previousBlock->mergeBlock($textBlock);
						unset($textDocument->textBlocks[$count]);
						
						//MERGE - Add to $markedForRemoval or unset($textDocument->textBlocks[$count]); 
						/*
							prevBlock.MergeNext(block);
							it.Remove();
						*/
					}
					else
					{
						$previousBlock = $textBlock;
					}
				}
				else
				{
					$previousBlock = $textBlock;
				}
				$count++;
			}
		}
	}
?>  