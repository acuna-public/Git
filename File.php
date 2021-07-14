<?php
  
  namespace Git;
  
  class File extends \AssocArray {
    
    protected function getRequiredPairs (): array {
      return ['name', 'modified'];
    }
    
  }