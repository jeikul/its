<?php
	/* 连接选择数据库 */
	if ( ($dbName = $_SESSION["mysql_db_name"]) == "" )
	  $dbName = "control" ;
	$dbHost = "localhost" ;
	$dbLink = mysqli_connect ( $dbHost, "otago", "Otago@2022", $dbName )
		or die("Could not connect : " . mysql_error());
	mysqli_select_db ( $dbLink, $dbName ) or die( "Could not SELECT database " . $dbName . ", " . mysql_error () ) ;
	mysqli_query ( $dbLink, "SET NAMES UTF8" ) ;
  fnLog ( "db connected\r\n" ) ;

	function mysql_execute ( $query ) 
	{
	  GLOBAL $dbLink ;
		mysql_log ( $query ) ;
		$result = mysqli_query ( $dbLink, $query ) or die ( "MySQL error #" . mysqli_errno($dbLink) . "," . mysqli_error ($dbLink) . ", <pre>" . $query . "</pre>" ) ;
		return $result ;
	}

	function mysql_log ( $message ) {
	  GLOBAL $dbName ;
		GLOBAL $dbHost ;
    $today = strftime ( "%y%m%d" ) ;
		$fileName = "/var/log/mysql/$dbHost.$today.log" ;
		$handle = fopen ( $fileName, 'a+' ) ;
		if ( $handle !== false ) {
			fprintf ( $handle, "%s\r\n", $message ) ;
			fclose( $handle );
		}
	}

?>
