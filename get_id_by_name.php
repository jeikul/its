<?php
  $dbControl = "control" ;
  session_start () ;
	require "../util/php/log.php" ;
	require "mysql.php" ;
	if ( $_GET["db"] != "" )
	  mysqli_select_db ( $dbLink, $_GET["db"] ) or die ( "Cannot select " . $_GET["db"] ) ; ;

  fnLog ( "name=" . $_GET["name"] ) ;
	$lsSQL = "SELECT id FROM " . $_GET["table"] . " WHERE fdName LIKE '%" . $_GET["name"] . "%'" ;
	$rs = mysql_execute ( $lsSQL ) ;
	$row = mysqli_fetch_assoc ( $rs ) ;
	if ( $row )
	  print $row["id"] ;
	mysqli_free_result ( $rs ) ;
?>
