<?php
include 'FctCommunes.php';
	if(isset($_GET['params']))
	{
		$params=$_GET['params'];
		try
		{
//Tracer("LireBlob".$sql);
			$ab=new AccesBd();
			$ab->Init();
			$tab_params=preg_split('/\|/',$params);
			$nom_fic=$tab_params[0];
			$octet_debut=$tab_params[1];
			$taille_bloc=$tab_params[2];
			$ab->LirePartielFic($nom_fic,$octet_debut,$taille_bloc);
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireBlob($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§§§pile=(".$e->getTraceAsString().")");
		}
	}
?>