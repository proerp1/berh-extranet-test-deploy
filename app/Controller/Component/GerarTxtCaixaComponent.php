<?php 
//cnab 400
/*
	HEADER ARQUIVO
		SEQ	 	| INICIO |	FINAL |	TAM |	MÁSCARA	 | DESCRICAO 
		**H1 	| 001		 |	001		| 001	| 9(001)	 | Preencher ‘0’
		**H2 	| 002		 |	002		| 001	| 9(001)	 | Preencher '1'
		**H3 	| 003		 |	009		| 007	| X(007)	 | Preencher 'REM.TST', se remessa teste; Preencher ‘REMESSA’, se em produção
		**H4 	| 010		 |	011		| 002	| 9(002)	 | Preencher ‘01’
		**H5 	| 012		 |	026		| 015	| X(015)	 | Preencher 'COBRANCA'
		**H6 	| 027		 |	030		| 004	| 9(004)	 | Preencher com o Código agência de vinculação do Beneficiário, com 4 dígitos
		**H7 	| 031		 |	036		| 006	| 9(006)	 | Preencher com o Código que identifica a Empresa na CAIXA, fornecido pela agência de vinculação
		**H8 	| 037		 |	046		| 010	| X(010)	 | Preencher espaços
		**H9 	| 047		 |	076		| 030	| X(030)	 | Preencher com o Nome da Empresa
		**H10 | 077		 |	079		| 003	| 9(003)	 | Preencher ‘104’
		**H11 | 080		 |	094		| 015	| X(015)	 | Preencher 'C ECON FEDERAL'
		**H12 | 095		 |	100		| 006	| 9(006)	 | Preencher com a data da criação do arquivo, no formato DDMMAA (Dia, Mês e Ano)
		**H13 | 101		 |	389		| 289	| X(289)	 | Preencher espaços
		**H14 | 390		 |	394		| 005	| 9(005)	 | Número sequencial adotado e controlado pelo responsável pela geração do arquivo para ordenar os
																							 	arquivos encaminhados; iniciar a partir de ‘00001’' e evoluir de 1 em 1 para cada Header de Arquivo
		**H15 | 395		 |	400		| 006	| 9(006)	 | Preencher ‘000001’

	HEADER LOTE
		SEQ	 	| INICIO |	FINAL |	TAM |	MÁSCARA	 | DESCRICAO 
		**L1 	| 001		 |	001		| 001	| 9(001)	 | Preencher ‘1’
		**L2 	| 002		 |	003		| 002	| 9(002)	 | Preencher com o tipo de Inscrição da Empresa Beneficiária: ‘01’ = CPF ou ‘02’ = CNPJ
		**L3 	| 004		 |	017		| 014	| 9(014)	 | Preencher com Número de inscrição da Empresa (CNPJ) ou Pessoa Física (CPF) a que se está
																							 	fazendo referência, de acordo com o código do campo acima
		**L4 	| 018		 |	021		| 004	| 9(004)	 | Preencher com o Código da Agência de vinculação do Beneficiário, com 4 dígitos
		**L5 	| 022		 |	027		| 006	| 9(006)	 | Preencher com o Código que identifica a Empresa na CAIXA, fornecido pela agência de vinculação
		**L6 	| 028		 |	028		| 001	| 9(001)	 | Preencher com a forma de emissão do boleto desejada: ‘1’ = Banco Emite ou ‘2’ = Cliente Emite
		**L7 	| 029		 |	029		| 001	| 9(001)	 | Código adotado pela FEBRABAN para identificar o responsável pela distribuição do boleto.
																								Id Entrega do Boleto
																								‘0’ = Postagem pelo Beneficiário
																								‘1’ = Pagador via Correio
																								‘2’ = Beneficiário via Agência CAIXA
																								‘3’ = Pagador via e-mail
		**L8 	| 030		 |	031		| 002	| 9(002)	 | Informar ‘00’
		**L9 	| 032		 |	056		| 025	| X(025)	 | Preencher com Seu Número de controle do título (exemplos: nº da duplicata no caso de cobrança de
																								duplicatas, nº da apólice, em caso de cobrança de seguros)
		**L10	| 057		 |	058		| 002	| 9(002)	 | Quando a emissão for feita pelo Beneficiário, obedecer ao seguinte formato:
																								CC, onde:
																								CC = 14 (para Nosso Número da Cobrança Registrada)
																								CC = 24 (para Nosso Número da Cobrança Sem Registro)
		**L11	| 059		 |	073		| 015	| 9(015)	 | Quando a emissão for feita pelo Beneficiário, obedecer ao seguinte formato:
																								NNNNNNNNNNNNNNN = Número livre do cliente / Beneficiário
		**L12	| 074		 |	076		| 003	| X(003)	 | Preencher com espaços
		**L13	| 077		 |	106		| 030	| X(030)	 | Preencher com Mensagem a ser impressa no boleto
		**L14	| 107		 |	108		| 002	| 9(002)	 | Preencher de acordo com a modalidade de cobrança contratada: ‘01’ = Cobrança Registrada ou
																								‘02’ = Cobrança Sem Registro
		**L15	| 109		 |	110		| 002	| 9(002)	 | Preencher com a ação desejada para o título, conforme tabela da Nota Explicativa NE017
		**L16	| 111		 |	120		| 010	| X(010)	 | Preencher com Seu Número de controle do título (exemplos: nº da duplicata no caso de cobrança
																								de duplicatas, nº da apólice, em caso de cobrança de seguros)
		**L17	| 121		 |	126		| 006	| 9(006)	 | Preencher com a Data de Vencimento do Título, no formato DDMMAA (Dia, Mês e Ano);
																								para os vencimentos “À Vista” ou “Contra-apresentação”, ver Nota Explicativa NE019
		**L18	| 127		 |	139		| 013	| 9(013)	 | Preencher com o Valor Nominal do Título, utillizando 2 decimais
		**L19	| 140		 |	142		| 003	| 9(003)	 | Preencher ‘104’
		**L20	| 143		 |	147		| 005	| 9(005)	 | Preencher com zeros
		**L21	| 148		 |	149		| 002	| 9(002)	 | Preencher conforme tabela da Nota Explicativa NE022
		**L22	| 150		 |	150		| 001	| 9(001)	 | Identificação de Título - Aceito -> 'A' / Não Aceito -> 'N'
		**L23	| 151		 |	156		| 006	| 9(006)	 | Data da Emissão do Título
		**L24	| 157		 |	158		| 002	| 9(002)	 | Código adotado pela FEBRABAN para identificar o tipo de prazo a ser considerado para o protesto.
																								‘01’ = Protestar Dias Corridos
																								‘02’ = Devolver (Não Protestar)
		**L25	| 159		 |	160		| 002	| 9(002)	 | '0'
		**L26	| 161		 |	173		| 013	| 9(013)	 | Juros de Mora por dia/Valor; 2 decimais
		**L27	| 174		 |	179		| 006	| 9(006)	 | Data limite para concessão do desconto
		**L28	| 180		 |	192		| 013	| 9(013)	 | Valor do Desconto a ser concedido; 2 decimais
		**L29	| 193		 |	205		| 013	| 9(013)	 | Valor do IOF a ser recolhido; 2 decimais
		**L30	| 206		 |	218		| 013	| 9(013)	 | Valor do abatimento a ser concedido; 2 decimais
		**L31	| 219		 |	220		| 002	| 9(002)	 | Identificador do Tipo de Inscrição do Pagador
		**L32	| 221		 |	234		| 014	| 9(014)	 | Número de Inscrição do Pagador
		**L33	| 235		 |	274		| 040	| X(040)	 | Nome do Pagador
		**L34	| 275		 |	314		| 040	| X(040)	 | Endereço do Pagador
		**L35	| 315		 |	326		| 012	| X(012)	 | Bairro do Pagador
		**L36	| 327		 |	334		| 008	| 9(008)	 | CEP do Pagador
		**L37	| 335		 |	349		| 015	| X(015)	 | Cidade do Pagador
		**L38	| 350		 |	351		| 002	| X(002)	 | Estado do Pagador
		**L39	| 352		 |	357		| 006	| 9(006)	 | Definição da data para pagamento de multa
		**L40	| 358		 |	367		| 010	| 9(010)	 | Valor nominal da multa; 2 decimais
		**L41	| 368		 |	389		| 022	| X(022)	 | Nome do Sacador/Avalista
		**L42	| 390		 |	391		| 002	| 9(002)	 | Terceira Instrução de Cobrança
		**L43	| 392		 |	393		| 002	| 9(002)	 | Número de dias para início do protesto/devolução
		**L44	| 394		 |	394		| 001	| 9(001)	 | Código da Moeda = '1'
		**L45	| 395		 |	400		| 006	| 9(006)	 | Número Sequencial do Registro no Arquivo

	MENSAGENS
		SEQ	 	| INICIO |	FINAL |	TAM |	MÁSCARA	 | DESCRICAO 
		**M1 	| 001		 |	001		| 001	| 9(001)	 | Preencher ‘2’
		**M2 	| 002		 |	003		| 002	| 9(002)	 | Preencher com o tipo de Inscrição da Empresa Beneficiária: ‘01’ = CPF ou ‘02’ = CNPJ
		**M3 	| 004		 |	017		| 014	| 9(014)	 | Preencher com Número de inscrição da Empresa (CNPJ) ou Pessoa Física (CPF) a que se está
																							 	fazendo referência, de acordo com o código do campo acima
		**M4 	| 018		 |	021		| 004	| 9(004)	 | Código de 04 posições que identifica a agência de vinculação do Beneficiário
		**M5 	| 022		 |	027		| 006	| 9(006)	 | Preencher com o Código que identifica a Empresa na CAIXA, fornecido pela agência de vinculação
		**M6 	| 028		 |	031		| 004	| X(004)	 | Espaços
		**M7 	| 032		 |	056		| 025	| X(025)	 | Espaços
		**M8	| 057		 |	073		| 017	| 9(017)	 | Mesma regra do **L10 E **L11
		**M9 	| 074		 |	106		| 033	| X(033)	 | Espaços
		**M10	| 107		 |	108		| 002	| 9(002)	 | Informar ‘01’ = Cobrança Registrada ou ‘02’ = Cobrança Sem Registro
		**M11	| 109		 |	110		| 002	| 9(002)	 | Preencher com a ação desejada para o título, conforme tabela da Nota Explicativa NE017
		**M12	| 111		 |	139		| 029	| X(029)	 | Espaços
		**M13	| 140		 |	142		| 003	| 9(003)	 | ‘104’
		**M14	| 143		 |	182		| 040	| X(040)	 | Informar conforme NE030
		**M15	| 183		 |	222		| 040	| X(040)	 | Informar conforme NE030
		**M16	| 223		 |	262		| 040	| X(040)	 | Informar conforme NE030
		**M17	| 263		 |	302		| 040	| X(040)	 | Informar conforme NE030
		**M18	| 303		 |	342		| 040	| X(040)	 | Informar conforme NE030
		**M19	| 343		 |	382		| 040	| X(040)	 | Informar conforme NE030
		**M20	| 383		 |	394		| 012	| X(012)	 | Espaços
		**M21	| 395		 |	400		| 006	| 9(006)	 | Número sequencial para ordenar os arquivos encaminhados

	TRAILER
		SEQ	 	| INICIO |	FINAL |	TAM |	MÁSCARA	 | DESCRICAO 
		**T1 	| 001		 |	001		| 001	| 9(001)	 | Preencher ‘9’
		**T2 	| 002		 |	394		| 393	| X(393)	 | Espaços
		**T3	| 395		 |	400		| 006	| 9(006)	 | Número sequencial para ordenar os arquivos encaminhados

*/

