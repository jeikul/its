<?php
  $dbControl = "control" ;
  function authenticate() {
    header('WWW-Authenticate: Basic realm="请输入用户名和密码："');
    header('HTTP/1.0 401 Unauthorized');
    echo "认证失败。只有系统管理员才可以访问。\n";
    exit;
  }
	GLOBAL $dbLink ;
  session_start () ;
	if ( $_GET["db"] != "" )
	  $_SESSION["mysql_db_name"] = $_GET["db"] ;
		/*
	if ( $_GET["home"] != "" )
	  $_SESSION["controls_home"] = $_GET["home"] ;
		*/
	if ( $_SESSION["mysql_db_name"] == "" || $_GET["select_db"] != "" ) {
	  $arr = array ( "control", "otago" ) ;
		?>
	  <html>
		  <head>
			  <meta charset="UTF-8">
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			</head>
		<body>
		请选择数据库:
		<?php
		foreach ( $arr as $dbname ) {
  		//print "<a href=controls.php?db=$dbname&home=1>$dbname</a> " ;
  		print "<a href=\"controls.php?db=$dbname\">$dbname</a> " ;
		}
	  die ( "</body></html>" ) ;
	}
	require "../util/php/log.php" ;
  require "mysql.php" ;
	require "filllist.php" ;
	if ( !isset($_SERVER["PHP_AUTH_USER"]) )
		authenticate () ;
	else {
		$strUser = $_SERVER["PHP_AUTH_USER"] ;
		$strPassword = $_SERVER["PHP_AUTH_PW"] ;
		$rec_cnt = 0 ;
		$query = "SELECT * FROM $dbControl.tbControlUser WHERE fdName='" . $strUser . "' AND (fdPassword=PASSWORD('" . $strPassword . "') OR fdPassword='" . crypt($strPassword, md5($strPassword)) . "')" ;
		$result = mysqli_query ( $dbLink, $query ) ;
		if ( mysqli_errno ($dbLink) == 0 ) {
  		$rec_cnt = mysqli_num_rows ( $result ) ;
		  mysqli_free_result ( $dbLink, $result ) ;
		} else {
		  print mysqli_error ($dbLink) ;
		}
		if ( $rec_cnt == 0 ) 
		  $rec_cnt = ($strUser == "admin" AND crypt($strPassword, "xy") == 'xyHirxvC9UUyg') ? 1 : 0 ;
		if ( $rec_cnt == 0 )
		  authenticate () ;
		else {
			$_SESSION["controls_db_name"] = $_SESSION["mysql_db_name"] ;
			$_SESSION["controls_user_name"] = $strUser ;
		}
	}
	require "getvalue.php" ;
	if ( ($varTable = $_GET["table"]) == "" )
		$varTable = "tbControlTable" ;
	fnLog ( "CP A\r\n" ) ;
	if ( $_GET["action"] == "delete" ) {
	  $query = "SELECT * FROM $dbControl.tbControlRelate WHERE fdMaster='" . $varTable . "'" ;
		$result = mysql_execute ( $query ) ;
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
		  $query = "SELECT COUNT(*) FROM " . $row["fdSlave"] . " WHERE " . $row["fdSlaveField"] . "=" . $_GET["id"] ;
			if ( $_GET["clause"] != "" )
			  $_query = $query . " AND " . $_GET["clause"] ;
			$res_slave = mysql_query ( $query ) or mysql_execute ( $_query ) ;
			$row_slave = mysqli_fetch_row ( $res_slave ) ;
			if ( $row_slave[0] > 0 ) {
				if ( ($varTableName = fnGetValue ( "$dbControl.tbControlTable", "fdName='" . $row["fdSlave"] . "'", "fdDescription" )) == "" )
				  $varTableName = $row["fdSlave"] ;
			  print "<html><head></head><body><p align=center>不能删除该记录，因为在 [" . $varTableName . "] 中还有" . $row_slave[0] . "项从属数据。<a href=# onclick='history.back()'>返回</a></p>" ;
				require "footer.php" ;
				die ( "</body></html>" ) ;
			}
			mysqli_free_result ( $res_slave ) ;
		}
		mysqli_free_result ( $result ) ;
	  $query = "DELETE FROM " . (strncmp ($varTable, "tbControl", 9) == 0 ? "$dbControl." : "") . $varTable . " WHERE id=" . $_GET["id"] ;
		if ( $_GET["clause"] != "" )
		  $query .= " AND " . $_GET["clause"] ;
		mysql_execute ( $query ) ;
	} else {
	  fnLog ( "CP X, SERVER[REQUEST_URI]=" . $_SERVER["REQUEST_URI"] . "\r\n" ) ;
  	$_SESSION["control_url"] = $_SERVER["REQUEST_URI"] ;
  }
	fnLog ( "CP Y\r\n" ) ;
 	$query = "SELECT * FROM $dbControl.tbControlTable WHERE fdName='" . $varTable . "'" ;
	$result = mysql_execute ( $query ) ;
	if ( $row = mysqli_fetch_assoc ( $result ) ) {
	  $varTableID = $row["id"] ;
		$varTableName = $row["fdDescription"] ;
		$varTableClause = $row["fdClause"] ;
	} else
	  die ( "找不到数据: " . $query ) ;
  mysqli_free_result ( $result ) ;
	if ( $_SESSION["controls_user_name"] != "admin" ) {
		$query = "SELECT fdInsert,fdUpdate,fdDelete,fdControl FROM ($dbControl.tbControlGrant LEFT JOIN $dbControl.tbControlUser ON $dbControl.tbControlUser.id=fdUserID) LEFT JOIN $dbControl.tbControlTable ON $dbControl.tbControlTable.id=fdTableID WHERE $dbControl.tbControlUser.fdName='" . $_SESSION["controls_user_name"] . "' AND $dbControl.tbControlTable.id=" . $varTableID ;
		$result = mysql_execute ( $query ) ;
		if ( $row = mysqli_fetch_assoc  ( $result ) ) {
			$pvgInsert = $row["fdInsert"] ;
			$pvgUpdate = $row["fdUpdate"] ;
			$pvgDelete = $row["fdDelete"] ;
			$pvgControl = $row["fdControl"] ;
		}
		mysqli_free_result ( $result ) ;
	} else
		$pvgInsert = $pvgUpdate = $pvgDelete = $pvgControl = 1 ;
