<?php
  //Класс отвественный за проверку IP в спамлистах на DNSBL.
   gc_collect_cycles();
  class spam{
      
      var $arr_ip;
      
      
      //Конструктор класса.
      function __construct($arr_ip = null){
          if($arr_ip != null){
              $this->arr_ip = $arr_ip;
          }
      }
      
      //Метод отправки результата на почту
      function sendMail($mail = null, $subject = null, $test = null){
          if($mail != null && $subject != null && $test != null){
              mail($mail, $subject, $test);
              //var_dump(mail($mail, $subject, $test));
          }
      }
      
      
      
      //Генерировать случайную строку пользовательского агента
      function get_random_useragent_strings(){
          $agents = array(
          'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
          'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0',
          'Mozilla/5.0 (X11; OpenBSD amd64; rv:28.0) Gecko/20100101 Firefox/28.0',
          'Mozilla/5.0 (X11; Linux x86_64; rv:28.0) Gecko/20100101 Firefox/28.0',
          'Opera/12.80 (Windows NT 5.1; U; en) Presto/2.10.289 Version/12.02',
          'Opera/9.80 (Windows NT 6.1; U; es-ES) Presto/2.9.181 Version/12.00',
          'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)',
          );
          return $agents[rand( 0, (count($agents)-1) )];
      }
      
      
      // Получить HTML-код страницы для указанного URL
      function curl_get_page_html($url = '', $user_agent = ''){
          if($user_agent=='')
            $user_agent = spam::get_random_useragent_strings();
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
          curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
          curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30);
          $html = curl_exec($ch);
          $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          if($http_code!=200)
            return false;
          return $html;
      }
      
      // Проверить IP на BL на сервисе whoer.net
      function whoer_net_check_ip($ip = ''){
          $html = spam::curl_get_page_html('http://whoer.net/check?host='.trim($ip));
          if(!$html)
            return array('error'=>1, 'inblack'=>0);
          if(strpos($html, 'Invalid IP address'))
            return array('error'=>1, 'inblack'=>0);
          preg_match('#(.*?)#is', $html, $match);
          if(!isset($match[1]) || $match[1]=='')
            return array('error'=>1, 'inblack'=>0);
          if(strpos($match[1], 'Yes'))
            return array('error'=>0, 'inblack'=>1);
          return array('error'=>0, 'inblack'=>0);
      }
      
      // Получить доступный массив хостов DNSBL
      function get_dnsbl_hosts(){
          return array(
            "abuse.rfc-ignorant.org",
            "access.redhawk.org",
            "aspews.ext.sorbs.net",
            "blackholes.brainerd.net",
            "blackholes.five-ten-sg.com",
            "blackholes.wirehub.net",
            "blacklist.junkemailfilter.com",
            "blacklist.sci.kun.nl",
            "blacklist.woody.ch",
            "bl.deadbeef.com",
            "bl.emailbasura.org",
            "block.dnsbl.sorbs.net",
            "bl.redhatgate.com",
            "bl.spamcannibal.org",
            "bl.spamcop.net",
            "bl.technovision.dk",
            "c10.rbl.hk",
            "cbl.abuseat.org",
            "cbl.anti-spam.org.cn",
            "cblless.anti-spam.org.cn",
            "cblplus.anti-spam.org.cn",
            "combined.njabl.org",
            "db.wpbl.info",
            "dialups.mail-abuse.org",
            "dialups.visi.com",
            "dnsbl-0.uceprotect.net",
            "dnsbl-1.uceprotect.net",
            "dnsbl-2.uceprotect.net",
            "dnsbl-3.uceprotect.net",
            "dnsbl.ahbl.org",
            "dnsbl.cyberlogic.net",
            "dnsbl.jammconsulting.com",
            "dnsbl.kempt.net",
            "dnsbl.njabl.org",
            "dnsbl.sorbs.net",
            "duinv.aupads.org",
            "dul.dnsbl.sorbs.net",
            "dul.ru",
            "fl.chickenboner.biz",
            "hil.habeas.com",
            "hostkarma.junkemailfilter.com",
            "http.dnsbl.sorbs.net",
            "http.opm.blitzed.org",
            "images.rbl.msrbl.net",
            "ips.backscatterer.org",
            "ircbl.ahbl.org",
            "ix.dnsbl.manitu.net",
            "korea.services.net",
            "l2.bbfh.ext.sorbs.net",
            "list.dnswl.org",
            "mail-abuse.blacklist.jippg.org",
            "map.spam-rbl.com",
            "misc.dnsbl.sorbs.net",
            "msgid.bl.gweep.ca",
            "multi.surbl.org",
            "multi.uribl.com",
            "no-more-funn.moensted.dk",
            "ohps.dnsbl.net.au",
            "omrs.dnsbl.net.au",
            "orid.dnsbl.net.au",
            "orvedb.aupads.org",
            "osps.dnsbl.net.au",
            "osrs.dnsbl.net.au",
            "owfs.dnsbl.net.au",
            "owps.dnsbl.net.au",
            "phishing.rbl.msrbl.net",
            "probes.dnsbl.net.au",
            "proxy.bl.gweep.ca",
            "psbl.surriel.com",
            "query.bondedsender.org",
            "rbl-plus.mail-abuse.org",
            "rbl.snark.net",
            "rdts.dnsbl.net.au",
            "relays.bl.gweep.ca",
            "relays.bl.kundenserver.de",
            "relays.mail-abuse.org",
            "relays.nether.net",
            "ricn.dnsbl.net.au",
            "rmst.dnsbl.net.au",
            "rot.blackhole.cantv.net",
            "rsbl.aupads.org",
            "satos.rbl.cluecentral.net",
            "sbl.csma.biz",
            "smtp.dnsbl.sorbs.net",
            "socks.dnsbl.sorbs.net",
            "socks.opm.blitzed.org",
            "sorbs.dnsbl.net.au",
            "spam.dnsbl.sorbs.net",
            "spamguard.leadmon.net",
            "spam.olsentech.net",
            "spamrbl.imp.ch",
            "spamsites.dnsbl.net.au",
            "spamsources.dnsbl.info",
            "spamsources.fabel.dk",
            "spam.wytnij.to",
            "t1.bl.dnsbl.net.au",
            "t1.dnsbl.net.au",
            "ubl.unsubscore.com",
            "ucepn.dnsbl.net.au",
            "virbl.bit.nl",
            "virbl.dnsbl.bit.nl",
            "virus.rbl.jp",
            "virus.rbl.msrbl.net",
            "web.dnsbl.sorbs.net",
            "whois.rfc-ignorant.org",
            "wingate.opm.blitzed.org",
            "wormrbl.imp.ch",
            "wpbl.dnsbl.net.au",
            "zombie.dnsbl.sorbs.net",
            "b.barracudacentral.org", // http://barracudacentral.org/
            "xbl.spamhaus.org", // http://spamhaus.org
            "zen.spamhaus.org",
            "cbl.spamhaus.org",
            "sbl.spamhaus.org",
            "pbl.spamhaus.org"
            );
      }
      
      
      // Проверить IP в сервисах DNSBL
      function dns_bl_check_ipaddress($ipaddress = '', $dnsbl_array = array()){
          if(!is_array($dnsbl_array) || !count($dnsbl_array))
            $dnsbl_array = spam::get_dnsbl_hosts();
          $result = array($ipaddress => array());
          $result[$ipaddress]['inblack'] = 0;
          if($ipaddress=='')
            return false;
          $reverse_ip = implode(".", array_reverse(explode(".", $ipaddress)));
          foreach($dnsbl_array as $dnsbl_host){
              $is_listed = checkdnsrr($reverse_ip.".".$dnsbl_host.".", "A") ? 1 : 0;
              $result[$ipaddress][$dnsbl_host] = $is_listed;
              if($is_listed)
                $result[$ipaddress]['inblack']++;
          }
          return $result;
      }
      
      
      //Метод подготовки HTML кода для отправки по почте в текстовом виде..
      function prepHTMLtext($html = null){
          if($html != null){
              return str_replace('<br/>', '
', $html);
          }
      }
      
      
      
      //Метод преобразования массива данных в текст.
      function changeArr($arr = null, $short = false){
          if($arr != null){
              $ret = '';
              foreach($arr as $k => $v){
                  if(count($v) >= 2){
                      $name = $k;
                      if($short){
                          $ret .= $name.'<br/>'.'inblack '.$v['inblack'].'<br/>';
                      }
                      else{
                          $ret .= $name.'<br/>';
                          foreach($v as $kk => $vv){
                              $ret .= $kk.' - '.$vv.'<br/>';
                          }
                          $ret .= '<br/>';
                          unset($kk, $vv);
                      }   
                  }   
              }
              unset($k, $v, $arr, $name);
              return $ret;
          }
      }
      
      
      //Метод получения проверенного IP адреса в текстовом виде.
      function checkIpaddressDNSBL($short = false){
          if(count($this->arr_ip) >= 1){
              $ret = '';
              foreach($this->arr_ip as $k => $v){
                  $arr = $this->dns_bl_check_ipaddress($v);
                  $ret .= $this->changeArr($arr, $short).'<br/>'; 
                  
              }
              unset($k, $v, $arr);
              return $ret;
          }
      }
      
      
      
      
      
      
  }
  //IP для проверки.
  $arr[] = 'XXX.XXX.XXX.XXX';
  $arr[] = 'XXX.XXX.XXX.XXX';
  $spam = new spam($arr);
  
  $p = $spam->checkIpaddressDNSBL();
  $message = 'Результат сканирования IP-адресов в СПАМ листах на DNSBL.<br/><br/>';
  $message =  mb_convert_encoding($message, "WINDOWS-1251", "UTF-8");
  
  //print $p;
  $spam->sendMail('user@mail.com.ua', 'Почтовый сервер', $spam->prepHTMLtext($message.$p));
?>
