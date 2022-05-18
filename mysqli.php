<?php
#		Script: mysqli.php
#		  Date: 24.01.2017
# 		Author: Marko "Okram" Livental
# 	Decription:	PHP 7+ jaoks mysqli_ andmebaasiga ühenduse ja päringute tegemiseks.
# 		Update: Lisatud vormilt failinimede muutmine 
# 		Update: 16.05.2019 - Viimati sisestatud kirje ID ja mitme SQL lause lisamine. GoogleChart jaoks meetod
# PDO


# Konstandid andmebaasiga ühendamiseks
define("DB_SERVER", "localhost");
define("DB_USER", "allanjuhken");
define("DB_PASS", "Minuphp1"); // Siia parool :)
define("DB_NAME", "allanjuhken_esimene"); //andmebaasi nimi

class Db {
	
	# Andmebaasi ühendus
	function dbConnect() {
		$con = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);			
		if ($con->connect_errno) {
			echo "<strong>Viga andmebaasiga:</strong> ".$con->connect_error;
			return false;
		}
		mysqli_set_charset($con, "utf8");
		return $con;
	}
	
	# Andmebaasist küsimine ja tulemuse saamine. SELECT result massiivina 
	# Käsklus mille kutsud välja oma skriptis
	function dbGetArray($sql) {
		$res = $this->dbQuery($sql);		
		if ($res !== false) {
			$data = array();			
			if($res) {
				while($row = mysqli_fetch_assoc($res)) {
					$data[] = $row;
				}
				if (is_array($data) and count($data) > 0) {
					return $data;
				} else {
					return false;
				}
			} else {
				echo '<div class="message punane"><strong>Viga andmebaasist k&uuml;simisega</strong></div>';
				return false;
			}
		} else {
			return false;
		}
	}
	
	# UPDATE, INSERT ja DELETE käskluste tegemiseks. 
	# Käsklus mille kutsud välja oma skriptis
	function dbQuery($sql) {
		$con = $this->dbConnect();
		if($con) {
			$res = mysqli_query($con, $sql);		
			if ($res === false) {			
				echo '<div class="message punane"><strong>[DEF] Vigane SQL p&auml;ring:</strong> '.$sql.'</div>';
				return false;
			}
			mysqli_close($con);
			return $res;
		}
		return false;
	}

	# Lisamine andmebaasi, tagastatakse false või viimase kirje id
	function dbQueryLastId($sql) {
		$con = $this->dbConnect();
		if($con) {
			$res = mysqli_query($con, $sql);		
			if ($res === false) {			
				echo '<div class="message punane"><strong>[LST ID]Vigane SQL p&auml;ring:</strong> '.$sql.'</div>';
				return false;
			}
			$result = mysqli_insert_id($con);
			mysqli_close($con);
			return $result;
		}
		return false;
	}

	# Lisab mitu kirjet korraga andmebaasi
	function dbQueryMulti($sql) {
		$con = $this->dbConnect();
		if($con) {
			$res = mysqli_multi_query($con, $sql);		
			if ($res === false) {			
				echo '<div class="message punane"><strong>[multi] Vigane SQL p&auml;ring:</strong> '.$sql.'</div>';
				return false;
			}
			mysqli_close($con);
			return $res;
		}
		return false;
	}
	
	# Andmebaasi lisamiseks/muutmiseks väljade varjestamiseks. Teksti muutujad saavad "" ümber. 
	# Numbrid mitte. 
	# Käsklus mille kutsud välja oma skriptis
	function dbFix($var) {
		if (!is_numeric($var)) {
			# $var = '"'.addslashes($var).'"';
			$var = '"'.addslashes(htmlspecialchars($var, ENT_QUOTES, 'UTF-8', false)).'"';
		}
		return $var;
	}
	
	# Vormilt saadud info kontrollimiseks
	# Käsklus mille kutsud välja oma skriptis
	# While doing a data insert, it is best to use the 
	# function get_magic_quotes_gpc() to check if the 
	# current configuration for magic quote is set or not. 
	# If this function returns false, then use the function 
	# addslashes() to add slashes before the quotes.
	function getVar($name)  {
		# Kas on olemas GET ja kas see on masiiv
		$var = false;
		if (isset($_GET) and is_array($_GET)) {
			if (isset($_GET[$name])) {
				$var = $_GET[$name];
			}
		} else {
			global $HTTP_GET_VARS;
			if (isset($HTTP_GET_VARS[$name])) {
				$var = $HTTP_GET_VARS[$name];
			}
		}
		# Kas on olemas POST ja kas see on masiiv
		if (isset($_POST) and is_array($_POST)) {
			if (isset($_POST[$name])) {
				$var =  $_POST[$name];
			}
		} else {
			global $HTTP_POST_VARS;
			if (isset($HTTP_POST_VARS[$name])) {
				$var =  $HTTP_POST_VARS[$name];
			}
		}
		/*if (get_magic_quotes_gpc() == 1) {	
			$var = stripslashes($var);
		}*/
		return $var;
	} # getVar lõpp
	
	# Näitab PHP massiivi (Array) inimlikul kujul 
	# Käsklus mille kutsud välja oma skriptis
	function show($array) {
		echo '<pre>'; // Eelvormindatud tekst
		print_r($array);
		echo '</pre>';
	}	
	
	# Teeb andmebaasi tulemuse Google chart jaoks sobivaks
	# https://blog.programster.org/php-converting-data-for-google-charts
	# json_encode vajab lisa parameetrit JSON_NUMERIC_CHECK
	function convertDataToGoogleChart($data) {
		$newData = array();
		$firstLine = true;

		foreach ($data as $dataRow) {
			if ($firstLine) {
				$newData[] = array_keys($dataRow);
				$firstLine = false;
			}
			//$this->naita(array_values($dataRow));
			$newData[] = array_values(array_map("html_entity_decode", $dataRow));
			//$newData[] = array_values($dataRow);
		}
		return $newData;
	}
	
	# Muudab andmebaasi kuupäeva kujult YYYY-MM-DD
	# kujule DD.MM.YYYY
	function dbDateToEstDate($date) {
		return date('d.m.Y', strtotime($date));
	}
	
	# Muudab andmebaasi kuupäeva kujult YYYY-MM-DD HH:MM:SS
	# kujule DD.MM.YYYY HH:MM:SS
	function dbDateToEstDateClock($date) {
		return date('d.m.Y H:i:s', strtotime($date));
	}
		
	# Andmebaasi kuupäevast eemaldatakse kella osa. Kuju jääb ikka andmebaasiks
	# Muudab andmebaasi kuupäeva kujult YYYY-MM-DD HH:MM:SS => YYYY-MM-DD
	function dbDateRemoveClock($date) {
		return date('Y-m-d', strtotime($date));
	}
	
	# Kuupäev kujul MM-DD (12-31) muudetakse kujule DD. KUUNIMI (31. Detsember)
	function dbMDtoDMM($date) {
		$kuunimed = array('','Jaanuar','Veebruar','Märts','Aprill','Mai','Juuni','Juuli','August','September','Oktoober','November','Detsember');
		$osad = explode('-', $date);
		$result = $osad[1].'. '.$kuunimed[(int)$osad[0]];
		return $result;
	}
	
	# Kuupäev kujul YYYY-MM muudetakse kujule KUUNIMI aasta (Jaanuar 2019)
	function dbYMtoMMY($date) {
		$kuunimed = array('','Jaanuar','Veebruar','Märts','Aprill','Mai','Juuni','Juuli','August','September','Oktoober','November','Detsember');
		$kuuNimi = $kuunimed[(int)explode('-', $date)[1]];
		return $kuuNimi.' '.explode('-', $date)[0];
	}
	
	# Vormilt saadud failinime tõstmine ette öeldud kausta
	# lisaks korrastatakse failinime ja kui on juba selline fail olemas, lisatakse 
	# sinna juurde järjekorra number.
	function saveFile($name, $dir) {
		if (isset($_FILES[$name])) {
			$tmp = $_FILES[$name]['tmp_name'];  // Ajutine nimi failile
			$file = $_FILES[$name]['name'];     // Nimi mis jaab failile
			# echo '<b>'.$tmp.'</b> '.$file;  // See oli testiks mis ja kus on
			if (is_uploaded_file($tmp)) {
				$file = $this->fixFilename($file);
				# leitud tüübid ilma punktideta
				$sp = explode('.',$file);
				$file = $sp[0];
				# Loendab kokku osad kuni eelviimase osani
				for ($i=1;$i<count($sp)-1;$i++) {
					$file .= $sp[$i];
					if ($i != count($sp)-2) {
						$file .= '.';
					}
				}
				if (count($sp)>1) {
					# Juhul kui on laiend olemas lisa punkt
					$ext = '.'.$sp[count($sp)-1];
				} else {
					$ext = '';
				}
				$count = '';
				while (file_exists($dir.$file.$count.$ext)){
					# Tüübi kontroll ===
					if ($count === ''){
						$count = 0;
					} else {
						$count++;
					}
				}
				move_uploaded_file($tmp, $dir.$file.$count.$ext);
				return $dir.$file.$count.$ext;
			}
		}
		return false;
	}
	
	# See teeb failinimed normaasleks. Eelmine funktsioon kasutab seda!
	function fixFilename($file){
		$file = trim($file);
		# From ja TO rida peavad olema sama pikad, ehk
		# mis millega asendatakse
		$from = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ", "ä", "ö", "ü", "õ", "Ä", "Ö", "Ü", "Õ", " ", "š", "ž", "Š", "Ž");
		$to  = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "a", "o", "u", "6", "A", "O", "U", "6", "_", "s", "z", "S", "Z");
		$file = str_replace($from, $to, $file);

		$ok = 'abcdefghijklmnopqrstuvwxyz1234567890_.';

		for ($i = 0; $i < strlen($file); $i++) {
			# NB! loogelised sulud ainult stringide puhul
			# ehk sama rida pikemalt: substr($file, $i, 1)
			$ch = strtolower($file{$i});
			if (strpos($ok, $ch) === false){
				# Kui ei ole omistatud stringis ($ok)
				# siis asendatakse see @ märgiga
				$file{$i} = '@';
			}
		}
		$file = str_replace('@', '', $file);
		if (strlen($file)>0) {
			if ($file{0}=='.') {
				return 'empty'.$file;
			} 
			else {
				return $file;
			}
		} 
		else {
			return 'empty';
		}
	}	
}
# Ilma selle reata on eelnevad klassis olevad funktsioonid kasutud
# Muutuja ($kl) mida kasutad enda skriptides antud klassis olevate funktsioonide välja kutsumiseks
# Näiteks: $kl->naita($massiiv); # Näitab muutja massiiv ($massiiv) sisu inimlikul kujul.
$kl = new Db;
?>