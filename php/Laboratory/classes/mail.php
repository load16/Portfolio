<?php
  // Класс ответсвенный за комуникацию с почтой.
  
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  
  require 'library/vendor/autoload.php';
  
  
  class mail {
      
      var $host;
      var $email;
      var $password;
      var $imap;
      var $mails_id;
      
      
      
      var $tools;                                                            //Панель инструментов.
      var $heat;                                                            //Заглавие задачи.
      var $footer;                                                            //Футер.
      var $content;                                                            //Контент модуля.
      var $log;                                                                //Объект логирования.
      var $config;                                                            //Объект конфигурации. 
      var $PHPMailer;    
      
      //Конструктор класса.
      function __construct($host = null, $email = null, $password = null){
          $this->config = config::getInstance();                            //Создаем объект конфигурации.
          if($host != null && $email != null && $password != null){
              //$this->host = '{'.$host.':993/imap/ssl}INBOX';
              $this->host = '{'.$host.':'.$this->config->conf['simaport'].'/imap/'.$this->config->conf['imapsecure'].'}INBOX'; 
              $this->email = $email;
              $this->password = $password;
              $this->getArrayId();                                                      //Формируем массив идентификаторов.
          }
          $this->PHPMailer = new PHPMailer(true);                            //Создаем объект майлер.
          $this->setParametrs();                                             //Устанавливаем начальные параметры.
      }
      
      //Деструктор класса.
      function __destruct(){
          imap_close($this->imap);                                                      //Закрываем соединение.
          unset($this->mails_id);                                                       //Сбрасываем переиенные.
      }
      
      
      //Метод установки параметров отправки писем.
      private function setParametrs(){
          $this->PHPMailer->CharSet = 'utf-8';
          $this->PHPMailer->SMTPDebug = 2;                                                                 // Enable verbose debug output
          $this->PHPMailer->isSMTP();                                                                      // Set mailer to use SMTP
          $this->PHPMailer->Host = $this->config->conf['smtphost'];                                      // Specify main and backup SMTP servers
          $this->PHPMailer->SMTPAuth = true;                                                               // Enable SMTP authentication
          $this->PHPMailer->Username = $this->config->conf['smtpuser'];                                 // SMTP username
          $this->PHPMailer->Password = $this->config->conf['smtppass'];                                   // SMTP password
          $this->PHPMailer->SMTPSecure = $this->config->conf['smtpsecure'];                                     // Enable TLS encryption, `ssl` also accepted
          $this->PHPMailer->Port = $this->config->conf['smtpport'];                                       // TCP port to connect to
          $this->PHPMailer->setFrom($this->config->conf['mailfrom'], $this->config->conf['nameserver']);
          $this->PHPMailer->isHTML(true);                                                                  // Set email format to HTML
          $this->PHPMailer->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
      }
      
      
      
      //Метод отправки письма с вложением.
      public function sendMailAttachment($to = null, $subject = null, $text = null, $filefullname = null, $filename = null){
          if($to != null && $subject != null && $text != null && $filefullname  != null && $filename != null){//Проверка необходимого.
              $text = str_replace("\n.", "\n..", $text);                                    //Проводим необходимые замены в тексте письма.
              $text = htmlspecialchars($text);
              $this->PHPMailer->addAddress($to);                                            //Устанавливаем данные для отправки. 
              $this->PHPMailer->Subject = $subject;
              $this->PHPMailer->SMTPDebug = 0;                                              //Отключить оповещение в консоль.
              //$this->PHPMailer->Body = $text;
              $this->PHPMailer->msgHTML($text); 
              //$this->PHPMailer->AltBody = $text;
              unset($to, $subject, $text);                                                   //Сброс переменных.
              $os = PHP_OS;                                                                  //Определяем ОС.
              $os = strtolower($os);                                                         //Переводим в нижний регистр.
              
              //$filename = mb_convert_encoding($filename, "WINDOWS-1251", "auto");
              //$this->PHPMailer->addStringAttachment($filefullname, $filename, 'base64', 'application/pdf');
              if(is_array($filefullname)){                                                   //Поверка наличия массива.
                  foreach($filefullname as $k => $v){                                        //Если массив то обходим его.
                      if (strpos($os, 'win') !== false){                                     //Проверка наличичя ОС Windows.
                          $n = mb_convert_encoding($v, "WINDOWS-1251", "auto");              //Декодируем под ОС.
                          $f = $filename[$k];                                                //Получаем короткое имя файла. 
                      }
                      else{
                          $n = $v;
                          $f = $k;
                      }
                      $this->PHPMailer->addAttachment($n, $f);                               //Добавляем вложение.
                  }
                  unset($k, $v, $f, $n);
              }
              else{                                                                           //Если переменная на массив, то..
                  if (strpos($os, 'win') !== false){                                          //Проверка наличичя ОС Windows.
                      $filefullname = mb_convert_encoding($filefullname, "WINDOWS-1251", "auto"); //Декодируем под ОС. 
                  }
                  $this->PHPMailer->addAttachment($filefullname, $filename);                   //Добавляем вложение. 
              }
              return $this->PHPMailer->send();                                                 //Отправляем. Получаем результат. 
              
          }
      }
      
       
      
      
      //Метод получения маасива идентификаторов писем.
      private function getArrayId(){ 
          $this->imap = imap_open($this->host, $this->email, $this->password);
          $this->mails_id = imap_search($this->imap, 'ALL');
      }
      
      
      //Метод получения массива писем.
      public function getArrayMail(){
          if($this->imap != null && $this->mails_id != null){
              foreach($this->mails_id as $id){
                  $header = imap_header($this->imap, $id);
                  $body = imap_body($this->imap, $id);
                  imap_delete($this->imap, $id);
                  $arr[$id]['body'] = $body;
              }
              unset($id);
              imap_expunge($this->imap);                                                // Удаление помеченных писем 
          }
          return $arr;
      }
  }
?>
