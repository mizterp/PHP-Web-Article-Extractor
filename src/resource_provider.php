<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		By Christian Kohlschuetter, Peter Fankhauser, Wolfgang Nejdl

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
       		$this->loadResource($resourceLocation);
  		}
		
		// Loads resource into memory
		public function loadResource($resourceName)
		{
			$resourceLocation = dirname(__FILE__)."/../res/".$resourceName;
			$this->resourceContent = ResourceProvider::readResourceToArray($resourceLocation);
		}
	}
?>  