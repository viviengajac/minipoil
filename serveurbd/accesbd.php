<?php
/*
function EcrireEnsembleDeTable()
{
	writer.WriteStartObject();
    writer.WritePropertyName("t");
    writer.WriteStartArray();
    foreach (AZTable t in et.tables)
            {
                writer.WriteStartObject();
                writer.WritePropertyName("c");
                writer.WriteStartArray();
                foreach (AZColonne c in t.cols)
                {
                    writer.WriteStartObject();
                    writer.WritePropertyName(c.nom_col);
                    writer.WriteValue(c.type_col);
                    writer.WriteEndObject();
                }
                writer.WriteEndArray();
                writer.WritePropertyName("l");
                writer.WriteStartArray();
                foreach (AZLigne l in t.ligs)
                {
                    writer.WriteStartObject();
                    writer.WritePropertyName("s");
                    writer.WriteValue(l.statut_lig);
                    writer.WritePropertyName("v");
                    writer.WriteStartArray();
                    foreach (AZChampHttp c in l.champs)
                    {
                        if (t.cols[c.num_champ].type_col != TypeCol.ChampBlob)
                        {
                            writer.WriteStartObject();
                            writer.WritePropertyName(c.num_champ.ToString());
                            switch (t.cols[c.num_champ].type_col)
                            {
                                case TypeCol.ChampBool:
                                    int val_bool = c.val_champ == "0" ? 0 : 1;
                                    writer.WriteValue(val_bool);
                                    break;
                                case TypeCol.ChampBlob:
                                    int val_blob = 0;
                                    writer.WriteValue(val_blob);
                                    break;
                                case TypeCol.ChampChar:
                                    writer.WriteValue(c.val_champ);
                                    break;
                                case TypeCol.ChampDate:
                                    if (c.val_champ.Length == 8)
                                    {
                                        // format date simple
                                        int annee = Convert.ToInt32(c.val_champ.Substring(0, 4));
                                        int mois = Convert.ToInt32(c.val_champ.Substring(4, 2));
                                        int jour = Convert.ToInt32(c.val_champ.Substring(6, 2));
                                        int date_complete = annee * 10000 + mois * 100 + jour;
                                        writer.WriteValue(date_complete);
                                    }
                                    else
                                    {
                                        // format date + heure
                                        writer.WriteValue(c.val_champ);
                                    }
                                    break;
                                case TypeCol.ChampDouble:
                                    double val_double = Convert.ToDouble(c.val_champ);
                                    writer.WriteValue(val_double);
                                    break;
                                case TypeCol.ChampGuid:
                                    writer.WriteValue(c.val_champ);
                                    break;
                                case TypeCol.ChampInt:
                                    int val_int = Convert.ToInt32(c.val_champ);
                                    writer.WriteValue(val_int);
                                    break;
                                case TypeCol.ChampString:
                                    writer.WriteValue(c.val_champ);
                                    break;
                                default:
                                    writer.WriteValue(c.val_champ);
                                    break;
                            }
                            writer.WriteEndObject();
                        }
                    }
                    writer.WriteEndArray();
                    writer.WriteEndObject();
                }
                writer.WriteEndArray();
                writer.WriteEndObject();
            }
            writer.WriteEndArray();
            writer.WriteEndObject();
        }

}
*/

	function WriteStartObject()
	{
		print "{";
	}
	function WriteEndObject()
	{
		print "}";
	}
	function WritePropertyName($nom_prop)
	{
		print "\"".$nom_prop."\":";
	}
	function WritePropertyNumber($nom_prop)
	{
		print $nom_prop.":";
	}
	function WriteValue($val)
	{
		print $val;
	}
	function WriteStartArray()
	{
		print "[";
	}
	function WriteEndArray()
	{
		print "]";
	}
	function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	}
	function TranscrireEnJsonUneTable($results)
	{
		WriteStartObject();
		WritePropertyName("t");
		WriteStartArray();
//		foreach (AZTable t in et.tables)
//            {
		for($num_table=0;$num_table<1;$num_table++)
		{
			WriteStartObject();
            WritePropertyName("c");
            WriteStartArray();
			$nb_col=odbc_num_fields($results);
            for ($i=1;$i<=$nb_col;$i++)
            {
				$nom_col=odbc_field_name($results,$i);
				$max_length=odbc_field_len($results,$i);
				$type_col=odbc_field_type($results,$i);
				if($i>1)
				{
					print ",";
				}
				WriteStartObject();
                WritePropertyName($nom_col);
				$type_col_int=0;
				switch($type_col)
				{
					case "bit":
						$type_col_int=4;
						break;
					break;
					case "datetime":
						$type_col_int=3;
						break;
					break;
					case "int identity":
					case "int":
						$type_col_int=1;
						break;
					case "nvarchar":
					case "varchar":
						$type_col_int=2;
						break;
					default:
						print "type_col_inconnu:".$type_col;
						break;
				}
                WriteValue($type_col_int);
                WriteEndObject();
            }
            WriteEndArray();
			print ",";
            WritePropertyName("l");
            WriteStartArray();
			$nb_lig=odbc_num_rows($results);
            for ($i=1;$i<=$nb_lig;$i++)
            {
				if($i>1)
				{
					print ",";
				}
				odbc_fetch_into($results,$row);
				WriteStartObject();
                WritePropertyName("s");
				$etat=$row[0];
				$etat_int=1;	// pas modifi?
                WriteValue($etat_int);
				print ",";
				WritePropertyName("v");
				WriteStartArray();
				$debut=1;
				for($j=1;$j<=$nb_col;$j++)
				{
					$num_val=$j-1;
					$val_col=$row[$num_val];
//					print "val_col=".$val_col;
					if(is_null($val_col) || strlen($val_col)==0)
					{
					}
					else
					{
//						print "passe";
						if($debut == 1)
						{
							$debut=0;
						}
						else
						{
							print ",";
						}
						WriteStartObject();
//						WritePropertyName($num_val);
						WritepropertyNumber($num_val);
						$type_col=odbc_field_type($results,$j);
//						print "(j=$j,type_col=".$type_col.")";
						switch($type_col)
						{
							case "bit":
								WriteValue($val_col);
								break;
							case "int identity":
							case "int":
								WriteValue($val_col);
								break;
							case "datetime":
								$suffixe=" 00:00:00.000";
								if(endswith($val_col,$suffixe))
								{
//									$val_col=substr($val_col,0,strlen($val_col)-strlen($suffixe));
									$val_col=substr($val_col,0,4).substr($val_col,5,2).substr($val_col,8,2);
									WriteValue($val_col);
								}
								else
								{
									print "\"";
									WriteValue($val_col);
									print "\"";
								}
								break;
							case "nvarchar":
							case "varchar":
								print "\"";
								$val_format=str_replace("\\","\\\\",$val_col);
								WriteValue(utf8_encode($val_format));
								print "\"";
								break;
							default:
								print "type inconnu:".$type_col;
								break;
						}
						WriteEndObject();
					}
				}
				WriteEndArray();
                WriteEndObject();
            }
			WriteEndArray();
			WriteEndObject();
        }
		WriteEndArray();
		WriteEndObject();
	}
	function Tracer($msg)
	{
		$monfichier=fopen('d:\\temp\\accesbd.err','a+');
		fputs($monfichier,$msg);
		fclose($monfichier);
	}
	
	function LireUneTable($sql)
	{
		$srv="BERTRAND-PC\\BD_MSSQL";
//		$srv="g6c9lnn2e";
		$uid="pericles_sa";
		$pwd="pericles_sa";
		$bd="bcr";
		$bd="gestion_pm";
/*
	$cnx_info=array("UID"=>$uid,"PWD"=>$pwd,"Database"=>$bd);
	try
	{
		$cnx=new PDO("sqlsrv:server=$srv;Database=$bd",$uid,$pwd);
		$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e)
	{
		die ("Erreur connexion".$e);
	}
	print("{\"t\":[{\"c\":[");
	$stmt=$cnx->query($sql);
	while($row=$stmt->fetch(PDO::FETCH_ASSOC))
	{
		print_r($row);
	}
	print("]}]}");
	$stmt=null;
	$cnx=null;
*/
//		$cnx = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$srv;Database=$bd;", $uid, $pwd);
		$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$srv;Database=$bd;", $uid, $pwd);
//		print "pass?\n";
		$results = odbc_prepare($cnx,$sql);
//		print "1\n";
		odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
/*
		$nb_col=odbc_num_fields($results);
		$nb_lig=odbc_num_rows($results);
//		print "nb_lignes=".$nb_lig.", nb_col=".$nb_col;
		for($i=1;$i<=$nb_col;$i++)
		{
			$nom_col=odbc_field_name($results,$i);
			$max_length=odbc_field_len($results,$i);
			$type=odbc_field_type($results,$i);
			print "col[$i]=($nom_col,$max_length,$type)<br/>";
		}
		print "<br/>";
		for($i=1;$i<=$nb_lig;$i++)
		{
			odbc_fetch_into($results,$row);
			for($j=0;$j<$nb_col;$j++)
			{
				print $row[$j]."<br/>";
			}
		}
*/
		TranscrireEnJsonUneTable($results);
		odbc_free_result($results);
		odbc_close($cnx);
	}
	
	function Query ($sql,$test_erreur)
	{
		$results = odbc_prepare($this->dbh,$sql);
		if ($test_erreur == true)
		{
			odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
		}
		else
		{
			odbc_execute($results);
		}
		return $results;
	}

