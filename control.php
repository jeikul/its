<?php
  $dbControl = "control" ;
  session_start () ;
	require "../util/php/log.php" ;
	require "mysql.php" ;
	require "filllist.php" ;
	if ( $_GET["db"] != "" )
	  mysqli_select_db ( $dbLink, $_GET["db"] ) or die ( "Cannot select " . $_GET["db"] ) ; ;
  $query = "SELECT * FROM $dbControl.tbControlTable WHERE fdName='" . $_GET["table"] . "'" ;
	$result = mysql_execute ( $query ) ;
	if ( $row = mysqli_fetch_assoc ( $result ) ) {
	  $strTableDescription = $row["fdDescription"] ;
		$varTableID = $row["id"] ;
	} else
	  die ( "no data found: " . $query ) ;
	mysqli_free_result ( $result ) ;
	fnLog ( "CP1\r\n" ) ;
	if ( $_GET["action"] == "new" )
	  $strAction = "新增" ;
  else if ( $_GET["action"] == "edit" )
		$strAction = "编辑" ;
	else if ( $_GET["action"] == "delete" )
	  $strAction = "删除" ;
	else if ( $_GET["action"] == "_new" ) {
		$query = "SELECT * FROM $dbControl.tbControlField WHERE fdTableID=" . $varTableID . " ORDER BY fdOrder,id" ;
		$result = mysql_execute ( $query ) ;
		if ( mysqli_num_rows ( $result ) == 0 )
			die ( "no data found: " . $query ) ;
	  $query = "INSERT INTO " . (strncmp ($_GET["table"], "tbControl", 9) == 0 ? "$dbControl." : "") . $_GET["table"] . "(" ;
		$i = 0 ;
		$strList = "" ;
    while ( $row = mysqli_fetch_assoc ( $result ) ) {
		  if ( $i ++ > 0 )
			  $strList .= "," ;
		  $strList .= $row["fdName"] ;
		}
		$query .= $strList . ") VALUES (" ;
		$temp = "SELECT " . $strList . " FROM " . (strncmp ($_GET["table"], "tbControl", 9) == 0 ? "$dbControl." : "") . $_GET["table"] . " WHERE id=-1" ;
		fnLog ( "temp=$temp\r\n" ) ;
		$record = mysql_execute ( $temp ) ;
		mysqli_data_seek ( $result, 0 ) ;
		$i = 0 ;
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
		  if ( $i > 0 )
			  $query .= "," ;
			$objField = mysqli_fetch_field ( $record ) ;
			$liType = $objField->type ;
			$varSize = $objField->length ;
			fnLog ( "liType=$liType($varSize)\r\n" ) ;
			if ( $row["fdHide"] != 1 ) {
			  fnLog ( "CP $i,liType=$liType($varSize),row[fdInitType]=" . $row["fdInitType"]. ",post[row[fdName]]=" . $_POST[$row["fdName"]] . "," . MYSQLI_TYPE_NEWDECIMAL . "\r\n" ) ;
				if ( $liType == MYSQLI_TYPE_NEWDECIMAL || $liType == MYSQLI_TYPE_FLOAT || $liType == MYSQLI_TYPE_SHORT || $liType == MYSQLI_TYPE_LONG && $varSize > 4 ) {
				  fnLog ( "here\r\n" ) ;
				  if ( $_POST[$row["fdName"]] == "" )
					  $query .= "0" ;
					else
  				  $query .= $_POST[$row["fdName"]] ;
				} else if ( $liType == MYSQLI_TYPE_TINY ) {
				  if ( $_POST[$row["fdName"]] == "on" )
					  $query .= "1" ;
					else
  				  $query .= "0" ;
				} else if ( $liType == MYSQLI_TYPE_VAR_STRING && $varSize == 77 ) {
				  $query .= "'" . crypt(trim($_POST[$row["fdName"]]), md5(trim($_POST[$row["fdName"]]))) . "'" ;
				} else if ( $liType == MYSQLI_TYPE_VAR_STRING || $liType == MYSQLI_TYPE_BLOB || $liType == MYSQLI_TYPE_DATE ) {
				  $query .= "'" . addslashes (trim($_POST[$row["fdName"]])) . "'" ;
				} else if ( $liType == MYSQLI_TYPE_TIME )
				  $query .= "'" . $_POST[$row["fdName"]] . "'" ;
			} else if ( $row["fdInitType"] == 1 ) {
			  if ( $liType == MYSQLI_TYPE_LONG )
				  $query .= $row["fdInitValue"] ;
				else if ( $liType == MYSQLI_TYPE_VAR_STRING || $Type == MYSQLI_TYPE_BLOB )
				  $query .= "'" . $row["fdInitValue"] . "'" ;
			} else if ( $row["fdInitType"] == 2 ) {
			  if ( $liType == MYSQLI_TYPE_LONG )
				  $query .= $_SESSION[$row["fdInitValue"]] ;
				else if ( $liType == MYSQLI_TYPE_VAR_STRING || $liType == MYSQLI_TYPE_BLOB )
				  $query .= "'" . $_SESSION[$row["fdInitValue"]] . "'" ;
			} else if ( $row["fdInitType"] == 3 ) {
			  if ( $liType == MYSQLI_TYPE_LONG )
				  if ( $_GET[$row["fdName"]] == "" )
					  $query .= "0" ;
				  else
				    $query .= $_GET[$row["fdName"]] ;
				else if ( $liType == MYSQLI_TYPE_VAR_STRING || $liType == MYSQLI_TYPE_BLOB )
				  $query .= "'" . $_GET[$row["fdName"]] . "'" ;
			} else if ( $row["fdInitType"] == 4 ) {
			  $strTemp = "SELECT MAX(" . $row["fdName"] . ") FROM " . $_GET["table"] ;
				if ( $_GET["clause"] != "" )
				  $strTemp .= " WHERE " . $_GET["clause"] ;
				$res_temp = mysql_execute ( $strTemp ) ;
        $row_temp = mysqli_fetch_row ( $res_temp ) ;
				$query .= ($row_temp[0] + 1) ;
				mysqli_free_result ( $res_temp ) ;
			} else if ( $row["fdInitType"] == 5 ) {
			  for ( $newID = 1; ; $newID = $newID * 2 ) {
			    $strTemp = "SELECT * FROM " . $_GET["table"] . " WHERE " . $row["fdName"] . "=" . $newID ;
					if ( $_GET["clause"] != "" )
						$strTemp .= " AND " . $_GET["clause"] ;
					$res_temp = mysql_execute ( $strTemp ) ;
					$rows_temp = mysqli_num_rows ( $res_temp ) ;
					mysqli_free_result ( $res_temp ) ;
					if ( $rows_temp == 0 ) {
					  $query .= $newID ;
					  break ;
					}
				}
			} else if ( $row["fdInitType"] == 6 ) {
			  $strTemp = "SELECT * FROM $dbControl.tbControlRelate WHERE fdSlave='" . $_GET["table"] . "' AND LENGTH(fdCommonField)>1" ;
				$res_temp = mysql_execute ( $strTemp ) ;
				$liNeedClause = mysqli_num_rows ( $res_temp ) > 0 ;
				mysqli_free_result ( $res_temp ) ;
			  for ( $newID = 1; ; $newID = $newID + 1 ) {
			    $strTemp = "SELECT * FROM " . $_GET["table"] . " WHERE " . $row["fdName"] . "=" . $newID ;
					if ( $liNeedClause && $_GET["clause"] != "" )
						$strTemp .= " AND " . $_GET["clause"] ;
					$res_temp = mysql_execute ( $strTemp ) ;
					$rows_temp = mysqli_num_rows ( $res_temp ) ;
					mysqli_free_result ( $res_temp ) ;
					if ( $rows_temp == 0 ) {
					  $query .= $newID ;
					  break ;
					}
				}
			}
		  fnLog ( "query=$query\r\n" ) ;
			$i ++ ;
		}
		mysqli_free_result ( $record ) ;
		mysqli_free_result ( $result ) ;
		$query .= ")" ;
		mysql_execute ( $query ) ;
    die ( "<html><head><meta http-equiv=refresh content=\"0;url=" . $_SESSION["control_url"] . "\"></head><body></body></html>" ) ;
	} else if ( $_GET["action"] == "_edit" ) {
		$query = "SELECT * FROM $dbControl.tbControlField WHERE fdTableID=" . $varTableID . " AND (fdHide IS NULL OR fdHide!=1) ORDER BY fdOrder,id" ;
		$result = mysql_execute ( $query ) ;
		if ( mysqli_num_rows ( $result ) == 0 )
			die ( "no data found: " . $query ) ;
    $query = "SELECT " ;
    while ( $row = mysqli_fetch_assoc ( $result ) )
		  $query .= $row["fdName"] . "," ;
		$query .= "id FROM " . (strncmp ($_GET["table"], "tbControl", 9) == 0 ? "$dbControl." : "") . $_GET["table"] . " WHERE id=" . $_GET["id"] ;
		$record = mysql_execute ( $query ) ;
	  $query = "UPDATE " . (strncmp ($_GET["table"], "tbControl", 9) == 0 ? "$dbControl." : "") . $_GET["table"] . " SET " ;
		mysqli_data_seek ( $result, 0 ) ;
		$i = 0 ;
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
		  fnLog ( "i=$i\r\n" ) ;
		  $objField = mysqli_fetch_field ( $record ) ;
			$liType = $objField->type ;
			$varSize = $objField->length ;
		  if ( $i ++ > 0 )
			  $query .= "," ;
			if ( $liType == MYSQLI_TYPE_NEWDECIMAL || $liType == MYSQLI_TYPE_FLOAT || $liType == MYSQLI_TYPE_SHORT || $liType == MYSQLI_TYPE_LONG && $varSize == 11 ) {
			  $query .= $row["fdName"] . "=" ;
				if ( $_POST[$row["fdName"]] == "" )
					$query .= "0" ;
				else
					$query .= $_POST[$row["fdName"]] ;
			} else if ( $liType == MYSQLI_TYPE_TINY ) {
			  $query .= $row["fdName"] . "=" ;
				if ( $_POST[$row["fdName"]] == "on" )
					$query .= "1" ;
				else
					$query .= "0" ;
			} else if ( $liType == MYSQLI_TYPE_VAR_STRING && $varSize == 77 ) {
				$query .= $row["fdName"] . "='" . crypt(trim($_POST[$row["fdName"]]), md5(trim($_POST[$row["fdName"]]))) . "'" ;
			} else if ( $liType == MYSQLI_TYPE_VAR_STRING || $liType == MYSQLI_TYPE_BLOB ) {
				$query .= $row["fdName"] . "='" . addslashes (trim($_POST[$row["fdName"]])) . "'" ;
			} else if ( $liType == MYSQLI_TYPE_TIME )
			  $query .= $row["fdName"] . "='" . $_POST[$row["fdName"]] . "'" ;
		}
		mysqli_free_result ( $record ) ;
		mysqli_free_result ( $result ) ;
		$query .= " WHERE id=" . $_GET["id"] ;
		if ( $_GET["clause"] != "" )
		  $query .= " AND " . $_GET["clause"] ;
		mysql_execute ( $query ) ;
		fnLog ( "SESSION[control_url]=" . $_SESSION["control_url"] . "\r\n" ) ;
    die ( "<html><head><meta http-equiv=refresh content=\"0;url=" . $_SESSION["control_url"] . "\"></head><body></body></html>" ) ;
	} else
	  die ( "unhandled action:" . $_GET["action"] ) ;
