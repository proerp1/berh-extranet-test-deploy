<?php
App::uses('ZenviaApi', 'Lib');
class LerErroSerasaComponent extends Component
{
    public $components = ['Email'];

    public function ler($arquivo)
    {
        $ErrosPefin = ClassRegistry::init('ErrosPefin');
        $CadastroPefin = ClassRegistry::init('CadastroPefin');
        $CadastroPefinErros = ClassRegistry::init('CadastroPefinErros');
        $CadastroPefinLote = ClassRegistry::init('CadastroPefinLote');

        $fp = fopen($arquivo, "r");
            
        $identificação_arquivo = "";
        $codigo_envio          = "";
        $numero_remessa        = "";
        $cnpj_informante       = "";
        $telefone_informante   = "";
        $numero_remessa = "";
    
        while (!feof($fp)) {
            $linha = fgets($fp, 4096);

            if ( substr($linha,0,1 ) == 0 ){
                $identificação_arquivo = substr($linha,104,15);
                $codigo_envio          = substr($linha,125, 1);
                $numero_remessa        = substr($linha,119, 6);
                $cnpj_informante       = substr($linha,1,9);
                $telefone_informante   = substr($linha,22,8);
            }
            $cod_erro                            = substr($linha,533,60);
            $sequencia             = substr($linha,593,7);
            $codigo_registro       = substr($linha,0,1);

            $pefin = $CadastroPefin->find('first', ['conditions' => ['CadastroPefin.n_remessa' => $numero_remessa, 'CadastroPefin.n_sequencial' => $sequencia]]);

            if (!empty($pefin)) {
                if ( $codigo_registro == 0 or $codigo_registro == 1 or $codigo_registro == 9 ){
                    if ($identificação_arquivo == "SERASA-CONVEM04" and $codigo_envio == "R" and $cnpj_informante == "008663497" ){
                        $operacao = $pefin['CadastroPefinLote']['tipo'] == 'inclusao' ? 'I' : 'B';

                        $this->atualiza_pefin($pefin, $cod_erro, $operacao);
                    }
                }

                $CadastroPefinLote->updateAll(
                    ['CadastroPefinLote.status_id' => 21], //set
                    ["CadastroPefinLote.remessa" => trim($numero_remessa)] //where
                );

            } else {

                $codigo_registro = substr($linha, 0, 1);
                
                if ($codigo_registro == 0 && $codigo_registro != '') {
                    $identificacao_arquivo = substr($linha, 104, 15);
                    $codigo_envio          = substr($linha, 125, 1);
                    $numero_remessa        = substr($linha, 119, 6);
                    $cnpj_informante       = substr($linha, 1, 9);
                    $telefone_informante   = substr($linha, 22, 8);
                }

                if ($codigo_registro == 1) {
                    $tipo_comunicado = trim(substr($linha, 530, 1));
                    $operacao = substr($linha, 1, 1);
                    $cod_erro = substr($linha, 533, 60);
                    if ($codigo_envio == "I") {
                        $cod_erro = substr($linha, 533, 12);
                    }
                }

                if ($codigo_registro == 2) {
                    $principalid = trim(substr($linha, 305, 25));
                }

                $sequencia             = substr($linha, 593, 7);
                $doc_principal = substr($linha, 33, 15);
                $doc_principal_tp = substr($linha, 32, 1);
                $cod_natureza = trim(substr($linha, 24, 3));
                $numero_titulo = trim(substr($linha, 438, 16));

                $num_ag = substr($linha, 442, 4);
                $num_ag = ltrim($num_ag, '0');
                $num_cheque = substr($linha, 446, 6);
                $num_cheque = ltrim($num_cheque, '0');

                if ($doc_principal_tp == 2) {
                    $doc_principal = substr($doc_principal, 4);
                } else {
                    $doc_principal = substr($doc_principal, 1);
                }

                $doc_principal = ereg_replace("[^0-9]", "", $doc_principal);

                if ($codigo_registro == 1 || $codigo_registro == 2) {
                    $nome_devedor = trim(substr($linha, 105, 70));
                    $valor = trim(substr($linha, 423, 15));
                    $valor = $this->str_insert(".", $valor, strlen($valor) - 2);
                    $valor = number_format($valor, 2, '.', '');
                    $coobrigado = trim(substr($linha, 68, 1));
                    $coobrigado_doc = trim(substr($linha, 70, 15));
                    $condition = ["and" => ["CadastroPefin.principal_id is null", "CadastroPefin.valor" => $valor, "REPLACE(REPLACE(REPLACE(CadastroPefin.documento, '.', ''),'-',''),'/','')" => $doc_principal], "or" => []];

                    if ($cod_natureza == 'DC') {
                        $condition['and'] = array_merge($condition['and'], ["CadastroPefin.num_agencia like '%$num_ag%'", "CadastroPefin.num_cheque like '%{$num_cheque}%'"]);
                    } else {
                        //$condition['and'] = array_merge($condition['and'], ["REPLACE(REPLACE(REPLACE(REPLACE(`CadastroPefin`.`numero_titulo`, ',',''), '.', ''),'-',''),'/','')" => str_replace([',', '.', '-', '/'], '', $numero_titulo)]);

                        //comentado dia 16/09/2021
                        //$numero_titulo = str_replace([',', '.', '/'], '', $numero_titulo);


                        //comentado 26/07/21
                        //$condition['and'] = array_merge($condition['and'], ["REPLACE(REPLACE(REPLACE(REPLACE(`CadastroPefin`.`numero_titulo`, ',',''), '.', ''),'-',''),'/','') like '%$numero_titulo%'"]);

                        //$condition['and'] = array_merge($condition['and'], ["REPLACE(REPLACE(REPLACE(`CadastroPefin`.`numero_titulo`, ',',''), '.', ''),'/','') = '$numero_titulo'"]);

                        $condition['and'] = array_merge($condition['and'], ["trim(CadastroPefin.numero_titulo) = '$numero_titulo'"]);
                    }

                    $pefin = $CadastroPefin->find('first', ['conditions' => $condition]);

                    if (!empty($pefin)) {
                        
                        if ($identificacao_arquivo == "SERASA-CONVEM04" and $cnpj_informante == "008663497") {

                            $this->atualiza_pefin($pefin, $cod_erro, $operacao);
                            
                            if ($coobrigado != '') {
                                if ($coobrigado == 'F') {
                                    $coobrigado_doc = substr($coobrigado_doc, 4);
                                } else {
                                    $coobrigado_doc = substr($coobrigado_doc, 1);
                                }

                                $coobrigado = $CadastroPefin->find('first', ['conditions' => ["CadastroPefin.principal_id" => $pefin['CadastroPefin']['id'], "REPLACE(REPLACE(REPLACE(CadastroPefin.coobrigado_documento, '.', ''),'-',''),'/','')" => $coobrigado_doc]]);

                                $this->atualiza_pefin($coobrigado, $cod_erro, $operacao);
                            }
                        }
                    }

                    $CadastroPefinLote->updateAll(
                        ['CadastroPefinLote.status_id' => 21], //set
                        ["CadastroPefinLote.remessa" => trim($numero_remessa)] //where
                    );
                }

            }

        }
    }

