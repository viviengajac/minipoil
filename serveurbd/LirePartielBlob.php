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
			$db_ou_fs=$tab_params[0];
			$nom_table=$tab_params[1];
			$id_doc=$tab_params[2];
			$type_fic=$tab_params[3];
			$octet_debut=$tab_params[4];
			$taille_bloc=$tab_params[5];
			$ab->LirePartielBlob($db_ou_fs,$nom_table,$id_doc,$type_fic,$octet_debut,$taille_bloc);
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireBlob($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§§§pile=(".$e->getTraceAsString().")");
		}
	}
?>