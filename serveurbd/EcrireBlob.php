<?php
include 'FctCommunes.php';
	if(isset($_GET['params']))
	{
		$params=$_GET['params'];
		$donnees=file_get_contents('php://input');
		try
		{
//Tracer("******************");
//Tracer("EcrireBlob: params=".$params);
			$ab=new AccesBd();
			$ab->Init();
			$tab_params=preg_split('/\|/',$params);
			$db_ou_fs=$tab_params[0];
			$nom_table=$tab_params[1];
			$id_doc=$tab_params[2];
			$type_fic=$tab_params[3];
			$ab->EcrireBlob($db_ou_fs,$nom_table,$id_doc,$type_fic,'php://input');
			print "OK";
		}
		catch(Exception $e)
		{
			TracerErreur("Erreur: EcrireBlob($params):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
		}
	}
?>