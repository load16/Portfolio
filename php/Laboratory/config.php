<?php
	// База данных!
	//$dbms = 'mysqli';
	$dbhost = 'localhost';
	$dbport = '';
	$dbname = 'XXXXXXXXX';
	$dbuser = 'XXXXX';
	$dbpasswd = 'XXXX';
	
	//Почта
	$mailmessage = false;
	$mailer = 'smtp';
	$mailfrom = 'XXXXX@XXXXXXX';
	$fromname = 'Анализы с ХСМГЦ';
	$smtpuser = 'XXXXX@XXXXXXX';
	$smtppass = 'XXXXXXX';
	$smtphost = 'smtp.XXXXXXX';
    $imaphost = 'imap.XXXXXXX';
	$smtpsecure = 'ssl';
    $imapsecure = 'novalidate-cert'; 
    $simaport = '143';
	$smtpport = '465';
    $subject = 'Анализы с ХСМГЦ';
    $message = 'Здравствуйте!
    Вам отправлены анализы во вложении.    
    Это письмо отправила автоматизированная система, отвечать на него не надо.
    Заранее приносим извинение за возможно ошибочную работу системы, и надеемся на Ваше понимание

    
    
    С уважением,
    КНП ХОР «МСМГЦ-ЦР(о)З»
    ';
	
	
	//Лог
	$patchlog = 'C:\Ampps\www\Laboratory\log';
	
	//Каталог запуска многопоточного скрипта. 
	$patchmach = 'C:\Ampps\www\smssrv\\';
    //Каталог поиска файлов
    $patchseach = 'C:\Ampps\www\Laboratory\search\\';
    $patchfullseach = 'C:\Ampps\www\Laboratory\fullsearch\\'; 
    $patchtemp = 'C:\Ampps\www\Laboratory\temp\\';
    //Возраст учетной записи пациента.
    $age_p = 28; 
	
	//Многопоточный режим.
	$much = false;
	
	//Работа скрипта отправки писем.
    
    $timestart = '09:00:00';
    $timestop = '18:00:00';
    $permit = true;
	$testphone = 'XXXXXXXX';
	

	// @define('PHPBB_INSTALLED', true);
	// @define('DEBUG', true);
	// @define('DEBUG_EXTRA', true);
	  
?>
