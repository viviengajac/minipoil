<?php
include 'FctCommunes.php';
	if(isset($_GET['params']))
	{
		$params=$_GET['params'];
		try
		{
//Tracer("******************");
//Tracer("EcrireBlob: params=".$params);
			$tab_params=preg_split('/\|/',$params);
			$nom_fic=$tab_params[0];
			$octet_debut=$tab_params[1];
			$taille=$tab_params[2];
			$ab=new AccesBd();
			$ab->Init();
			$ab->LireFicParParties($nom_fic,$octet_debut,$taille);
			print "OK";
		}
		catch(Exception $e)
		{
			TracerErreur("Erreur: lireFicParParties($params):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
		}
	}
?>