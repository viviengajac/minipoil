<?php
	function RemplacerEtoile($sql)
	{
//		return str_replace("*","%",$sql);
		return $sql;
	}
	function Tracer($msg)
	{
		$mondossier="c:\\test";
		if (!file_exists($mondossier)) {
			mkdir("c:\\test");
			$monfichier=fopen('c:\\test\\accesbd.err','a+');
		}
		else {			
			$monfichier=fopen('c:\\test\\accesbd.err','a+');
		}
		fputs($monfichier,date("Ymd:H:i:s")."\t".$msg."\n");
		fclose($monfichier);
//		print $msg."<br>";
	}
	function TracerErreur($msg)
	{
		$mondossier="c:\\test";
		if (!file_exists($mondossier)) {
			mkdir("c:\\test");
			$monfichier=fopen('c:\\test\\accesbd.err','a+');
		}
		else {			
			$monfichier=fopen('c:\\test\\accesbd.err','a+');
		}
		fputs($monfichier,date("Ymd:H:i:s")."\t".$msg."\n");
		fclose($monfichier);
		print $msg."<br>";
	}
	function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0)
		{
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	}
	function myErrorHandler($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno))
		{
			// Ce code d'erreur n'est pas inclus dans error_reporting(), donc il continue
			// jusqu'au gestionaire d'erreur standard de PHP
			return false;
		}
		// $errstr doit peut être être échappé :
		$errstr = htmlspecialchars($errstr);
		switch ($errno)
		{
		case E_ERROR:
		case E_USER_ERROR:
			/*
			echo "<b>Mon ERREUR</b> [$errno] $errstr<br />\n";
			echo "  Erreur fatale sur la ligne $errline dans le fichier $errfile";
			echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			echo "Arrêt...<br />\n";
			*/
			$msg="Erreur [$errno]: $errstr\nligne $errline dans $errfile";
			TracerErreur($msg);
			break;
//			exit(1);
		case E_WARNING:
		case E_USER_WARNING:
//			echo "<b>Mon ALERTE</b> [$errno] $errstr<br />\n";
			$msg="Erreur légère [$errno]: $errstr\nligne $errline dans $errfile";
			TracerErreur($msg);
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			echo "<b>Mon AVERTISSEMENT</b> [$errno] $errstr<br />\n";
//			return false;
			break;
		default:
			echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";
//			return false;
			break;
		}
		/* Ne pas exécuter le gestionnaire interne de PHP */
		return true;
	}
	class Cellule
	{
		var $m_num_col;
		var $m_val_col;
	}
	
	class Ligne
	{
		private $m_nb_cellules=0;
		private $m_tab_cellules=array();
		function DonnerNbCellules()
		{
			return $this->m_nb_cellules;
		}
		function DonnerTabCellules()
		{
			return $this->m_tab_cellules;
		}
		function DonnerUneCellule($i)
		{
			return $this->m_tab_cellules[$i];
		}
		function AjouterCellule($cellule)
		{
			$this->m_tab_cellules[$this->m_nb_cellules]=$cellule;
			$this->m_nb_cellules++;
		}
	}
	class Nd
	{
		var $m_nom_nd;
		var $type_nd;
	}
	
	class AccesJSon
	{
		private $m_start_object="{";
		private $m_end_object="}";
		private $m_start_array="[";
		private $m_end_array="]";
		private $m_nb_col=0;
		private $m_tab_nom_col;
		private $m_tab_type_col;
		private $m_nb_val=0;
		private $m_tab_val;
		private $m_nb_lig=0;
		private $m_lignes;
		private $m_type_cnx;
		private $m_tampon_sortie="";
		function __construct($type_cnx)
		{
			$this->m_type_cnx=$type_cnx;
		}
		function DonnerNbCol()
		{
			return $this->m_nb_col;
		}
		function DonnerTabNomCol()
		{
			return $this->m_tab_nom_col;
		}
		function DonnerTabTypeCol()
		{
			return $this->m_tab_type_col;
		}
		function DonnerNbLig()
		{
			return $this->m_nb_lig;
		}
		function DonnerTabLig()
		{
			return $this->m_lignes;
		}
		function WriteStartObject()
		{
//			print $this->m_start_object;
			$this->m_tampon_sortie.=$this->m_start_object;
		}
		function WriteEndObject()
		{
//			print $this->m_end_object;
			$this->m_tampon_sortie.=$this->m_end_object;
		}
		function WritePropertyByName($nom_prop, $val)
		{
//			print "\"".$nom_prop."\":";
//			print $nom_prop.":".$val;
			$this->m_tampon_sortie.=$nom_prop.":".$val;
		}
		function WritePropertyByNumber($nom_prop,$val)
		{
//			print $nom_prop.":".$val;
			$this->m_tampon_sortie.=$nom_prop.":".$val;
		}
		function WriteValue($val)
		{
//			print $val;
			$this->m_tampon_sortie.=$val;
		}
		function WriteStartArray()
		{
//			print $this->m_start_array;
			$this->m_tampon_sortie.=$this->m_start_array;
		}
		function WriteEndArray()
		{
//			print $this->m_end_array;
			$this->m_tampon_sortie.=$this->m_end_array;
		}
		function WriteSeparateurLigCol()
		{
			$this->m_tampon_sortie.=",";
		}
		function WriteTabCel($nb_cel,$tab_cel)
		{
			$this->m_tampon_sortie.="v:".$nb_cel.$tab_cel;
		}
		function TranscrireEnJsonUneTable($results)
		{
//Tracer("debut objet liste de tables");
//Tracer("results=".$results);
			$this->m_tampon_sortie="";
			$this->WriteStartObject();
//Tracer("propriete tables");
			$this->WritePropertyByName("t","1");
//			$this->WriteValue("1");
//Tracer("debut tableau des tables");
			$this->WriteStartArray();
			for($num_table=0;$num_table<1;$num_table++)
			{
//Tracer("debut objet table");
				$this->WriteStartObject();
//Tracer("propriete colonnes");
//T(")debut tableau colonnes(");
				switch($this->m_type_cnx)
				{
					case "PDO":
						$nb_col=$results->columnCount();
						break;
					case "ODBC13":
						$nb_col=odbc_num_fields($results);
						break;
					case "MSSQL":
						$nb_col=sqlsrv_num_fields($results);
//						$nb_col=odbc_num_fields($results);
//Tracer("MSSQL: nb_col=$nb_col");
						$metadata=sqlsrv_field_metadata($results);
						break;
				}
				$this->WritePropertyByName("c",$nb_col);
//				$this->WriteValue($nb_col);
				$this->WriteStartArray();
//Tracer("nb_col=$nb_col");
				for ($i=0;$i<$nb_col;$i++)
				{
					switch($this->m_type_cnx)
					{
						case "PDO":
							$col_meta=$results->getColumnMeta($i);
							$nom_col=$col_meta['name'];
							$max_length=$col_meta['len'];
							$type_col=$col_meta['native_type'];
//							$type_col_bis=$col_meta['pdo_type'];
//Tracer("nom_col=".$nom_col.", type_col=".$type_col);
							if(startsWith($nom_col,"id_") && !endsWith($nom_col,"WITH"))
								$type_col="int";
							break;
						case "ODBC13":
							$nom_col=odbc_field_name($results,$i+1);
							if(strlen($nom_col)==0)
								$nom_col="col".$i;
							$max_length=odbc_field_len($results,$i+1);
							$type_col=odbc_field_type($results,$i+1);
							break;
						case "MSSQL":
							$nom_col=$metadata[$i]["Name"];
							$max_length=$metadata[$i]["Size"];
							$type_col=$metadata[$i]["Type"];
//Tracer("nom_col=".$nom_col.", type_col=".$type_col);
						break;
					}
					/*
					if($i>0)
					{
						print ",";
					}
					*/
//T(")debut objet colonne(");
					$this->WriteStartObject();
//T(")propriete nom de colonne(");
					$type_col_int=0;
					switch(strtolower($type_col))
					{
						case "bit":
						case -7:
							$type_col_int=4;
							break;
						case "date":
						case "datetime":
						case 93:
							$type_col_int=3;
							break;
						break;
						case "int identity":
						case "int":
						case 4:
						case "long":
						case "longlong":
							$type_col_int=1;
							break;
						case "nvarchar":
						case "varchar":
						case -9:
						case 12:
						case "var_string":
						case "string":
							$type_col_int=2;
							break;
						case "float":
						case "newdecimal":
							$type_col_int=5;
							break;
						default:
							print "type_col_inconnu:".$type_col;
							break;
					}
//T(")valeur type colonne(");
					$this->WritePropertyByName($nom_col,$type_col_int);
//					$this->WriteValue($type_col_int);
//T(")fin objet colonne(");
					$this->WriteEndObject();
				}
//T(")fin tableau colonnes(");
				$this->WriteEndArray();
//				print ",";
				$this->WriteSeparateurLigCol();
//T(")propriete lignes(");
//T(")debut tableau des lignes(");
				switch($this->m_type_cnx)
				{
					case "PDO":
						$nb_lig=$results->rowCount();
						break;
					case "ODBC13":
//						$nb_lig=odbc_num_rows($results);
						$nb_lig=99999;
						break;
					case "MSSQL":
						$nb_lig=sqlsrv_num_rows($results);
//Tracer("MSSQL: nb_lig=($nb_lig)");
//						$nb_lig=9;
						break;
				}
//				$this->WritePropertyByName("l",$nb_lig);
				$this->WritePropertyByName("l","%nb_lig%");
//				$this->WriteValue($nb_lig);
				$this->WriteStartArray();
//Tracer("TranscrireEnJSonUneTable: nb_lig=($nb_lig)");
				$fini=0;
				$num_lig=0;
				while ($fini==0)
				{
					switch($this->m_type_cnx)
					{
						case "PDO":
							$row=$results->fetch(PDO::FETCH_BOTH);
							if($row ===false)
								$fini=1;
							break;
						case "ODBC13":
							$nb_col_resultat=odbc_fetch_into($results,$row);
							if($nb_col_resultat!=$nb_col)
								$fini=1;
							break;
						case "MSSQL":
//							$row=sqlsrv_fetch_array($results,SQLSRV_FETCH_ASSOC);
//Tracer("avant sql_fetch");
//$azer=SQLSRV_SCROLL_NEXT;
//Tracer('azer='.$azer);
							$row=sqlsrv_fetch_array($results);
//Tracer("apres sql_fetch");
							if($row===false ||$num_lig>=$nb_lig)
								$fini=1;
							break;
					}
//					if($row)
					if($fini==0)
					{
						/*
						if($num_lig>0)
						{
							print ",";
						}
						*/
//T(")debut objet ligne(");
						$this->WriteStartObject();
//T(")propriete statut(");
/*
						$this->WritePropertyName("s");
//				$etat=$row[0];
						$etat_int=1;	// pas modifi?
//T(")valeur statut(");
						$this->WriteValue($etat_int);
						print ",";
//T(")propriete valeurs des colonnes(");
						$this->WritePropertyName("v");
						*/
//T(")debut tableau des valeurs(");
//						$this->WritePropertyByName("v","%nb_cel%");
						$tab_cel="[";
						$nb_cel=0;
//						$this->WriteStartArray();
						$debut=1;
//Tracer("nb cols=($nb_col)");
						for($j=0;$j<$nb_col;$j++)
						{
							switch($this->m_type_cnx)
							{
								case "PDO":
									$val_col=$row[$j];
									$val_col_string=$val_col;
									$col_meta=$results->getColumnMeta($j);
									$nom_col=$col_meta['name'];
									$max_length=$col_meta['len'];
									$type_col=$col_meta['native_type'];
									if(startsWith($nom_col,"id_") && !endsWith($nom_col,"WITH"))
										$type_col="int";
//Tracer("col($j)=($val_col_string); type_col=($type_col)"); 
									break;
								case "ODBC13":
									$val_col=$row[$j];
									$val_col_string=$val_col;
									$type_col=odbc_field_type($results,$j+1);
									break;
								case "MSSQL":
									$val_col=$row[$metadata[$j]["Name"]];
									$type_col=$metadata[$j]["Type"];
//Tracer("j=".$j.", metadata=".$metadata[$j].", val_col=".$val_col.", type_col=".$type_col);
									if($val_col!=null)
									{
										if($type_col==93)
											$val_col_string=$val_col->format('Ymd');
										else
											$val_col_string=$val_col;
									}
									else
										$val_col_string="";
//echo "val_col=(".$val_col_string."); type_col=".$type_col."\n";
									break;
							}
//print "val_col=".$val_col.", type_col=".$type_col;
							if(is_null($val_col))
							{
							}
							else if(strlen($val_col_string)>0)
							{
//						print "passe";
/*
								if($debut == 1)
								{
									$debut=0;
								}
								else
								{
									print ",";
								}
								*/
//T(")debut objet valeur de colonne(");
//								$this->WriteStartObject();
								$tab_cel.="{";
//						WritePropertyName($num_val);
//T(")propriete numero de colonne(");
//								$this->WritePropertyNumber($j);
//						print "(j=$j,type_col=".$type_col.")";
								$type_col_lower=strtolower($type_col);
								switch($type_col_lower)
								{
									case "bit":
									case -7:
//T(")valeur de colonne(");
//										$this->WriteValue($val_col);
//										$this->WritePropertyByNumber($j,$val_col);
										$tab_cel.=$j.":".$val_col;
										$nb_cel++;
										break;
									case "int identity":
									case "int":
									case 4:
									case "long":
									case "longlong":
									case "float":
									case "newdecimal":
//T(")valeur de colonne(");
//										$this->WriteValue($val_col);
//										$this->WritePropertyByNumber($j,$val_col);
										$tab_cel.=$j.":".$val_col;
										$nb_cel++;
										break;
									case "date":
									case "datetime":
									case 93:
										$suffixe1=" 00:00:00.000";
										$suffixe2=" 00:00:00";
//Tracer("valeur date:($val_col_string)");
										if(endswith($val_col_string,$suffixe1) || endswith($val_col_string,$suffixe2))
										{
//									$val_col=substr($val_col,0,strlen($val_col)-strlen($suffixe));
											$val_col=substr($val_col,0,4).substr($val_col,5,2).substr($val_col,8,2);
//Tracer("cas 1: val_col=($val_col)");
//											$this->WriteValue($val_col);
//											$this->WritePropertyByNumber($j,$val_col);
											$tab_cel.=$j.":".$val_col;
											$nb_cel++;
										}
										elseif(strlen($val_col)>10)	//cas date et heure non nulle
										{
											$val_col=substr($val_col,0,4).substr($val_col,5,2).substr($val_col,8,2).substr($val_col,11,2).substr($val_col,14,2).substr($val_col,17,2);
//Tracer("cas 2: val_col=($val_col)");
											$tab_cel.=$j.":".$val_col;
											$nb_cel++;
										}
										elseif(strlen($val_col)==10 && substr($val_col,4,1)=="-" && substr($val_col,7,1)=="-")
										{
//									$val_col=substr($val_col,0,strlen($val_col)-strlen($suffixe));
											$val_col=substr($val_col,0,4).substr($val_col,5,2).substr($val_col,8,2);
//Tracer("cas 1: val_col=($val_col)");
//											$this->WriteValue($val_col);
//											$this->WritePropertyByNumber($j,$val_col);
											$tab_cel.=$j.":".$val_col;
											$nb_cel++;
										}
										else
										{
//											print "\"";
//T(")valeur de colonne(");
//											$this->WriteValue($val_col_string);
											$this->WritePropertyByNumber($j,$val_col_string);
											$tab_cel.="\"".$j.":".$val_col_string."\"";
//Tracer("cas 3: val_col=($val_col_string)");
											$nb_cel++;
//											print "\"";
										}
										break;
									case "nvarchar":
									case "varchar":
									case -9:
									case 12:
									case "var_string":
									case "string":
//										print "\"";
										$val_format=str_replace("\\","\\\\",$val_col);
//T(")valeur de colonne(");
//											$this->WriteValue($val_format);
//											$this->WritePropertyByNumber($j,$val_format);
										$tab_cel.=$j.":".$val_col;
										$nb_cel++;
//										print "\"";
										break;
									default:
										print "type inconnu:".$type_col;
										break;
								}
//T(")fin objet valeur de colonne(");
//								$this->WriteEndObject();
								$tab_cel.="}";
							}
						}
						$this->WriteTabCel($nb_cel,$tab_cel);
					}
					else
					{
						$fini=1;
					}
					if($fini==0)
					{
//T(")fin tableau des valeurs(");
						$this->WriteEndArray();
//T(")fin objet ligne(");
						$this->WriteEndObject();
						$num_lig++;
						if($this->m_type_cnx == "MSSQL" && $num_lig>=$nb_lig)
						{
							$fini=1;
						}
					}
				}
//T(")fin tableau des lignes(");
				$this->WriteEndArray();
//T(")fin objet table(");
				$this->WriteEndObject();
			}
//T(")fin tableau des tables(");
			$this->WriteEndArray();
//T(")fin objet liste des tables(");
			$this->WriteEndObject();
//Tracer("tampon_sortie avant remplacement du nb de lignes=(".$this->m_tampon_sortie.")");
			$sortie=str_replace("%nb_lig%",$num_lig,$this->m_tampon_sortie);
//Tracer("tampon_sortie après remplacement du nb de lignes=(".$this->m_tampon_sortie.")");
//Tracer("avant affichage sortie");
//Tracer("sortie=(".$sortie.")");
			print $sortie;
		}
		private $tabd;
		private $ind;
		function MessageException($msg)
		{
			return $msg." à l'indice ".$this->ind;
		}
		function LireNomPropriete()
		{
			/*
			if($this->tabd[$this->ind]!="\"")
				throw new Exception($this->MessageException("Pas de guillemet avant un nom de propriété"));
			$this->ind++;
			*/
			$fini=0;
			$nom_prop="";
			while($fini==0)
			{
				if($this->tabd[$this->ind]==":")
					$fini=1;
				else
				{
					$nom_prop.=$this->tabd[$this->ind];
				}
				$this->ind++;
			}
			return $nom_prop;
		}
		function LireValPropriete()
		{
			/*
			if($this->tabd[$this->ind]!="\"")
				throw new Exception($this->MessageException("Pas de guillemet avant un nom de propriété"));
			$this->ind++;
			*/
			$fini=0;
			$val_prop="";
			while($fini==0)
			{
			if($this->tabd[$this->ind]=="}")
				$fini=1;
				else
				{
					$val_prop.=$this->tabd[$this->ind];
					$this->ind++;
				}
			}
			return $val_prop;
		}
		function LireEntier()
		{
			$fini_entier=0;
			$val_entier=0;
//Tracer("debut LireEntier");
			while($fini_entier==0)
			{
				$c=$this->tabd[$this->ind];
//Tracer("c=($c)");
				if($c == '0' || $c == '1' || $c == '2' || $c == '3' || $c == '4' || $c == '5' || $c == '6' || $c == '7' || $c == '8' || $c == '9')
				{
//Tracer("val_entier 1=($val_entier)");
					$val_entier*=10;
//Tracer("val_entier 2=($val_entier)");
					$val_entier+=intval($c);
//Tracer("val_entier 3=($val_entier)");
					$this->ind++;
				}
				else
					$fini_entier=1;
			}
//Tracer("retour de LireEntier: val_entier=($val_entier)");
			return $val_entier;
		}
		function DecoderTableJSon($donnees)
		{
//Tracer("Debut de DecoderTableJSon");
			$this->m_nb_col=0;
			$this->m_tab_col=array();
			$this->m_nb_val=0;
			$this->m_tab_val=array();
			$this->tabd=str_split($donnees);
			$this->ind=0;
			$c=$this->tabd[$this->ind];
//			Tracer("c=".$c);
			if($this->tabd[$this->ind]!=$this->m_start_object)
				throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de début"));
			$this->ind++;
			$nom_prop=$this->LireNomPropriete();
			if($nom_prop!="t")
				throw new Exception($this->MessageException("DecoderTableJSon: Pas de propriété tables ('t')"));
			$nb_tab=$this->LireEntier();
			/*
			if($this->tabd[$this->ind]!=":")
				throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
			$this->ind++;
			*/
			if($this->tabd[$this->ind]!=$this->m_start_array)
				throw new Exception($this->MessageException("DecoderTableJSon: Pas de début du tableau des tables"));
			$this->ind++;
			$fini_tables=0;
			while($fini_tables==0)
			{
				if($this->tabd[$this->ind]==$this->m_end_array)
				{
					//plus de table a decoder
					$fini_tables=1;
				}
				else
				{
//Tracer("Debut de table");
					if($this->tabd[$this->ind]!=$this->m_start_object)
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de début de table"));
					$this->ind++;
					$nom_prop=$this->LireNomPropriete();
//Tracer("nom_prop=".$nom_prop);
					if($nom_prop!="c")
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de propriété colonnes d'une table ('c')"));
					/*
					if($this->tabd[$this->ind]!=":")
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
					$this->ind++;
					*/
					$nb_col=$this->LireEntier();
					if($this->tabd[$this->ind]!=$this->m_start_array)
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de début du tableau des colonnes"));
					$this->ind++;
					$fini_col=0;
					while($fini_col==0)
					{
						if($this->tabd[$this->ind]==$this->m_end_array)
						{
							//plus de colonne a decoder
							$fini_col=1;
						}
						else
						{
							if($this->tabd[$this->ind]!=$this->m_start_object)
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de début de colonne"));
							$this->ind++;
							$nom_col=$this->LireNomPropriete();
							$this->m_tab_nom_col[$this->m_nb_col]=$nom_col;
							/*
							if($this->tabd[$this->ind]!=":")
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
							$this->ind++;
							*/
							$type_col=$this->LireEntier();
							$this->m_tab_type_col[$this->m_nb_col]=$type_col;
							$this->m_nb_col++;
							if($this->tabd[$this->ind]!=$this->m_end_object)
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de fin de colonne"));
							$this->ind++;
							/*
							if($this->tabd[$this->ind]==",")
								$this->ind++;
							*/
						}
					}
					if($this->tabd[$this->ind]!=$this->m_end_array)
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de fin du tableau des colonnes"));
					$this->ind++;
//Tracer("fin des colonnes");
					if($this->tabd[$this->ind]!=",")
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
					$this->ind++;
					$nom_prop=$this->LireNomPropriete();
//Tracer("nom_prop=".$nom_prop);
					if($nom_prop!="l")
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de propriété lignes d'une table ('l')"));
					/*
					if($this->tabd[$this->ind]!=":")
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
					$this->ind++;
					*/
					$nb_lig=$this->LireEntier();
					if($this->tabd[$this->ind]!=$this->m_start_array)
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de début du tableau des lignes"));
					$this->ind++;
//Tracer("Debut des lignes: ind=".$this->ind);
					$fini_lig=0;
					$this->m_nb_lig=0;
					$this->m_lignes=array();
					while($fini_lig==0)
					{
						if($this->tabd[$this->ind]==$this->m_end_array)
						{
							//plus de ligne a decoder
							$fini_lig=1;
						}
						else
						{
							$une_ligne=new Ligne();
							if($this->tabd[$this->ind]!=$this->m_start_object)
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de début de ligne"));
							$this->ind++;
							/*
							$nom_col=$this->LireNomPropriete();
							if($nom_col!="s")
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de valeur de statut de ligne ('s')"));
							if($this->tabd[$this->ind]!=":")
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
							$this->ind++;
							$etat=$this->LireEntier();
							$this->m_tab_nom_col[$this->m_nb_col]=$nom_col;
							if($this->tabd[$this->ind]!=":")
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
							$this->ind++;
							*/
//Tracer("Debut d'une ligne: ind=".$this->ind);
							$nom_prop=$this->LireNomPropriete();
//Tracer("nom_prop=".$nom_prop);
							if($nom_prop!="v")
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de propriété valeurs d'une ligne ('v')"));
							/*
							if($this->tabd[$this->ind]!=":")
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur :"));
							$this->ind++;
							*/
							$nb_val=$this->LireEntier();
							if($this->tabd[$this->ind]!=$this->m_start_array)
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de début du tableau des valeurs de la ligne"));
							$this->ind++;
							$fini_col_lig=0;
							while($fini_col_lig==0)
							{
								if($this->tabd[$this->ind]==$this->m_end_array)
								{
									//plus de colonne a decoder pour cette ligne
									$fini_col_lig=1;
								}
								else
								{
//Tracer("Debut d'une valeur de ligne: ind=".$this->ind);
									if($this->tabd[$this->ind]!=$this->m_start_object)
										throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de début d'une valeur de la ligne"));
									$this->ind++;
//Tracer("Avant LireEntier: ind=".$this->ind);
									$num_col=$this->LireEntier();
//Tracer("Apres LireEntier: lecture d'une cellule: ind=".$this->ind.", num_col=".$num_col);
									if($this->tabd[$this->ind]!=":")
										throw new Exception($this->MessageException("DecoderTableJSon: Pas de séparateur entre numéro de colonne et valeur"));
									$this->ind++;
//Tracer("Avant LirePropriete: ind=".$this->ind);
									$val_col=$this->LireValPropriete();
//Tracer("Apres LirePropriete: ind=".$this->ind.", val_col=".$val_col);
									if($this->tabd[$this->ind]!=$this->m_end_object)
										throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de fin d'une valeur de la ligne"));
									$this->ind++;
									$une_cellule=new Cellule();
									$une_cellule->m_num_col=$num_col;
									$une_cellule->m_val_col=$val_col;
//									$une_ligne->m_cellules[$une_ligne->m_nb_cellules]=$une_cellule;
//									$une_ligne->m_nb_cellules++;
									$une_ligne->AjouterCellule($une_cellule);
									/*
									if($this->tabd[$this->ind]==",")
										$this->ind++;
									*/
//Tracer("Fin d'une valeur de ligne: ind=".$this->ind);
								}
							}
							if($this->tabd[$this->ind]!=$this->m_end_array)
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de fin du tableau des valeurs de la ligne"));
							$this->ind++;
							if($this->tabd[$this->ind]!=$this->m_end_object)
								throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de fin de ligne"));
							$this->ind++;
							$this->m_lignes[$this->m_nb_lig]=$une_ligne;
							$this->m_nb_lig++;
						}
					}
					if($this->tabd[$this->ind]!=$this->m_end_array)
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de fin du tableau des lignes"));
					$this->ind++;
					if($this->tabd[$this->ind]!=$this->m_end_object)
						throw new Exception($this->MessageException("DecoderTableJSon: Pas de fin d'une table"));
					$this->ind++;
				}
			}
			if($this->tabd[$this->ind]!=$this->m_end_array)
				throw new Exception($this->MessageException("DecoderTableJSon: Pas de fin du tableau des tables"));
			$this->ind++;
			if($this->tabd[$this->ind]!=$this->m_end_object)
				throw new Exception($this->MessageException("DecoderTableJSon: Pas de caractère de fin"));
		}
		function DecoderDirJSon($donnees)
		{
//Tracer("Debut de DecoderTableJSon");
			$this->m_nb_nd=0;
			$this->m_tab_nd=array();
			$this->tabd=str_split($donnees);
			$this->ind=0;
			$c=$this->tabd[$this->ind];
//			Tracer("c=".$c);
			if($c!=$this->m_start_array)
				throw new Exception($this->MessageException("DecoderDirJSon: Pas de caractère de début"));
			$this->ind++;
			$fini=0;
			while($fini==0)
			{
				if($this->tabd[$this->ind]==$this->m_end_array)
					$fini=1;
				else
				{
					$nom_nd=$this->LireNomPropriete();
					$type_nd=$this->tabd[$this->ind];
				}
				$this->ind++;
			}
		}
	}

	class AccesBd
	{
		private $m_cnxstr;
		private $m_srv;
		private $m_uid;
		private $m_pwd;
		private $m_bd;
		private $m_type_cnx;
		private $m_type_bd_sql;
		/*
		function __construct($srv,$bd,$uid,$pwd,$type_bd_sql,$type_cnx)
		{
			$this->m_srv=$srv;
			$this->m_uid=$uid;
			$this->m_pwd=$pwd;
			$this->m_bd=$bd;
			$this->m_type_cnx=$type_cnx;
			$this->m_type_bd_sql=$type_bd_sql;
		}
		*/		
		function Init()
		{
			$fic_strcnx=fopen('strcnx_php.txt','r');
			$strcnx=fgets($fic_strcnx);
			fclose($fic_strcnx);
			$params=preg_split('/\|/',$strcnx);
			$this->m_cnxstr=$params[0];
			$this->m_uid=$params[1];
			$this->m_pwd=$params[2];
			$this->m_bd=$params[3];
			$this->m_type_cnx=$params[4];
			$this->m_type_bd_sql=$params[5];
			$this->m_srv=$params[6];
//Tracer("AccesBd:($strcnx)->($this->m_cnxstr)/($this->m_uid)/($this->m_pwd)/($this->m_bd)/($this->m_type_cnx)/($this->m_type_bd_sql)");
			/*
			$this->m_srv=$params[0];
			$this->m_uid=$params[1];
			$this->m_pwd=$params[2];
			$this->m_bd=$params[3];
			$this->m_type_cnx=$params[4];
			$this->m_type_bd_sql=$params[5];
			*/
		}
		function RecupererParams()
		{
//			print $this->m_cnxstr."/".$this->m_srv."/".$this->m_uid."/".$this->m_bd."/".$this->m_type_cnx."/".$this->m_type_bd_sql;
			$sep="/";
			print $this->m_type_cnx.$sep.$this->m_type_bd_sql.$sep.$this->m_srv.$sep.$this->m_bd;
		}
		function RecupererNomBd()
		{
//			print $this->m_cnxstr."/".$this->m_srv."/".$this->m_uid."/".$this->m_bd."/".$this->m_type_cnx."/".$this->m_type_bd_sql;
			return $this->m_bd;
		}
		function MessageException($msg)
		{
			return $msg;
		}
		function TransformerSqlPourMySql($sql)
		{
//Tracer("Debut de TransformerSqlPourMysl($sql)");
//			if(startsWith($sql,"exec "))
			$needle="exec ";
			if(substr(strtolower($sql), 0, strlen($needle)) == $needle)
			{
//Tracer("requete a transformer");
				$params=substr($sql,5);
				$sql="call ".$params;
				$pos=strpos($params," ");
				if($pos>0)
				{
					$nom_fonction=substr($params,0,$pos);
					$liste_params=substr($params,1+$pos);
					$sql="call ".$nom_fonction."(".$liste_params.")";
//T("sql=(".$sql.")");
				}
				else
				{
					$sql=$sql."()";
				}
			}
			return $sql;
		}
	
		function LireListe($sql)
		{
//Tracer("LireListe: type_bd_sql=".$this->m_type_bd_sql);
			if(startsWith($this->m_type_bd_sql,"mysql"))
			{
//Tracer("avant TransformerSqlPourMySql");
				$sql=$this->TransformerSqlPourMySql($sql);
//Tracer("apres TransformerSqlPourMySql: sql=(".$sql.")");
			}
			try
			{
//Tracer("type_cnx=".$this->m_type_cnx.", sql=".$sql);
//Tracer("LireListe: 1: bd=".$this->m_bd.", sql=".$sql);
				switch($this->m_type_cnx)
				{
					case "PDO":
//						$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("LireListe: 2");
//						$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
						$cnx=new PDO($this->m_cnxstr,$this->m_uid,$this->m_pwd);
						if($cnx)
						{
//Tracer("LireListe: 3");
							$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("LireListe: 4: sql=".$sql);
							$results=$cnx->query($sql);
//Tracer("LireListe: 5");
							if($results === false)
							{
								$msg="Erreur d'exécution du SQL pour sql=(".$sql.")";
								TracerErreur($msg);
								$errs=$cnx->errorinfo();
								foreach($errs as $err)
								{
									$msg=$err['message']."/".$err['code'];
									TracerErreur($msg);
								}
							}
							else
							{
								$nb=0;
								$fini=0;
								while ($fini==0)
								{
									$row=$results->fetch(PDO::FETCH_BOTH);
									if($row ===false)
										$fini=1;
									if($fini==0)
									{
										$val_col=$row[0];
										$tab[$nb]=$val_col;
										$nb++;
									}
								}
//Tracer("pdo.LireListe: 6");
							}
						}
						else
						{
							$msg="Erreur de connexion au serveur SQL";
							TracerErreur($msg);
						}
						break;
					case "ODBC10":
						$cnx = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$srv;Database=$bd;", $uid, $pwd);
						break;
					case "ODBC13":
						$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $this->m_uid, $this->m_pwd, SQL_CUR_USE_ODBC);
						$results = odbc_prepare($cnx,$sql);
//						odbc_setoption($result, 6, SQL_CUR_USE_ODBC, 0)
//echo "appel de odbc_execute($results)<br/>";
						odbc_execute($results);	//  or die($this->DbErreur("sql_query",$sql));
//						$aj=new AccesJSon($this->m_type_cnx);
//						$aj->TranscrireEnJsonUneTable($results);
						odbc_free_result($results);
						odbc_close($cnx);
						break;
					case "MSSQL":
//Tracer("LireListe: MSSQL: sql=($sql)");
						sqlsrv_configure("WarningsReturnAsErrors", 0);
						$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
//Tracer("LireListe: srv=".$this->m_srv.", bd=".$this->m_bd.", uid=".$this->m_uid.", pwd=".$this->m_pwd);
						$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
						if($cnx)
						{
							$params = array();
//							$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
							$options =  array( "Scrollable" => SQLSRV_CURSOR_STATIC );
							$results = sqlsrv_query($cnx,$sql,$params,$options);
//Tracer("LireListeMSSQL(".$sql.")");
//							$results = sqlsrv_query($cnx,$sql);
//							$results = sqlsrv_prepare($cnx,$sql,$params,$options);
							if($results === false)
							{
								$msg="Erreur d'exécution du SQL pour sql=(".$sql.")";
								TracerErreur($msg);
								$errs=sqlsrv_errors();
								foreach($errs as $err)
								{
									$msg=$err['message']."/".$err['code'];
									TracerErreur($msg);
								}
							}
							else
							{
//							sqlsrv_execute($results);
//Tracer("appel de transcrireenjson");
//								$aj=new AccesJSon($this->m_type_cnx);
//								$aj->TranscrireEnJsonUneTable($results);
								sqlsrv_free_stmt($results);
							}
						}
						else
						{
							$msg="Erreur de connexion au serveur SQL";
							TracerErreur($msg);
						}
						break;
				}
			}
			catch (Exception $e)
			{
				$msg="Erreur LireListe($sql):".$e->getMessage();
				print $msg;
				TracerErreur($msg);
				switch($this->m_type_cnx)
				{
					case "PDO":
					break;
				}
			}
			return $tab;
		}
		function LireUneTable($sql)
		{
//Tracer("LireUneTable: type_bd_sql=".$this->m_type_bd_sql);
			if(startsWith($this->m_type_bd_sql,"mysql"))
			{
//Tracer("avant TransformerSqlPourMySql");
				$sql=$this->TransformerSqlPourMySql($sql);
//Tracer("apres TransformerSqlPourMySql: sql=(".$sql.")");
			}
//Tracer("type_cnx=".$this->m_type_cnx.", sql=".$sql);
//Tracer("LireUneTable: 1: bd=".$this->m_bd.", sql=".$sql);
			switch($this->m_type_cnx)
			{
				case "PDO":
//						$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("LireUneTable: 2");
//						$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					$cnx=new PDO($this->m_cnxstr,$this->m_uid,$this->m_pwd);
					if($cnx)
					{
//Tracer("LireUneTable: 3");
						$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("LireUneTable: 4: sql=".$sql);
						$results=$cnx->query($sql);
//Tracer("LireUneTable: 5");
						if($results === false)
						{
							$msg="Erreur d'exécution du SQL pour sql=(".$sql.")";
							TracerErreur($msg);
							$errs=$cnx->errorinfo();
							foreach($errs as $err)
							{
								$msg=$err['message']."/".$err['code'];
								TracerErreur($msg);
							}
						}
						else
						{
							$aj=new AccesJSon($this->m_type_cnx);
							$aj->TranscrireEnJsonUneTable($results);
//Tracer("pdo.LireUneTable: 6");
						}
					}
					else
					{
						$msg="Erreur de connexion au serveur SQL";
						TracerErreur($msg);
					}
					break;
				case "ODBC10":
					$cnx = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$srv;Database=$bd;", $uid, $pwd);
					break;
				case "ODBC13":
					$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $this->m_uid, $this->m_pwd, SQL_CUR_USE_ODBC);
					$results = odbc_prepare($cnx,$sql);
//						odbc_setoption($result, 6, SQL_CUR_USE_ODBC, 0)
//echo "appel de odbc_execute($results)<br/>";
					odbc_execute($results);	//  or die($this->DbErreur("sql_query",$sql));
					$aj=new AccesJSon($this->m_type_cnx);
					$aj->TranscrireEnJsonUneTable($results);
					odbc_free_result($results);
					odbc_close($cnx);
					break;
				case "MSSQL":
//Tracer("LireUneTable: MSSQL: sql=($sql)");
					sqlsrv_configure("WarningsReturnAsErrors", 0);
					$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
//Tracer("LireUneTable: srv=".$this->m_srv.", bd=".$this->m_bd.", uid=".$this->m_uid.", pwd=".$this->m_pwd);
					$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
					if($cnx)
					{
						$params = array();
//							$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
						$options =  array( "Scrollable" => SQLSRV_CURSOR_STATIC );
						$results = sqlsrv_query($cnx,$sql,$params,$options);
//Tracer("LireUneTableMSSQL(".$sql.")");
//							$results = sqlsrv_query($cnx,$sql);
//							$results = sqlsrv_prepare($cnx,$sql,$params,$options);
						if($results === false)
						{
							$msg="Erreur d'exécution du SQL pour sql=(".$sql.")";
							TracerErreur($msg);
							$errs=sqlsrv_errors();
							foreach($errs as $err)
							{
								$msg=$err['message']."/".$err['code'];
								TracerErreur($msg);
							}
						}
						else
						{
//							sqlsrv_execute($results);
//Tracer("appel de transcrireenjson");
							$aj=new AccesJSon($this->m_type_cnx);
							$aj->TranscrireEnJsonUneTable($results);
							sqlsrv_free_stmt($results);
						}
					}
					else
					{
						$msg="Erreur de connexion au serveur SQL";
						TracerErreur($msg);
					}
					break;
			}
			/*
			catch (Exception $e)
			{
				$msg="Erreur LireUneTable($sql):".$e->getMessage();
				print $msg;
				TracerErreur($msg);
				switch($this->m_type_cnx)
				{
					case "PDO":
					break;
				}
			}
			*/
		}
		function PreparerUneValeur($sql)
		{
//Tracer("LireUneValeur: 1: type_bd_sql=".$this->m_type_bd_sql);
			if(startsWith($this->m_type_bd_sql,"mysql"))
				$sql=$this->TransformerSqlPourMySql($sql);
//Tracer("LireUneValeur apres TransfoPourMySql: sql='$sql)");
			$val_col="";
			switch($this->m_type_cnx)
			{
				case "PDO":
//Tracer("LireUneValeur: 1: bd=".$this->m_bd);
//					$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("LireUneValeur: 2");
//					$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					$cnx=new PDO($this->m_cnxstr,$this->m_uid,$this->m_pwd);
					if($cnx)
					{
						$cnx->exec('set names utf8');
//Tracer("LireUneValeur: 3");
						$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("LireUneValeur: 4: sql=".$sql);
						$results=$cnx->query($sql);
//Tracer("LireUneValeur: 5");
						if($results === false)
						{
							$msg="Erreur d'exécution du SQL pour sql=(".$sql.")";
//							TracerErreur($msg);
							$errs=$cnx->errorinfo();
							foreach($errs as $err)
							{
								$msg.=$err['message']."/".$err['code'];
							}
							$val_col=$msg;
						}
						else
						{
							$row=$results->fetch(PDO::FETCH_BOTH);
//Tracer("LireUneValeur: 11");
							$val_col=$row[0];
						}
					}
					else
					{
						$msg="Erreur de connexion au serveur SQL";
						$val_col=$msg;
					}
					return $val_col;
//Tracer("pdo.LireUneValeur: 6");
					break;
				case "ODBC13":
					$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $this->m_uid, $this->m_pwd, SQL_CUR_USE_ODBC);
					$results = odbc_prepare($cnx,$sql);
					@odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
					$nb_cols=odbc_num_fields($results);
					if($nb_cols==1)
					{
						odbc_fetch_into($results,$row);
						$val_col=$row[0];
					}
					odbc_free_result($results);
					odbc_close($cnx);
					return $val_col;
					break;
				case "MSSQL":
					$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
//Tracer("LireUneValeur: srv=".$this->m_srv.", bd=".$this->m_bd.", uid=".$this->m_uid.", pwd=".$this->m_pwd);
					$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
					if($cnx)
					{
						// Call a simple query
						$params = array();
						$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
//						$options =  array( "Scrollable" => SQLSRV_CURSOR_STATIC  );
						$results = sqlsrv_query($cnx,$sql,$params,$options);
//Tracer("LireUneValeur: apres sqlsrv_query");
//Tracer("LireUneValeur: results=".$results);
						if( $results === false)
						{
							$msg="Erreur d'exécution du SQL pour sql=(".$sql.")";
							TracerErreur($msg);
							$errs=sql_errors();
							foreach($errs as $err)
							{
								$msg.=$err;
							}
							$val_col=$msg;
						}
						else
						{
							$nb_col=sqlsrv_num_fields($results);
							$nb_lig=sqlsrv_num_rows($results);
							$metadata=sqlsrv_field_metadata($results);
							$row=sqlsrv_fetch_array($results,SQLSRV_FETCH_ASSOC);
							$val_col=$row[$metadata[0]["Name"]];
							sqlsrv_free_stmt($results);
						}
					}
					else
					{
						$msg="Erreur de connexion au serveur SQL";
						$val_col=$msg;
					}
					return $val_col;
					break;
			}
		}
		function LireUneValeur($sql)
		{
			print $this->PreparerUneValeur($sql);
		}
	
		function PreparerExecSql($sql)
		{
//Tracer("Debut de ExecSql($sql): type_bd_sql=($this->m_type_bd_sql)");
			$ret="";
			if(startsWith($this->m_type_bd_sql,"mysql"))
				$sql=$this->TransformerSqlPourMySql($sql);
//Tracer("apres transfo pour mysql: sql=($sql)");
//			try
//			{
				switch($this->m_type_cnx)
				{
					case "ODBC13":
						$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $uid, $this->m_pwd);
						odbc_exec($cnx,$sql);
						odbc_close($cnx);
						break;
					case "MSSQL":
						$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
						$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
						$stmt=sqlsrv_prepare($cnx,$sql);
						sqlsrv_execute($stmt);
						sqlsrv_close($cnx);
						break;
					case "PDO":
//						$cnx = new PDO('mysql:host=$srv;dbname=$bd;charset=utf8', $uid, $pwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
//						$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("Exec Sql PDO: 2");
//						$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
						$cnx=new PDO($this->m_cnxstr,$this->m_uid,$this->m_pwd);
						if($cnx)
						{
							$cnx->exec('set names utf8');
//Tracer("Exec Sql PDO: 3");
							$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("Exec Sql PDO: 4: ".$sql);
							$cnx->exec($sql);
//Tracer("Exec Sql PDO: 5");
						}
						else
						{
							$msg="Erreur de connexion au serveur SQL";
							$ret=$msg;
						}
//Tracer("Exec Sql PDO: 6");
						break;
				}
