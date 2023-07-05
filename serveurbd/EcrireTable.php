<?php
include 'FctCommunes.php';
//Tracer("début de EcrireTable");
	if(isset($_GET['sql']))
	{
		/*
		$sql_et_donnees=$_GET['sql'];
//Tracer("params=($sql_et_donnees)");
		$pos1=strpos($sql_et_donnees,"§");
		$pos2=strpos($sql_et_donnees,"?");
		$sql=substr($sql_et_donnees,$pos2,$pos1-$pos2);
		$donnees=substr($sql_et_donnees,$pos1+2);
//Tracer("sql=".$sql);
//Tracer("donnees=".$donnees);
		*/
		$sql_et_donnees=$_GET['sql'];
//Tracer("EcrireTable: sql_et_donnnees=$sql_et_donnees");
		$pos1=strpos($sql_et_donnees,"§");
//Tracer("EcrireTable: pos1=$pos1");
		if($pos1>0)
		{
//Tracer("cas 1");
			$pos2=strpos($sql_et_donnees,"?");
			$sql=substr($sql_et_donnees,$pos2,$pos1-$pos2);
			$donnees=substr($sql_et_donnees,$pos1+2);
		}
		else
		{
//Tracer("cas 2");
			$sql=$sql_et_donnees;
			$donnees=file_get_contents('php://input');
		}
//Tracer("EcrireTable: sql=($sql), donnees=($donnees)");
		try
		{
			/*
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
			$ab=new AccesBd($srv,$bd,$uid,$pwd,$type_bd_sql,$type_cnx);
			*/
			$ab=new AccesBd();
			$ab->Init();
			$ab->EcrireUneTable($sql,$donnees);
			print "OK";
		}
		catch(Exception $e)
		{
			Tracer("Erreur PHP 1");
			TracerErreur("Erreur: ".$e->getMessage()."§sql=($sql)§donnees=($donnees)§pile=(".$e->getTraceAsString().")");
//			print "Erreur: LireUneTable($sql):".$e->getMessage();
		}
	}
?>