    public function atualiza_pefin($pefin, $cod_erro, $operacao)
    {
        $ErrosPefin = ClassRegistry::init('ErrosPefin');
        $CadastroPefin = ClassRegistry::init('CadastroPefin');
        $CadastroPefinErros = ClassRegistry::init('CadastroPefinErros');

        if (trim($cod_erro) == "" || trim($cod_erro) == "022" || trim($cod_erro) == "306") {
            
            if ($operacao == 'I') {
                $status = 25; // incluido
                //$data = "'".date('Y-m-d')."'";
                $name = 'data_inclusao';
            } else {
                $status = 24; // baixado
                //$data = "'".date('Y-m-d')."'";
                $name = 'data_baixa';
            }

            $CadastroPefin->updateAll(
                ['CadastroPefin.status_id' => $status, 'CadastroPefin.'.$name => 'current_timestamp', 'CadastroPefin.updated' => 'current_timestamp'], //set
                ["CadastroPefin.id" => $pefin['CadastroPefin']['id']] //where
            );

        } else {

            $comeco = 0;
            for ($i=0; $i < 20; $i++) {
                $erro1 = substr($cod_erro, $comeco, 3);

                if (trim($erro1) != "") {
                    $erro = $ErrosPefin->find('first', ['conditions' => ['ErrosPefin.codigo' => $erro1]]);

                    $data_erro_retorno = ['CadastroPefinErros' => ['cadastro_pefin_id' => $pefin['CadastroPefin']['id'], 'erros_pefin_id' => $erro['ErrosPefin']['id']]];

                    $CadastroPefinErros->create();
                    $CadastroPefinErros->save($data_erro_retorno);
                }

                $comeco = $comeco+3;
            }

            $status = 23; // erro

            $CadastroPefin->updateAll(
                ['CadastroPefin.status_id' => $status], //set
                // ["CadastroPefin.n_remessa" => $numero_remessa, "CadastroPefin.n_sequencial" => $sequencia] //where
                ["CadastroPefin.id" => $pefin['CadastroPefin']['id']] //where
            );

        }

        try {
            if ($status == 25 && $pefin['Customer']['enviar_email_sms_negativacao']) {
                if ($pefin['CadastroPefin']['email'] != '') {
                    $this->envia_email($pefin);
                }

                if ($pefin['CadastroPefin']['celular'] != '') {
                    $this->envia_sms($pefin);
                }
                // TODO: enviar email e sms
            }
        } catch (Exception $e) {
            
        }
    }
    

    public function str_insert($insertstring, $intostring, $offset)
    {
        $part1 = substr($intostring, 0, $offset);
        $part2 = substr($intostring, $offset);

        $part1 = $part1 . $insertstring;
        $whole = $part1 . $part2;
        return $whole;
    }

    public function envia_email($pefin)
    {
        $dados = [
            'viewVars' => [
                'email' => trim($pefin['CadastroPefin']['email']),
                'pefin'  => $pefin
            ],
            'template' => 'carta_negativacao',
            'subject'  => 'Carta negativação',
            'config'   => 'default'
        ];

        $this->Email->send($dados);
    }

    public function envia_sms($pefin)
    {
        $num = str_replace([' ', '-', '(', ')'], '', $pefin['CadastroPefin']['celular']);
        $ZenviaApi = new ZenviaApi();

        $ZenviaApi->sendSms('55'.$num, $pefin['CadastroPefin']['nome'].', a empresa '.$pefin['Customer']['nome_secundario'].' solicitou inclusão de seu nome no cadastro negativo da Serasa Experian DUVIDAS '.$pefin['Customer']['telefone1']);
    }
}
