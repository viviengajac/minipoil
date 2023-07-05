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
			$ab->LireTailleFic($nom_fic);
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireBlob($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§§§pile=(".$e->getTraceAsString().")");
		}
	}
?>