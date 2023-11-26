<?php
	
	namespace Git;
	
	class GitHub extends \Git {
		
		const VERSION = '1.0';
		
		public $url = 'https://api.github.com';
		
		function getCommit ($data): Commit {
			
			//$data = $this->request ('repos/'.$this->config['login'].'/'.$repo.'/commits/'.$id);
			
			$commit = new Commit ();
			
			$comm = $data['head_commit'];
			
			$commit->setId ($comm['id']);
			
			$date = new \DateTime ($comm['timestamp']);
			
			$date->setTimezone (new \DateTimeZone (date_default_timezone_get ()));
			
			$commit->setModified ($date->getTimestamp ());
			
			foreach (['added', 'removed', 'modified'] as $status)
				foreach ($comm[$status] as $file)
					$commit->addFile (new File (['name' => $file, 'status' => $status]));
			
			return $commit->validate ();
			
		}
		
		function createCommit ($repo, $text, $parent = 0) {
			
			return $this->request ('repos/'.$this->config['login'].'/'.$repo.'/git/commits', [
				
				'message' => $text,
				'tree' => $parent,
				
			]);
			
		}
		
		function readFile ($repo, $file): string {
			
			$data = $this->request ('repos/'.$this->config['login'].'/'.$repo.'/contents/'.$file, [], $file);
			return base64_decode ($data['content']);
			
		}
		
		protected function request ($path, $file = '', $params = []) {
			
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
				throw new \GitException ($data, $info['http_code'], $file);
			elseif ($error = curl_error ($curl))
				throw new \GitException ($error, curl_errno ($curl), $file);
			
			$data = json_decode ($data, true);
			
			if (isset ($data['message']))
				throw new \GitException ($data['message'], 403, $file);
			
			curl_close ($curl);
			
			return $data;
			
		}
		
	}