<?php
require_once("prorede/Prorede.class.php");

class GerarTxtComponent extends Component
{
    public $components = ['Bootstrap', 'Session'];
    
    public function gerar($pefin, $sequencia, $tipo)
    {
        $CadastroPefin = ClassRegistry::init('CadastroPefin');
        $DOCUMENT_ROOT = env('DOCUMENT_ROOT');

        $cnpj_credcheck = $this->tiraCaracteres('08.663.497/0001-30');

        // REGISTRO HEADER
        $outputstring  = "0"; // TAM 001 - Código do Registro
        $outputstring .= $this->zerosEsq(substr($cnpj_credcheck, 0, 8), 9);	 // TAM 009 - Número do CNPJ da instituição informante ajustado à direita e preenchido com zeros à esquerda
        $outputstring .= $this->tiraCaracteres(date("Ymd")); // TAM 008 - Data do movimento (AAAAMMDD) – data de geração do arquivo
        $outputstring .= $this->zerosEsq('69', 4); // TAM 004 - Número de DDD do telefone de contato da instituição informante
        $outputstring .= $this->tiraCaracteres('32250443'); // TAM 008 - Número do telefone de contato da instituição informante
        $outputstring .= '0000'; // TAM 004 - Número de ramal do telefone de contato da instituição informante
        $outputstring .= $this->completaComEspaco('CREDCHECK', 70);	 // TAM 070 - Nome do contato da instituição informante
        $outputstring .= "SERASA-CONVEM04"; // TAM 015 - Identificação do arquivo fixo “SERASA-CONVEM04”
        $outputstring .= $this->zerosEsq($sequencia, 6); // TAM 006 - Número da remessa do arquivo seqüencial do 000001, incrementando de 1 a cada novo movimento
        $outputstring .= "E"; // TAM 001 - Código de envio de arquivo: “E” - Entrada “R” - Retorno
        $outputstring .= $this->geraEspacos(4); /* TAM 004 - Diferencial de remessa, caso a instituição informante tenha necessidade de enviar mais de uma remessa
                                                 * independentes por deptos., no mesmo dia, de 0000 à 9999. Caso contrário, em branco. */
        $outputstring .= "004"; // TAM 003 - Identificação do tipo de arquivo: 004 – PEFIN PRÓ-REDE
        $outputstring .= $this->geraEspacos(8); // TAM 008 - Informar o LOGON a ser utilizado na contabilização das cartas comunicado e anotações.
        $outputstring .= $this->geraEspacos(392); // TAM 392 - Deixar em branco
        $outputstring .= $this->geraEspacos(60); /* TAM 060 - Código de erros – 3 posições ocorrendo 20 vezes.
                                                  *	Ausência de códigos indica que foi aceito no movimento de retorno. Na entrada, preencher com brancos.*/
        $outputstring .= "0000001"; // TAM 007 - Seqüência do registro no arquivo igual a 0000001 para o header.
        
        $outputstring .= "\n";

        $i = 0;
        
        // REGISTRO DETALHE
        foreach ($pefin as $dados) {
            $outputstring .= "1"; // TAM 001 - Código do registro = ‘1’ – detalhes
            if ($tipo == 'inclusao') { // TAM 001 - Código da Operação I – inclusão E – exclusão
                $outputstring .= "I";
            } else {
                $outputstring .= "E";
            }

            $outputstring .= substr($cnpj_credcheck, 8, 6); // TAM 006 - Filial e dígito do CNPJ da contratante

            if (isset($dados['CadastroPefin']['venc_divida_nao_formatado'])) {
                $dataOcorrencia = $dados['CadastroPefin']['venc_divida_nao_formatado'];
            } else {
                $dataOcorrencia = '';
            }
            $outputstring .= $this->completaComEspaco($this->tiraCaracteres($dataOcorrencia), 8); // Data da ocorrência - não superior a 4 anos e 11 meses, e inferior à 4 dias da data do movimento

            if ($dados['NaturezaOperacao']['sigla'] == "DC") {
                $data_termino_contrato = $this->tiraCaracteres($dataOcorrencia);
            } else {
                if (isset($dados['CadastroPefin']['data_compra_nao_formatado'])) {
                    $data_termino_contrato = $this->tiraCaracteres($dados['CadastroPefin']['data_compra_nao_formatado']);
                } else {
                    $data_termino_contrato = '';
                }
            }

            $outputstring .= $this->completaComEspaco($data_termino_contrato, 8); // TAM 008 - Data do término do contrato. Caso não possua, repetir a data da ocorrência (vide observação para natureza “DC”)
            $outputstring .= $this->completaComEspaco($dados['NaturezaOperacao']['sigla'], 3); // TAM 003 - Código de natureza da operação
            $outputstring .= $this->completaComEspaco("", 4); // TAM 004 - Código da praça Embratel (que originou a dívida )

            if ($dados['CadastroPefin']['tipo_pessoa'] == 1) {
                $tipo_pessoa = 'J';
            } else {
                $tipo_pessoa = 'F';
            }

            $outputstring .= $tipo_pessoa; // TAM 001 - Tipo de pessoa do principal: F – Física ou J – Jurídica

            $outputstring .= $dados['CadastroPefin']['tipo_pessoa']; // TAM 001 - Tipo do primeiro docto. do principal: 1 – CNPJ ou 2 – CPF
            $outputstring .= $this->zerosEsq($this->tiraCaracteres($dados['CadastroPefin']['documento']), 15); // TAM 015 - Primeiro documento do principal: Ajustado à direita e preenchido com zeros à esquerda
            if ($tipo == 'inclusao') { // TAM 002 - Motivo da baixa
                $outputstring .= $this->completaComEspaco("", 2);
            } else {
                $outputstring .= $dados['MotivoBaixa']['codigo'];
            }
            $outputstring .= $this->completaComEspaco("", 1); // TAM 001 - Tipo do segundo documento do principal: 3 – RG, se houver. Se não, espaços (só para pessoa física).
            $outputstring .= $this->completaComEspaco("", 15); // TAM 015 - Segundo documento do principal, se houver, se não, espaços.
            $outputstring .= $this->completaComEspaco("", 2); // TAM 002 - UF quando documento for RG, se não, espaços.

            // Para os campos abaixo, de Seq. 15 a 21, referentes ao coobrigado, se o registro for do principal, deixar em branco.
            if ($dados['CadastroPefin']['coobrigado_tipo_pessoa'] == 1) {
                $tipo_pessoa_coobrigado = 'J';
            } elseif ($dados['CadastroPefin']['coobrigado_tipo_pessoa'] == 2) {
                $tipo_pessoa_coobrigado = 'F';
            } else {
                $tipo_pessoa_coobrigado = " ";
            }
            $outputstring .= $tipo_pessoa_coobrigado; // TAM 001 - Tipo de pessoa do coobrigado: F – Física | J – Jurídica
                $outputstring .= $dados['CadastroPefin']['coobrigado_tipo_pessoa'] ? $dados['CadastroPefin']['coobrigado_tipo_pessoa'] : ' '; // TAM 001 - Tipo do primeiro documento do coobrigado: 1 – CNPJ | 2 – CPF
                if ($dados['CadastroPefin']['coobrigado_documento'] != null) {
                    $outputstring .= $this->zerosEsq($this->tiraCaracteres($dados['CadastroPefin']['coobrigado_documento']), 15); // TAM 015 - Documento do coobrigado Ajustado à direita e preenchido com zeros à esquerda
                } else {
                    $outputstring .= $this->completaComEspaco("", 15);
                }
            $outputstring .= $this->completaComEspaco("", 2); // TAM 002 - Espaços
            $outputstring .= $this->completaComEspaco("", 1);
            $outputstring .= $this->completaComEspaco("", 15);
            $outputstring .= $this->completaComEspaco("", 2);

            if ($dados['CadastroPefin']['tem_coobrigado'] == 1) {
                $outputstring .= $this->completaComEspaco(strtoupper($this->tiraCaracteres($dados['CadastroPefin']['coobrigado_nome'])), 70); // TAM 070 - Nome do devedor
            } else {
                $outputstring .= $this->completaComEspaco(strtoupper($this->tiraCaracteres($dados['CadastroPefin']['nome'])), 70); // TAM 070 - Nome do devedor
            }

            // TAM 008 - A data do nascimento (AAAAMMDD) deve ser superior a 18 anos (só para pessoa física). Se não, colocar 00000000.
            if ($dados['CadastroPefin']['tipo_pessoa'] == 1) {
                $outputstring .= $this->zerosEsq("", 8);
            } else {
                $outputstring .= $this->zerosEsq($this->tiraCaracteres($dados['CadastroPefin']['nascimento']), 8);
            }

            $outputstring .= $this->completaComEspaco(strtoupper($dados['CadastroPefin']['pai']), 70); 		// TAM 070 - Nome do pai. Caso não possua, brancos.
            $outputstring .= $this->completaComEspaco(strtoupper($dados['CadastroPefin']['mae']), 70); 		// TAM 070 - Nome do mãe. Caso não possua, brancos.

            $enderecoComp = substr($this->tiraCaracteres($dados['CadastroPefin']['endereco'])." ".$dados['CadastroPefin']['numero']." ".$this->tiraCaracteres($dados['CadastroPefin']['complemento']), 0, 45);
            $outputstring .= $this->completaComEspaco(strtoupper($enderecoComp), 45);  										// TAM 045 - Endereço completo (rua, Av., nº etc.)
            $outputstring .= $this->completaComEspaco(strtoupper($this->tiraCaracteres($dados['CadastroPefin']['bairro'])), 20); // TAM 020 - Bairro correspondente
            $outputstring .= $this->completaComEspaco(strtoupper($this->tiraCaracteres($dados['CadastroPefin']['cidade'])), 25); // TAM 025 - Município correspondente
            $outputstring .= $this->completaComEspaco($dados['CadastroPefin']['estado'], 2); // TAM 002 - UF
            $outputstring .= $this->completaComEspaco($this->tiraCaracteres($dados['CadastroPefin']['cep']), 8); // TAM 008 - CEP

            if (isset($dados['CadastroPefin']['valor_nao_formatado'])) {
                $valor = $this->tiraCaracteres($dados['CadastroPefin']['valor_nao_formatado']);
            } else {
                $valor = 0;
            }
                
            $outputstring .= $this->zerosEsq($valor, 15); // TAM 015 - Valor com 2 decimais, alinhar a direita com zeros a esquerda

            if ($dados['NaturezaOperacao']['sigla'] == "DC") {
                $outputstring .= $this->zerosEsq($dados['CadastroPefin']['num_banco'], 4);
                $outputstring .= $this->zerosEsq($dados['CadastroPefin']['num_agencia'], 4);
                $outputstring .= $this->zerosEsq($dados['CadastroPefin']['num_cheque'], 6);
                $outputstring .= $this->zerosEsq($dados['CadastroPefin']['alinea'], 2);
            } else {
                $outputstring .= $this->completaComEspaco($dados['CadastroPefin']['numero_titulo'], 16);
            }
        
            if ($dados['NaturezaOperacao']['sigla'] == "DC") {
                $outputstring .= $this->zerosEsq($dados['CadastroPefin']['num_conta_corrente'], 9);
            } else {
                $outputstring .= $this->completaComEspaco($dados['CadastroPefin']['nosso_numero'], 9);
            }

            $outputstring .= "J"; // TAM 001 - Tipo de pessoa credora da dívida: colocar fixo – Jurídica (J)
            $outputstring .= "1"; // TAM 001 - Tipo de documento do credor da dívida: colocar fixo CNPJ = 1
            $outputstring .= $this->zerosEsq($this->tiraCaracteres($dados['Customer']['documento']), 15); // TAM 015 - Número do CNPJ/CPF: completos, ajustados à direita e preenchidos com zeros à esquerda
            $clienteNomeSecundario = substr($dados['Customer']['nome_secundario'], 0, 45);
            $outputstring .= $this->completaComEspaco(strtoupper($clienteNomeSecundario), 45); // TAM 045 - Nome do Associado / Filial
            $outputstring .= $this->geraEspacos(5); // TAM 005 - Deixar em branco
            $outputstring .= $this->geraEspacos(1); // TAM 001 - Indicativo do Tipo de Comunicado ao Devedor: Branco - FAC
            $outputstring .= $this->geraEspacos(2); // TAM 002 - Deixar em branco
            $outputstring .= $this->geraEspacos(60); /* TAM 060 - Códigos de erros – 3 posições ocorrendo 20 vezes.
                                                      * Ausência de códigos indica que o registro foi aceito no movto de retorno. Na entrada, preencher com brancos. */
            $outputstring .= $this->zerosEsq(($i + 2), 7); // TAM 007 - Seqüência do registro no arquivo
            $outputstring .= "\n";

            $CadastroPefin->updateAll(
                ['CadastroPefin.status_id' => 26, "CadastroPefin.n_remessa" => "'".$this->zerosEsq($sequencia, 6)."'", "CadastroPefin.n_sequencial" => "'".$this->zerosEsq(($i + 2), 7)."'", 'CadastroPefin.user_updated_id' => CakeSession::read("Auth.User.id"), 'CadastroPefin.updated' => 'now()'], //set
                ['CadastroPefin.id' => $dados['CadastroPefin']['id']] //where
            );
            
            $i++;
        }

        $outputstring .= "9"; // TAM 001 - Código do registro = ‘9’
        $outputstring .= $this->geraEspacos(592); // TAM 592 - Deixar em branco
        $sequencia_trailler = $i + 2;
        $outputstring .= $this->zerosEsq($sequencia_trailler, 7); // TAM 007 - Seqüência do registro no arquivo.

        $txt_name = APP."/webroot/files/pefin_txt/pefin".$this->zerosEsq($sequencia, 6).".txt";

        $fp = fopen($txt_name, 'wt', true);
        fwrite($fp, $outputstring, strlen($outputstring));
        fclose($fp);

        return "pefin".$this->zerosEsq($sequencia, 6).".txt";
    }

    public function gerar_prorede($dados)
    {
        $prorede = new GerarProrede;

        return $prorede->gerar($dados);
    }

    public function tiraCaracteres($string)
    {
        //$chars = array('/' , '.' , '-', '(', ')' , ',', '_');

        $un_chars = ["á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç", ':', '%', '/', '.', '-', '(', ')' , ',', '_', 'º', '°', 'ª'];
        $perm_chars = ["a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", '', '', '', '', '', '', '', '', '', ' ', ' ', ' '];

        $limpo = str_replace($un_chars, $perm_chars, trim($string));
        
        return $limpo;
    }

    public function zerosEsq($campo, $tamanho)
    {
        $cp = str_pad($campo, $tamanho, 0, STR_PAD_LEFT);
        return $cp;
    }

    public function geraEspacos($qtde)
    {
        $sp = str_pad('', $qtde);
        return $sp;
    }

    public function completaComEspaco($campo, $tamanho)
    {
        $campo = substr($campo, 0, $tamanho);
        
        $cp = str_pad($campo, $tamanho);

        return $cp;
    }
}