//Tracer("Exec Sql PDO: 7");
//			}
//			catch (Exception $e)
//			{
//				Tracer("Erreur ExecSql($sql):".$e->getMessage());
//			}
//Tracer("Exec Sql PDO: 8");
			return $ret;
		}
		function ExecSql($sql)
		{
//Tracer("Exec Sql: A");
			$ret=$this->preparerExecSql($sql);
//Tracer("Exec Sql: B");
			if(startsWith($ret,"Erreur"))
				Tracer($ret);
//Tracer("Exec Sql: C");
			return $ret;
		}
		function EcrireUneTable($sql,$donnees)
		{
//			try
//			{
//Tracer("Debut de EcrireTable: sql=($sql)");
				$pos_params=strpos($sql,"@");
				$sql_sans_params=substr($sql,0,$pos_params);
//Tracer("Debut de EcrireTable: sql=($sql), pos_params=($pos_params), sql_sans_params=($sql_sans_params)");
				$aj=new AccesJSon($this->m_type_cnx);
				$aj->DecoderTableJSon($donnees);
				$nb_col=$aj->DonnerNbCol();
//Tracer("nb colonnes=".$nb_col);
				$tab_nom_col=$aj->DonnerTabNomCol();
				$tab_type_col=$aj->DonnerTabTypeCol();
				for($i=0;$i<$nb_col;$i++)
				{
					$nom_col=$tab_nom_col[$i];
					$type_col=$tab_type_col[$i];
//Tracer("col[$i]=($nom_col/$type_col)");
				}
				$nb_lig=$aj->DonnerNbLig();
//Tracer("nb lignes=".$nb_lig);
				$lignes=$aj->DonnerTabLig();
				for($i=0;$i<$nb_lig;$i++)
				{
//Tracer("EcrireUneTable: ligne[$i]");
					$sql_final=$sql_sans_params;
					$sql_traite=$sql;
					$une_ligne=$lignes[$i];
					$nb_cellules=$une_ligne->DonnerNbCellules();
//Tracer("nb_cellules=".$nb_cellules);
					$tab_cellules=$une_ligne->DonnerTabCellules();
					for($j=0;$j<$nb_col;$j++)	
					{
						$nom_col=$tab_nom_col[$j];
						$val_col_format="";
						for($k=0;$k<$nb_cellules;$k++)
						{
							$une_cellule=$une_ligne->DonnerUneCellule($k);
							$num_col=$une_cellule->m_num_col;
							if($num_col==$j)
							{
								$val_col=$une_cellule->m_val_col;
								$type_col=$tab_type_col[$num_col];
//Tracer("cellule[$j]=($num_col/$val_col/$nom_col)/$type_col");
								$val_col_format="";
								switch($type_col)
								{
									case 1:
										// entier
										$val_col_format=$val_col;
										break;
									case 2;
										// chaine de caracteres
										$val_col_format="'".str_replace("'","''",$val_col)."'";
										break;
									case 3:
										// date
										if(strlen($val_col)>10)
										{
											$val_col_format="'".substr($val_col,0,4).'-'.substr($val_col,4,2).'-'.substr($val_col,6,2).' '.substr($val_col,8,2).':'.substr($val_col,10,2).':'.substr($val_col,12,2)."'";
										}
										else if (strlen($val_col)==8)
										{
											$val_col_format="'".substr($val_col,0,4).'-'.substr($val_col,4,2).'-'.substr($val_col,6,2)."'";
										}
										else if (strlen($val_col)==0)
										{
											$val_col_format="null";
										}
										else
										{
											throw new Exception($this->MessageException("Date invalide (".$val_col.")"));
										}
//Tracer("val_col_format=($val_col_format)");
										break;
									case 4:
										// bit
										$val_col_format=$val_col;
										break;
									case 5:
										// flottant
										$val_col_format=$val_col;
										break;
									default:
										throw new Exception($this->MessageException("Type de colonne inconnu (".$type_col.")"));
								}
							}
						}
						if($j>0)
							$sql_final.=",";
						$val_sql=$val_col_format;
						if(strlen($val_col_format)>0)
							$sql_final.=$val_col_format;
						else
						{
							$sql_final.="null";
							$val_sql="null";
						}
//Tracer("col[$j]=($val_sql/$tab_nom_col[$j])/$tab_type_col[$j]");
						$sql_traite=str_replace("@".$nom_col."@",$val_sql,$sql_traite);
//Tracer("sql_traite=($sql_traite)");
					}
//Tracer("avant exec ($sql_traite)");
					$this->PreparerExecSql($sql_traite);
//Tracer("apres exec ($sql_traite)");
				}
//Tracer("fin");
//			}
//			catch (Exception $e)
//			{
//				Tracer("Erreur Ecrire une table:$sql:$donnees".$e->getMessage());
//			}
		}
