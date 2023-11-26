<?php
	
	class GitException extends Exception {
		
		public $fileName = '';
		
		function __construct ($mess, $code, $file) {
			
			parent::__construct ($mess, $code);
			
			$this->fileName = $file;
			
		}
		
	}