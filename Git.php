<?php
	
	require 'Commit.php';
	
	abstract class Git {
		
		protected $config = [];
		
		const GET = 'GET', POST = 'POST', PUT = 'PUT', DELETE = 'DELETE';
		
		function __construct (array $config = []) {
			$this->config = $config;
		}
		
		abstract function getCommit ($data): Git\Commit;
		abstract function createCommit ($repo, $text, $parent = 0);
		abstract function readFile ($repo, $file): string;
		
	}