/*
		function EcrireBlob($sql)
		{
			switch($this->m_type_cnx)
			{
				case "ODBC13":
					$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $this->m_uid, $this->m_pwd);
					$results = odbc_prepare($cnx,$sql);
					@odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
					$nb_cols=odbc_num_fields($results);
					if($nb_cols==1)
					{
						odbc_fetch_into($results,$row);
						$val_col=$row[0];
						print $val_col;
					}
					odbc_free_result($results);
					odbc_close($cnx);
					break;
				case "MSSQL":
					$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
					$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
					// Call a simple query
					$params = array();
					$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
					$results = sqlsrv_query($cnx,$sql,$params,$options);
//					$nb_col=sqlsrv_num_fields($results);
//					$nb_lig=sqlsrv_num_rows($results);
//					$metadata=sqlsrv_field_metadata($results);
					$row=sqlsrv_fetch_array($results,SQLSRV_FETCH_ASSOC);
					$val_col=$row['doc'];
					print base64_encode($val_col);
					sqlsrv_free_stmt($results);
					sqlsrv_close($cnx);
					break;
				case "PDO":
//Tracer("EcrireBlob: 1: bd=".$this->m_bd);
					$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("EcrireBlob: 2");
					$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => DO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					$cnx->exec('set names utf8');
//Tracer("EcrireBlob: 3");
					$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("EcrireBlob: 4: sql=".$sql);
					$results=$cnx->prepare($sql);
//Tracer("EcrireBlob: 4bis");
					$results->execute();
//Tracer("EcrireBlob: 5");
//					$col_meta=$results->getColumnMeta(0);
//Tracer("EcrireBlob: 6");
//					$nom_col=$col_meta['name'];
//Tracer("EcrireBlob: 7");
//					$max_length=$col_meta['len'];
//Tracer("EcrireBlob: 8");
//					$type_col=$col_meta['native_type'];
//Tracer("EcrireBlob: 9");
//					$nb_lig=$results->rowCount();
//Tracer("EcrireBlob: 10");
					$row=$results->fetch(PDO::FETCH_BOTH);
//Tracer("EcrireBlob: 11");
					$val_col=$row[0];
//Tracer("EcrireBlob: 12");
					print base64_encode($val_col);
//Tracer("EcrireBlob: 13");
					break;
			}
		}
		function EcrireBlob($sql,$nom_fic)
		{
//Tracer("EcrireBlob($sql,$nom_fic)");
//Tracer("EcrireBlob: type_cnx=".$this->m_type_cnx);
			switch($this->m_type_cnx)
			{
				case "ODBC13":
				/ *
					$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $this->m_uid, $this->m_pwd);
					$results = odbc_prepare($cnx,$sql);
					@odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
					$nb_cols=odbc_num_fields($results);
					if($nb_cols==1)
					{
						odbc_fetch_into($results,$row);
						$val_col=$row[0];
						print $val_col;
					}
					odbc_free_result($results);
					odbc_close($cnx);
				* /
					break;
				case "MSSQL":
				/ *
					$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
					$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
					// Call a simple query
					$params = array();
					$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
					$results = sqlsrv_query($cnx,$sql,$params,$options);
//					$nb_col=sqlsrv_num_fields($results);
//					$nb_lig=sqlsrv_num_rows($results);
//					$metadata=sqlsrv_field_metadata($results);
					$row=sqlsrv_fetch_array($results,SQLSRV_FETCH_ASSOC);
					$val_col=$row['doc'];
					print base64_encode($val_col);
					sqlsrv_free_stmt($results);
					sqlsrv_close($cnx);
				* /
					break;
				case "PDO":
				/ *
//Tracer("EcrireUnFichier: 1: bd=".$this->m_bd);
					mb_internal_encoding( 'UTF-8' );
					$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("EcrireUnFichier: 2");
					$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => DO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					$cnx->exec('set names utf8');
//Tracer("EcrireUnFichier: 3");
					$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					$doc=file_get_contents($nom_fic);
//Tracer("contenu du blob=[[[".$doc."]]]");
//Tracer(file_get_contents($nom_fic));
//Tracer("après file_get_contents");
//Tracer("EcrireUnFichier: 4: sql=".$sql);
//					$sql_bis=str_replace("@doc","file_get_contents('$nom_fic')",$sql);
					$sql_bis=str_replace("@doc",":doc",$sql);
//Tracer("EcrireUnFichier: avant execsql");
//					$params[]=['doc'=>file_get_contents($nom_fic)];
//Tracer("EcrireBlob: contenu=".file_get_contents($nom_fic));
					$params[]=['doc'=>base64_decode(file_get_contents($nom_fic))];
//					$stmt=$cnx->prepare($sql_bis);
//					$stmt->execute($params);
					foreach ($params as $param)
					{
						$stmt = $cnx->prepare($sql_bis);
						$stmt->execute($param);
					}
* /
					$cnx=new PDO($this->m_cnxstr,$this->m_uid,$this->m_pwd);
					if($cnx)
					{
						$cnx->exec('set names utf8');
//Tracer("Exec Sql PDO: 3");
						$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("Exec Sql PDO: 4: ".$sql);
//						$doc=file_get_contents($nom_fic);
//Tracer("contenu du blob=[[[".$doc."]]]");
//Tracer(file_get_contents($nom_fic));
//Tracer("après file_get_contents");
//Tracer("EcrireUnFichier: 4: sql=".$sql);
//					$sql_bis=str_replace("@doc","file_get_contents('$nom_fic')",$sql);
//						$sql_bis=str_replace("@doc","convert(varbinary,:doc)",$sql);
//Tracer('type de bd='.$this->m_type_bd_sql);
						if($this->m_type_bd_sql=="mssql")
						{
							$sql_bis=str_replace("@doc_db","convert(varbinary(MAX),:doc_db)",$sql);
//							$contenu=file_get_contents($nom_fic);
							$contenu=base64_decode(file_get_contents($nom_fic));
						}
						else
						{
							$sql_bis=str_replace("@doc_db",":doc_db",$sql);
							$contenu=base64_decode(file_get_contents($nom_fic));
						}
//Tracer("EcrireUnFichier: avant execsql");
//					$params[]=['doc'=>file_get_contents($nom_fic)];
//Tracer("EcrireBlob: contenu=".file_get_contents($nom_fic));
//						$contenu_brut=file_get_contents($nom_fic);
						/ *
$encoding = mb_detect_encoding($contenu_brut);
Tracer("EcrireBlob: encoding=(".$encoding.")");
$contenu_brut = mb_convert_encoding($contenu_brut, 'UTF-8', $encoding);
* /
/ *
$encoding = mb_detect_encoding($contenu);
Tracer("EcrireBlob: encoding apres decodage=(".$encoding.")");
$contenu = mb_convert_encoding($contenu, 'UTF-8', $encoding);
* /
						$params[]=['doc_db'=>$contenu];
//Tracer("EcrireBlob: sql=".$sql_bis);
//Tracer("EcrireBlob: contenu=".$contenu);
						foreach ($params as $param)
						{
//Tracer("EcrireBlob: boucle 1");
							$stmt = $cnx->prepare($sql_bis);
//Tracer("EcrireBlob: boucle 2");
							if($this->m_type_bd_sql=="mssql")
							{
								$stmt->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);
							}
							$stmt->execute($param);
//Tracer("EcrireBlob: boucle 3");
						}
//Tracer("EcrireUnFichier: 13");
//Tracer("Exec Sql PDO: 5");
					}
					else
					{
						$msg="Erreur de connexion au serveur SQL";
						TracerErreur($msg);
					}
					break;
			}
		}
		function EcrireFic($nom_fic,$contenu)
		{
Tracer("EcrireFic($nom_fic,$contenu)");
Tracer("EcrireFic: type_cnx=".$this->m_type_cnx);
			$contenu_binaire=base64_decode(file_get_contents($contenu));
			$fichier=fopen($nom_fic,'w');
			fputs($fichier,$contenu_binaire);
			fclose($fichier);
		}
		*/
		function NomFicBlob($nom_table,$id_doc,$type_fic)
		{
			$prefixe="D:/BLOBS/AccesBdPm/";
			return $prefixe.$nom_table."/".$nom_table."_".$id_doc.$type_fic;
		}
		function EcrireBlobDb($nom_table,$id_doc,$id_type_fic,$nom_fic)
		{
			try
			{
				$nom_col_id="id_".$nom_table;
				$sql="update ".$nom_table." set id_type_fic=".$id_type_fic." where ".$nom_col_id."=".$id_doc;
				$ret=$this->preparerExecSql($sql);
				switch($this->m_type_cnx)
				{
					case "ODBC13":
				/*
						$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $this->m_uid, $this->m_pwd);
						$results = odbc_prepare($cnx,$sql);
						@odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
						$nb_cols=odbc_num_fields($results);
						if($nb_cols==1)
						{
							odbc_fetch_into($results,$row);
							$val_col=$row[0];
							print $val_col;
						}
						odbc_free_result($results);
						odbc_close($cnx);
				*/
						break;
					case "MSSQL":
				/*
						$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
						$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
						// Call a simple query
						$params = array();
						$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
						$results = sqlsrv_query($cnx,$sql,$params,$options);
//						$nb_col=sqlsrv_num_fields($results);
//						$nb_lig=sqlsrv_num_rows($results);
//						$metadata=sqlsrv_field_metadata($results);
						$row=sqlsrv_fetch_array($results,SQLSRV_FETCH_ASSOC);
						$val_col=$row['doc'];
						print base64_encode($val_col);
						sqlsrv_free_stmt($results);
						sqlsrv_close($cnx);
				*/
						break;
					case "PDO":
				/*
//Tracer("EcrireUnFichier: 1: bd=".$this->m_bd);
						mb_internal_encoding( 'UTF-8' );
						$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("EcrireUnFichier: 2");
						$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => DO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
						$cnx->exec('set names utf8');
//Tracer("EcrireUnFichier: 3");
						$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
						$doc=file_get_contents($nom_fic);
//Tracer("contenu du blob=[[[".$doc."]]]");
//Tracer(file_get_contents($nom_fic));
//Tracer("après file_get_contents");
//Tracer("EcrireUnFichier: 4: sql=".$sql);
//						$sql_bis=str_replace("@doc","file_get_contents('$nom_fic')",$sql);
						$sql_bis=str_replace("@doc",":doc",$sql);
//Tracer("EcrireUnFichier: avant execsql");
	//					$params[]=['doc'=>file_get_contents($nom_fic)];
//Tracer("EcrireBlob: contenu=".file_get_contents($nom_fic));
						$params[]=['doc'=>base64_decode(file_get_contents($nom_fic))];
//						$stmt=$cnx->prepare($sql_bis);
//						$stmt->execute($params);
						foreach ($params as $param)
						{
							$stmt = $cnx->prepare($sql_bis);
							$stmt->execute($param);
						}
*/
						$cnx=new PDO($this->m_cnxstr,$this->m_uid,$this->m_pwd);
						if($cnx)
						{
							$cnx->exec('set names utf8');
//Tracer("Exec Sql PDO: 3");
							$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("Exec Sql PDO: 4: ".$sql);
//							$doc=file_get_contents($nom_fic);
//Tracer("contenu du blob=[[[".$doc."]]]");
//Tracer(file_get_contents($nom_fic));
//Tracer("après file_get_contents");
//Tracer("EcrireUnFichier: 4: sql=".$sql);
//						$sql_bis=str_replace("@doc","file_get_contents('$nom_fic')",$sql);
//						$sql_bis=str_replace("@doc","convert(varbinary,:doc)",$sql);
//Tracer('type de bd='.$this->m_type_bd_sql);
							$nom_col_id="id_".$nom_table;
							$sql="update ".$nom_table." set doc_db=@doc_db where ".$nom_col_id."=".$id_doc;
							if($this->m_type_bd_sql=="mssql")
							{
								$sql_bis=str_replace("@doc_db","convert(varbinary(MAX),:doc_db)",$sql);
//								$contenu=file_get_contents($nom_fic);
								$contenu=base64_decode(file_get_contents($nom_fic));
							}
							else
							{
								$sql_bis=str_replace("@doc_db",":doc_db",$sql);
								$contenu=base64_decode(file_get_contents($nom_fic));
							}
//Tracer("EcrireUnFichier: avant execsql");
//					$params[]=['doc'=>file_get_contents($nom_fic)];
//Tracer("EcrireBlob: contenu=".file_get_contents($nom_fic));
//						$contenu_brut=file_get_contents($nom_fic);
						/*
$encoding = mb_detect_encoding($contenu_brut);
Tracer("EcrireBlob: encoding=(".$encoding.")");
$contenu_brut = mb_convert_encoding($contenu_brut, 'UTF-8', $encoding);
*/
/*
$encoding = mb_detect_encoding($contenu);
Tracer("EcrireBlob: encoding apres decodage=(".$encoding.")");
$contenu = mb_convert_encoding($contenu, 'UTF-8', $encoding);
*/
							$params[]=['doc_db'=>$contenu];
//Tracer("EcrireBlob: sql=".$sql_bis);
//Tracer("EcrireBlob: contenu=".$contenu);
							foreach ($params as $param)
							{
//Tracer("EcrireBlob: boucle 1");
								$stmt = $cnx->prepare($sql_bis);
//Tracer("EcrireBlob: boucle 2");
								if($this->m_type_bd_sql=="mssql")
								{
									$stmt->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);
								}
								$stmt->execute($param);
//Tracer("EcrireBlob: boucle 3");
							}
//Tracer("EcrireUnFichier: 13");
//Tracer("Exec Sql PDO: 5");
						}
						else
						{
							$msg="Erreur de connexion au serveur SQL";
							TracerErreur($msg);
						}
						break;
				}
			}
			catch (Exception $e)
			{
				$msg="Erreur EcrireBlobDb:".$e->getMessage();
				print $msg;
				TracerErreur($msg);
			}
		}
		function EcrireBlobFs($nom_table,$id_doc,$type_fic,$id_type_fic,$nom_fic)
		{
			$nom_fic_complet=$this->NomFicBlob($nom_table,$id_doc,$type_fic);
			/*
			if(file_exists($nom_fic_complet))
			{
				$msg="Erreur EcrireBlobFs: le fichier ".$nom_fic_complet." existe déjà";
				print $msg;
				TracerErreur($msg);
			}
			else
			{
			*/
				try
				{
					$contenu=file_get_contents($nom_fic);
					$contenu_binaire=base64_decode($contenu);
//Tracer("EcrireFic($nom_fic,$contenu)");
//Tracer("EcrireBlobFs: nom_fic=".$nom_fic_complet);
					set_error_handler("myErrorHandler");
					if($fichier=fopen($nom_fic_complet,'w'))
					{
//Tracer("EcrireBlobFs: nom_fic_complet=".$nom_fic_complet);
						fputs($fichier,$contenu_binaire);
//Tracer("EcrireBlobFs: nom_fic2=".$nom_fic);
						fclose($fichier);
//Tracer("EcrireBlobFs: nom_fic3=".$nom_fic);
						restore_error_handler();
						$lg_blob=strlen($contenu);
//Tracer("EcrireBlobFs: lg_blob=".$lg_blob);
						$sql="update ".$nom_table." set doc_fs=".$lg_blob.",id_type_fic=".$id_type_fic." where id_".$nom_table."=".$id_doc;
						$this->PreparerExecSql($sql);
						print "OK";
					}
					else
					{
						restore_error_handler();
						$msg="Erreur EcrireBlobFs: le fichier ".$nom_fic_complet." existe déjà";
						TracerErreur($msg);
					}
				}
				catch (Exception $e)
				{
//Tracer("EcrireBlobFs: erreur: nom_fic=".$nom_fic);
					$msg="Erreur EcrireBlobFs:".$e->getMessage();
					print $msg;
					TracerErreur($msg);
				}
//			}
		}
		function EcrireBlob($db_ou_fs,$nom_table,$id_doc,$type_fic,$nom_fic)
		{
//Tracer("EcrireBlob($db_ou_fs,$nom_table,$id_doc,$type_fic)");
//Tracer("EcrireBlob: type_cnx=".$this->m_type_cnx);
			$sql="select id_type_fic from type_fic where code_type_fic='".$type_fic."'";
			$id_type_fic=$this->PreparerUneValeur($sql);
			if(startsWith($id_type_fic,"Erreur"))
				print $id_type_fic;
			else
			{
				if($db_ou_fs=="db")
				{
					$this->EcrireBLobDb($nom_table,$id_doc,$id_type_fic,$nom_fic);
				}
				else
				{
//Tracer("Avant appel de EcrireBlobFs");
					$this->EcrireBlobFs($nom_table,$id_doc,$type_fic,$id_type_fic,$nom_fic);
				}
			}
		}	
		function LireBlobDb($nom_table,$id_doc)
		{
			$sql="select doc_db from ".$nom_table." where id_".$nom_table."=".$id_doc;
			switch($this->m_type_cnx)
			{
				case "ODBC13":
					$cnx = odbc_connect("Driver={ODBC Driver 13 for SQL Server};Server=$this->m_srv;Database=$this->m_bd;", $this->m_uid, $this->m_pwd);
					$results = odbc_prepare($cnx,$sql);
					@odbc_execute($results) or die($this->DbErreur("sql_query",$sql));
					$nb_cols=odbc_num_fields($results);
					if($nb_cols==1)
					{
						odbc_fetch_into($results,$row);
						$val_col=$row[0];
						print $val_col;
					}
					odbc_free_result($results);
					odbc_close($cnx);
					break;
				case "MSSQL":
					$connectionInfo = array( "Database"=>$this->m_bd, "UID"=>$this->m_uid, "PWD"=>$this->m_pwd);
					$cnx = sqlsrv_connect($this->m_srv, $connectionInfo);
					// Call a simple query
					$params = array();
					$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
					$results = sqlsrv_query($cnx,$sql,$params,$options);
//					$nb_col=sqlsrv_num_fields($results);
//					$nb_lig=sqlsrv_num_rows($results);
//					$metadata=sqlsrv_field_metadata($results);
					$row=sqlsrv_fetch_array($results,SQLSRV_FETCH_ASSOC);
					$val_col=$row['doc_db'];
					print base64_encode($val_col);
					sqlsrv_free_stmt($results);
					sqlsrv_close($cnx);
					break;
				case "PDO":
//Tracer("LireBlob: 1: bd=".$this->m_bd);
//					$cnx_info=array("UID"=>$this->m_uid,"PWD"=>$this->m_pwd,"Database"=>$this->m_bd);
//Tracer("LireBlob: 2");
//					$cnx=new PDO("$this->m_type_bd_sql:host=$this->m_srv;dbname=$this->m_bd;charset=utf8",$this->m_uid,$this->m_pwd); //,array(PDO::ATTR_ERRMODE => DO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					$cnx=new PDO($this->m_cnxstr,$this->m_uid,$this->m_pwd);
					if($cnx)
					{
						$cnx->exec('set names utf8');
//Tracer("LireBlob: 3");
						$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//Tracer("LireBlob: 4: sql=".$sql);
						$results=$cnx->prepare($sql);
//Tracer("LireBlob: 4bis");
						if($results === false)
						{
							$msg="Erreur d'exécution du SQL pour sql=(".$sql.")";
							TracerErreur($msg);
							$errs=$cnx->errorinfo();
							foreach($errs as $err)
							{
								$msg=$err['message']."/".$err['code'];
								TracerErreur($msg);
							}
						}
						else
						{
							$results->execute();
//Tracer("LireBlob: 5");
//					$col_meta=$results->getColumnMeta(0);
//Tracer("LireBlob: 6");
//					$nom_col=$col_meta['name'];
//Tracer("LireBlob: 7");
//					$max_length=$col_meta['len'];
//Tracer("LireBlob: 8");
//					$type_col=$col_meta['native_type'];
//Tracer("LireBlob: 9");
//					$nb_lig=$results->rowCount();
//Tracer("LireBlob: 10");
							$row=$results->fetch(PDO::FETCH_BOTH);
//Tracer("LireBlob: 11");
							$val_col=$row[0];
//Tracer("LireBlob: 12");
							print base64_encode($val_col);
						}
//Tracer("LireBlob: 13");
					}
					else
					{
						$msg="Erreur de connexion au serveur SQL";
						TracerErreur($msg);
					}
					break;
			}
		}
		function LireFic($nom_fic)
		{
//Tracer("LireFic($nom_fic)");
			$fichier=fopen($nom_fic,'r');
			$contenu=fread($fichier,filesize($nom_fic));
			fclose($fichier);
			print base64_encode($contenu);
		}
		function LireTailleFic($nom_fic)
		{
//Tracer("LireTaillefic($nom_fic)");
			$taille=filesize($nom_fic);
//Tracer("LireTaillefic($nom_fic): taille=".$taille);
			print $taille;
		}
		function LirePartielFic($nom_fic,$octet_debut,$taille)
		{
//Tracer("LirePartielFic($nom_fic)");
			$fichier=fopen($nom_fic,'r');
			$contenu=fread($fichier,filesize($nom_fic));
			fclose($fichier);
			$contenu_partiel=substr($contenu,$octet_debut,$taille);
			$contenu_code=base64_encode($contenu_partiel);
			print ($contenu_code);
//			print (substr($contenu_code,$octet_debut,$taille));
		}
		function LireTailleBlobFs($nom_table,$id_doc,$type_fic)
		{
			$nom_fic=$this->NomFicBlob($nom_table,$id_doc,$type_fic);
			$this->LireTailleFic($nom_fic);
			/*
//Tracer("LireBlobFs($nom_fic)");
			$fichier=fopen($nom_fic,'r');
			$contenu=fread($fichier,filesize($nom_fic));
			fclose($fichier);
			print base64_encode($contenu);
			*/
		}
		function LireBlobFs($nom_table,$id_doc,$type_fic)
		{
			$nom_fic=$this->NomFicBlob($nom_table,$id_doc,$type_fic);
			$this->LireFic($nom_fic);
			/*
//Tracer("LireBlobFs($nom_fic)");
			$fichier=fopen($nom_fic,'r');
			$contenu=fread($fichier,filesize($nom_fic));
			fclose($fichier);
			print base64_encode($contenu);
			*/
		}
		function LirePartielBlobFs($nom_table,$id_doc,$type_fic,$octet_debut,$taille_bloc)
		{
			$nom_fic=$this->NomFicBlob($nom_table,$id_doc,$type_fic);
			$this->LirePartielFic($nom_fic,$octet_debut,$taille_bloc);
			/*
//Tracer("LireBlobFs($nom_fic)");
			$fichier=fopen($nom_fic,'r');
			$contenu=fread($fichier,filesize($nom_fic));
			fclose($fichier);
			print base64_encode($contenu);
			*/
		}
		function LireBlob($db_ou_fs,$nom_table,$id_doc,$type_fic)
		{
//Tracer("LireBlob");
			if($db_ou_fs=="db")
				$this->LireblobDb($nom_table,$id_doc);
			else
				$this->LireBlobFs($nom_table,$id_doc,$type_fic);
		}
		function LirePartielBlob($db_ou_fs,$nom_table,$id_doc,$type_fic,$octet_debut,$taille_bloc)
		{
//Tracer("LireBlob");
			if($db_ou_fs=="db")
				$this->LirePartielBlobDb($nom_table,$id_doc,$octet_debut,$taille_bloc);
			else
				$this->LirePartielBlobFs($nom_table,$id_doc,$type_fic,$octet_debut,$taille_bloc);
		}
		function LireTailleBlob($db_ou_fs,$nom_table,$id_doc,$type_fic)
		{
//Tracer("LireBlob");
			if($db_ou_fs=="db")
				$this->LireTailleBlobDb($nom_table,$id_doc);
			else
				$this->LireTailleBlobFs($nom_table,$id_doc,$type_fic);
		}
	}
?>