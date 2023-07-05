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
			$ab->LireTailleBlob($db_ou_fs,$nom_table,$id_doc,$type_fic);
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireBlob($sql):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§§§pile=(".$e->getTraceAsString().")");
		}
	}
?>