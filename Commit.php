<?php
	
	namespace Git;
	
	class Commit extends \AssocArray {
		
		function getRequiredPairs (): array {
			return ['id', 'modified', 'files'];
		}
		
		function setId ($id) {
			$this->set ('id', $id);
		}
		
		function setModified (long $modified) {
			$this->set ('modified', $modified);
		}
		
		function addFile (File $file) {
			$this->pairs['files'][] = $file;
		}
		
	}