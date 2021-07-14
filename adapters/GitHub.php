<?php
  
  namespace Git;
  
  class GitHub extends \Git {
    
    const VERSION = '1.0';
    
    public $url = 'https://api.github.com';
    
    function getCommit ($repo, $id): Commit {
      
      $data = $this->request ('repos/'.$this->config['login'].'/'.$repo.'/commits/'.$id);
      
      $date = new \DateTime ($data['commit']['author']['date']);
      
      $date->setTimezone (new \DateTimeZone (date_default_timezone_get ()));
      
      $commit = ['id' => $data['sha'], 'files' => [], 'modified' => $date];
      
      foreach ($data['files'] as $file)
        $commit['files'][] = new File (['name' => $file['filename'], 'status' => $file['status']]);
      
      $commit = new Commit ($commit);
      
      return $commit->validate ();
      
    }
    
    function createCommit ($repo, $text, $parent = 0) {
      
      return $this->request ('repos/'.$this->config['login'].'/'.$repo.'/git/commits', [
        
        'message' => $text,
        'tree' => $parent,
        
      ]);
      
    }
    
    function readFile ($repo, $file): string {
      
      $data = $this->request ('repos/'.$this->config['login'].'/'.$repo.'/contents/'.$file);
      return base64_decode ($data['content']);
      
    }
    
    protected function request ($path, $params = []) {
      
      $curl = curl_init ();
      
      $options = [
        
        CURLOPT_URL => $this->url.'/'.$path,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'GitHub/'.self::VERSION,
        CURLOPT_USERPWD => $this->config['login'].':'.$this->config['token'],
        
      ];
      
      if ($params) {
        
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = json_encode ($params);
        
      }
      
      $options[CURLOPT_HTTPHEADER] = [
        
        'Accept: application/vnd.github.v3+json',
        //'Content-Type: application/json',
        
      ];
      
      curl_setopt_array ($curl, $options);
      
      $info = curl_getinfo ($curl);
      
      $data = curl_exec ($curl);
      
      if ($info['http_code'] and !in_array ($info['http_code'], [200]))
        throw new \GitException ($data, $info['http_code']);
      elseif ($error = curl_error ($curl))
        throw new \GitException ($error, curl_errno ($curl));
      
      $data = json_decode ($data, true);
      
      if (isset ($data['message']))
        throw new \GitException ($data['message']);
      
      curl_close ($curl);
      
      return $data;
      
    }
    
  }