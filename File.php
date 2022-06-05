<?php
	
	namespace Git;
	
	class File extends \AssocArray {
		
		function getRequiredPairs (): array {
			return ['name', 'modified'];
		}
		
	}