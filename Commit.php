<?php
  
  namespace Git;
  
  class Commit extends \AssocArray {
    
    protected function getRequiredPairs (): array {
      return ['id', 'modified', 'files'];
    }
    
  }