class GerarTxtCaixaComponent extends Component {
	public $components = array('CnabFunctions');
	
	public function gerar($contas, $sequencia, $lote_id, $nome_arquivo){
		$CnabCaixaLote = ClassRegistry::init('CnabCaixaLote');
		$Income = ClassRegistry::init('Income');

		// REGISTRO HEADER
			$outputstring = "0"; //**H1
			$outputstring .= "1"; //**H2
			$outputstring .= "REM.TST"; //**H3
			$outputstring .= "01"; //**H4
			$outputstring .= $this->CnabFunctions->completaComEspaco("COBRANCA", 15); //**H5
			$outputstring .= $this->CnabFunctions->zerosEsq('1234', 4); //**H6 ??
			$outputstring .= "222222"; //**H7 ??
			$outputstring .= $this->CnabFunctions->completaComEspaco("", 10); //**H8
			$outputstring .= $this->CnabFunctions->completaComEspaco("CREDCHECK", 30); //**H9
			$outputstring .= "104"; //**H10
			$outputstring .= $this->CnabFunctions->completaComEspaco("C ECON FEDERAL", 15); //**H11
			$outputstring .= date('dmy'); //**H12
			$outputstring .= $this->CnabFunctions->completaComEspaco("", 289); //**H13
			$outputstring .= $this->CnabFunctions->zerosEsq($sequencia, 5); //**H14
			$outputstring .= "000001"; //**H15

			$outputstring .= "\r\n";

		$i = 2;

		// REGISTRO DETALHE
		foreach ($contas as $dados) {
			$outputstring .= "1"; //**L1
			$outputstring .= "02"; //**L2
			$outputstring .= $this->CnabFunctions->zerosEsq('11930512000173', 14); //**L3
			$outputstring .= $this->CnabFunctions->zerosEsq('', 4); //**L4 ??
			$outputstring .= $this->CnabFunctions->zerosEsq('', 6); //**L5 ??
			$outputstring .= "2"; //**L6
			$outputstring .= "0"; //**L7 ??
			$outputstring .= "00"; //**L8
			$outputstring .= $this->CnabFunctions->completaComEspaco($dados['Income']['id'], 25); //**L9
			$outputstring .= "14".$this->CnabFunctions->zerosEsq($dados['Income']['doc_num'], 15); //**L10 e **L11 
			$outputstring .= $this->CnabFunctions->completaComEspaco("", 3); //**L12
			$outputstring .= $this->CnabFunctions->completaComEspaco("", 30); //**L13
			$outputstring .= "02"; //**L14
			$outputstring .= "01"; //**L15
			$outputstring .= $this->CnabFunctions->completaComEspaco($dados['Income']['id'], 10); //**L16
			$outputstring .= date('dmy', strtotime($dados['Income']['vencimento_nao_formatado'])); //**L17
			$outputstring .= $this->CnabFunctions->zerosEsq($this->CnabFunctions->tira_caracteres($dados['Income']['valor_total_nao_formatado']), 13); //**L18
			$outputstring .= "104"; //**L19
			$outputstring .= $this->CnabFunctions->zerosEsq("", 5); //**L20
			$outputstring .= "02"; //**L21 ??
			$outputstring .= "A"; //**L22
			$outputstring .= date('dmy', strtotime($dados['Income']['created'])); //**L23
			$outputstring .= "01"; //**L24 ??
			$outputstring .= "00"; //**L25 ??
			$outputstring .= $this->CnabFunctions->zerosEsq("", 13); //**L26
			$outputstring .= $this->CnabFunctions->zerosEsq("", 6); //**L27
			$outputstring .= $this->CnabFunctions->zerosEsq("", 13); //**L28
			$outputstring .= $this->CnabFunctions->zerosEsq("", 13); //**L29
			$outputstring .= $this->CnabFunctions->zerosEsq("", 13); //**L30
			$outputstring .= "02"; //**L31
			$outputstring .= $this->CnabFunctions->zerosEsq($this->CnabFunctions->tira_caracteres($dados['Customer']['documento']), 14); //**L32
			$outputstring .= $this->CnabFunctions->completaComEspaco($this->CnabFunctions->tira_caracteres($dados['Customer']['nome_secundario']), 40); //**L33
			$outputstring .= $this->CnabFunctions->completaComEspaco($this->CnabFunctions->tira_caracteres($dados['Customer']['endereco']), 40); //**L34
			$outputstring .= $this->CnabFunctions->completaComEspaco($this->CnabFunctions->tira_caracteres($dados['Customer']['bairro']), 12); //**L35
			$outputstring .= $this->CnabFunctions->zerosEsq($this->CnabFunctions->tira_caracteres($dados['Customer']['cep']), 8); //**L36
			$outputstring .= $this->CnabFunctions->completaComEspaco($this->CnabFunctions->tira_caracteres($dados['Customer']['cidade']), 15); //**L37
			$outputstring .= $this->CnabFunctions->completaComEspaco($this->CnabFunctions->tira_caracteres($dados['Customer']['estado']), 2); //**L38
			$outputstring .= $this->CnabFunctions->zerosEsq("", 6); //**L39
			$outputstring .= $this->CnabFunctions->zerosEsq("", 10); //**L40
			$outputstring .= $this->CnabFunctions->completaComEspaco("", 22); //**L41 ??
			$outputstring .= $this->CnabFunctions->zerosEsq("", 2); //**L42 ??
			$outputstring .= $this->CnabFunctions->zerosEsq("", 2); //**L43
			$outputstring .= "1"; //**L44
			$outputstring .= $this->CnabFunctions->zerosEsq($i,6); //**L45
			
			$outputstring .= "\r\n";

			$Income->updateAll(
				['Income.cnab_gerado' => 1, 'Income.cnab_lote_id' => $lote_id, 'Income.cnab_num_sequencial' => "'".$this->CnabFunctions->zerosEsq($i, 6)."'", 'Income.user_updated_id' => CakeSession::read('Auth.User.id'), 'Income.updated' => 'current_timestamp'], //set
				['Income.id' => $dados['Income']['id']] //where
			);

			$i++;
		}

		// REGISTRO MENSAGENS
			$outputstring .= "2"; //**M1
			$outputstring .= "02"; //**M2
			$outputstring .= $this->CnabFunctions->zerosEsq('05795928000123', 14); //**M3
			$outputstring .= $this->CnabFunctions->zerosEsq('', 4); //**M4 ??
			$outputstring .= $this->CnabFunctions->zerosEsq('', 6); //**M5 ??
			$outputstring .= $this->CnabFunctions->geraEspacos(4); //**M6
			$outputstring .= $this->CnabFunctions->geraEspacos(25); //**M7
			$outputstring .= "14".$this->CnabFunctions->zerosEsq($sequencia, 15); //**M8
			$outputstring .= $this->CnabFunctions->geraEspacos(33); //**M9
			$outputstring .= "01"; //**M10
			$outputstring .= "01"; //**M11
			$outputstring .= $this->CnabFunctions->geraEspacos(29); //**M12
			$outputstring .= "104"; //**M13
			$outputstring .= $this->CnabFunctions->geraEspacos(40); //**M14
			$outputstring .= $this->CnabFunctions->geraEspacos(40); //**M15
			$outputstring .= $this->CnabFunctions->geraEspacos(40); //**M16
			$outputstring .= $this->CnabFunctions->geraEspacos(40); //**M17
			$outputstring .= $this->CnabFunctions->geraEspacos(40); //**M18
			$outputstring .= $this->CnabFunctions->geraEspacos(40); //**M19
			$outputstring .= $this->CnabFunctions->geraEspacos(12); //**M20
			$outputstring .= $this->CnabFunctions->zerosEsq($i,6); //**M21

			$outputstring .= "\r\n";

			$i++;
		
		// TRAILER DE ARQUIVO
			$outputstring .= '9'; //**T1
			$outputstring .= $this->CnabFunctions->geraEspacos(393); //**T2
			$outputstring .= $this->CnabFunctions->zerosEsq($i,6); //**T3
		
		$txt_name = APP."webroot/files/cnab_txt/".$nome_arquivo;

		$fp = fopen($txt_name,'wt',true);	
		fwrite($fp, $outputstring, strlen($outputstring));
		fclose($fp);

		return true;
	}
}
?>