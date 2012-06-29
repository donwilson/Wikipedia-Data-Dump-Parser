<?php
	
	// Configuration
	
	define('MYSQL_HOST',		"localhost");
	define('MYSQL_USER',		"");
	define('MYSQL_PASSWORD',	"");
	define('MYSQL_DATABASE',	"");
	
	
	// No Man's Land
	
	set_time_limit(0);
	ini_set('memory_limit', "64M");
	
	mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);
	mysql_select_db(MYSQL_DATABASE);
	
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	$path_data = $path ."data". DIRECTORY_SEPARATOR;
	$path_storage = $path ."storage". DIRECTORY_SEPARATOR;
	
	$files = glob($path_data ."enwiki-*-pages-articles.xml-p*");
	
	$tmp_table = "tmp_wikidatadump_". time();
	
	if(empty($files)) {
		print "Nothing to do...". PHP_EOL;
		
		die;
	}
	
	// Create the temporary table, we'll delete it later...
	mysql_query("
		CREATE TABLE `". mysql_real_escape_string($tmp_table) ."` (
			`hash` char(32) NOT NULL,
			`id` int(11) NOT NULL,
			`title` varchar(512) NOT NULL,
			PRIMARY KEY (`hash`),
			UNIQUE KEY `id` (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	") or die("MySQL Error (". __LINE__ ."): ". mysql_error());
	
	// performance enhancements (not yet approved):
	
	mysql_query("SET SESSION UNIQUE_CHECKS=0");
	mysql_query("SET SESSION FOREIGN_KEY_CHECKS=0");
	mysql_query("SET SESSION SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
	
	foreach($files as $file) {
		
		$file = substr($file, 2);
		
		preg_match("#\.xml\-p([0-9]+?)p([0-9]+?)$#si", $file, $match);
		
		print "Reading ". $file ."...". PHP_EOL;
		
		$save_dir_base = $path_storage . "pages-articles_". $match['1'] ."-". $match['2'] . DIRECTORY_SEPARATOR;
		mkdir($save_dir_base, 777, true);
		
		$db_table = "pages_articles_". $match['1'] ."-". $match['2'];
		
		mysql_query("CREATE TABLE IF NOT EXISTS `". mysql_real_escape_string($db_table) ."` LIKE `". mysql_real_escape_string($tmp_table) ."`") or die("MySQL Error (". __LINE__ ."): ". mysql_error());
		mysql_query("ALTER TABLE `". mysql_real_escape_string($db_table) ."` DISABLE KEYS");
		mysql_query("LOCK TABLES `". mysql_real_escape_string($db_table) ."` WRITE");
		
		$xml = new XMLReader();
		$xml->open($file);
		
		$doc = new DOMDocument;
		
		while($xml->read() && $xml->name !== 'page');
		
		while($xml->name === "page") {
			$node = simplexml_import_dom($doc->importNode($xml->expand(), true));
			$xml->next('page');
			
			$page = array(
				 'id'		=> (int)$node->id
				,'hash'		=> md5( (int)$node->id )
				,'title'	=> (string)$node->title
				,'body'		=> (string)$node->revision->text
			);
			
			$save_dir = substr($page['hash'], 0, 2) . DIRECTORY_SEPARATOR . substr($page['hash'], 2, 2);
			$save_file = substr($page['hash'], 4);
			
			mysql_query("INSERT INTO `". mysql_real_escape_string($db_table) ."` SET `id` = '". mysql_real_escape_string($page['id']) ."', `hash` = '". mysql_real_escape_string($page['hash']) ."', `title` = '". mysql_real_escape_string(utf8_encode($page['title'])) ."'") or print("MySQL Error (". __LINE__ ."): ". mysql_error());
			
			if(!is_dir($save_dir_base . $save_dir)) {
				mkdir($save_dir_base . $save_dir, 0777, true);
			}
			
			file_put_contents($save_dir_base . $save_dir . DIRECTORY_SEPARATOR . $save_file, $page['body']);
			chmod($save_dir_base . $save_dir . DIRECTORY_SEPARATOR . $save_file, 0777);
			
			unset($node);
			unset($page);
		}
		
		mysql_query("UNLOCK TABLES");
		mysql_query("ALTER TABLE `". mysql_real_escape_string($db_table) ."` ENABLE KEYS");
		
		$xml->close();
		
		unset($xml);
	}
	
	mysql_query("DROP TABLE `". mysql_real_escape_string($tmp_table) ."`") or print("MySQL Error (". __LINE__ ."): ". mysql_error());
	
	print "All done". PHP_EOL;
