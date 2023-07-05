<?php
include 'FctCommunes.php';
	if(isset($_GET['sql']))
	{
		$sql=$_GET['sql'];
//Tracer("params=($sql_et_donnees)");
		try
		{
			$ab=new AccesBd();
			$ab->Init();
			$ab->ExecSql($sql);
			print "OK";
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: EcrireBlob($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§sql=($sql)§§pile=(".$e->getTraceAsString().")");
		}
	}
?>