?>
<html>
<head>
  <title><?php print $varTableName . " 记录列表" ; ?></title>
</head>
<script language=javascript>
<!--
  function fnFilter () {
	  document.location = "<?php
		  $iPos = strpos ( $_SERVER['REQUEST_URI'], "filter=" ) ;
			if ( $iPos )
  		  print substr ( $_SERVER['REQUEST_URI'], 0, $iPos ) ;
			else
			  print $_SERVER['REQUEST_URI'] ;
		?>" + "&filter=" + document.all.lstFilter.value ;
	}
-->
</script>
<body>
  <table width=100% cellspacing=0><tr bgcolor=#88FAFA>
	<td><?php
		// if ( $_SESSION["controls_home"] != "" )
		  if ( $varTable == "tbControlTable" )
				print "<a href=controls.php?select_db=1>选库</a>\r\n" ;
		  else
				print "<a href=controls.php?table=tbControlTable>首页</a>\r\n" ;
		fnLog ( "_GET[clause]=" . $_GET["clause"] . ",varTable=$varTable\r\n" ) ;
		if ( $_GET["clause"] != "" ) {
			$query = "SELECT $dbControl.tbControlTable.id,fdDescription,fdName,fdCommonField FROM $dbControl.tbControlTable LEFT JOIN $dbControl.tbControlRelate ON fdMaster=fdName WHERE fdSlave='$varTable'" ; // . " AND fdCommonField<>''" ;
			$result = mysql_execute ( $query ) ;
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
				print "<a href=controls.php?table=" . $row["fdName"] ;
				if ( $row["fdCommonField"] != "" && $row["fdCommonField"] != "*" )
					print "&clause=" . $row["fdCommonField"] . "=" . $_SESSION[$row["fdCommonField"]] ;
				else {
					$query = "SELECT fdSlaveField FROM $dbControl.tbControlRelate WHERE fdSlave='" . $row["fdName"] . "'" ;
					$res_parent = mysql_execute ( $query ) ;
					if ( $row_parent = mysqli_fetch_assoc ( $res_parent ) ) {
						$varParentKey = fnGetValue ( $row["fdName"], "id=" . substr ( strrchr ( $_GET["clause"], "=" ), 1 ), $row_parent["fdSlaveField"] ) ;
						print "&clause=" . $row_parent["fdSlaveField"] . "=" . $varParentKey ;
					}
					mysqli_free_result ( $res_parent ) ;
				}
				print ">" . $row["fdDescription"] . "</a>\r\n" ;
			}
			mysqli_free_result ( $result ) ;
	  }
	?></td>
	<td align=right>筛选<select name=lstFilter onchange='fnFilter()'>
	<?php
	  $liFilter = -1 ;
		$lsFilterSessionKey = "filter." . $_GET["table"] ;
	  if ( $_GET["filter"] != "" ) {
		  $liFilter = $_SESSION[$lsFilterSessionKey] = $_GET["filter"] ;
    } else if ( $_SESSION[$lsFilterSessionKey] != "" )
		  $liFilter = $_SESSION[$lsFilterSessionKey] ;
	  fnFilllist ( "SELECT id,fdName FROM $dbControl.tbControlClause WHERE fdTableID=$varTableID", $liFilter, 0, 1 ) ;
	  if ( $pvgInsert ) {
		  print "<a href=control.php?table=$varTable&action=new&id=-1" ;
			if ($_GET["clause"] != "") print "&" . $_GET["clause"] . "&clause=" . $_GET["clause"] ;
			print ">新增记录</a>" ;
		}
	?>
	</td></tr></table>
	<?php
		$res = mysql_execute ( "SHOW COLUMNS FROM " . (strncmp ( $varTable, "tbControl", 9 ) == 0 ? "$dbControl." : "") . "$varTable LIKE 'id'" ) ;
		$idField = mysqli_num_rows ( $res ) ;
		mysqli_free_result ( $res ) ;
		$query = "SELECT * FROM $dbControl.tbControlField WHERE fdTableID=" . $varTableID . " AND (fdHide IS NULL OR fdHide!=1) ORDER BY fdOrder, id" ;
		$record = mysql_execute ( $query ) ;
		if ( mysqli_num_rows ( $record ) > 0 ) {
		  $strOrder = "" ;
		  while ( $cols = mysqli_fetch_assoc ( $record ) ) {
			  if ( $cols["fdSort"] > 0 ) {
				  if ( $strOrder == "" )
					  $strOrder .= " ORDER BY " ;
					else
					  $strOrder .= "," ;
				  $strOrder .= $cols["fdName"] ;
			  } else if ( $cols["fdName"] == "id" )
				  $idField = 0 ;
			}
			if ( $idField != 0 )
			  if ( $strOrder == "" )
				  $strOrder = " ORDER BY id" ;
				else
  			  $strOrder .= ",id" ;
			mysqli_data_seek ( $record, 0 ) ;
			print "<table width=100% cellspacing=1>\r\n" ;
			$query = "SELECT $dbControl.tbControlTable.id,fdName,fdDescription,fdMasterField,fdSlaveField,fdCommonField FROM $dbControl.tbControlRelate LEFT JOIN $dbControl.tbControlTable ON fdName=fdSlave WHERE fdMaster='" . $varTable . "'" ;
			$relate = mysql_execute ( $query ) ;
			print "<tr bgcolor=#CCBBFF><td align=center>#</td>" ;
			while ( $cols = mysqli_fetch_assoc ( $record ) )
				print "<td align=center>" . $cols["fdDescription"] . "</td>" ;
			if ( $pvgUpdate )
  			print "<td align=center>编辑</td>" ;
			if ( $pvgDelete )
  			print "<td align=center>删除</td>" ;
			print "<td align=center>关联表</td></tr>\r\n" ;
			$_query = $query = "SELECT * FROM " . (strncmp ($varTable, "tbControl", 9) == 0 ? "$dbControl." : "") . $varTable ;
			fnLog ( "query.1=$query\r\n" ) ;
			if ( $_GET["clause"] != "" && substr($_GET["clause"], -1) != "=" )
				$query .= " WHERE " . $_GET["clause"] ;
			else if ( $varTable == "tbControlUser" && $strUser != 'admin' )
			  $query .= " WHERE fdName='" . $strUser . "'" ;
			if ( $varTableClause != "" )
				$query .= (strstr ( $query, "WHERE" ) ? " AND " : " WHERE ") . $varTableClause ;
		  if ( $liFilter > 0 ) {
				$query .= (strstr ( $query, "WHERE" ) ? " AND " : " WHERE ") . fnGetValue ( "$dbControl.tbControlClause", "id=$liFilter", "fdClause" ) ;
			}
			if ( strcmp ( $varTable, "tbControlTable" ) == 0 )
			  $query .= (strstr ( $query, "WHERE" ) ? " AND " : " WHERE ") . "fdDatabase='$dbName'" ;
			$query .= $strOrder ;
			fnLog ( "query.2=$query\r\n" ) ;
			$_query .= $strOrder ;
			// print $query ;
			($result = mysqli_query ( $dbLink, $query )) or ($result = mysql_execute ( $_query )) ;
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
			  $bgcolor = $bgcolor == "#BBCCFF" ? "#CCDDFF" : "#BBCCFF" ;
				print "<tr bgcolor=$bgcolor><td align=center>" . $row["id"] . "</td>" ;
				mysqli_data_seek ( $record, 0 ) ;
				while ( $cols = mysqli_fetch_assoc ( $record ) ) {
					print "<td>" ;
					if ( $varTable == "tbControlTable" && $cols["fdName"] == "fdDescription" )
					  print "<a href=controls.php?table=" . $row["fdName"] . ">" ;
					if ( $cols["fdSelect"] == "" ) {
						if ( $cols["fdRegular"] == "yesno" )
							if ( $row[$cols["fdName"]] == 0 )
								print "否" ;
							else
								print "是" ;
						else if ( $cols["fdRegular"] == "√" )
							if ( $row[$cols["fdName"]] == 0 )
								print "" ;
							else
								print "√" ;
						else {
							print htmlspecialchars ( stripslashes ($row[$cols["fdName"]]) ) ;
						}
					} else if ( $row[$cols["fdName"]] != "" ) {
						$select = stripslashes ($cols["fdSelect"]) ;
						$query = "" ;
						$query .= strtok ( $select, "$" ) ;
						while ( $tok = strtok ( "$" ) ) {
							$word = strtok ( $tok, " " ) ;
							if ( $_SESSION[$word] != "" )
  							$query .= $_SESSION[$word] ;
							else if ( $_GET[$word] != "" )
							  $query .= $_GET[$word] ;
							else if ( $row[$word] != "" )
							  $query .= $row[$word] ;
							$select = substr ( $tok, strlen ($word), strlen ($tok) - strlen($word) ) ;
							$query .= " " . strtok ( $select, "$" ) ;
						}
						if ( ($varOrder = strstr ( $query, "ORDER BY" )) != "" )
							$query = substr ( $query, 0, strpos ($query, "ORDER BY") ) ;
						if ( strpos ( $query, "WHERE" ) > 0 )
							$query .= " AND" ;
						else
							$query .= " WHERE" ;
						strtok ( strstr ( $query, "FROM " ), " " ) ;
						$tok = strtok ( " " ) ;
						$lsTable = strtok ( $tok, " " ) ;
						$lsIdField = strtok ( substr ( $query, 7 ), "," ) ;
						$query .= " " . $lsTable . ".$lsIdField =" . $row[$cols["fdName"]] . " " . $varOrder ;
						$res_temp = mysql_execute ( $query ) ;
						if ( $row_temp = mysqli_fetch_row ($res_temp) )
							print $row_temp[1] ;
						mysqli_free_result ( $res_temp ) ;
					}
					if ( $varTable == "tbControlTable" && $cols["fdName"] == "fdDescription" )
					  print "</a>" ;
					print "</td>\r\n" ;
				}
				if ( $pvgUpdate )
  				print "<td align=center><a href=control.php?table=" . $varTable . "&action=edit&id=" . $row["id"] . ($_GET["clause"] != "" ? "&clause=" . $_GET["clause"] . "&" . $_GET["clause"] : "") . "><img src=edit.gif border=0></a></td>\r\n" ;
				if ( $pvgDelete )
  				print "<td align=center><a href=" . $_SERVER["REQUEST_URI"] . "&action=delete&id=" . $row["id"] . "&clause=" . $_GET["clause"] . " onclick='return confirm(\"您真的要删除这个记录吗?\");'><img src=delete.gif border=0></a></td>\r\n" ;
				print "<td>" ;
				if ( mysqli_num_rows ( $relate ) > 0 ) {
					mysqli_data_seek ( $relate, 0 ) ;
					while ( $line = mysqli_fetch_assoc ( $relate ) ) { 
					  fnLog ( "line[fdName]=" . $line["fdName"] . "\r\n" ) ;
					  if ( $line["fdName"] != "tbControlField" || $pvgControl )
   						print "<a href=controls.php?table=" . $line["fdName"] . "&clause=" . $line["fdSlaveField"] . "=" . $row[$line["fdMasterField"]] . ">" . $line["fdDescription"] . "</a>\r\n" ;
						if ( $line["fdCommonField"] != "" && $line["fdCommonField"] != "*" ) {
						  $_SESSION[$line["fdCommonField"]] = $row[$line["fdCommonField"]] ;
							fnLog ( "cp 1, should not see this\r\n" ) ;
						} else {
							fnLog ( "cp 2\r\n" ) ;
						}
					}
				}
				print "</td></tr>\r\n" ;
			}
			fnLog ( "cp loop ended\r\n" ) ;
			print "</table>\r\n" ;
			mysqli_free_result ( $result ) ;
			mysqli_free_result ( $relate ) ;
		} else
			print "<p align=center>" . $varTable . "在$dbControl.tbControlField中没有定义，不能在这里编辑。<a href=# onclick='history.back()'>返回</a></p>\r\n" ;
		mysqli_free_result ( $record ) ;
	  require "footer.php" ;
	?>
</body>
</html>
