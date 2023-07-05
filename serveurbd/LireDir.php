<?php
include 'FctCommunes.php';
	if(isset($_GET['nom_dir']))
	{
		$nom_dir=$_GET['nom_dir'];
		try
		{
//Tracer("LireDir".$nom_dir);
			if (is_dir($nom_dir))
			{
				if ($dh = opendir($nom_dir))
				{
					echo "[";
					$debut=1;
					while (($file = readdir($dh)) !== false)
					{
						if($file!="." && $file != "..")
						{
							if($debut==0)
								echo ",";
							echo $file.":";
							if(is_dir($nom_dir."/".$file))
							{
								echo "D";
							}
							else
							{
								echo "F";
							}
							$debut=0;
						}
					}
					closedir($dh);
					echo "]";
				}
			}
			else
			{
				throw new ErrorException("Répertoire inconnu: $nom_dir");
			}
		}
		catch(Exception $e)
		{
//			TracerErreur("Erreur: LireDir($nom_dir):".$e->getMessage()."\r\n\r".$e->getTraceAsString());
			TracerErreur("Erreur: ".$e->getMessage()."§nom_dir=($nom_dir)§§pile=(".$e->getTraceAsString().")");
		}
	}
?>