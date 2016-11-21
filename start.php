
	<?php 
		
		echo <<<END
		<html>
		
		<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>РЕЗУЛЬТАТЫ</title>
		<link rel="stylesheet" type="text/css" href="/test/css/style.css" />
		
	</head>
	<body>
END;
	
		require_once 'login.php';
		$db_server = mysql_connect($db_hostname, $db_username,$db_password);

		If (!$db_server) die("Can not connect to a database!!".mysql_error());
		
		mysql_select_db($db_database)or die(mysql_error());
		
	
		$airport = $_POST['airport'];
		$year_from    = $_POST['year_from'];
		$month_from    = $_POST['month_from'];
		$year_to    = $_POST['year_to'];
		$month_to   = $_POST['month_to'];

	
	$startdate='';
	switch ($month_from) {
				case '1':
					$startdate='Январь';
					break;
				case '2':
					$startdate='Февраль';
					break;
				case '3':
					$startdate='Март';
					break;
				case '4':
					$startdate='Апрель';
					break;
				case '5':
					$startdate='Май';
					break;
				case '6':
					$startdate='Июнь';
					break;
				case '7':
					$startdate='Июль';
					break;
				case '8':
					$startdate='Август';
					break;
				case '9':
					$startdate='Сентябрь';
					break;
				case '10':
					$startdate='Октябрь';
					break;
				case '11':
					$startdate='Ноябрь';
					break;
				case '12':
					$startdate='Декабрь';
					break;
			}
			$enddate = '';
			switch ($month_to) {
				case 1:
					$enddate='Январь';
					break;
				case 2:
					$enddate='Февраль';
					break;
				case 3:
					$enddate='Март';
					break;
				case 4:
					$enddate='Апрель';
					break;
				case 5:
					$enddate='Май';
					break;
				case 6:
					$enddate='Июнь';
					break;
				case 7:
					$enddate='Июль';
					break;
				case 8:
					$enddate='Август';
					break;
				case 9:
					$enddate='Сентябрь';
					break;
				case 10:
					$enddate='Октябрь';
					break;
				case 11:
					$enddate='Ноябрь';
					break;
				case 12:
					$enddate='Декабрь';
					break;
			}
	$startyear = 0;
	switch ($year_from) {
		case 0:
			$startyear=2012;
			break;
		case 1:
			$startyear=2013;
			break;
		case 2:
			$startyear=2014;
			break;
		case 3:
			$startyear=2015;
			break;
		case 4:
			$startyear=2016;
			break;
	}
	$endyear = 0;
	switch ($year_to) {
		case 0:
			$endyear=2012;
			break;
		case 1:
			$endyear=2013;
			break;
		case 2:
			$endyear=2014;
			break;
		case 3:
			$endyear=2015;
			break;
		case 4:
			$endyear=2016;
			break;
	}
	echo  "<h1>"." ЗАПРОС: "." </h1> ";	
		echo "<table>";
		echo "<tr><th>Аэропорт:</th><th>С :</th><th>Год</th><th>ПО :</th><th>Год</th></tr>";
		echo "<tr><td>$airport</td><td>$startdate</td><td>$startyear</td><td>$enddate</td><td>$endyear</td></tr>";
		echo "</table>";
		
	$start = $year_from*12+$month_from;
	$end = $year_to*12+$month_to;
	$queryin = "SELECT * FROM pax_airport_in WHERE AIRPORT LIKE '$airport%'";
	$queryout = "SELECT * FROM pax_airport_out WHERE AIRPORT LIKE '$airport%'";
		$resultin = mysql_query($queryin);
		$resultout = mysql_query($queryout);
		if(!($resultin)&&!($resultout)) die (mysql_error());
		echo  "<h1>"." ПАССАЖИРЫ: "." </h1> ";	
		$rowsin = mysql_num_rows($resultin);
		$colsin = mysql_num_fields($resultin);
		$rowsout = mysql_num_rows($resultout);
		$colsout = mysql_num_fields($resultout);

		echo "<table>";
		echo "<tr><th>АЭРОПОРТ ВЫЛЕТА</th><th>ПРИБЫЛО ПАССАЖИРОВ</th><th>ОТПРАВЛЕНО ПАССАЖИРОВ</th></tr>";

		for ($j=0; $j<$rowsin; ++$j)
		{
			$summain=0;
			$summaout=0;
			// passangers incoming and passangers outgoing are in different tables
			$rowin = mysql_fetch_row($resultin);
			$rowout = mysql_fetch_row($resultout);
			$airport_name=$rowin[0];
			if ($airport_name!=$rowout[0]) echo "ATTENTION: resulting sets do not match each other";//in case our don't fit each other
			for ($i=$start; $i<=$end; $i++)
				{
					//echo " FIELD #".$i."  = ".$rowin[$i]."<br\>";
					//echo " FIELD #".$i."  = ".$rowout[$i]."<br\>";
					$summain+=$rowin[$i];
					$summaout+=$rowout[$i];	
				}
			if ($summain+$summaout) echo "<tr><td>$airport_name</td><td>$summain</td><td>$summaout</td></tr>";
		}
		echo "</table>";

		
		$queryin_air = "SELECT aircarriers.CompanyName, inc_aport_carrier_flights.* FROM `inc_aport_carrier_flights` LEFT JOIN aircarriers ON inc_aport_carrier_flights.CompanyCode=aircarriers.CompanyCode WHERE AIRPORT LIKE '$airport%'";
		$queryout_air = "SELECT aircarriers.CompanyName, out_aport_carrier_flights.* FROM `out_aport_carrier_flights` LEFT JOIN aircarriers ON out_aport_carrier_flights.CompanyCode=aircarriers.CompanyCode WHERE AIRPORT LIKE '$airport%'";
		$resultin = mysql_query($queryin_air);
		$resultout = mysql_query($queryout_air);
		
		if(!($resultin) OR !($resultout)) die (mysql_error());
		echo  "<h1>"." СТАТИСТИКА ПО АВИАКОМПАНИЯМ: "." </h1> ";	
		$rowsin = mysql_num_rows($resultin);
		$colsin = mysql_num_fields($resultin);
		$rowsout = mysql_num_rows($resultout);
		$colsout = mysql_num_fields($resultout);
	
		echo "<table>";
		echo "<tr><th>АЭРОПОРТ ВЫЛЕТА</th><th>АВИАКОМПАНИЯ</th><th>ПРИБЫЛО РЕЙСОВ</th><th>УБЫЛО РЕЙСОВ</th><th>СРЕДНЕЕ ЗА НЕДЕЛЮ</th></tr>";

		for ($j=0; $j<$rowsin; ++$j)
		{
			$summain=0;
			$summaout=0;
			$averagein=0;
			$averageout=0;
			$rowin = mysql_fetch_row($resultin);
			$rowout = mysql_fetch_row($resultout);
			$airport_name=$rowin[1];
			$aircarrier=$rowin[0];
			$airport_name_out=$rowout[1];
			$aircarrier_out=$rowout[0];
			$text = $rowin[0];
			if ($airport_name != $airport_name_out) echo "WARNING: MISMATCH ON AIRPORT BETWEEN SELECT ON IN AND OUT TRAFFIC!!! <\br> ";
            //echo " START = ".$start." END = ".$end."<br\>";
			for ($i=$start+2; $i<=$end+2; $i++)
				{
					//echo " FIELD #".$i."  = ".$rowin[$i]."<br\>";
					$summain+=$rowin[$i];
					$summaout+=$rowout[$i];
				}
			 if($summain OR $summaout ) {
					$averagein=round($summain/(($end+1-$start)*4),1);
					$averageout=round($summaout/(($end+1-$start)*4),1);
					echo "<tr><td>$airport_name</td><td>$aircarrier</td><td>$summain</td><td>$summaout</td><td>$averagein</td></tr>";}
			}
		echo "</table>";	
	echo <<<_END
	<a href="index.html" > <img src="/prod/src/arrow_left.png" alt="Go back" title="Back" width="64" height="64"></a>
	</body>
	</html>
_END;
	mysql_close($db_server);
	?>
	
