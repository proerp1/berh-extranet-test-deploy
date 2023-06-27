<?php
App::uses('StringParser', 'Lib');
use GuzzleHttp\Client;

class StringRequestComponent extends Component
{
    public $components = ['Session', 'GeraCrednetLightEnvio', 'SoapClient', 'SaveLogConsulta'];

    private $prod_url = 'https://sitenet43-2.serasa.com.br/Prod/consultahttps';
    private $homolog_url = 'https://mqlinuxext.serasa.com.br/Homologa/consultahttps';

    //homolog
    private $usuario = "13137277";
    private $senha = "372@St21";
    // prod
    /*private $usuario = "43090723";
    private $senha = "15012502";*/


    private $nova_senha = "        ";

    public function postRequest($string)
    {
        $client = new Client();

        try {
            $response = $client->post($this->prod_url, [
                'form_params' => [
                    'p' => $this->usuario . $this->senha . $this->nova_senha . $string
                ]
            ]);

            if ($response->getStatusCode()) {
                $body = $response->getBody();
                // Implicitly cast the body to a string and echo it

                $contents = $body->getContents();

                if (\strpos($contents, 'CLIENTE CONSULTANTE NAO CADASTRADO') !== false) {
                    return ['success' => false, 'message' => 'CLIENTE CONSULTANTE NAO CADASTRADO ⁻ Por favor entre em contato com o nosso atendimento'];
                }

                return ['success' => true, 'string' => $contents, 'url' => $this->usuario . $this->senha . $this->nova_senha . $string];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Ocorreu algum erro!'];
    }

    public function infoBusca($info, $id)
    {
        if (!$this->checkLimit($id)) { //limite mensal de consultas
            return ['success' => false, 'message' => 'Atenção! Você excedeu o limite mensal de consultas.'];
        }

        return $this->SoapClient->access($info);
    }

    public function crednet($post, $customerId, $userId, $customerTipoPessoa, $customerCnpj)
    {
        $check_features = isset($post["feature_check"]) ? $post["feature_check"] : [];

        $id = $post['product_id'];

        if ($post["tipo_pessoa"] == "1") {
            $tipo = "F";
        } else {
            $tipo = "J";
        }

        $doc = str_replace(["-", ".", "/"], "", $post["documento"]);
        $doc = str_pad($doc, 15, "0", STR_PAD_LEFT);

        $ddd = str_pad(str_replace(['-', '(', ')', ' '], '', $post["ddd"]), 3, "0", STR_PAD_LEFT);
        $tel = str_pad(str_replace(['-', '(', ')', ' '], '', $post["tel"]), 9, "0", STR_PAD_LEFT);
        $cep = str_pad(str_replace(['-', '(', ')', ' '], '', $post["cep"]), 9, "0", STR_PAD_LEFT);

        $estado = $post["estado"];

        //simulacao
        $cnpj_empresa = "";

        $features = [];
        $features_sec = [];

        $features["alerta_identidade"] = in_array(40, $check_features) ? "CAFY" : "    ";
        $features["fat_presum"] = in_array(96, $check_features) ? "C8FQ" : "    ";
        $features["score_pj"] = in_array(145, $check_features) ? "P8GS" : "    ";
        $features["limite_creditopj"] = in_array(99, $check_features) ? "P8GT" : "    ";
        $features["alerta_obito"] = in_array(78, $check_features) ? "AL24" : "    ";
        $features["part_soc"] = "RXPS";
        $features["relac_mercado_pf"] = in_array(79, $check_features) ? "RMF3" : "    ";
        $features["relac_mercado_pj"] = in_array(104, $check_features) ? "RMJ3" : "    ";
        $features["socio_admin"] = "NRC5";
        $features["part_emp_pj"] = in_array(106, $check_features) ? "NRC6" : "    ";
        $features_sec["renda_pro"] = in_array(41, $check_features) ? "RECD" : "    ";
        $features_sec["capacidade"] = in_array(39, $check_features) ? "RECF" : "    ";
        $features_sec["comprimetimento"] = in_array(77, $check_features) ? "RECH" : "    ";

        //FIM FEATURES

        // 08191672/0001-33
        $str_features = "";

        if ($tipo == "F") {
            $features["fat_presum"] = "    ";
            $features["score_pj"] = "    ";
            $features["socio_admin"] = "    ";

            // PF
            $features["anot_compl"] = in_array(108, $check_features) ? "RXCF" : "    ";
            // $features_sec["score_pf"] = in_array(109, $check_features) ? "REHM" : "    ";
            $features_sec["score_pf"] = in_array(144, $check_features) ? "REHMHSPN" : "    ";

            foreach ($features as $key => $value) {
                if (trim($value) != "") {
                    $str_features .= $value;
                }
            }
            $str_features = str_pad($str_features, 80, " ");
        } else {
            $features_sec["limite_credito"] = "    ";
            $features["alerta_identidade"] = "    ";
            $features["renda_presum"] = "    ";
            $features["part_soc"] = "    ";

            // PJ
            $features["anot_compl"] = in_array(111, $check_features) ? "RXCJ" : "    ";
            // $features_sec["clas_risco"] = in_array(110, $check_features) ? "P8JSC66M" : "    ";
            $features_sec["clas_risco"] = in_array(145, $check_features) ? "REH3" : "    ";
            $features_sec["gast_estim"] = in_array(112, $check_features) ? "CGJN" : "    ";

            foreach ($features as $key => $value) {
                if (trim($value) != "") {
                    $str_features .= $value;
                }
            }
            $str_features = str_pad($str_features, 80, " ");
        }

        $str_features_sec = "";
        foreach ($features_sec as $key => $value) {
            if (trim($value) != "") {
                $str_features_sec .= $value . "                     ";
            }
        }
        $str_features_sec = str_pad($str_features_sec, 86, " ");

        $cnpj_empresa = str_replace([".", "/", "-"], "", $customerCnpj);
        if ($customerTipoPessoa == '2') {
            $ProredeCustomer = ClassRegistry::init('ProredeCustomer');
            $prorede = $ProredeCustomer->find('count', ['conditions' => ['ProredeCustomer.customer_id' => $customerId], 'recursive' => -1]);

            if ($prorede) {
                $cnpj_empresa = str_replace([".", "/", "-"], "", $customerCnpj);
            }
        }

        $cnpj_empresa = str_pad($cnpj_empresa, 15, "0", STR_PAD_LEFT);

        $url = "B49C      " . $doc . $tipo . "C                          N99SINIAN                  D           D           00N                       S         1          " . $cnpj_empresa . "                                                                                                                                                                                                                                          P002RE02                     " . $str_features_sec . "N00100PPX21P 0NN N                                                                                                 N00300" . $ddd . $tel . $cep . $estado . $str_features . "      T999";

        $result = $this->postRequest($url);

        if (!$result['success']) {
            return compact('result');
        }

        if ($result['success']) {
            if (strpos($result['string'], 'AUTORIZACAO ENCERRADA') !== false) {
                return ['result' => ['success' => false, 'message' => substr($result['string'], strpos($result['string'], 'AUTORIZACAO ENCERRADA'), 32)]];
            }

            $StringParser = new StringParser();
            if ($post["tipo_pessoa"] == "1") {
                $StringParser->type = 'fisica';
            } else {
                $StringParser->type = 'juridica';
            }
            $result = $StringParser->parseCrednet($result['string'], $id);
            //logging
            $log_consulta_id = $this->SaveLogConsulta->save($id, $url, $check_features, $customerId, $userId);

            $dados_log_itens = [
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => $tipo == 'J' ? 'CNPJ' : 'CPF',
                        'campo' => $post["documento"],
                        'user_creator_id' => $userId
                    ]
                ],
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => 'DDD',
                        'campo' => $ddd,
                        'user_creator_id' => $userId
                    ]
                ],
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => 'Telefone',
                        'campo' => $tel,
                        'user_creator_id' => $userId
                    ]
                ],
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => 'CEP',
                        'campo' => $cep,
                        'user_creator_id' => $userId
                    ]
                ],
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => 'Estado',
                        'campo' => $estado,
                        'user_creator_id' => $userId
                    ]
                ]
            ];

            //insert log_consulta_itens
            $NovaVidaLogConsultaItem = ClassRegistry::init('NovaVidaLogConsultaItem');
            $NovaVidaLogConsultaItem->saveMany($dados_log_itens);
            //fim Logging
        }

        return compact('result', 'log_consulta_id');
    }

    public function crednetLight($post, $customerId, $userId, $customerTipoPessoa, $customerCnpj)
    {
        $check_features = isset($post["feature_check"]) ? $post["feature_check"] : [];
        $id = $post['product_id'];

        if ($post["tipo_pessoa"] == "1") {
            $tipo = "F";
        } else {
            $tipo = "J";
        }

        $doc = str_replace(["-", ".", "/"], "", $post['documento']);

        $ddd = str_pad(str_replace(['-', '(', ')', ' '], '', $post["ddd"]), 3, "0", STR_PAD_LEFT);
        $tel = str_pad(str_replace(['-', '(', ')', ' '], '', $post["tel"]), 8, "0", STR_PAD_LEFT);
        $cep = str_pad(str_replace(['-', '(', ')', ' '], '', $post["cep"]), 9, "0", STR_PAD_LEFT);

        $estado = $post["estado"];

        $ProredeCustomer = ClassRegistry::init('ProredeCustomer');
        $LogConsulta = ClassRegistry::init('LogConsulta');
        //simulacao
        $cnpj_empresa = "";

        $features = [];

        /*$features["score"] = in_array(140, $check_features) ? "REHM" : "    ";
        $features["class"] = in_array(141, $check_features) ? "REJSC66M" : "        ";*/
        $features["score"] = in_array(146, $check_features) ? "REFSHSPN" : "        ";
        $features["class"] = in_array(147, $check_features) ? "REH5    " : "        ";
        //FIM FEATURES

        $cnpj_empresa = str_replace([".", "/", "-"], "", $customerCnpj);
        if ($customerTipoPessoa == '2') {
            $prorede = $ProredeCustomer->find('count', ['conditions' => ['ProredeCustomer.customer_id' => $customerId], 'recursive' => -1]);

            if ($prorede) {
                $cnpj_empresa = str_replace([".", "/", "-"], "", $customerCnpj);
            }
        }

        $cnpj_empresa = str_pad($cnpj_empresa, 15, "0", STR_PAD_LEFT);

        $dadosLight = [
            'usuario' => $this->usuario,
            'doc' => $doc,
            'tipo' => $tipo,
            'tel' => $tel,
            'ddd' => $ddd,
            'cep' => $cep,
            'estado' => $estado,
            'cnpj_empresa' => $cnpj_empresa,
            'features' => $features,
            'nacional' => (isset($post['nacional']) ? $post['nacional'] : null)
        ];

        $url = $this->GeraCrednetLightEnvio->gera($dadosLight);

        $result = $this->postRequest($url);

        if (!$result['success']) {
            return compact('result');
        }

        // $this->Session->delete("Consulta.CrednetLight");

        if ($result['success']) {
            if (strpos($result['string'], 'AUTORIZACAO ENCERRADA') !== false) {
                return ['result' => ['success' => false, 'message' => substr($result['string'], strpos($result['string'], 'AUTORIZACAO ENCERRADA'), 32)]];
            }

            $StringParser = new StringParser();
            if ($post["tipo_pessoa"] == "1") {
                $StringParser->type = 'fisica';
            } else {
                $StringParser->type = 'juridica';
            }
            $result = $StringParser->parseCrednet($result['string'], $id);
            //logging
            $log_consulta_id = $this->SaveLogConsulta->save($id, $url, $check_features, $customerId, $userId);

            $dados_log_itens = [
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => $tipo == 'J' ? 'CNPJ' : 'CPF',
                        'campo' => $post["documento"],
                        'user_creator_id' => $userId
                    ]
                ],
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => 'Estado',
                        'campo' => $estado,
                        'user_creator_id' => $userId
                    ]
                ]
            ];

            //insert log_consulta_itens
            $NovaVidaLogConsultaItem = ClassRegistry::init('NovaVidaLogConsultaItem');
            $NovaVidaLogConsultaItem->saveMany($dados_log_itens);
        }

        return compact('result', 'log_consulta_id');
    }

    public function concentre($post, $customerId, $userId, $customerTipoPessoa, $customerCnpj)
    {
        $check_features = isset($post["feature_check"]) ? $post["feature_check"] : [];
        $id = $post['product_id'];

        if ($post["tipo_pessoa"] == "1") {
            $tipo = "F";
        } else {
            $tipo = "J";
        }

        $doc = str_replace([".", "-", "/"], "", $post["documento"]);
        $doc = str_pad($doc, 15, "0", STR_PAD_LEFT);
        $ddd = str_pad($post["ddd"], 4, "0", STR_PAD_LEFT);
        $tel = str_pad(str_replace("-", "", $post["tel"]), 10, "0", STR_PAD_LEFT);
        $tem_tel = ($ddd . $tel == "00000000000000") ? "N" : "S";

        $ProredeCustomer = ClassRegistry::init('ProredeCustomer');

        $features = [];

        $features["concentre_detalhe"] = 'R';
        if ($tipo == "F") {
            $features["fat_presum"] = " ";
            $features["reg_cons"] = in_array(38, $check_features) ? "S" : "N";
            $features["indi_merca"] = in_array(114, $check_features) ? "S" : "N";
            $features["limite_credito"] = "N"; // nao esta sendo usado mais
            $features["score"] = "N";
            $features["score_modelo"] = "    ";
            $featuresp002["score_modelo"] = "RSHMHSPN";
            $features["concentre_detalhe"] = in_array(142, $check_features) ? "D" : "R";
            $score20 = '        ';
        } else {
            $features["reg_cons"] = in_array(87, $check_features) ? "S" : "N";
            $features["indi_merca"] = in_array(115, $check_features) ? "S" : "N";
            $features["limite_credito"] = in_array(89, $check_features) ? "S" : "N";
            $features["score"] = "N";
            $features["score_modelo"] = "    ";
            $features["concentre_detalhe"] = in_array(143, $check_features) ? "D" : "R";
            $features["fat_presum"] = in_array(86, $check_features) ? "S" : "N";
            $score20 = 'RSHC    ';
        }

        $features["part_empre"] = in_array(116, $check_features) ? "S" : "N";

        $features["renda_pres"] = "N"; // nao esta sendo usado mais
        $features["renda_pres"] = $tipo == "J" ? " " : $features["renda_pres"]; // nao esta sendo usado mais

        $features["part_soc"] = $tipo == "F" ? "S" : " ";
        $features["socs_admin"] = $tipo == "J" ? "S" : " ";

        $featuresp002["renda_pro"] = in_array(74, $check_features) ? "RSRD" : "    ";
        $featuresp002["capacidade"] = in_array(117, $check_features) ? "RSCF" : "    ";
        $featuresp002["comprometimento"] = in_array(138, $check_features) ? "RSCH" : "    ";
        $featuresp002["alert_obit"] = in_array(113, $check_features) ? "AL23" : "    ";
        //FIM FEATURES

        $cont = 0;
        $str_featuresp002 = "";
        foreach ($featuresp002 as $key => $value) {
            if (trim($value) != "" && $cont <= 3) {
                $str_featuresp002 .= $value . "                 ";
            }
        }

        if (strlen($str_featuresp002) > 50) {
            $str_featuresp002 = substr($str_featuresp002, 0, 50);
        }
        if (strlen($str_featuresp002) != 50) {
            $str_featuresp002 = str_pad($str_featuresp002, 50, " ", STR_PAD_RIGHT);
        }

        $cnpj_empresa = str_replace([".", "/", "-"], "", $customerCnpj);
        if ($customerTipoPessoa == '2') {
            $prorede = $ProredeCustomer->find('count', ['conditions' => ['ProredeCustomer.customer_id' => $customerId], 'recursive' => -1]);

            if ($prorede) {
                $cnpj_empresa = str_replace([".", "/", "-"], "", $customerCnpj);
            }
        }

        $cnpj_empresa = str_pad($cnpj_empresa, 15, "0", STR_PAD_LEFT);

        $url = "B49C      " . $doc . $tipo . "C     FI                   S99SINIAN                              DN                                                         " . $cnpj_empresa . "                                                                                                                                                                                                                                          P002RSPU                     " . $score20 . "                 " . $str_featuresp002 . "           I00100" . $features["concentre_detalhe"] . "S" . $features["reg_cons"] . $features["score"] . $features["score_modelo"] . $tem_tel . $ddd . $tel . $features["limite_credito"] . "     " . $features["part_soc"] . "          " . $features["fat_presum"] . $features["renda_pres"] . $features["socs_admin"] . $features["part_empre"] . " " . $features["indi_merca"] . "                                                               T999";

        $result = $this->postRequest($url);

        if (!$result['success']) {
            return compact('result');
        }

        if ($result['success']) {
            if (strpos($result['string'], 'AUTORIZACAO ENCERRADA') !== false) {
                return ['result' => ['success' => false, 'message' => substr($result['string'], strpos($result['string'], 'AUTORIZACAO ENCERRADA'), 32)]];
            }

            $StringParser = new StringParser();
            if ($post["tipo_pessoa"] == "1") {
                $StringParser->type = 'fisica';
            } else {
                $StringParser->type = 'juridica';
            }
            $result = $StringParser->parseCrednet($result['string'], $id);

            //logging
            $log_consulta_id = $this->SaveLogConsulta->save($id, $url, $check_features, $customerId, $userId);

            $dados_log_itens = [
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => $tipo == 'J' ? 'CNPJ' : 'CPF',
                        'campo' => $post["documento"],
                        'user_creator_id' => $userId
                    ]
                ],
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => 'DDD',
                        'campo' => $ddd,
                        'user_creator_id' => $userId
                    ]
                ],
                [
                    'NovaVidaLogConsultaItem' => [
                        'log_consulta_id' => $log_consulta_id,
                        'tipo_campo' => 'Telefone',
                        'campo' => $tel,
                        'user_creator_id' => $userId
                    ]
                ]
            ];

            //insert log_consulta_itens
            $NovaVidaLogConsultaItem = ClassRegistry::init('NovaVidaLogConsultaItem');
            $NovaVidaLogConsultaItem->saveMany($dados_log_itens);
        }

        // $this->verificaContinuacao($result['string'], 'Concentre');

        return compact('result', 'log_consulta_id');
    }

    public function verificaContinuacao($str, $session_name)
    {
        $continuacao = substr($str, 57, 3);

        if ($continuacao == 'CON') {
            $reenvia = substr($str, 0, 400);

            $result = $this->postRequest($reenvia); //dumps the content, you can manipulate as you wish to

            if ($result['success']) {
                $this->Session->write("Consulta.{$session_name}.str_reduzida", $this->Session->read("Consulta.{$session_name}.str_reduzida") . substr($result['string'], 400));
                $this->verificaContinuacao($result['string'], $session_name);
            }
        }

        return true;
    }

    public function formatAnswer($str, $type)
    {
        switch ($type) {
            case '1':
                $str = substr($str, 6, 2) . "/" . substr($str, 4, 2) . "/" . substr($str, 0, 4);
                break;

            case '2':
                if (trim($str) != null) {
                    $formating = substr($str, 0, -2) . "." . substr($str, -2, 2);
                    $str = "R$ " . number_format($formating, 2, ",", ".");
                }

                break;

            case '3':
                # code...
                break;

            case '4':
                $str = substr($str, 0, 2) . "/" . substr($str, 2, 2) . "/" . substr($str, 4, 4);
                break;

            case '5':
                if (trim($str) != null) {
                    $str = "R$ " . number_format((int)$str, 2, ",", ".");
                }
                break;

            case '6':
                $str = (int)$str . "%";
                break;

            case '7':
                $str = substr($str, 0, 2) . "/" . substr($str, 2, 4);
                break;

            case '8':
                $str = substr($str, 0, 4) . "/" . substr($str, 4, 2);
                break;

            case '9':
                $str = $str;
                break;

            case '10':
                $str = (int)$str;
                break;

            case '11':
                $str = "R$ " . $str;
                break;

            case '12':
                $ini = (int)$str;
                $str = substr($ini, 0, -1) . "," . substr($ini, -1, 1) . " %";
                break;

            case '13':
                $str = '<img src="' . $str . '">';
                break;

            case '14':
                if (trim($str) != null) {
                    $str = substr($str, 0);
                    $str = "R$ " . number_format(((int)$str) * 1000, 2, ",", ".");
                }
                break;

            case '15':
                $str = substr($str, 1);
                $str = substr($str, 6, 2) . "/" . substr($str, 4, 2) . "/" . substr($str, 0, 4);
                break;

            case '16':
                $ini = (int)$str;
                $str = substr($ini, 0, -2) . "," . substr($ini, -2, 2) . " %";
                break;

            case '17':
                $ini = (int)$str;
                $ini = $ini / 100;
                $str = (string)$ini . " %";
                break;

            // case '18':
            //  if (substr($str, 0, 4)) {
            //    $str[0] = substr($str, 0, 4);
            //    $str[1] = substr($str, 4, 2);
            //    $str[2] = substr($str, 6, 2);
            //    $str[3] = substr($str, 8, 8);
            //    $str[4] = substr($str, 16, 16);
            //  }

            default:
                # code...
                break;
        }

        return trim($str);
    }

    public function formatCompileCreditBureau($respostas)
    {
        $AnswerItem = ClassRegistry::init('AnswerItem');
        $ItensOpcoes = ClassRegistry::init('ItensOpcoes');
        $compiled = [];

        foreach ($respostas as $resposta) {
            $key = $resposta['Answer']['respostaRegistro'];
            if ($key == "F900" && in_array($resposta['Answer']['respostaSubtipo'], ["CRP30", "CRP31", "AL05", "CAFI0", "CAFI1", "CAFI2", "CLC6", "BPME0", "BPME1", "BPTC0", "BPTC1", "BPCM0", "BPCM1"])) {
                $key = $resposta['Answer']['respostaRegistro'].$resposta['Answer']['respostaSubtipo'];
            }
            $tam = $AnswerItem->find('all', ['conditions' => ['AnswerItem.respostaID' => $resposta['Answer']['respostaID']], 'recursive' => -1, 'fields' => ['sum(AnswerItem.itemRespostaByte) as tam']]);

            $compiled[$key] = ["resp_id"         => $resposta['Answer']['respostaID'],
                "titulo"          => $resposta['Answer']['respostaNome'],
                "grupo_resposta"  => $resposta['Answer']['respostaPaiID'],
                "qtde_agrupar"    => $resposta['Answer']['respostaQtdeColunas'],
                "registro"        => $resposta['Answer']['respostaRegistro'],
                "flag_restricao"  => $resposta['Answer']['respostaFlagRestricao'],
                "ordem"           => $resposta['Answer']['respostaNumeroOrdem'],
                "subregistro"     => $resposta['Answer']['respostaSubtipo'],
                "informativo"     => $resposta['Answer']['respostaInformativo'],
                "tam"             => $tam,
                "visivel_cliente" => $resposta['Answer']['respostaVisivelCliente']
            ];


            $itens = $AnswerItem->find('all', ['conditions' => ['AnswerItem.respostaID' => $resposta['Answer']['respostaID'], 'AnswerItem.itemRespostaVisivelCliente' => 1], 'recursive' => -1]);

            foreach ($itens as $item) {
                $opt = [];
                if ($item['AnswerItem']['itemRespostaMultivalorado'] == 1) {
                    $opt = $ItensOpcoes->find('list', ['conditions' => ['ItensOpcoes.itemRespostaID' => $item['AnswerItem']['itemRespostaID']], 'fields' => ['itemOpcaoCodigo', 'itemOpcaoNome']]);
                }
                $compiled[$key]["itens"][] = ["cord" => [$item['AnswerItem']['itemRespostaInicio'], $item['AnswerItem']['itemRespostaByte']],
                    "opts" => $opt,
                    "nome" => $item['AnswerItem']['itemRespostaNome'],
                    "formato" => $item['AnswerItem']['itemRespostaFormatacao'],
                    "multivalorado" => $item['AnswerItem']['itemRespostaMultivalorado'],
                    "visivel_cliente" => $item['AnswerItem']['itemRespostaVisivelCliente'],
                    "msg_person"    => $item['AnswerItem']['itemRespostaMsgPersonalizada'],
                    "item_id"     => $item['AnswerItem']['itemRespostaID']
                ];
            }
        }

        return $compiled;
    }

    public function mountArray($list)
    {
        $arr_resp = [];
        foreach ($list as $key => $bloco) {
            $string_bloco = $bloco['dados'];
            $arr_key = $bloco['info']['resp_id'].trim($bloco['info']['titulo']);

            $arr_resp[$arr_key][$key] = [];
            if (isset($bloco['info']['itens'])) {
                foreach ($bloco['info']['itens'] as $resps) {
                    $nome = trim($resps['nome']);
                    $resposta = trim(substr($string_bloco, $resps['cord'][0]-1, $resps['cord'][1]));

                    if (!empty($resps['opts']) and $resposta != '') {
                        $resposta = $resps['opts'][$resposta];
                    }

                    $arr_resp[$arr_key][$key] = array_merge($arr_resp[$arr_key][$key], [$nome => $this->formatAnswer($resposta, $resps["formato"])]);
                }
                $arr_resp[$arr_key] = array_values($arr_resp[$arr_key]);
            } else {
                $arr_resp[$arr_key] = $string_bloco;
            }
        }

        return $arr_resp;
    }
}
