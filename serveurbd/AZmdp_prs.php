<?php
include 'FctCommunes.php';
	if(isset($_GET['p'])&&isset($_GET['m']))
	{
		$d=$_GET['d'];
		$m=$_GET['m'];
//Tracer("params=($sql_et_donnees)");
		try
		{
			$ab=new AccesBd();
			$ab->Init();
			$ab->ExecSql("update prs set mdp='$m' where id_prs=$ip");
			print "OK";
		}
		catch(Exception $e)
		{
			print "Erreur de mise à jour du mot de passe";
		}
	}
?>