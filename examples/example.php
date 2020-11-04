<?php
require_once "../src/cURLSession.php";

    $session = new cURLSession();
    
    $session->headers = [
        "User-Agent: Mozilla/5.0 (Linux; U; Android 4.1.1; en-us; Google Nexus 4 - 4.1.1 - API 16 - 768x1280 Build/JRO03S) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30",
        "Referer: https://google.com",
    ];
    
    // $session->proxy = null; // = "login:password@ip:port" or "ip:port"

    $session->set_cookies([
        // domain name, cookie name, cookie value
        ["httpbin.org", "name1", "value1"],
        ["httpbin.org", "name2", "value2"],
        ["httpbin.org", "name3", "value3"]
    ]);
    
    /*
     * this is how to make request and get response text
     */
    $response = $session->request("https://httpbin.org/cookies");
    
    echo $response;
    
    $response = $session->request("https://httpbin.org/headers");
    
    echo $response;
    
/*
// this code outputs

{
  "cookies": {
    "name1": "value1",
    "name2": "value2",
    "name3": "value3"
  }
}
{
  "headers": {
    "Cookie": "name1=value1; name2=value2; name3=value3",
    "Host": "httpbin.org",
    "Referer": "https://google.com",
    "User-Agent": "Mozilla/5.0 (Linux; U; Android 4.1.1; en-us; Google Nexus 4 - 4.1.1 - API 16 - 768x1280 Build/JRO03S) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30",
  }
}
*/