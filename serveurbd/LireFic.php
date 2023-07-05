<?php
include 'FctCommunes.php';
	if(isset($_GET['nom_fic']))
	{
		$nom_fic=$_GET['nom_fic'];
		try
		{
Tracer("LireFic".$nom_fic);
			$ab=new AccesBd();
			$ab->Init();
			$ab->LireFic($nom_fic);
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireFic($nom_fic):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§§nom_fic=($nom_fic)§pile=(".$e->getTraceAsString().")");
		}
	}
?>