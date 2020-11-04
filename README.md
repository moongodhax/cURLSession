# cURLSession

This small library allows you to use php cURL with proxies, headers and cookies. You can send multiple requests with one cookie session.

Эта небольшая библиотека позволяет использовать php cURL с прокси, заголовками и куки, а так же позволяет слать запросы в пределах одной куки-сессии.

# Example

```php
require_once "src/cURLSession.php";

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
```

This code outputs:

```json
{
  "cookies": {
    "name1": "value1",
    "name2": "value2",
    "name3": "value3"
  }
}
{
  "headers": {
    "Accept": "*/*",
    "Cookie": "name1=value1; name2=value2; name3=value3",
    "Host": "httpbin.org",
    "Referer": "https://google.com",
    "User-Agent": "Mozilla/5.0 (Linux; U; Android 4.1.1; en-us; Google Nexus 4 - 4.1.1 - API 16 - 768x1280 Build/JRO03S) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30",
    "X-Amzn-Trace-Id": "Root=1-5fa2b6d1-669993b2090017d00bdb90c7"
  }
}
```
