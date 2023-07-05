<?php
include 'FctCommunes.php';
	try
	{
		$nom_dir_base='d:/BLOBS/AccesBdPm/';
		$ab=new AccesBd();
		$ab->Init();
		/*
		$params=$ab->RecupererParams();
		echo $params;
		echo "<br>";
		$tab_params=preg_split('/\//',$params);   // preg_split('#/#',$params);
		print_r($tab_params);
		*/
		$nom_bd=$ab->RecupererNomBd();
		$sql = "select id_prs from prs where mdp is null";
		$tab=$ab->LireListe($sql);
		$nb_tab=count($tab);
//echo 'nb_tab='.$nb_tab;
		for($i=0;$i<$nb_tab;$i++)
		{
			$id_prs=$tab[$i];
echo 'id='.$id_prs.'<br>';
			$sql="update prs set mdp=substring(MD5(RAND()) from 1 for 10) where id_prs=".$id_prs;
			$ab->ExecSql($sql);
		}
		echo "fini";
	}
//Tracer("pdo.LireUneTable: 6");
	catch(Exception $e)
	{
		TracerErreur("Erreur: GenererMdp($nom_dir):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
	}

?>