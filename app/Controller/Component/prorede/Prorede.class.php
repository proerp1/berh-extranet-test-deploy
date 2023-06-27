<?php
 class GerarProrede {
	/*
		* @descr: Gera o arquivo de Prorede
	*/

	function limit($palavra, $limite){
		if (strlen($palavra) >= $limite){
			$var = substr($palavra, 0, $limite);
		} else{
			$max = (int)($limite-strlen($palavra));
			$var = $palavra.$this->complemento_registro($max, "brancos");
		}

		return $var;
	}

	function limit_log($palavra, $limite){
		if (strlen($palavra) >= $limite){
			$var = substr($palavra, 0, $limite);
		} else{
			$max = (int)($limite-strlen($palavra));
			$var = $this->complemento_registro($max, "zeros").$palavra;
		}

		return $var;
	}

	function sequencial($i){
		if ($i < 10){
			return $this->zeros(0, 5).$i;
		} else if($i >= 10 && $i < 100){
			return $this->zeros(0, 4).$i;
		}	else if($i >= 100 && $i < 1000){
			return $this->zeros(0, 3).$i;
		} else if($i >= 1000 && $i < 10000){
			return $this->zeros(0, 2).$i;
		} else if($i >= 10000 && $i < 100000){
			return $this->zeros(0, 1).$i;
		}
	}
	
	function zeros($min, $max){
		$zeros = "";
		$x = ($max - strlen($min));
		for ($i = 0; $i < $x; $i++){
			$zeros .= "0";
		}

		return $zeros.$min;
	}

	function complemento_registro($int, $tipo){
		if ($tipo == "zeros"){
			$space = "";
			for ($i = 1; $i <= $int; $i++){
				$space .= "0";
			}
		} else if($tipo == "brancos"){
			$space = "";
			for($i = 1; $i <= $int; $i++){
				$space .= " ";
			}
		}

		return $space;
	}

	function remove_accents($str){
	  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
	  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
	  
	  return str_replace($a, $b, $str);
	}

	function mascara_telefone($fone, $tipo){
		$telefone = str_replace(array("(", ")", "-", " "), "", $fone);
		if($tipo == "ddd"){
			$telefone = substr($telefone, 0, 2);
		} else{
			$telefone = substr($telefone, 2);
		}
		
		return $telefone;
	}

	public function gerar($dados){
		$fusohorario = 3; // Como o servidor de hospedagem é a dreamhost pego o fuso para o horario do brasil
		$timestamp = mktime(date("H") - $fusohorario, date("i"), date("s"), date("m"), date("d"), date("Y"));

		$DATAHORA["PT"] = gmdate("d/m/Y H:i:s", $timestamp);
		$DATAHORA["EN"] = gmdate("Y-m-d H:i:s", $timestamp);
		$DATA["PT"] = gmdate("d/m/Y", $timestamp);
		$DATA["EN"] = gmdate("Y-m-d", $timestamp);
		$DATA["DIA"] = gmdate("d",$timestamp);
		$DATA["MES"] = gmdate("m",$timestamp);
		$DATA["ANO"] = gmdate("y",$timestamp);
		$DATA["ANOCOMP"] = gmdate("Y",$timestamp);
		$HORA = gmdate("H:i:s", $timestamp);
		$HORACOMP = gmdate("His", $timestamp);

		define("REMESSA", APP."webroot/files/prorede_txt/", true);
		// $txt_name = APP."webroot/files/prorede_txt/prorede".$this->zerosEsq($sequencia, 6).".txt";
		$nome = "prorede".$this->limit(str_pad($dados["empresa"]["numero_remessa"], 6, "0", STR_PAD_LEFT), 6).'.txt';

		$filename = REMESSA.$nome;
		
		$conteudo = "";

		$empresa = $dados["empresa"];
		$clientes = $dados["clientes"];

		## HEADER
		$conteudo .= "0"; // Tipo de registro = 0 001 001 (001)
		$conteudo .= $this->limit(str_pad($empresa["cnpj_empresa"], 14, "0", STR_PAD_LEFT), 14); // CNPJ do Cliente Serasa (Banco) - Ajuste à direita e preenchido com zeros a esquerda 002 014 (014)
		$conteudo .= $DATA["ANOCOMP"].$DATA["MES"].$DATA["DIA"]; // Data da remessa (AAAAMMDD) 016 008 (008)
		$conteudo .= $this->limit(str_pad("SERASA-PROREDE", 15, " ", STR_PAD_RIGHT), 15); // Identificação do arquivo (SERASA-PROREDE) 024 015 (015)
		$conteudo .= $this->limit(str_pad($empresa["numero_remessa"], 4, "0", STR_PAD_LEFT), 4); // Número da remessa - número sequencial, iniciando de 0001 (Ajuste à direita e preenchido com zeros a esquerda.) 039 004 (004)
		$conteudo .= $this->complemento_registro(162, "brancos"); // Filler (reservado) 043 162 (162)
		$conteudo .= $this->complemento_registro(30, "brancos"); // Códigos de erros (10 ocorrências com 3 posições cada) - (usado somente para retorno, na entrada enviar espaço) 205 030 (030)
		$conteudo .= $this->complemento_registro(15, "brancos"); // Filler (reservado) 235 015 (015)
		$conteudo .= $this->limit(str_pad("1", 7, "0", STR_PAD_LEFT), 7); // Sequência do Registro (0000001) 250 007 (007)

		$conteudo .= chr(13).chr(10); // Quebra de linha

		$i = 2;
		foreach($clientes as $cliente){
			## INFORMAÇÕES DO CLIENTE
			$conteudo .= "1"; // Tipo de registro = 1 001 001 (001)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["nome_fantasia"])), 50); // Nome Fantasia 002 050 (050)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["endereco"])), 35); // Endereço 052 035 (035)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["bairro"])), 15); // Bairro 087 015 (015)
			$conteudo .= $this->zeros(str_replace("-", "", $cliente["cep"]), 8); // CEP (inicializar com zeros caso não tenha conteúdo) 102 008 (008)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["cidade"])), 15); // Cidade 110 015 (015)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["uf"])), 2); // UF 125 002 (002)
			$conteudo .= $this->limit(str_pad($this->mascara_telefone($cliente["telefone"], "ddd"), 5, "0", STR_PAD_LEFT), 5); // DDD (inicializar com zeros caso não tenha conteúdo) 127 005 (005)
			$conteudo .= $this->limit(str_pad($this->mascara_telefone($cliente["telefone"], ""), 9, "0", STR_PAD_LEFT), 9); // Fone (inicializar com zeros caso não tenha conteúdo) 132 009 (009)
			$conteudo .= $this->limit(str_pad($cliente["ramal"], 5, "0", STR_PAD_LEFT), 5); // Ramal (inicializar com zeros caso não tenha conteúdo) 141 005 (005)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["contato"])), 30); // Contato 146 030 (030)
			$conteudo .= $this->limit(str_pad(str_replace(array(".", "/", "-"), "", trim($cliente["documento"])), 15, "0", STR_PAD_LEFT), 15); // CNPJ ou CPF do Cliente que pertence ao cliente Serasa (Prorede) 176 015 (015)
			$conteudo .= $this->limit(str_pad($cliente["ramo_atividade"], 5, "0", STR_PAD_LEFT), 5); // Ramo de Atividade 191 005 (005)
			$conteudo .= $cliente["status"]; // Status 196 001 (001)
			$conteudo .= $DATA["ANOCOMP"].$DATA["MES"].$DATA["DIA"]; // Data (Inclusão  ou Cancelamento ou Reativação) (AAAAMMDD) 197 008 (008)
			$conteudo .= $this->complemento_registro(30, "brancos"); // Códigos de erros (10 ocorrências com 3 posições cada) - (usado somente para retorno, na entrada enviar espaço) 205 030 (030)
			$conteudo .= $this->limit(str_pad($cliente["agencia_bancaria"], 4, "0", STR_PAD_LEFT), 4); // Código da agência bancária do Cliente do Cliente Prorede 235 004 (004)
			$conteudo .= $this->limit(str_pad($cliente["conta_corrente"], 10, "0", STR_PAD_LEFT), 10); // Número da conta corrente do Cliente do Cliente Prorede 239 010 (010)
			$conteudo .= $cliente["tipo_documento"]; // Tipo do Documento ('1'=CNPJ  ou '2'=CPF) 249 001 (001)			
			$conteudo .= $this->limit(str_pad($i++, 7, "0", STR_PAD_LEFT), 7); // Sequência do Registro  (0000001) 250 007 (007)

			$conteudo .= chr(13).chr(10); // Quebra de linha

			$conteudo .= "2"; // Tipo de registro = 2 001 001 (001)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["razao_social"])), 100); // Razão Social 002 100 (100)
			$conteudo .= $this->limit(utf8_decode($this->remove_accents($cliente["email"])), 100); // Email 102 100 (100)
			$conteudo .= $this->complemento_registro(30, "brancos"); // Códigos de erros (10 ocorrências com 3 posições cada) 205 030 (030)
			$conteudo .= $this->complemento_registro(18, "brancos");
			$conteudo .= $this->limit(str_pad($i++, 7, "0", STR_PAD_LEFT), 7); // Sequência do Registro  (0000001) 250 007 (007)

			$conteudo .= chr(13).chr(10); // Quebra de linha
		}

		## FOOTER
		$conteudo .= "9"; // Tipo de registro = 9 001 001 (001)
		$conteudo .= $this->complemento_registro(203, "brancos"); // Filler (reservado) 002 203 (203)
		$conteudo .= $this->complemento_registro(30, "brancos"); // Códigos de erros (10 ocorrências com 3 posições cada) - (usado somente para retorno, na entrada enviar espaço) 205 030 (030)
		$conteudo .= $this->complemento_registro(15, "brancos"); // Filler (reservado) 235 015 (015)
		$conteudo .= $this->limit(str_pad($i, 7, "0", STR_PAD_LEFT), 7); // Sequência do Registro  (0000001) 250 007 (007)

		file_put_contents($filename, $conteudo);

		return $nome;
	}

 }
?>