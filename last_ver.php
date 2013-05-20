<!DOCTYPE html>
<html class="no-js" dir="ltr" lang="en-US">
	<head>
	<title>xml sitemap checker</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" media="all" href="assets/style.css" />
	<link rel="stylesheet" id="responsive-css"  href="assets/responsive.css" type="text/css" media="screen" />
	<!--[if IE 8]>
        <link rel="stylesheet" href="assets/ie8.css" />
    <![endif]-->
	  
	</head>
	<body>

	
	
	<div class="sitemap">
	  
  <?php
  
	$url_checked = 0; 
	$errors = 0; 
	set_time_limit(3000000);  
	$xml_tag = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'; 

	
	function ping_bing($url){
  
  echo "<h2>Bing... </h2>";
  
  
  $ping_url = 'http://www.bing.com/webmaster/ping.aspx?siteMap='.$url;

  
  $lines = file($ping_url);
  
  if ($lines > 0) { echo "<h2>NOTIFIED</h2></br>"; 
  } else {  echo "<h2>Failed</h2></br>"; }

  }
	
	
	function ping_google($url){
  
	echo "<h2>Google... </h2>";  
	$ping_url = 'http://www.google.com/webmasters/sitemaps/ping?sitemap='.$url;
	$lines = file($ping_url);
  
	if ($lines > 0) { echo "<h2>NOTIFIED</h2></br>"; 
  } else {  echo "<h2>Failed</h2></br>"; }

  
  }
  
  function ping_yahoo($url){
  
	$ping_url = 'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=YahooDemo&url='.$url;

	echo "<h2>Yahoo... </h2>";
	$lines = file($ping_url);
  
	if ($lines > 0) { echo "<h2>NOTIFIED</h2></br>"; 
  } else {  echo "<h2>Failed</h2></br>"; }

  }
	

  function query_tracking($url, $time, $url_checked, $errors){     

      

	mysql_connect("localhost","","") or die(mysql_error()); 
	
  	mysql_select_db("") or die(mysql_error()); 

  	$query = "INSERT INTO sitepxaa_dbxml (url, time, url_checked, errors) VALUES ('$url', '$time', '$url_checked', '$errors')";
	
  	mysql_query($query) or die(mysql_error()); 
	
  	
  }

	function get_file_extension($file_name)   
	{
		return substr(strrchr($file_name,'.'),1);
	}
  

  	function gunzip_or_not($file_name) {    
  	echo $file_name;

    if (substr(strrchr($file_name,'.'),1) == "gz") {   
	
	
  	echo "<h1>Gunzip detected!</h1>";
	
  	$string = implode("", gzfile($file_name));
  	$gzip_temporary = uniqid('temp', true) . '.tmp';
  	$fp = fopen($gzip_temporary, "w");
  	fwrite($fp, $string, strlen($string));
  	fclose($fp);
	
  	return $gzip_temporary;
	
  }
     else return $file_name;
  }
  
  
    function check_url($url) {        
  
    if (!(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url))) {   
	
  	echo "<h1>URL entered is not valid</h1>";
  	echo "<h2>Go back to the <a href=\"index.html\">homepage</a></h2>";
  	exit();
  } 
	
      if (!preg_match("~^(?:f|ht)tp?://~i", $url)) {

  	$url = "http://" . $url;
	
      }
  	return $url;
  }
  
 function remote_xml_exists($url) {     

		$curl = curl_init($url);

      curl_setopt($curl, CURLOPT_NOBODY, true);

      $result = curl_exec($curl);

      $ret = false;

      if ($result !== false) {

          $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
          if ($statusCode == 200) {
              $ret = true;   
          }
      }

      curl_close($curl);

      return $ret;
  }
	$url_file = "url_list.txt";

  	$sitemap_url = $_REQUEST["sitemap_user_url"]; 
	
  	$valid_url = check_url($sitemap_url); 
	
	
	$url_file_handler = fopen($url_file, 'a');
  	fwrite($url_file_handler, "\n");
	fwrite($url_file_handler, $valid_url);
	fclose($url_file_handler);
  	
	
  	if (remote_xml_exists($valid_url) == false) {
   
  	echo "<h1>File not found at $sitemap_url</h1>";
  	echo "<h2>Go back to the <a href=\"index.html\">homepage</a></h2>";
  	exit();
  } 

    echo "<h1>Checking $sitemap_url</h1>";


    $checked_url = gunzip_or_not($valid_url);  
  
  
    
    if ( ! $objDOM = new DOMDocument()) exit();
  
    $objDOM->load($checked_url);
	
    $note = $objDOM->getElementsByTagName("url");  
  
  
    while (true) {
  	$xml_filename = uniqid('sitemap', true) . '.xml';
  	if (!file_exists(sys_get_temp_dir() . $xml_filename)) break;
  	}
	
      $xml_filehandler = fopen($xml_filename, "w"); 
  	  fwrite($xml_filehandler,$xml_tag);

    while (true) {
  	$csv_filename = uniqid('log', true) . '.csv';
  	if (!file_exists(sys_get_temp_dir() . $csv_filename)) break;
  	}
    
  	$csv_filehandler = fopen($csv_filename, "w"); 
		
	echo "<pre><code>" ;
  

  	list($usec, $sec) = explode(' ', microtime());
      $script_start = (float) $sec + (float) $usec;


    foreach( $note as $value )  {
  
  

    $url = $value->getElementsByTagName("loc");
  	$strurl  = $url->item(0)->nodeValue;
	

  	$url = $value->getElementsByTagName("lastmod");
  	$str_lastmod  = ($url->item(0)->nodeValue);
	

  	$url = $value->getElementsByTagName("changefreq");
  	$str_changefreq  = ($url->item(0)->nodeValue);
	 
  	//get priority
  	$url = $value->getElementsByTagName("priority");
  	$str_priority  = ($url->item(0)->nodeValue);
	
	
  	$strurl = ltrim($strurl);
  	$headers = get_headers($strurl, 1);
    
  	$url_checked++;  
	
  	if($headers[0] == 'HTTP/1.1 200 OK') {

  			echo "<span>URL: $strurl $headers[0] </span>" ;
  			fwrite($xml_filehandler,"<loc>$strurl</loc>");			
  			if ($str_lastmod >0) fwrite($xml_filehandler,"<lastmod>$str_lastmod</lastmod>");
  			if ($str_changefreq >0) fwrite($xml_filehandler,"<changefreq>$str_changefreq</changefreq>");
  			if ($str_priority >0) fwrite($xml_filehandler,"<priority>$str_priority</priority>");
  	}
    
	else {
  				echo "$strurl <br> $headers[0] URL does not exist <br>";
  				fwrite($csv_filehandler,"$strurl; $headers[0] - ERROR<br>");
  				$errors++;
  			}
		
	
	
  	
  	}
     echo "</pre></code></div>" ;	
     echo "<div class='steps'>";

	 
  	fwrite($xml_filehandler,"</urlset>");
  	fclose($xml_filehandler);
  	fclose($csv_filehandler); 	 
	
	
  	//end time
  	list($usec, $sec) = explode(' ', microtime());
      $script_end = (float) $sec + (float) $usec;
  	$elapsed_time = round($script_end - $script_start, 2);

	
  	echo "<h1>Done! Sitemap checked in $elapsed_time seconds</h1>";
  	echo "Download <a href=\"$xml_filename\">XML Sitemap</a><br>";
	
  	if (filesize($csv_filename) > "0") {  
  	echo "Download <a href=\$csv_filename\">CSV logfile</a>";
  	}
	
  	else { 
  				echo "<h3>No errors found. Sitemap at $sitemap_url is fine.</h3>";
				
  			}
			
	echo "<h1>Pinging search engines....</h1></br>";		
			
	ping_bing($sitemap_url);
  	ping_google($sitemap_url);
	
	
  		query_tracking($checked_url, $elapsed_time, $url_checked, $errors);

  	?>
	
        </div>

	</body>
</html>
