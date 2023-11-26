<?php
	
	class GitException extends Exception {
		
		public $file = '';
		
		function __construct ($mess, $code, $file) {
			
			parent::__construct ($mess, $code);
			
			$this->file = $file;
			
		}
		
	}