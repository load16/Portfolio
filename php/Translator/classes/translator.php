<?php
  //Класс ответственный за перевод текста на Google Translate.
  class translator {
      
      
      //Конструктор класса.
      function __construct(){
           
      }
       
       //Деструктор класса.
      function __destruct(){
          
      }
      
      
      
        function translate($ru_str) {
            $curlHandle = curl_init(); // init curl
            // options
            $postData=array();
            $postData['client']= 't';
            $postData['text']= $ru_str;
            $postData['hl'] = 'en';
            $postData['sl'] = 'ru';
            $postData['tl'] = 'en';
            curl_setopt($curlHandle, CURLOPT_URL, 'http://translate.google.com/translate_a/t'); // set the url to fetch 
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                'User-Agent: Mozilla/5.0 (X11; U; Linux i686; ru; rv:1.9.1.4) Gecko/20091016 Firefox/3.5.4',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: ru,en-us;q=0.7,en;q=0.3',
                'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
                'Keep-Alive: 300',
                'Connection: keep-alive'
            ));
            curl_setopt($curlHandle, CURLOPT_HEADER, 0); // set headers (0 = no headers in result)
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1); // type of transfer (1 = to string)
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10); // time to wait in
            curl_setopt($curlHandle, CURLOPT_POST, 0);
            if ( $postData!==false ) {
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($postData));
            }
            $content = curl_exec($curlHandle); // make the call
            curl_close($curlHandle); // close the connection
            $content = str_replace(',,',',"",',$content);
            $content = str_replace(',,',',"",',$content);
            $result = json_decode($content);
            return $result[0][0][0];
        }
      
  }
?>
