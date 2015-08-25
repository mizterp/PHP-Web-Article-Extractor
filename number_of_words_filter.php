<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		whitepaper http://www.l3s.de/~kohlschuetter/publications/wsdm187-kohlschuetter.pdf
		and 'boilerpipe' by Dr. Christian Kohlschütter
	
		Code author: Luke Hines
		Licence: PHP Web Article Extractor is licensed under a Creative Commons Attribution 4.0 International License.
	*/
	
	/*
		Number of words filter based on C4.8 Machine learning algorithm
		Reference: 'Algorithm 2: Classifier based on Number of Words' 
		in 'Boilerplate Detection using Shallow Text Features'
	*/
	
	class NumberOfWordsFilter
	{
		public static function Filter(&$textDocument)
		{
			$prevBlock;
			$nextBlock;
			$count = 0;
			foreach ($textDocument->textBlocks as $textBlock) 
			{
				$validContent = false;
				$currentBlock = $textBlock;
				
				if(sizeof($textDocument->textBlocks) > $count)
				{
					$nextBlock = $textDocument->textBlocks[$count+1];
				}
				else
				{
					break; // No next block
				}
				
				if(!isset($prevBlock))
				{
					$prevBlock = $textBlock;
					continue;
				}
				
				// Check link densitys
				if($currentBlock->linkDensity <= 0.333333)
				{
					if($prevBlock->linkDensity <= 0.555556)
					{
						if($currentBlock->numWords <= 16)
						{
							if($nextBlock->numWords <= 15)
							{
								if($prevBlock->numWords <= 4)
								{
									$validContent = false;
								}
								else
								{
									$validContent = true;
								}
							}
							else
							{
								$validContent = true;
							}
						}
						else
						{
							$validContent = true;
						}
					}
					else
					{
						if($currentBlock->numWords <= 40)
						{
							if($nextBlock->numWords <= 17)
							{
								$validContent = false;
							}
							else
							{
								$validContent = true;
							}
						}
						else
						{
							$validContent = true;
						}
					}
				}
				else
				{
					$validContent = false;
				}
				
				$textBlock->isContent = $validContent;
				$prevBlock = $textBlock;
				$count++;
			}
		}
	}
?>  