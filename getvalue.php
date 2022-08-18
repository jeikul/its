<?php
  function fnGetValue ( $avTable, $avClause, $avField ) {
	  GLOBAL $dbLink ;
    $query = "SELECT " . $avField . " FROM " . $avTable . " WHERE " . $avClause ;
		$result = mysqli_query ( $dbLink, $query ) or die ( "MySQL error #" . mysqli_errno( $dbLink ) . "," . mysqli_error ( $dbLink ) . ", <pre>" . $query . "</pre>" ) ;
    if ( $line = mysqli_fetch_row ($result) )
      $ret = $line[0] ;
    else
      $ret = "" ;
    mysqli_free_result ($result) ;
    return $ret ;
  }
?>
