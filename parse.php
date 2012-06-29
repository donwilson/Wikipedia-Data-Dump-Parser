<?php
	set_time_limit(0);
	ini_set('memory_limit', "64M");
	
	mysql_connect("localhost", "", "");
	mysql_select_db("");
	
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	
	$files = glob("./data/enwiki-*-pages-articles.xml-p*");
	
	if(empty($files)) {
		print "Nothing to do...". PHP_EOL;
		
		die;
	}
	
	// Create the temporary table, we'll delete it later...
	$tmp_table_contents = file_get_contents($path ."init.sql");
	
	mysql_query($tmp_table_contents);
	
	foreach($files as $file) {
		
		$file = substr($file, 2);
		
		preg_match("#\.xml\-p([0-9]+?)p([0-9]+?)$#si", $file, $match);
		
		print "Reading ". $file ."...". PHP_EOL;
		
		$save_dir_base = $path . "pages-articles_". $match['1'] ."-". $match['2'] . DIRECTORY_SEPARATOR;
		mkdir($save_dir_base, 777);
		
		$db_table = "pages_articles_". $match['1'] ."-". $match['2'];
		
		
		mysql_query("CREATE TABLE `". mysql_real_escape_string($db_table) ."` LIKE `tmp_pages_articles_tmp`");
		
		$xml = new XMLReader();
		$xml->open($path . $file);
		
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
			
			mysql_query("INSERT INTO `". mysql_real_escape_string($db_table) ."` SET `id` = '". mysql_real_escape_string($page['id']) ."', `hash` = '". mysql_real_escape_string($page['hash']) ."', `title` = '". mysql_real_escape_string($page['title']) ."'") or print(mysql_error() . PHP_EOL);
			
			@mkdir($save_dir_base . $save_dir, 0777, true);
			file_put_contents($save_dir_base . $save_dir . DIRECTORY_SEPARATOR . $save_file, $page['body']);
			chmod($save_dir_base . $save_dir . DIRECTORY_SEPARATOR . $save_file, 0777);
			
			unset($node);
			unset($page);
		}
		
		$xml->close();
		
		unset($xml);
	}
	
	mysql_query("DROP TABLE `tmp_pages_articles_tmp`");
	
	print "All done". PHP_EOL;
