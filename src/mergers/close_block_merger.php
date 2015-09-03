<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		By Christian Kohlschuetter, Peter Fankhauser, Wolfgang Nejdl

		Code author: Luke Hines
		Licence: PHP Web Article Extractor is made available under the MIT License.
	*/
	
	class CloseBlockMerger
	{
		const BLOCK_DISTANCE = 1;
		
		public static function merge(&$textDocument, $onlyContent)
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
			
			foreach ($textDocument->textBlocks as $key => $textBlock) 
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
						// Perform merger of this block into the previous block
						$previousBlock->text .= "\r\n\r\n";
						$previousBlock->text .= $textBlock->text;
						$previousBlock->numWords += $textBlock->numWords;
						$previousBlock->numWordsInAnchorText += $textBlock->numWordsInAnchorText;
						$previousBlock->numWordsInWrappedLines += $textBlock->numWordsInWrappedLines;
						$previousBlock->numFullTextWords += $textBlock->numFullTextWords;
						$previousBlock->offsetBlocksStart = min($previousBlock->offsetBlocksStart,$textBlock->offsetBlocksStart);
						$previousBlock->offsetBlocksEnd = max($previousBlock->offsetBlocksEnd,$textBlock->offsetBlocksEnd);
						$previousBlock->tagLevel = min($previousBlock->tagLevel,$textBlock->tagLevel);
						$previousBlock->isContent = ($previousBlock->isContent || $textBlock->isContent);
						$previousBlock->currentContainedTextElements = array_merge($previousBlock->currentContainedTextElements,$textBlock->currentContainedTextElements);
						$previousBlock->labels = array_merge($previousBlock->labels,$textBlock->labels);
						$previousBlock->calculateDensities();
						unset($textDocument->textBlocks[$key]); // Safe as per PHP 'foreach' specification
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
			}
		}
	}
?>  