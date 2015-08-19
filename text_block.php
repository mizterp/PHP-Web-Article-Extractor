<?php
	class TextBlock
	{
		public $isContent = false;
		public $text;
		public $labels = array();
		public $numWords;
		public $numWordsInAnchorText;
		public $numWordsInWrappedLines;
		public $numWrappedLines;
		public $textDensity;
		public $linkDensity;
		public $numFullTextWords;
		public $tagLevel;
		public $currentContainedTextElements = array();
		public $offsetBlocksStart;
		public $offsetBlocksEnd;
		
		public function calculateDensities()
		{
			if ($this->numWordsInWrappedLines == 0)
			{
				$this->numWordsInWrappedLines = $this->numWords;
				$this->numWrappedLines = 1;
			}
			$this->textDensity = $this->numWordsInWrappedLines / $this->numWrappedLines;
			$this->linkDensity = $this->numWords == 0 ? 0 : $this->numWordsInAnchorText / $this->numWords;
		}
	}
?>  