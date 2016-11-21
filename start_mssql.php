<?php 
		
		echo <<<END
		<html>
		
		<head>
		<title>АГЕНТЫ</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8">
		
		<link rel="stylesheet" type="text/css" href="/test/css/style.css" />
		
	</head>
	<body>
END;
	
		require_once 'login_agents.php';
		
		$datetime = new DateTime();
		$datestr = $datetime->format('d-m-Y');
		
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		If (!$conn) {
					echo "Can not connect to a database!!";
					die(print_r(sqlsrv_errors(),true));
					}
		
		$tsql = "select [Income], CONVERT(time,[Time]),[Outcome No_],[Owner Name] from $tablename WHERE  CONVERT (date, [Date Income])= CONVERT (date, GETDATE()) ORDER BY [Time]; "; //������

		$stmt = sqlsrv_query( $conn, $tsql);
		
		if ( $stmt === false ) {
							echo "Error in statement preparation/execution.\n";
							die( print_r( sqlsrv_errors(), true));
						}
		sqlsrv_fetch( $stmt );
		$direction='';
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// Top of the table
		echo "<table>";
		echo '<tr><th>№ </th><th>ВРЕМЯ</th><th>РЕЙС</th><th>АВИАКОМПАНИЯ</th><th>АГЕНТЫ</th></tr>';
		// Iterating through the array
		$counter=1;
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 
			
			if ($row[0]==0){
			
				$time_of_dep=$row[1]->format('H:i');
				$aircarrier=iconv('windows-1251','utf-8',$row[3]);
				$flightcode=iconv('windows-1251','utf-8',$row[2]);
				
		
			//LOOK UP list of agents
			$textsql='SELECT  registry.agent1,agents.name FROM registry LEFT JOIN agents ON registry.agent1=agents.tab_num WHERE route="'.$flightcode.'" AND date=CURDATE() ';// AND DATE_FORMAT(date,"%d-%m-%Y") = "'.$datestr.'")'; 
			$answsql=mysqli_query($db_server,$textsql);
			$num_of_ags=mysqli_num_rows($answsql);
			$result_arr = mysqli_fetch_row($answsql);//$answsql->fetch_array(MYSQLI_NUM);;
			$ag1_in=$result_arr[1];
			
			$textsql='SELECT  registry.agent1,agents.name FROM registry LEFT JOIN agents ON registry.agent2=agents.tab_num WHERE route="'.$flightcode.'" AND date=CURDATE() ';
			$answsql=mysqli_query($db_server,$textsql);
			$num_of_ags=mysqli_num_rows($answsql);
			$result_arr = mysqli_fetch_row($answsql);
			$ag2_in=$result_arr[1];
			
			$textsql='SELECT  registry.agent3,agents.name FROM registry LEFT JOIN agents ON registry.agent3=agents.tab_num WHERE route="'.$flightcode.'" AND date=CURDATE() ';
			$answsql=mysqli_query($db_server,$textsql);
			$num_of_ags=mysqli_num_rows($answsql);
			$result_arr = mysqli_fetch_row($answsql);
			$ag3_in=$result_arr[1];
			
			echo "<tr><td rowspan=\"3\" >$counter</td><td rowspan=\"3\">$time_of_dep</td>
						<td rowspan=\"3\">$flightcode</td>
						<td rowspan=\"3\"><a href=enter_agents.php?flightcode=$flightcode&step=1;>$aircarrier</a></td>
						<td>$ag1_in </td></tr><tr><td>$ag2_in</td> </tr><tr><td>$ag3_in</td></tr>";
			$counter+=1;
			}
		}
		echo "</table>";
	
	sqlsrv_close($conn);
	?>
	
