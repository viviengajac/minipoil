<?php
include 'FctCommunes.php';
	if(isset($_GET['sql']))
	{
		$sql_et_donnees=$_GET['sql'];
//Tracer("params=($sql_et_donnees)");
		$pos1=strpos($sql_et_donnees,"§");
		$pos2=strpos($sql_et_donnees,"?");
		$sql=substr($sql_et_donnees,$pos2,$pos1-$pos2);
		$donnees=substr($sql_et_donnees,$pos1+2);
		try
		{
			$ab=new AccesBd();
			$ab->Init();
			$ab->EcrireBlob($sql,$donnees);
			print "OK";
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: EcrireBlob($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
//			TracerErreur("Erreur: ".$e->getMessage()."sql=($sql)donnees=($donnees)pile=(".$e->getTraceAsString().")");
			TracerErreur("Erreur: ".$e->getMessage()."sql=($sql)donnees=($donnees)pile=(".$e->getTraceAsString().")");
		}
	}
?>