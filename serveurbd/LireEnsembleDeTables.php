<?php
include 'FctCommunes.php';
//Tracer("début");
//	header("Location: http://www.bertrandgajac.hopto.org:9002/AccesBdPm/LireEnsembleDeTables.php");
//	header("Access-Control-Allow-Origin: *");
//	header("Content-Location: http://www.bertrandgajac.hopto.org");
//	header("Access-Control-Request-Method: POST");
//	header("Access-Control-Request-Headers: Content-Type, Authorization");
	if(isset($_GET['sql']))
	{
		$sql=RemplacerEtoile($_GET['sql']);
//Tracer("sql=".$sql);
//print "sql=($sql)";
		try
		{
//Tracer("LireEnsembleDeTables($sql)");
//print "sql=$sql\n";
//			LireUneTable("ODBC13",$sql);
//			LireUneTable("MSSQL",$sql);
/*
			// pour MSSQL
			$srv="BERTRAND-PC\\BD_MSSQL";
//			$srv="g6c9lnn2e";
			$uid="pericles_sa";
			$pwd="pericles_sa";
			// pour PDO/MySQL
			$srv="localhost";
			$uid="root";
			$pwd="";
			$bd="gestion_pm";
			$type_bd_sql="mysql";
			$type_cnx="PDO";
//Tracer("LireEnsembleDeTables: 1");
//			$ab=new AccesBd($srv,$bd,$uid,$pwd,$type_bd_sql,$type_cnx);
*/
//Tracer("LireEnsembleDeTables: 0");
			$ab=new AccesBd();
//Tracer("LireEnsembleDeTables: 1");
			$ab->Init();
//Tracer("LireEnsembleDeTables: appel de LireUneTable($sql)");
			$ab->LireUneTable($sql);
//Tracer("LireEnsembleDeTables: 3");
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireUneTable($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§§sql=($sql)§pile=(".$e->getTraceAsString().")");
//			print "Erreur: LireUneTable($sql):".$e->getMessage();
		}
	}
?>