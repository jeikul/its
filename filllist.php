<?php
  function fnFillList ( $asSQL, $avVal, $avMultiple, $avNullable ) {
	  GLOBAL $dbLink ;
    $selected = 0 ;
	  $result = mysqli_query ( $dbLink, $asSQL ) or die ( "MySQL error #" . mysqli_errno($dbLink) . "," . mysqli_error ($dbLink) . ", <pre>" . $asSQL . "</pre>" ) ;
    while ( $line = mysqli_fetch_row ($result )) {
      print '<option value=' . $line[0] ;
      if ( $avMultiple ) {
        if ( (($line[0] + 0) & ($avVal + 0)) != 0 )
          print " selected" ;
      } elseif ( $line[0] == $avVal ) {
        print " selected" ;
        $selected = 1 ;
      }
      print '>' . $line[1] ;
    }
    mysqli_free_result ($result) ;
    if ( ! $avMultiple && $avNullable != 0 ) {
      print "<option value=-1" ;
			if ( $selected != 1 )
			  print " selected" ;
			print ">-请选择-" ;
		}
    print "</select>\r\n" ;
  }
?>
