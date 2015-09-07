<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Code author: Luke Hines
		Licence: PHP Web Article Extractor is made available under the MIT License.
	*/
	
	class ResourceProvider
	{
		// Flattens a multidimentional array
		private static function flatten(array $array) 
		{
			$return = array();
			array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
			return $return;
		}
		
		// Reads resource file to array of strings
		private static function readResourceToArray($resourceLocation)
		{
			$resource = array_map('str_getcsv', file($resourceLocation));
			return ResourceProvider::flatten($resource);
		}

		public $resourceContent;
		
		function __construct($resourceLocation) 
		{
			if((($temp = strlen($resourceLocation) - strlen(".lst")) >= 0 && strpos($resourceLocation, ".lst", $temp) !== FALSE)) // Attempting to load resource file
			{
       			$this->loadResource($resourceLocation);
       		}
       		else // Attempting to load resource directory
       		{
       			$this->loadResourceDirectory($resourceLocation);
       		}
  		}
		
		// Loads resource into memory
		public function loadResource($resourceName)
		{
			$resourceLocation = dirname(__FILE__)."/../res/".$resourceName;
			$this->resourceContent = ResourceProvider::readResourceToArray($resourceLocation);
		}
		
		// Loads all resources found in a directory into memory
		public function loadResourceDirectory($directoryName)
		{
			$resourceLocation = dirname(__FILE__)."/../res/".$directoryName;
			$results = array();
			
			foreach (scandir($resourceLocation) as $file) 
			{
    			//Only load file if it has a .lst extention (list)
				if((($temp = strlen($file) - strlen(".lst")) >= 0 && strpos($file, ".lst", $temp) !== FALSE))
				{
					$fileEntry = array();
					$fileKey = str_replace(".lst","",$file);
					$fileEntry[0] = $fileKey;
					$fileEntry[1] = ResourceProvider::readResourceToArray($resourceLocation."/".$file);
					$results[] = $fileEntry;
				}
			}
			$this->resourceContent = $results;
		}
	}
?>  