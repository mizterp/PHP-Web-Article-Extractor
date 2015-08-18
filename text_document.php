<?php
	require 'text_block.php';

	class TextDocument 
	{
		public $title;
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
				$result .= '\n';;
			}
			return $result;
		}
	    
    }
?>  