?>
<html>
<head>
  <title><?php print $strAction . $strTableDescription; ?></title>
</head>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<body>
  <table width=100% cellspacing=0><tr bgcolor=#88FAFA>
	  <td><?php print $strAction . $strTableDescription; ?></td>
		<td align=right><a href=# onclick="history.back()">返回</a></td>
	</tr></table>
	<?php
	  $varOldURI = $_SERVER["REQUEST_URI"] ;
		$varPos = strpos ( $varOldURI, "action=" ) ;
		$varNewURI = substr_replace ( $varOldURI, "_", $varPos + 7, 0 ) ;
	?>
	<table align=center><form name=fmControl method=post action=<?php print $varNewURI; ?>>
	  <?php
		  $query = "SELECT * FROM $dbControl.tbControlField WHERE fdTableID=" . $varTableID . " AND (fdHide IS NULL OR fdHide=0) ORDER BY fdOrder, id" ;
			$result = mysql_execute ( $query ) ;
			if ( mysqli_num_rows ( $result ) == 0 )
			  die ( "no data found: " . $query ) ;
			$query = "SELECT " ;
			while ( $row = mysqli_fetch_assoc ( $result ) )
			  $query .= $row["fdName"] . "," ;
		  $query .= "id FROM " . (strncmp ($_GET["table"], "tbControl", 9) == 0 ? "$dbControl." : "") . $_GET["table"] . " WHERE id=" . $_GET["id"] ;
			fnLog ( "_get[clause]=" . $_GET["clause"] . "<<<" ) ;
			if ( $_GET["clause"] != "" )
			  $query .= " AND " . $_GET["clause"] ;
			$record = mysql_execute ( $query ) ;
			$line = mysqli_fetch_assoc ( $record ) ;
			$i = 0 ;
			mysqli_data_seek ( $result, 0 ) ;
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
			  print "<tr valign=top><td align=right>" . $row["fdDescription"] . ":</td><td>" ;
				$objField = mysqli_fetch_field ( $record ) ;
				$liType = $objField->type ;
				$varSize = $objField->length ;
				fnLog ( "liType=" . $objField->type . ", varSize=" . $objField->length . ",MYSQLI_TYPE_VAR_STRING=" . (MYSQLI_TYPE_VAR_STRING + 0) . ",row[fdInitType]=" . $row["fdInitType"] ) ;
				if ( mysqli_num_rows ( $record ) > 0 )
					$varInitValue = stripslashes ($line[$row["fdName"]]) ;
				else if ( $row["fdInitType"] == 1 )
					$varInitValue = $row["fdInitValue"] ;
				else if ( $row["fdInitType"] == 2 )
					$varInitValue = $_SESSION[$row["fdInitValue"]] ;
				else if ( $row["fdInitType"] == 3 )
					$varInitValue = $_GET[$row["fdInitValue"] != "" ? $row["fdInitValue"] : $row["fdName"]] ;
				else
				  $varInitValue = "" ;
			  fnLog ( "row[fdSelect]=" . $row["fdSelect"] . "\r\n" ) ;
				if ( $liType == MYSQLI_TYPE_VAR_STRING && $varSize == 77 ) {
				  print "<input type=password name=" . $row["fdName"] . ">" ;
			  } else if ( $liType == MYSQLI_TYPE_TINY ) {
				  print "<input type=checkbox name=" . $row["fdName"] ;
					if ( $varInitValue != 0 )
					  print " checked" ;
					print ">" ;
			  } else if ( /*$liType == MYSQLI_TYPE_LONG && $varSize >= 6 && */$row["fdSelect"] == "" || $liType == MYSQLI_TYPE_VAR_STRING || $liType == MYSQLI_TYPE_BLOB || $liType == MYSQLI_TYPE_TIME || $liType == MYSQLI_TYPE_FLOAT ) {
				  print "<input type=text name=" . $row["fdName"] . " value=\"" . htmlspecialchars($varInitValue) /*. (strlen($varInitValue)>0 && ord(substr($varInitValue,-1))>127?' ':'')*/ . "\" size=" ;
					if ( $varSize > 255 )
					  print "85" ;
					else if ( $varSize > 64 )
  					print round ($varSize / 3) ;
					else
					  print $varSize ;
					print ">" ;
				} else if ( /*$liType == MYSQLI_TYPE_SHORT &&*/ $row["fdSelect"] != "" ) {
				  print "<select name=" . $row["fdName"] . ">" ;
					$select = stripslashes ($row["fdSelect"]) ;
					$query = "" ;
					$query .= strtok ( $select, "$" ) ;
					while ( $tok = strtok ( "$" ) ) {
					  $word = strtok ( $tok, " " ) ;
						if ( $_GET[$word] != "" )
  						$query .= $_GET[$word] ;
						else if ( $_SESSION[$word] != "" )
  						$query .= $_SESSION[$word] ;
						else if ( $row[$word] != "" )
						  $query .= $row[$word] ;
						else
						  $query .= "-1" ;
						$select = substr ( $tok, strlen ($word), strlen ($tok) - strlen($word) ) ;
						$query .= " " . strtok ( $select, "$" ) ;
					}
					if ( $varInitValue == "" )
					  $varInitValue = -1 ;
				  fnLog ( "varInitValue=$varInitValue" ) ;
					fnFilllist ( $query, $varInitValue, 0, 1 ) ;
					print "<input type=text name=txt_" . $row["fdName"] . " onchange='fnFilter_" . $row["fdName"] . "()'>\r\n" ;
					print "<script>function fnFilter_" . $row["fdName"] . "() {\r\n" ;
					?>
					  str = "abc" ;
					  $.ajax({
						  url:"get_id_by_name.php",
							dataType: "TEXT",
							type: "get",
							data: {
							  table: "tbFood",
							  name: document.forms["fmControl"]["txt_<?php print $row["fdName"];?>"].value
							},
							success: function (data) {
					      document.forms["fmControl"]["<?php print $row["fdName"];?>"].value = data ;
							},
							error: function() {
							}
						}) ;
					<?php
					print "}</script>\r\n" ;
				} else
				  print "Not handled type:$liType($varSize)" ;
				print "</td></tr>\r\n" ;
				$i ++ ;
			}
			mysqli_free_result ( $record ) ;
			mysqli_free_result ( $result ) ;
		?>
	  <tr><td align=right></td><td>
		  <input type=submit value=确定>
			<input type=reset value=取消 onclick="history.back()">
		</td></tr>
	</form></table>
<?php
  require "footer.php" ;
?>
</body>
</html>