//	echo "abcd";
	if(isset($_GET['sql']))
	{
//		echo "sql=($sql)<br>";
		/*
		$serverName = "BERTRAND-PC\\BD_MSSQL"; //serverName\instanceName

// Vu que UID et PWD ne sont pas sp?cifi?s dans le tableau $connectionInfo,
// la connexion va tenter d'utiliser l'authentification Windows.
$connectionInfo = array( "Database"=>"gestion_pm");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connexion ?tablie.<br />";
}else{
     echo "La connexion n'a pu ?tre ?tablie.<br />";
     die( print_r( sqlsrv_errors(), true));
}
		*/
		
		$sql=$_GET['sql'];
//print "sql=".$sql.</br>;
//	$srv="g6c9lnn2e";
/*
		$srv="BERTRAND-PC\\BD_MSSQL";
		$uid="pericles_sa";
		$pwd="pericles_sa";
		$bd="gestion_pm";
/ *
	$cnx_info=array("UID"=>$uid,"PWD"=>$pwd,"Database"=>$bd);
	try
	{
		$cnx=new PDO("sqlsrv:server=$srv;Database=$bd",$uid,$pwd);
		$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e)
	{
		die ("Erreur connexion".$e);
	}
	print("{\"t\":[{\"c\":[");
	$stmt=$cnx->query($sql);
	while($row=$stmt->fetch(PDO::FETCH_ASSOC))
	{
		print_r($row);
	}
	print("]}]}");
	$stmt=null;
	$cnx=null;
* /
//		$cnx = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$srv;Database=$bd;", $uid, $pwd);
		$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$srv;Database=$bd;", $uid, $pwd);
//		print "pass?\n";
		$results = odbc_prepare($cnx,$sql);
//		print "1\n";
		odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
/ *
		$nb_col=odbc_num_fields($results);
		$nb_lig=odbc_num_rows($results);
//		print "nb_lignes=".$nb_lig.", nb_col=".$nb_col;
		for($i=1;$i<=$nb_col;$i++)
		{
			$nom_col=odbc_field_name($results,$i);
			$max_length=odbc_field_len($results,$i);
			$type=odbc_field_type($results,$i);
			print "col[$i]=($nom_col,$max_length,$type)<br/>";
		}
		print "<br/>";
		for($i=1;$i<=$nb_lig;$i++)
		{
			odbc_fetch_into($results,$row);
			for($j=0;$j<$nb_col;$j++)
			{
				print $row[$j]."<br/>";
			}
		}
* /
		TranscrireEnJsonUneTable($results);
		odbc_free_result($results);
		odbc_close($cnx);
*/
// Tracer($sql);
		LireUneTable($sql);
	}

	if(isset($_POST['sql']))
	{
		$sql=$_POST['sql'];
		Tracer("post:".$sql."\n");
		LireUneTable($sql);
		Tracer("fin post\n");
	}
?>