<?php
class cURLSession { 
    public    $headers = []; // array('Content-type: text/plain', 'Content-length: 100')
    public    $proxy = null; // "login:password@127.0.0.1:5000" or "127.0.0.1:5000"
    
    private $cookiefile = ""; 
    
    public function __construct() {
        // $this->cookiefile = $_SERVER['DOCUMENT_ROOT'] . "/cookies/cookie-" . uniqid(rand(), true) . ".txt";
        $this->cookiefile = "./cookie-" . uniqid(rand(), true) . ".txt";
        file_put_contents($this->cookiefile, "");
    }
    
    public function __destruct() {
        unlink($this->cookiefile);
    }
    
    private function _parse_proxy($proxy_str) {
        $proxy_arr = [
            "hostport" => "",
            "logpass" => ""
        ];
        
        if (strpos($proxy_str, "@") !== false) {
            $temp = explode("@", $proxy_str);
            $proxy_arr["hostport"] = $temp[1];
            $proxy_arr["logpass"] = $temp[0];
        }
        else {
            $proxy_arr["hostport"] = $proxy_str;
        }
        
        return $proxy_arr;
    }
    
    /*
        u can pass a netscape cookies string 
        or an array like this ["httpbin.org", "name", "value"]
    */
    public function set_cookies($cookies) {
        if (gettype($cookies) == "string") {
            file_put_contents($this->cookiefile, $cookies);
            return true;
        }
        else if (gettype($cookies) == "array") {
            $cookiefile = "";
            
            foreach ($cookies as $cookie) {
                $cookiefile .= $cookie[0]                     . "\t" . // domain
                               "TRUE"                         . "\t" . // include subdomains
                               "/"                            . "\t" . // path
                               "FALSE"                        . "\t" . // https only
                               (time() + (30 * 24 * 60 * 60)) . "\t" . // expires
                               $cookie[1]                     . "\t" . // name
                               $cookie[2]                     . "\n";  // value
            }
            
            file_put_contents($this->cookiefile, $cookiefile);
            return true;
        }
        else return false;
        
    }
    
    /*
     *  returns netscape cookies string
     */
    public function get_cookies() {
        return file_get_contents($this->cookiefile);
    }
    
    /*
     *  returns every cookie from file as array
     */
    public function get_parsed_cookies() {
        $cookies = [];
     
        $lines = explode("\n", file_get_contents($this->cookiefile));
     
        foreach ($lines as $line) {
            if (isset($line[0]) && substr_count($line, "\t") == 6) {
                $tokens = explode("\t", $line);
                $cookies[trim($tokens[5])] = trim($tokens[6]);
            }
        }
    
        return $cookies;
    }
    
    public function request($url,
                            $method = "get",
                            $headers = [], // array('Content-type: text/plain', 'Content-length: 100')
                            $data = []) {
        
        $s = curl_init();

        if ($this->proxy) {
            $proxy = $this->_parse_proxy($this->proxy);
            
            curl_setopt($s, CURLOPT_PROXYTYPE, 	CURLPROXY_HTTP);
            curl_setopt($s, CURLOPT_PROXY, 		$proxy["hostport"]);

            if ($proxy["logpass"]) {
                curl_setopt($s, CURLOPT_PROXYAUTH,    CURLAUTH_ANY);
                curl_setopt($s, CURLOPT_PROXYUSERPWD, $proxy["logpass"]);
            }
        }
        
        if ($method == "post") { 
            curl_setopt($s, CURLOPT_POST, 			true); 
            curl_setopt($s, CURLOPT_POSTFIELDS, 	$data); 
        }
        
        curl_setopt($s, CURLOPT_COOKIEJAR,          $this->cookiefile); // here will be written cookies
        curl_setopt($s, CURLOPT_COOKIEFILE,         $this->cookiefile); // from this file cookies will be read
        curl_setopt($s, CURLOPT_RETURNTRANSFER,		true); 
        curl_setopt($s, CURLOPT_SSL_VERIFYPEER, 	false);
        curl_setopt($s, CURLOPT_SSL_VERIFYHOST,     false);
        curl_setopt($s, CURLOPT_URL,				$url); 
        curl_setopt($s, CURLOPT_TIMEOUT,			10); 
        curl_setopt($s, CURLOPT_FOLLOWLOCATION,		true); // follow redirects
        // curl_setopt($s,	CURLOPT_HEADER,         true);  // includes header to answer
        
        if ($this->headers || $headers) {
            curl_setopt($s, CURLOPT_HTTPHEADER,		array_merge($this->headers, $headers));
        } 
        
        $response = curl_exec($s); 
        
        curl_close($s);
        
        return $response;
    }
} 
?>