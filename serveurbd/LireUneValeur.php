<?php
include 'FctCommunes.php';
	if(isset($_GET['sql']))
	{
		$sql=$_GET['sql'];
		try
		{
//			$fp=fopen("D:\\temp\\azer.txt","a+");
//			fputs($fp,date("Ymd:H:i:s")."\tLireValeur($sql)\r\n");
//			fclose($fp);
			$ab=new AccesBd();
			$ab->Init();
			$ab->LireUneValeur($sql);
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireUneValeur($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§sql=($sql)§§pile=(".$e->getTraceAsString().")");
		}
	}
?>