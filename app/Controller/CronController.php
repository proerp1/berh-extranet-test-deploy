<?php
App::uses('ApiBoleto', 'Lib/Credsis');
App::uses('ZenviaApi', 'Lib');
class CronController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'Meproteja'];
    public $uses = ['Customer', 'MovimentacaoCredor', 'EmailsCampanha', 'Customer', 'MailList', 'Income', 'CadastroPefin', 'ApontamentoMeProteja', 'CronMeProteja', 'ClienteMeProteja', 'PlanCustomer', 'PlanProduct', 'SocioMeProteja', 'CnabItem', 'SituacaoDocumento'];

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function cliente_inadimplente()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';
        $data = date('Y-m-d', strtotime('-30 days'));

        $bloqueados = $this->Customer->query("SELECT c.codigo_associado, c.id, c.status_id, c.nome_primario, MAX(m.created) as data FROM customers c INNER JOIN movimentacao_credor m ON m.customer_id = c.id WHERE c.status_id = 4 AND (SELECT MAX(mo.created) FROM movimentacao_credor mo WHERE mo.customer_id = c.id AND mo.status_id = 4) <= '$data' AND m.status_id = 4 and c.data_cancel = '1901-01-01' GROUP BY c.id");

        foreach ($bloqueados as $dados) {
            $this->Customer->id = $dados['c']['id'];
            $this->Customer->save(['Customer' => ['status_id' => 41]]);

            $data_movimentacao = ['MovimentacaoCredor' => ['status_id' => 41, 'customer_id' => $dados['c']['id']]];

            $this->MovimentacaoCredor->create();
            $this->MovimentacaoCredor->save($data_movimentacao);

            echo 'cliente '.$dados['c']['nome_primario'].' - id: '.$dados['c']['id'].' inadimplente <br>';
        }
    }

    public function bloqueia_clientes()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';
        $data = date('Y-m-d', strtotime('-5 days'));

        $contas_vencidas = $this->Income->find('all', ['conditions' => ['Income.vencimento' => $data, 'Income.status_id' => 15, 'Customer.status_id' => 3], 'group' => ['Customer.id']]);

        foreach ($contas_vencidas as $dados) {
            $this->Customer->id = $dados['Customer']['id'];
            $this->Customer->save(['Customer' => ['status_id' => 4]]);

            $data_movimentacao = ['MovimentacaoCredor' => ['status_id' => 4, 'customer_id' => $dados['Customer']['id']]];

            $this->MovimentacaoCredor->create();
            $this->MovimentacaoCredor->save($data_movimentacao);

            echo 'cliente '.$dados['Customer']['nome_primario'].' - id: '.$dados['Customer']['id'].' bloqueado <br>';
        }
    }

    public function send_mail()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $this->autoRender = false;
        $this->layout = 'ajax';

        //verificar campanha com status processando para enviar
        $condition = ['EmailsCampanha.send' => true, 'EmailsCampanha.processing' => true];
        $campanha = $this->EmailsCampanha->find('first', ['conditions' => $condition, 'fields' => ['EmailsCampanha.id', 'EmailsCampanha.subject', 'EmailsCampanha.content']]);

        
        if ($campanha) {
            $limite = 30; //limite de disparo de 50 emails por vez

            //verifica usuários pendentes
            $array_mail_list = $this->MailList->find('all', ['conditions' => ['MailList.email_campanha_id' => $campanha['EmailsCampanha']['id'], 'MailList.sent' => false], 'fields' => ['Customer.id', 'Customer.codigo_associado', 'Customer.nome_secundario', 'Customer.documento', 'Customer.email', 'Customer.email1', 'MailList.income_id'], 'limit' => $limite]);

            if (!empty($array_mail_list)) {
                //enviar array de dados para a função de diparo de emails
                $dados['EmailsCampanha']['id'] = $campanha['EmailsCampanha']['id'];
                $dados['EmailsCampanha']['subject'] = $campanha['EmailsCampanha']['subject'];
                $dados['EmailsCampanha']['content'] = $campanha['EmailsCampanha']['content'];
                $dados['EmailsCampanha']['config'] = 'fatura';
                $dados['EmailsCampanha']['customers'] = $array_mail_list;

                $this->Email->send_many($dados['EmailsCampanha']);
            } else {
                $this->EmailsCampanha->updateAll(['processing' => 0], ['id' => $campanha['EmailsCampanha']['id']]);
                die('todos emails enviados, encerrar processamento');
            }
        } else {
            die('nenhuma campanha encontrada');
        }

        die('email enviado');
    }

    public function pefin_decursado()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $this->CadastroPefin->updateAll(
            ['CadastroPefin.status_id' => 53],
            ['CadastroPefin.venc_divida <=' => date('Y-m-d', strtotime('-60 months'))]
        );

        die('foi');
    }

    public function cron_alerta_cliente()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $apontamentos = $this->ApontamentoMeProteja->cron_clientes_nenhuma_restricao();

        foreach ($apontamentos as $dados) {
            if (1 == $dados[0]['tipo_documento']) {
                $tipo_doc = 'CPF';
            } else {
                $tipo_doc = 'CNPJ';
            }

            if (1 == $dados[0]['tipo']) {
                $this->ApontamentoMeProteja->update_apontamento_cron($dados[0]['id']);
            } else {
                $this->ApontamentoMeProteja->update_apontamento_socios($dados[0]['id']);
            }

            $mensagem = "Informamos que não houve nenhuma alteração no seu {$tipo_doc}, fique tranquilo, o monitoramento Serasa Experian continua 24 horas por dia!";
            $mensagemSms = "BeRH: Informamos que nao houve nenhuma alteracao no seu {$tipo_doc}, fique tranquilo, o monitoramento Serasa Experian continua 24 horas por dia! Acesse o link: http://encr.pw/dUCvA";

            $dados = [
                'viewVars' => [
                    'nome' => $dados[0]['nome'],
                    'email' => explode(',', $dados[0]['email']),
                    'mensagem' => $mensagem,
                    'link' => 'http://berh.com.br/',
                ],
                'template' => 'alerta_cliente',
                'layout' => 'meproteja',
                'subject' => 'Antifraude Serasa Experian',
                'config' => 'default',
            ];

            $this->Email->send($dados);

            if ($dados[0]['celular']) {
                $num = str_replace([' ', '-', '(', ')'], '', $dados[0]['celular']);
                $ZenviaApi = new ZenviaApi();

                $ZenviaApi->sendSms('55'.$num, $mensagemSms);
            }
        }

        die('foi');
    }

    public function cron_renovacao_meproteja() {
        $this->autoRender = false;
        $this->layout = 'ajax';
        
        $cron_validade = $this->CronMeProteja->find_cron_clientes_validade();

        foreach ($cron_validade as $cron) {
            $clienteID = $cron['c']['clienteID'];

            $ultimContrat = $this->ClienteMeProteja->find('first', ['conditions' => ['ClienteMeProteja.clienteID' => $clienteID], 'order' => ['ClienteMeProteja.clienteMeProtejaID' => 'desc'], 'fields' => ['ClienteMeProteja.productID', 'Customer.documento']]);

            $plano = $this->PlanCustomer->find('first', ['conditions' => ['PlanCustomer.customer_id' => $clienteID, 'PlanCustomer.status_id' => 1], 'recursive' => -1]);

            $produto = $this->PlanProduct->find('first', [
                'fields' => [
                    'Product.id',
                    'Product.name',
                    'Product.descricao',
                    'Product.frequency',
                    'Price.id',
                    'Price.value',
                ],
                'joins' => [
                    [
                        'table' => 'product_prices',
                        'alias' => 'Price',
                        'type' => 'LEFT',
                        'conditions' => ['Price.data_cancel' => '1901-01-01', 'Price.product_id = PlanProduct.product_id', 'Price.price_table_id' => $plano['PlanCustomer']['price_table_id']],
                    ],
                ],
                'conditions' => ['PlanProduct.plan_id' => $plano['PlanCustomer']['plan_id'], 'Product.tipo' => 3],
                'order' => ['Product.valor' => 'asc'],
            ]);

            $cronID = $cron['c']['cronMeProtejaID'];
            $dataValidade = $cron['c']['cronMeProtejaValidade'];
            $status = $cron[0]['status'];
            $valor = $produto['Price']['value'];
            $cnpj_empresa = $ultimContrat['Customer']['documento'];

            $dias = $produto['Product']['frequency'];
            
            /*
            if ($status == 1) {
                $validade = date('Y-m-d', strtotime("+".$dias." days", strtotime($dataValidade)));              
            } else {
                $validade = date('Y-m-d', strtotime("+".$dias." days"));
            }*/

            $validade = date('Y-m-d', strtotime("+".$dias." days", strtotime($dataValidade)));

            //Incluir Empresa
            //$result = $this->get_include_company($cnpj_empresa, $dias);

            //Alteração
                $this->CronMeProteja->save([
                    'CronMeProteja' => [
                        'cronMeProtejaID' => $cronID,
                        'cronMeProtejaValidade' => $validade,
                        'usuarioIDAlteracao' => 1,
                        'cronMeProtejaDataAlteracao' => $cron['c']['cronMeProtejaValidade'],
                    ]
                ]);
                
                //ClienteMeProteja
                $save_cliente_proteja = [
                    'ClienteMeProteja' => [
                        'clienteID' => $clienteID,
                        'productID' => $produto['Product']['id'],
                        'cronMeProtejaID' => $cronID,
                        'usuarioIDCadastro' => 1,
                        'clienteMeProtejaValor' => $valor,
                        'clienteMeProtejaDias' => $dias,
                        'clienteMeProtejaValidade' => $validade,
                        'clienteMeProtejaDataCadastro' => $cron['c']['cronMeProtejaValidade'],
                    ]
                ];
                $this->ClienteMeProteja->create();
                $this->ClienteMeProteja->save($save_cliente_proteja);

            /*
            if ($result['success']) {
                //Alteração
                $this->CronMeProteja->save([
                    'CronMeProteja' => [
                        'cronMeProtejaID' => $cronID,
                        'cronMeProtejaValidade' => $validade,
                        'usuarioIDAlteracao' => 1,
                        'cronMeProtejaDataAlteracao' => date('Y-m-d H:i:s'),
                    ]
                ]);
                
                //ClienteMeProteja
                $save_cliente_proteja = [
                    'ClienteMeProteja' => [
                        'clienteID' => $clienteID,
                        'productID' => $produto['Product']['id'],
                        'cronMeProtejaID' => $cronID,
                        'usuarioIDCadastro' => 1,
                        'clienteMeProtejaValor' => $valor,
                        'clienteMeProtejaDias' => $dias,
                        'clienteMeProtejaValidade' => $validade,
                        'clienteMeProtejaDataCadastro' => date('Y-m-d H:i:s'),
                    ]
                ];
                $this->ClienteMeProteja->create();
                $this->ClienteMeProteja->save($save_cliente_proteja);
            } else {
                debug($result['error']);
                echo($result['message']);
            }
            */
        }

        die('foi');
    }

    //Inclui Empresa
    public function get_include_company($cnpj_empresa, $dias)
    {
        $cnpj_emp = str_replace(['.', '-', '/'], '', $cnpj_empresa);
        $cnpj_emp = substr($cnpj_emp, 0, 8);

        $result = $this->Meproteja->include_company($cnpj_emp, $dias);

        return $result;
    }

    public function cron_cancelamento_automatico(){
        $this->autoRender = false;
        $this->layout = 'ajax';
        
        $cron_empresas = $this->CronMeProteja->find_clientes_cron_expiracao();
        $cron_socios = $this->CronMeProteja->find_clientes_socios_expiracao();

        $this->exclui_socios($cron_socios);
        
        $this->exclui_empresas($cron_empresas);
        
        die('foi');
    }
    
    public function exclusao_manual_meproteja()
    {
        $socios = $this->CronMeProteja->query("
            SELECT c.id, c.documento, s.socioMeProtejaID, s.socioMeProtejaTipoDoc, s.socioMeProtejaDoc
            FROM tombamento_meproteja t
            INNER JOIN customers c ON c.id = t.codigo 
            INNER JOIN cronMeProteja cr ON cr.clienteID = c.id AND cr.cronMeProtejaDataCancel = '1901-01-01'
            INNER JOIN sociosMeProteja s on s.clienteID = c.id AND s.socioMeProtejaDataCancel = '1901-01-01'
        ");

        $empresas = $this->CronMeProteja->query("
            SELECT c.id, c.documento, cr.cronMeProtejaID
            FROM tombamento_meproteja t
            INNER JOIN customers c ON c.id = t.codigo 
            INNER JOIN cronMeProteja cr ON cr.clienteID = c.id AND cr.cronMeProtejaDataCancel = '1901-01-01'
        ");

        $this->exclui_socios($socios);
        
        $this->exclui_empresas($empresas);
        
        die('foi');
    }

    public function exclui_socios($socios)
    {
        if (!empty($socios)) {
            foreach ($socios as $socio) {
                $socioID = $socio['s']['socioMeProtejaID'];
                $cnpj_empresa = $socio['c']['documento'];
                $tipo_doc = $socio['s']['socioMeProtejaTipoDoc'];
                $doc = $socio['s']['socioMeProtejaDoc'];

                $result = $this->Meproteja->exclude_partner($cnpj_empresa, $tipo_doc, $doc);

                //Verifica se retornou algum erro
                if (!$result['success']) {
                    $msg = $result['error'];
                } else {
                    $msg = $result['result']->retorno->Mensagem;
                    
                    $this->SocioMeProteja->update_cancel_socio($socioID);
                }

                echo "$msg socio $socioID <br>";
            }
        }
    }

    public function exclui_empresas($empresas)
    {
        if (!empty($empresas)) {
            foreach ($empresas as $empresa) {
                $cronID = $empresa['cr']['cronMeProtejaID'];
                $clienteID = $empresa['c']['id'];
                $cnpj_empresa = $empresa['c']['documento'];

                $result = $this->Meproteja->exclude_company($cnpj_empresa);

                //Verifica se retornou algum erro
                if (!$result['success']) {
                    $msg = $result['error'];
                } else {
                    $msg = $result['result']->retorno->Mensagem;
                    
                    $this->CronMeProteja->update_cancel_cron($cronID);
                }

                echo "$msg cliente $clienteID cron $cronID <br>";
            }
        }
    }

    public function atualiza_boletos_confirmados()
    {
        ini_set('max_execution_time', 900);
        ini_set('max_input_time', 900);
        $itens = $this->CnabItem->find('all', [
            'conditions' => [
                'CnabItem.status_id not in(61,62,63)',
                'CnabItem.id_web is not null',
                'Income.data_cancel = \'1901-01-01\' ',
                'Income.status_id in (16)',
                'CnabItem.id_web !=' => '',
            ],
            'order' => ['Income.vencimento' => 'desc'],
        ]);

        $status = [
            "INCLUSAO" => 60,
            "BAIXA" => 61,
            "BAIXA_MANUAL" => 62,
            "CANCELAMENTO" => 63,
            "ALTERACAO" => 64,
            "ANTECIPACAO" => 65,
            "APROVACAO_ANTECIPACAO" => 66,
            "CANCELAMENTO_ANTECIPACAO" => 67
        ];

        foreach ($itens as $item) {
            $ApiBoleto = new ApiBoleto();
            $boleto = $ApiBoleto->buscarBoleto($item['CnabItem']['id_web'], false);

            if ($boleto['success']) {

                $tmp_boleto = json_decode(json_encode($boleto));

                $operacoes = $boleto['obj']['titulos']['item']['operacoes']['item'];
                if (!is_array($tmp_boleto->obj->titulos->item->operacoes->item)) {
                    $operacoes = [$boleto['obj']['titulos']['item']['operacoes']['item']];
                }
                
                foreach ($operacoes as $operacao) {
                    if ($operacao['operacao'] == 'BAIXA') {
                        $this->CnabItem->id = $item['CnabItem']['id'];
                        $this->CnabItem->save([
                            'CnabItem' => [
                                'status_id' => $status[$operacao['operacao']]
                            ]
                        ]);
                    
                        $this->Income->id = $item['CnabItem']['income_id'];
                        $this->Income->save([
                            'Income' => [
                                'status_id' => 17,
                                'data_pagamento' => $operacao['dataHora'],
                                'valor_pago' => $operacao['valor'],
                            ]
                        ]);
                    }
                }
            }
        }

        die();
    }

    public function atualiza_boletos_programado()
    {
        ini_set('max_execution_time', 900);
        ini_set('max_input_time', 900);
        $itens = $this->CnabItem->find('all', [
            'conditions' => [
                'CnabItem.status_id not in(61,62,63)',
                'CnabItem.id_web is not null',
                'Income.status_id in (15)',
                'CnabItem.id_web !=' => '',
            ],
        ]);

        $status = [
            "INCLUSAO" => 60,
            "BAIXA" => 61,
            "BAIXA_MANUAL" => 62,
            "CANCELAMENTO" => 63,
            "ALTERACAO" => 64,
            "ANTECIPACAO" => 65,
            "APROVACAO_ANTECIPACAO" => 66,
            "CANCELAMENTO_ANTECIPACAO" => 67
        ];

        foreach ($itens as $item) {
            $ApiBoleto = new ApiBoleto();
            $boleto = $ApiBoleto->buscarBoleto($item['CnabItem']['id_web'], false);

            if ($boleto['success']) {

                $tmp_boleto = json_decode(json_encode($boleto));

                $operacoes = $boleto['obj']['titulos']['item']['operacoes']['item'];
                if (!is_array($tmp_boleto->obj->titulos->item->operacoes->item)) {
                    $operacoes = [$boleto['obj']['titulos']['item']['operacoes']['item']];
                }

                foreach ($operacoes as $operacao) {
                    if ($operacao['operacao'] == 'INCLUSAO') {
                        $this->CnabItem->id = $item['CnabItem']['id'];
                        $this->CnabItem->save([
                            'CnabItem' => [
                                'status_id' => $status[$operacao['operacao']]
                            ]
                        ]);

                        $this->Income->id = $item['CnabItem']['income_id'];
                        $this->Income->save([
                            'Income' => [
                                'status_id' => 16
                            ]
                        ]);
                    }
                }
            }
        }

        die();
    }
    
    public function tombamento_meproteja()
    {
        $tombamentos = $this->PlanCustomer->query("
            SELECT t.processado, t.codigo, t.nome, c.id, c.documento
            FROM tombamento_meproteja t
            INNER JOIN customers c ON c.codigo_associado = t.codigo
            WHERE t.processado = 0
            limit 50");

        foreach ($tombamentos as $tombamento) {
            $plano = $this->PlanCustomer->find('first', ['conditions' => ['PlanCustomer.customer_id' => $tombamento['c']['id'], 'PlanCustomer.status_id' => 1], 'recursive' => -1]);
            $produto = $this->PlanProduct->find('first', [
                'fields' => [
                    'Product.id',
                    'Product.name',
                    'Product.descricao',
                    'Product.frequency',
                    'Price.id',
                    'Price.value',
                ],
                'joins' => [
                    [
                        'table' => 'product_prices',
                        'alias' => 'Price',
                        'type' => 'LEFT',
                        'conditions' => ['Price.product_id = PlanProduct.product_id', 'Price.price_table_id' => $plano['PlanCustomer']['price_table_id']],
                    ],
                ],
                'conditions' => ['PlanProduct.plan_id' => $plano['PlanCustomer']['plan_id'], 'Product.tipo' => 3, 'Product.id' => 434],
                'order' => ['Product.valor' => 'asc'],
            ]);

            if (!empty($produto)) {
                $cron = $this->CronMeProteja->find_cliente_validade($tombamento['c']['id']);

                $valor = $produto['Price']['value'];
                $cnpj_empresa = $tombamento['c']['documento'];
                $plano_gratis = false;

                $dias = $produto['Product']['frequency'];

                $cronMeProtejaID = null;

                if (!empty($cron)) {
                    $cronID = $cron[0]['c']['cronMeProtejaID'];
                    $dataValidade = $cron[0]['c']['cronMeProtejaValidade'];
                    $status = $cron[0][0]['status'];

                    if ($status == 1) {
                        $validade = date('Y-m-d', strtotime('+'.($dias + $cron[0][0]['dias']).' days', strtotime($dataValidade)));

                        echo $tombamento['c']['id']." Renovação realizada com sucesso <br>";
                    } else {
                        $validade = date('Y-m-d', strtotime('+'.$dias.' days'));

                        // codigo para excluir companhia da serasa
                        $this->Meproteja->exclude_company($cnpj_empresa);

                        //Incluir Empresa
                        $result = $this->get_include_company($cnpj_empresa, $dias);

                        //Verifica se retornou algum erro
                        if (!$result['success']) {
                            debug($result);
                            echo $tombamento['c']['id']."<br>";die;
                        } else {
                            $msg_sucess = $result['result']->retorno->Mensagem;

                            echo $tombamento['c']['id']." ".trim($msg_sucess)."<br>";
                            debug($result);
                        }
                    }

                    //Alteração
                    $save_cron = [
                        'CronMeProteja' => [
                            'cronMeProtejaID' => $cronID,
                            'usuarioIDAlteracao' => $this->Session->read('Auth.User.id'),
                            'cronMeProtejaDataAlteracao' => date('Y-m-d H:i:s'),
                            'cronMeProtejaValidade' => $validade,
                        ],
                    ];
                    $this->CronMeProteja->save($save_cron);
                    $cronMeProtejaID = $cronID;
                } else {
                    //Incluir Empresa
                    $result = $this->get_include_company($cnpj_empresa, $dias);

                    //Verifica se retornou algum erro
                    if (!$result['success']) {
                        debug($result);
                        echo $tombamento['c']['id']."<br>";die;
                    } else {
                        $msg_sucess = $result['result']->retorno->Mensagem;

                        $validade = date('Y-m-d', strtotime('+'.$dias.' days'));

                        $save_cron = [
                            'CronMeProteja' => [
                                'clienteID' => $tombamento['c']['id'],
                                'cronMeProtejaValidade' => $validade,
                                'usuarioIDCadastro' => $this->Session->read('Auth.User.id'),
                                'cronMeProtejaDataCadastro' => date('Y-m-d H:i:s'),
                            ],
                        ];

                        $this->CronMeProteja->create();
                        $this->CronMeProteja->save($save_cron);
                        $cronMeProtejaID = $this->CronMeProteja->id;

                        echo $tombamento['c']['id']." ".trim($msg_sucess)."<br>";
                        debug($result);
                    }
                }

                //ClienteMeProteja
                $save_cliente_proteja = [
                    'ClienteMeProteja' => [
                        'clienteID' => $tombamento['c']['id'],
                        'productID' => $produto['Product']['id'],
                        'cronMeProtejaID' => $cronMeProtejaID,
                        'usuarioIDCadastro' => $this->Session->read('Auth.User.id'),
                        'clienteMeProtejaValor' => $valor,
                        'clienteMeProtejaDias' => $dias,
                        'clienteMeProtejaValidade' => $validade,
                        'clienteMeProtejaDataCadastro' => date('Y-m-d H:i:s'),
                        'clienteMeProtejaIPCadastro' => ''
                    ],
                ];
                $this->ClienteMeProteja->create();
                $this->ClienteMeProteja->save($save_cliente_proteja);

                if ($result['success']) {
                    //ApontamentoMeProteja
                    $tipo_doc = '';
                    $cod_situacao = 0;
                    $situacaoDocumento = '';
                    if (isset($result['result']->dadosRelato->empresaConsultada)) {
                        $tipo_doc = 'CNPJ';
                        $cod_situacao = $result['result']->dadosRelato->empresaConsultada->SituacaoDocumento;
                        $situacaoDocumento = $this->SituacaoDocumento->find('first', ['conditions' => ['SituacaoDocumento.situacaoDocumentoTipoDoc' => $tipo_doc, 'SituacaoDocumento.situacaoDocumentoCodigo' => $cod_situacao], 'recursive' => -1]);

                        $situacaoDocumento = $situacaoDocumento['SituacaoDocumento']['situacaoDocumentoID'];
                    }

                    $qtde_apontamentos = $this->Meproteja->get_qtde_apontamentos($result['result']->dadosRelato->apontamentos);

                    $save_apontamento = [
                        'ApontamentoMeProteja' => [
                            'clienteID' => $tombamento['c']['id'],
                            'situacaoDocumentoID' => $situacaoDocumento,
                            'apontamentoMeProtejaTipo' => 1,
                            'apontamentoMeProtejaQtde' => $qtde_apontamentos,
                            'apontamentoMeProtejaString' => json_encode($result['result']->dadosRelato),
                            'usuarioIDCadastro' => $this->Session->read('Auth.User.id'),
                            'apontamentoMeProtejaDataCadastro' => date('Y-m-d H:i:s'),
                        ],
                    ];
                    $this->ApontamentoMeProteja->create();
                    $this->ApontamentoMeProteja->save($save_apontamento);
                }

                $this->PlanCustomer->query("update tombamento_meproteja t set t.processado = 1 WHERE t.codigo = '".$tombamento['t']['codigo']."'");
            }
        }

        die(); 
    }
}
