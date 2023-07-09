<?php

App::import('Controller', 'Incomes');
class BillingsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ImportarNegativacao', 'Email', 'Sms', 'ExcelGenerator'];
    public $uses = ['Billing', 'ProdutosNaoCadastrados', 'Status', 'Negativacao', 'Customer', 
                    'BillingMonthlyPayment', 'ContasReceberOld', 
                    'Income', 'LinhasNaoImportadas', 'NovaVidaLogConsulta', 'BillingNovaVida', 
                    'PlanCustomer', 'Instituicao', 
                    'ProductPrice', 'Resale', 'Product'];

    public $paginate = [
        'Billing' => ['limit' => 10, 'order' => ['Status.id', 'Billing.date_billing' => 'desc']],
        'BillingMonthlyPayment' => ['limit' => 10, 'order' => ['Customer.nome_primario', 'Customer.nome_secundario']],
        'Reembolso' => ['limit' => 165, 'order' => ['Customer.nome_primario', 'Customer.nome_secundario']],
        'Negativacao' => ['limit' => 10, 'order' => ['Product.name', 'Customer.nome_primario', 'Customer.nome_secundario']],
        'ProdutosNaoCadastrados' => ['limit' => 10, 'order' => ['ProdutosNaoCadastrados.name' => 'asc'], 'group' => 'ProdutosNaoCadastrados.name'],
        'LinhasNaoImportadas' => ['limit' => 200, 'order' => ['LinhasNaoImportadas.logon' => 'asc']]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allow('enviar_email_bloqueado', 'enviar_sms_bloqueado', 'enviar_email_boleto', 'enviar_sms_boleto', 'enviar_sms_boleto_payment_not_found', 'enviar_email_boleto_payment_not_found', 'enviar_sms_extrajudicial', 'enviar_comunicado_extrajudicial');
    }

    /***************
                DADOS
    ****************/
    public function index()
    {
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => [], 'or' => []];

        if (isset($_GET['data']) and '' != $_GET['data']) {
            $de = date('Y-m-d', strtotime('01-'.str_replace('/', '-', $_GET['data'])));
            $condition['and'] = array_merge($condition['and'], ['Billing.date_billing' => $de]);
        }

        if (isset($_GET['t']) and '' != $_GET['t']) {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $val_mensalidade = $this->BillingMonthlyPayment->find('all', ['conditions' => ['Customer.data_cancel' => '1901-01-01'], 'group' => 'BillingMonthlyPayment.billing_id', 'fields' => 'BillingMonthlyPayment.billing_id, sum(BillingMonthlyPayment.monthly_value) as valor_total']);

        $manutencao = $this->BillingMonthlyPayment->find('all', ['conditions' => ['Customer.data_cancel' => '1901-01-01'], 'group' => 'BillingMonthlyPayment.billing_id', 'fields' => 'BillingMonthlyPayment.billing_id, sum(PefinMaintenance.value) as valor_total']);

        $valor_desconto = $this->BillingMonthlyPayment->find('all', ['conditions' => ['Customer.data_cancel' => '1901-01-01'], 'group' => 'BillingMonthlyPayment.billing_id', 'fields' => 'BillingMonthlyPayment.billing_id, sum(round((BillingMonthlyPayment.desconto/100)*BillingMonthlyPayment.monthly_value_total,2)) as valor_total']);

        $data = $this->Paginator->paginate('Billing', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => ''];
        $this->set(compact('status', 'data', 'valor_consultas', 'val_mensalidade', 'manutencao', 'valor_pefin', 'valor_desconto', 'action', 'breadcrumb'));
    }

    public function add()
    {
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $this->Billing->create();
            if ($this->Billing->validates()) {
                $this->request->data['Billing']['user_creator_id'] = CakeSession::read('Auth.User.id');
                if ($this->Billing->save($this->request->data)) {
                    $id = $this->Billing->id;

                    $this->add_billing_monthly($this->request->data, $id, 'ativos');
                    //comentado rodolfo (Adicionei essa função no final da importação do arquivo serasa)
                    

                    $this->Session->setFlash(__('O faturamento foi salvo com sucesso'), 'default', ['class' => 'alert alert-success']);
                    $this->redirect(['action' => 'edit/'.$this->Billing->id]);
                } else {
                    $this->Session->setFlash(__('O faturamento não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => 'alert alert-danger']);
                }
            } else {
                $this->Session->setFlash(__('O faturamento não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => 'alert alert-danger']);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $this->set('statuses', $statuses);
        $this->set('action', 'Novo faturamento');
        $this->set('form_action', 'add');
    }

    public function add_billing_monthly($request_data, $id, $status)
    {
        $manutencao = $this->PefinMaintenance->find('first');

        $clientes = $this->Customer->query("SELECT customers.id, customers.pefin_maintenance, customers.desconto, plan_customers.mensalidade, IFNULL(plans.quantity, 0) AS quantity, customers.status_id AS customer_situacao, plans.`type`, login_consulta.id, customers.cobrar_taxa_boleto
                                                                                    FROM customers
                                                                                    INNER JOIN plan_customers ON plan_customers.customer_id = customers.id
                                                                                    INNER JOIN plans ON plans.id = plan_customers.plan_id
                                                                                    LEFT JOIN login_consulta ON login_consulta.customer_id = customers.id AND login_consulta.status_id = 1 AND login_consulta.data_cancel = '1901-01-01'
                                                                                    WHERE customers.data_cancel = '1901-01-01' AND plan_customers.data_cancel = '1901-01-01' AND plan_customers.status_id = 1 AND plans.data_cancel = '1901-01-01' AND plans.status_id = 1 AND customers.tipo_credor = 'C' and customers.faturar = 'S' and customers.status_id in (3, 4, 45)
                                                                                    GROUP BY customers.id
                                                                                    ");

        $dados_monthly = [];
        foreach ($clientes as $cliente) {
            $maintenance = $manutencao['PefinMaintenance']['id'];
            if (0 == $cliente['customers']['pefin_maintenance']) {
                $maintenance = 0;
            }

            $dados_monthly = ['BillingMonthlyPayment' => ['billing_id' => $id,
                'customer_id' => $cliente['customers']['id'],
                'pefin_maintenance_id' => $maintenance,
                'monthly_value' => $cliente['plan_customers']['mensalidade'],
                'monthly_value_total' => $cliente['plan_customers']['mensalidade'],
                'desconto' => $cliente['customers']['desconto'],
                'balance_available' => $cliente['plan_customers']['mensalidade'],
                'login_consulta_id' => $cliente['login_consulta']['id'],
                'quantity' => $cliente[0]['quantity'],
                'user_creator_id' => CakeSession::read('Auth.User.id'), ]];

            $ja_cadastrado = $this->BillingMonthlyPayment->find('count', ['conditions' => ['BillingMonthlyPayment.customer_id' => $cliente['customers']['id'], 'BillingMonthlyPayment.billing_id' => $id]]);

            if (0 == $ja_cadastrado) {
                $this->BillingMonthlyPayment->create();
                $this->BillingMonthlyPayment->save($dados_monthly);
            }

            if ($cliente['customers']['cobrar_taxa_boleto']) {
                $this->add_taxa_boleto($cliente['customers']['id'], $id);
            }

        }
    }

    public function add_taxa_boleto($customerId, $billingId)
    {
        $price = $this->Customer->query("SELECT pp.value
                                            FROM plan_customers pc
                                            INNER JOIN product_prices pp ON pp.price_table_id = pc.price_table_id AND pp.data_cancel = '1901-01-01'
                                            WHERE pc.customer_id = {$customerId} AND pp.product_id = 488
                                            AND pc.data_cancel = '1901-01-01'
                                            AND pc.status_id = 1");
        
        
        $dados_billing = [
            'BillingNovaVida' => [
                'billing_id' => $billingId,
                'product_id' => 488,
                'customer_id' => $customerId,
                'quantidade' => 1,
                'quantidade_cobrada' => 1,
                'valor_unitario' => $price[0]['pp']['value'],
                'valor_total' => $price[0]['pp']['value'],
            ]
        ];

        $this->BillingNovaVida->create();
        $this->BillingNovaVida->save($dados_billing);

        $update_valor_mensal = $this->Customer->query("UPDATE billing_monthly_payments b SET 
                                                    b.monthly_value_total = (b.monthly_value_total + {$price[0]['pp']['value']} )
                                                WHERE b.billing_id = {$billingId} 
                                                AND b.customer_id = {$customerId}
                                                AND b.data_cancel = '1901-01-01'
                                                ");
        
    }

    public function add_interest($id)
    {
        $this->Billing->id = $id;
        $billing = $this->Billing->read();

        $contas = $this->Income->find('all', [
            'fields' => ['Income.vencimento', 'Income.data_pagamento', 'Income.id', 'Income.customer_id', 'Income.valor_total', 'Customer.cobrar_juros'],
            'conditions' => [
                "Income.vencimento between '".$billing['Billing']['date_billing_nao_formatado']."' and '".date('Y-m-t', strtotime($billing['Billing']['date_billing_nao_formatado']))."'",
                'Income.status_id' => 17,
                'Income.vencimento < Income.data_pagamento',
                'Income.billing_id is not null',
            ],
        ]);

        foreach ($contas as $conta) {
            $faturamento_cliente = $this->BillingMonthlyPayment->find('first', [
                'conditions' => [
                    'BillingMonthlyPayment.billing_id' => $id,
                    'Customer.id' => $conta['Income']['customer_id'],
                ],
            ]);

            if (!empty($faturamento_cliente)) {
                $juros_multa = $this->calc_juros_multa($conta);

                $negativacao[] = [
                    'Negativacao' => [
                        'billing_id' => $id,
                        'product_id' => 408,
                        'customer_id' => $conta['Income']['customer_id'],
                        'qtde_consumo' => 1,
                        'qtde_excedente' => 0,
                        'valor_consumo' => 0,
                        'valor_excedente' => 0,
                        'valor_unitario' => $juros_multa['juros_multa'],
                        'valor_total' => $juros_multa['juros_multa'],
                        'user_creator_id' => CakeSession::read('Auth.User.id'),
                    ],
                ];

                $update = [
                    'id' => $faturamento_cliente['BillingMonthlyPayment']['id'],
                    'monthly_value_total' => $faturamento_cliente['BillingMonthlyPayment']['monthly_value_total'] + $juros_multa['juros_multa'],
                ];
                $this->BillingMonthlyPayment->save($update);
            }
        }
        $this->Negativacao->saveMany($negativacao);

        return true;
    }

    public function calc_juros_multa($conta)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $instituicao = $this->Instituicao->find('first');
        $multa = $instituicao['Instituicao']['multa'];
        $juros = $instituicao['Instituicao']['juros'];

        $valor_multa = 0;
        $valor_juros = 0;
        $valor_juros_dia = 0;

        $valor_multa = round((($conta['Income']['valor_total_nao_formatado']) * $multa) / 100, 2);

        $d = explode('-', $conta['Income']['data_pagamento_nao_formatado']);
        $data_venc = $d[2].'/'.$d[1].'/'.$d[0];
        $data_atual = mktime(0, 0, 0, $d[1], $d[2], $d[0]);

        $databd = explode('-', $conta['Income']['vencimento_nao_formatado']);
        $data = mktime(0, 0, 0, $databd[1], $databd[2], $databd[0]);

        $dias = ($data_atual - $data) / 86400;
        $dias = ceil($dias);

        $valor_juros_dia = round((($conta['Income']['valor_total_nao_formatado'] * $juros) / 100), 2);
        $valor_juros = round(($valor_juros_dia * $dias), 2);

        $juros_multa = $valor_juros + $valor_multa;

        return ['valor_multa' => $valor_multa, 'valor_juros' => $valor_juros, 'valor_juros_dia' => $valor_juros_dia, 'data_venc' => $data_venc, 'juros_multa' => $juros_multa];
    }

    public function edit($id = null)
    {
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Billing->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Billing->validates();
            $this->request->data['Billing']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->Billing->save($this->request->data)) {
                $this->Session->setFlash(__('O faturamento foi alterado com sucesso'), 'default', ['class' => 'alert alert-success']);
            } else {
                $this->Session->setFlash(__('O faturamento não pode ser alterado, Por favor tente de novo.'), 'default', ['class' => 'alert alert-danger']);
            }
        }

        $temp_errors = $this->Billing->validationErrors;
        $this->request->data = $this->Billing->read();
        $this->Billing->validationErrors = $temp_errors;

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $this->request->data['Billing']['date_billing_index'] => '', 'Dados' => ''];
        $this->set('form_action', 'edit');
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));

        $this->render('add');
    }

    public function delete($id)
    {
        $this->Permission->check(7, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->Billing->id = $id;
        $this->request->data = $this->Billing->read();

        $this->request->data['Billing']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['Billing']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->Billing->save($this->request->data)) {
            $this->Session->setFlash(__('O faturamento foi excluido com sucesso'), 'default', ['class' => 'alert alert-success']);
            $this->redirect(['action' => 'index']);
        }
    }

    /*********************
                MENSALIDADE
    **********************/
    public function mensalidade($id)
    {
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $condition = ['and' => ['BillingMonthlyPayment.billing_id' => $id, 'Customer.data_cancel' => '1901-01-01'], 'or' => []];

        // indicadores
        $total_clientes = $this->BillingMonthlyPayment->find('count', ['conditions' => $condition]);
        $valor_mensal = $this->BillingMonthlyPayment->find('first', ['conditions' => $condition, 'fields' => 'sum(BillingMonthlyPayment.monthly_value) as total']);
        $valor_pago = $this->BillingMonthlyPayment->find('first', ['conditions' => array_merge($condition['and'], ['BillingMonthlyPayment.status_id' => 9]), 'fields' => 'sum(BillingMonthlyPayment.monthly_value) as total']);
        $valor_a_pagar = $this->BillingMonthlyPayment->find('first', ['conditions' => array_merge($condition['and'], ['BillingMonthlyPayment.status_id' => 8]), 'fields' => 'sum(BillingMonthlyPayment.monthly_value) as total']);
        
        $valor_desconto = $this->BillingMonthlyPayment->find('first', ['conditions' => $condition, 'fields' => 'sum(round((BillingMonthlyPayment.desconto/100)*BillingMonthlyPayment.monthly_value_total,2)) as total']);

        $valor_total = (float) $valor_mensal[0]['total'];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => '%'.$_GET['q'].'%', 'Customer.nome_secundario LIKE' => '%'.$_GET['q'].'%', 'Customer.codigo_associado LIKE' => '%'.$_GET['q'].'%']);
        }

        $this->Income->unbindModel(['hasOne' => ['CnabItem', 'CnabItemSicoob']], false);
        $qtde_email_restante = $this->Income->find('count', ['conditions' => ['Income.billing_id' => $id, 'Income.email_sent' => 0, 'Customer.data_cancel' => '1901-01-01', 'Customer.email !=' => '']]);

        if (isset($_GET['exportar'])) {
            $nome = 'faturamento.xlsx';

            $data = $this->BillingMonthlyPayment->find('all', Hash::merge([
                'fields' => [
                    'BillingMonthlyPayment.monthly_value_total', 
                    'BillingMonthlyPayment.desconto', 
                    'PefinMaintenance.value', 
                    'Customer.codigo_associado',
                    'Customer.nome_primario',
                    'Customer.nome_secundario',
                    'Customer.documento',
                    'Customer.email',
                    'Resale.nome_fantasia',
                    'Seller.nome_fantasia',
                ],
                'conditions' => $condition, 
                'joins' => [
                    [
                        'table' => 'customers',
                        'alias' => 'Customer',
                        'type' => 'INNER',
                        'conditions' => ['Customer.id = BillingMonthlyPayment.customer_id']
                    ],
                    [
                        'table' => 'pefin_maintenances',
                        'alias' => 'PefinMaintenance',
                        'type' => 'INNER',
                        'conditions' => ['PefinMaintenance.id = BillingMonthlyPayment.pefin_maintenance_id']
                    ],
                    [
                        'table' => 'resales',
                        'alias' => 'Resale',
                        'type' => 'LEFT',
                        'conditions' => ['Customer.cod_franquia = Resale.id']
                    ],
                    [
                        'table' => 'sellers',
                        'alias' => 'Seller',
                        'type' => 'LEFT',
                        'conditions' => ['Customer.seller_id = Seller.id']
                    ],
                ],
                'recursive' => -1
            ], $this->paginate));

            $this->ExcelGenerator->gerarExcelFaturamento($nome, $data);

            $this->redirect("/files/excel/".$nome);
        } else {
            $data = $this->Paginator->paginate('BillingMonthlyPayment', $condition);
        }

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 5], 'order' => 'Status.name']);

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'Mensalidade' => ''];
        $this->set(compact('data', 'action', 'id', 'total_clientes', 'valor_mensal', 'valor_total', 'status', 'valor_pago', 'valor_a_pagar', 'faturamento', 'qtde_email_restante', 'valor_desconto', 'breadcrumb'));
    }

    public function gerar_contas_receber($id)
    {
        $this->autoRender = false;
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');

        $data = $this->BillingMonthlyPayment->find('all', ['conditions' => ['BillingMonthlyPayment.billing_id' => $id, 'Customer.data_cancel' => '1901-01-01'], 'order' => ['Customer.nome_primario', 'Customer.nome_secundario']]);


        foreach ($data as $mensalidade) {
            $total_sem_desconto = $mensalidade['BillingMonthlyPayment']['monthly_value_total'] + ($mensalidade['PefinMaintenance']['id'] != null ? $mensalidade['PefinMaintenance']['value_nao_formatado'] : 0);
            $total_com_desconto = $total_sem_desconto - (($mensalidade['BillingMonthlyPayment']['desconto'] / 100) * $total_sem_desconto);
            $resale = $this->Resale->find('first', ['conditions' => ['Resale.id' => $mensalidade['Customer']['cod_franquia']], 'recursive' => -1]);

            $dados_income[] = [
                'Income' => [
                    'name' => 'Mensalidade '.$mensalidade['Customer']['nome_secundario'],
                    'billing_id' => $id,
                    'billing_monthly_payment_id' => $mensalidade['BillingMonthlyPayment']['id'],
                    'customer_id' => $mensalidade['Customer']['id'],
                    'valor_bruto' => number_format($total_com_desconto, 2, ',', '.'),
                    'valor_total' => number_format($total_com_desconto, 2, ',', '.'),
                    'vencimento' => $mensalidade['Customer']['vencimento'].'/'.date('m/Y'),
                    'data_competencia' => $mensalidade['Billing']['date_billing'],
                    'status_id' => 15,
                    'observation' => 'faturamento '.$mensalidade['Billing']['date_billing'].' - Fatura com valor '.$total_sem_desconto.' e desconto de '.$mensalidade['BillingMonthlyPayment']['desconto'].'%',
                    'bank_account_id' => $resale['Resale']['bank_account_id'],
                    'user_creator_id' => CakeSession::read('Auth.User.id'),
                ],
            ];
        }

        $this->Billing->id = $id;
        $billing = $this->Billing->read();
        $this->Billing->save(['Billing' => ['conta_gerada' => 1]]);

        $this->Income->saveAll($dados_income);
        $this->Session->setFlash(__('Contas a receber geradas com sucesso'), 'default', ['class' => 'alert alert-success']);
        $this->redirect(['action' => 'mensalidade/'.$id]);
    }

    /*********************
                NEGATIVACAO
    **********************/

    public function negativacao($id)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $condition = ['and' => ['Negativacao.billing_id' => $id], 'or' => []];

        // indicadores
        $consultas_realizadas = $this->Negativacao->find('count', ['conditions' => $condition]);
        $valor_total = $this->Negativacao->find('first', ['conditions' => $condition, 'fields' => 'sum(Negativacao.valor_total) as total']);

        if (isset($_GET['q']) and '' != $_GET['q']) {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => '%'.$_GET['q'].'%', 'Customer.nome_secundario LIKE' => '%'.$_GET['q'].'%', 'Customer.codigo_associado LIKE' => '%'.$_GET['q'].'%', 'Product.name LIKE' => '%'.$_GET['q'].'%']);
        }

        $data = $this->Paginator->paginate('Negativacao', $condition);

        $form_action = '../billings/add_negativacao';

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'SERASA' => ''];
        $this->set(compact('data', 'id', 'action', 'form_action', 'valor_unitario', 'valor_total', 'consultas_realizadas', 'breadcrumb'));
    }

    public function add_negativacao()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $this->ImportarNegativacao->importar_negativacao($this->request->data['Negativacao']['csv'], $this->request->data['Negativacao']['billing_id']);
        //adicionei a função aqui porque a função Importar_negativacao da um data_cancel em todos os produtos do faturamento
        //cobra juros, documentado 05/05/2021
        //$this->add_interest($this->request->data['Negativacao']['billing_id']);

        $this->Session->setFlash(__('Importado com sucesso'), 'default', ['class' => 'alert alert-success']);
        $this->redirect(['action' => 'negativacao/'.$this->request->data['Negativacao']['billing_id']]);
    }

    /***********************
                DEMONSTRATIVO
    ************************/

    public function demonstrativo($id, $customer_id)
    {
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $faturamento_cliente = $this->BillingMonthlyPayment->find('first', ['conditions' => ['BillingMonthlyPayment.billing_id' => $id, 'Customer.id' => $customer_id],
            'fields' => ['BillingMonthlyPayment.*', 'PefinMaintenance.*', 'Customer.*', 'Income.*', 'StatusIncome.*', 'Status.*'],
            'joins' => [['table' => 'incomes',
                'alias' => 'Income',
                'type' => 'left',
                'conditions' => ['Income.billing_monthly_payment_id = BillingMonthlyPayment.id'],
            ],
                ['table' => 'statuses',
                    'alias' => 'StatusIncome',
                    'type' => 'left',
                    'conditions' => ['StatusIncome.id = Income.status_id'],
                ],
            ],
        ]);

        $negativacao = $this->Negativacao->find_negativacao_cliente($id, $customer_id);
        $pefin = $this->Pefin->find_pefin_cliente($id, $customer_id);
        $berh = $this->BillingNovaVida->find('all', ['conditions' => ['BillingNovaVida.billing_id' => $id, 'BillingNovaVida.customer_id' => $customer_id]]);
        $meproteja = $this->ClienteMeProteja->find('all', ['conditions' => ['ClienteMeProteja.billingID' => $id, 'ClienteMeProteja.clienteID' => $customer_id]]);

        $tipo = $negativacao ? $negativacao[0]['n']['type'] : 1;

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'Mensalidade' => '', 'Demonstrativo' => ''];
        $this->set(compact('data', 'id', 'action', 'faturamento', 'faturamento_cliente', 'negativacao', 'pefin', 'customer_id', 'tipo', 'berh', 'meproteja', 'breadcrumb'));
    }

    public function mensalidade_paga($billing_monthly_payment_id, $billing_id, $customer_id)
    {
        $update_data = ['BillingMonthlyPayment.status_id' => 9, 'BillingMonthlyPayment.user_updated_id' => CakeSession::read('Auth.User.id')];

        $this->BillingMonthlyPayment->updateAll(
            $update_data, //set
                ['BillingMonthlyPayment.id' => $billing_monthly_payment_id] //where
        );

        $this->Income->updateAll(
            ['Income.status_id' => 17], //set
                ['Income.billing_id' => $billing_id, 'Income.customer_id' => $customer_id] //where
        );

        $this->Session->setFlash(__('Status alterado com sucesso'), 'default', ['class' => 'alert alert-success']);
        $this->redirect(['action' => 'demonstrativo/'.$billing_id.'/'.$customer_id]);
    }

    public function update_negativacao($id)
    {
        if ($this->request->is(['post', 'put'])) {
            $valor_unitario = str_replace('.', '', $this->request->data['valor_unitario']);
            $valor_unitario = str_replace(',', '.', $valor_unitario);

            $this->Negativacao->id = $id;
            $this->Negativacao->save(['Negativacao' => [
                'qtde_consumo' => $this->request->data['quantidade'],
                'valor_unitario' => $valor_unitario,
                'valor_total' => $this->request->data['quantidade']*$valor_unitario,
            ]]);

            $this->Session->setFlash(__('Registros atualizados com sucesso!'), 'default', ['class' => 'alert alert-success']);
        }
        $this->redirect($this->referer());
    }

    public function update_pefin($id)
    {
        if ($this->request->is(['post', 'put'])) {
            $valor_unitario = str_replace('.', '', $this->request->data['valor_unitario']);
            $valor_unitario = str_replace(',', '.', $valor_unitario);

            $this->Pefin->id = $id;
            $this->Pefin->save(['Pefin' => [
                'qtde_realizado' => $this->request->data['quantidade'],
                'valor_unitario' => $valor_unitario,
                'valor_total' => $this->request->data['quantidade']*$valor_unitario,
            ]]);

            $this->Session->setFlash(__('Registros atualizados com sucesso!'), 'default', ['class' => 'alert alert-success']);
        }
        $this->redirect($this->referer());
    }

    public function update_hiper($id)
    {
        if ($this->request->is(['post', 'put'])) {
            $valor_unitario = str_replace('.', '', $this->request->data['valor_unitario']);
            $valor_unitario = str_replace(',', '.', $valor_unitario);

            $this->BillingNovaVida->id = $id;
            $this->BillingNovaVida->save(['BillingNovaVida' => [
                'quantidade' => $this->request->data['quantidade'],
                'valor_unitario' => $valor_unitario,
                'valor_total' => $this->request->data['quantidade']*$valor_unitario,
            ]]);

            $this->Session->setFlash(__('Registros atualizados com sucesso!'), 'default', ['class' => 'alert alert-success']);
        }
        $this->redirect($this->referer());
    }

    /***************
                PEFIN
    ****************/

    public function pefin($id)
    {
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $condition = ['and' => ['Pefin.billing_id' => $id], 'or' => []];

        // indicadores
        $consultas_realizadas = $this->Pefin->find('count', ['conditions' => $condition]);
        $valor_total = $this->Pefin->find('first', ['conditions' => $condition, 'fields' => 'sum(Pefin.valor_total) as total']);

        if (isset($_GET['q']) and '' != $_GET['q']) {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => '%'.$_GET['q'].'%', 'Customer.nome_secundario LIKE' => '%'.$_GET['q'].'%', 'Customer.codigo_associado LIKE' => '%'.$_GET['q'].'%', 'Product.name LIKE' => '%'.$_GET['q'].'%']);
        }

        $data = $this->Paginator->paginate('Pefin', $condition);

        $data_ini = $faturamento['Billing']['date_billing_nao_formatado'];
        $data_fim = date('Y-m-t', strtotime($faturamento['Billing']['date_billing_nao_formatado']));

        $form_action = '../billings/add_pefin';

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'PEFIN' => ''];
        $this->set(compact('data', 'id', 'action', 'form_action', 'valor_unitario', 'valor_total', 'consultas_realizadas', 'qtde_processar', 'breadcrumb'));
    }

    public function add_pefin($id)
    {
        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $data_ini = $faturamento['Billing']['date_billing_nao_formatado'];
        $data_fim = date('Y-m-t', strtotime($faturamento['Billing']['date_billing_nao_formatado']));

        $dados = $this->CadastroPefin->query("SELECT COUNT(d.id) AS qtde, c.codigo_associado, c.nome_primario, d.estado, d.tipo_pessoa, d.id
                                                                                        FROM cadastro_pefin AS d
                                                                                        INNER JOIN customers AS c ON d.customer_id = c.id and c.data_cancel
                                                                                         = '1901-01-01'
                                                                                        WHERE d.faturado = 0 AND d.status_id NOT IN (23) and d.created BETWEEN '$data_ini' AND '$data_fim 23:59:59' 
                                                                                            AND d.data_cancel = '1901-01-01' 

                                                                                        GROUP BY c.codigo_associado, d.estado, d.tipo_pessoa");

        $this->ImportarNegativacao->importar_pefin($dados, $id);

        $this->Session->setFlash(__('Importado com sucesso'), 'default', ['class' => 'alert alert-success']);
        $this->redirect(['action' => 'pefin/'.$id]);
    }

    /*********************
                BeRH
    **********************/
    public function berh($id)
    {
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $condition = ['and' => ['BillingNovaVida.billing_id' => $id, 'Customer.faturar' => 'S'], 'or' => []];

        if (isset($_GET['q']) and '' != $_GET['q']) {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => '%'.$_GET['q'].'%', 'Customer.nome_secundario LIKE' => '%'.$_GET['q'].'%', 'Customer.codigo_associado LIKE' => '%'.$_GET['q'].'%', 'Product.name LIKE' => '%'.$_GET['q'].'%']);
        }

        $data = $this->Paginator->paginate('BillingNovaVida', $condition);

        $qtde_processar = $this->NovaVidaLogConsulta->find('count', ['conditions' => ['Customer.faturar' => 's',  'NovaVidaLogConsulta.product_id' => 449, 'NovaVidaLogConsulta.faturado' => 0, 'date_format(NovaVidaLogConsulta.created, "%m-%Y")' => date('m-Y', strtotime(str_replace('/', '-', $faturamento['Billing']['date_billing'])))]]);

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'BeRH' => ''];
        $this->set(compact('id', 'action', 'qtde_processar', 'data', 'breadcrumb'));
    }

    public function processar_produtos_berh($id)
    {
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $registrosSql = "SELECT n.product_id, SUM(n.valor) AS total, n.valor, COUNT(n.id) AS qtde, n.customer_id, p.gratuidade, pl.total_gratuity, n.id
                                                FROM nova_vida_log_consultas n
                                                INNER JOIN plan_customers pc ON pc.id = n.plan_customer_id
                                                INNER JOIN plan_products p ON p.product_id = n.product_id AND p.plan_id = pc.plan_id
                                                INNER JOIN plans pl ON pl.id = pc.plan_id
                                                INNER JOIN customers c ON c.id = n.customer_id
                                                WHERE c.faturar = 'S' 
                                                and p.data_cancel = '1901-01-01' 
                                                and date_format(n.created, '%m-%Y') = '".date('m-Y', strtotime(str_replace('/', '-', $faturamento['Billing']['date_billing'])))."' 
                                                and n.product_id = 449 and n.faturado = 0 and pc.status_id = 1
                                                GROUP BY n.customer_id, n.product_id";

        $registros = $this->NovaVidaLogConsulta->query($registrosSql);
        $last_customer_id = '';
        $total_gratuidade_restante = 0;
        foreach ($registros as $registro) {
            //verifica se o produto tem gratuidade, se nao tiver cobra todas as consultas feitas
            if ($registro['p']['gratuidade'] > 0) {
                // conta o restante de consultas gratuitas o cliente tem
                if ($last_customer_id != $registro['n']['customer_id']) {
                    $total_gratuidade_restante = $registro['pl']['total_gratuity'];
                }

                // se nao sobrar mais qtde gratuite, computa o total consultado
                $qtde_cobrar = $total_gratuidade_restante > 0 ? $registro[0]['qtde'] - $total_gratuidade_restante : $registro[0]['qtde'];

                //se a quantidade de consultas for maior que a quantidade de gratuidade, calcula somente as excedentes
                if ($qtde_cobrar > 0) {
                    $valor_cobrar = $qtde_cobrar * $registro['n']['valor'];
                    $qtde_cobrada = $qtde_cobrar;
                } else {
                    $valor_cobrar = 0;
                    $qtde_cobrada = 0;
                }

                $total_gratuidade_restante -= $registro[0]['qtde'];
                $last_customer_id = $registro['n']['customer_id'];
            } else {
                $valor_cobrar = $registro[0]['total'];
                $qtde_cobrada = $registro[0]['qtde'];
            }

            $dados_billing = ['BillingNovaVida' => ['billing_id' => $id,
                'product_id' => $registro['n']['product_id'],
                'customer_id' => $registro['n']['customer_id'],
                'quantidade' => $registro[0]['qtde'],
                'quantidade_cobrada' => $qtde_cobrada,
                'valor_unitario' => $registro['n']['valor'],
                'valor_total' => $valor_cobrar,
            ]];

            $this->BillingNovaVida->create();
            $this->BillingNovaVida->save($dados_billing);

            $mensalidade = $this->BillingMonthlyPayment->find('first', ['conditions' => ['BillingMonthlyPayment.billing_id' => $id, 'BillingMonthlyPayment.customer_id' => $registro['n']['customer_id']]]);

            if (empty($mensalidade)) {
                $clientePlano = $this->PlanCustomer->find('first', ['conditions' => ['PlanCustomer.status_id' => 1, 'PlanCustomer.customer_id' => $registro['n']['customer_id']]]);
                $manutencao = $this->PefinMaintenance->find('first');

                $dados_monthly = ['BillingMonthlyPayment' => ['billing_id' => $id,
                    'pefin_maintenance_id' => $manutencao['PefinMaintenance']['id'],
                    'customer_id' => $registro['n']['customer_id'],
                    'login_consulta_id' => '',
                    'monthly_value' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                    'monthly_value_total' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                    'desconto' => $clientePlano['Customer']['desconto'],
                    'balance_available' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                    'quantity' => '2' == $clientePlano['Plan']['type'] ? '0' : $clientePlano['Plan']['quantity'],
                    'user_creator_id' => CakeSession::read('Auth.User.id'),
                    'created' => date('Y-m-d H:i:s'),
                ]];

                $this->BillingMonthlyPayment->create();
                $this->BillingMonthlyPayment->save($dados_monthly);

                $mensalidade = $this->BillingMonthlyPayment->read();
            }

            $mensalidade['BillingMonthlyPayment']['monthly_value_total'] += $valor_cobrar;
            $mensalidade['BillingMonthlyPayment']['user_updated_id'] = CakeSession::read('Auth.User.id');
            $this->BillingMonthlyPayment->save($mensalidade);

            $this->NovaVidaLogConsulta->save(['NovaVidaLogConsulta' => ['id' => $registro['n']['id'], 'faturado' => 1]]);
        }

        /*$this->NovaVidaLogConsulta->updateAll(
            ['NovaVidaLogConsulta.faturado' => 1, 'NovaVidaLogConsulta.updated' => 'current_timestamp()'], //set
            ["NovaVidaLogConsulta.faturado" => 0, "date_format(NovaVidaLogConsulta.created, '%m-%Y')" => date('m-Y', strtotime(str_replace('/', '-', $faturamento['Billing']['date_billing'])))] //where
        );*/

        $this->Session->setFlash(__('Registros processdos com sucesso!'), 'default', ['class' => 'alert alert-success']);
        $this->redirect(['action' => 'berh/'.$id]);
    }

    /*********************
                MEPROTEJA
    **********************/
    public function meproteja($id)
    {
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $condition = ['and' => ['ClienteMeProteja.billingID' => $id, 'Customer.faturar' => 'S'], 'or' => []];

        if (isset($_GET['q']) and '' != $_GET['q']) {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => '%'.$_GET['q'].'%', 'Customer.nome_secundario LIKE' => '%'.$_GET['q'].'%', 'Customer.codigo_associado LIKE' => '%'.$_GET['q'].'%', 'Product.name LIKE' => '%'.$_GET['q'].'%']);
        }

        $data = $this->Paginator->paginate('ClienteMeProteja', $condition);

        $action = 'Faturamento '.$faturamento['Billing']['date_billing_index'].' - Me Proteja';

        $qtde_processar = $this->ClienteMeProteja->find('count', ['conditions' => ['Customer.faturar' => 's', 'ClienteMeProteja.billingID is null', 'date_format(ClienteMeProteja.clienteMeProtejaDataCadastro, "%m-%Y")' => date('m-Y', strtotime(str_replace('/', '-', $faturamento['Billing']['date_billing'])))]]);

        $this->set(compact('id', 'action', 'qtde_processar', 'data'));
    }

    public function processar_meproteja($id)
    {
        $this->Permission->check(7, 'escrita') ? '' : $this->redirect('/not_allowed');

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $registrosSql = "SELECT cm.productID,
                               round(SUM(cm.clienteMeProtejaValor),2) AS total,
                               cm.clienteMeProtejaValor,
                               COUNT(cm.clienteMeProtejaID)  AS qtde,
                               cm.clienteID,
                               cm.clienteMeProtejaID
                        FROM clienteMeProteja cm
                                 INNER JOIN customers c ON c.id = cm.clienteID
                        WHERE c.faturar = 'S' 
                          and date_format(cm.clienteMeProtejaDataCadastro, '%m-%Y') = '".date('m-Y', strtotime(str_replace('/', '-', $faturamento['Billing']['date_billing'])))."' 
                          and cm.billingID is null
                        GROUP BY cm.clienteID, cm.productID";

        $registros = $this->ClienteMeProteja->query($registrosSql);

        foreach ($registros as $registro) {
            $valor_cobrar = $registro[0]['total'];

            $mensalidade = $this->BillingMonthlyPayment->find('first', ['conditions' => ['BillingMonthlyPayment.billing_id' => $id, 'BillingMonthlyPayment.customer_id' => $registro['cm']['clienteID']]]);

            if (empty($mensalidade)) {
                $clientePlano = $this->PlanCustomer->find('first', ['conditions' => ['PlanCustomer.status_id' => 1, 'PlanCustomer.customer_id' => $registro['cm']['clienteID']]]);
                $manutencao = $this->PefinMaintenance->find('first');

                $dados_monthly = ['BillingMonthlyPayment' => ['billing_id' => $id,
                    'pefin_maintenance_id' => $manutencao['PefinMaintenance']['id'],
                    'customer_id' => $registro['cm']['clienteID'],
                    'login_consulta_id' => '',
                    'monthly_value' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                    'monthly_value_total' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                    'desconto' => $clientePlano['Customer']['desconto'],
                    'balance_available' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                    'quantity' => '2' == $clientePlano['Plan']['type'] ? '0' : $clientePlano['Plan']['quantity'],
                    'user_creator_id' => CakeSession::read('Auth.User.id'),
                    'created' => date('Y-m-d H:i:s'),
                ]];

                $this->BillingMonthlyPayment->create();
                $this->BillingMonthlyPayment->save($dados_monthly);

                $mensalidade = $this->BillingMonthlyPayment->read();
            }

            $dados_billing = [
                'ClienteMeProteja' => [
                    'clienteMeProtejaID' => $registro['cm']['clienteMeProtejaID'],
                    'billingID' => $id,
                    'billingMonthlyPaymentID' => $mensalidade['BillingMonthlyPayment']['id'],
                ],
            ];

            $this->ClienteMeProteja->save($dados_billing);

            $mensalidade['BillingMonthlyPayment']['monthly_value_total'] += $valor_cobrar;
            $mensalidade['BillingMonthlyPayment']['user_updated_id'] = CakeSession::read('Auth.User.id');
            $this->BillingMonthlyPayment->save($mensalidade);
        }

        $this->Session->setFlash(__('Registros processdos com sucesso!'), 'default', ['class' => 'alert alert-success']);
        $this->redirect(['action' => 'meproteja/'.$id]);
    }

    /***********************************
                PRODUTOS NAO CADASTRADOS
    ************************************/
    public function produtos_nao_cadastrados($id)
    {
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['ProdutosNaoCadastrados.billing_id' => $id], 'or' => []];

        if (isset($_GET['q']) and '' != $_GET['q']) {
            $condition['or'] = array_merge($condition['or'], ['ProdutosNaoCadastrados.name LIKE' => '%'.$_GET['q'].'%']);
        }

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $data = $this->Paginator->paginate('ProdutosNaoCadastrados', $condition);

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'Produtos não cadastrados' => ''];
        $this->set(compact('data', 'id', 'action', 'breadcrumb'));
    }

    /*******************************
                LINHAS NAO IMPORTADAS
    ********************************/
    public function linhas_nao_importadas($id)
    {
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['LinhasNaoImportadas.billing_id' => $id], 'or' => []];

        if (isset($_GET['q']) and '' != $_GET['q']) {
            $condition['or'] = array_merge($condition['or'], ['LinhasNaoImportadas.logon LIKE' => '%'.$_GET['q'].'%', 'LinhasNaoImportadas.documento LIKE' => '%'.$_GET['q'].'%']);
        }

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $data = $this->Paginator->paginate('LinhasNaoImportadas', $condition);

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'Linhas não importadas' => ''];
        $this->set(compact('data', 'id', 'action', 'breadcrumb'));
    }

    /***************
                CRON
    ****************/

    public function enviar_email_bloqueado()
    {
        $this->autoRender = false;

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.check_cobranca' => 0, 'Income.email_sent' => 0, 'Income.status_id' => [15, 19], 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.email_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' AND i.email_sent = 0');
        }

        foreach ($data as $mensalidade) {
            $this->Income->id = $mensalidade['Income']['id'];
            $billing = $this->Income->read();
            $this->Income->save(['Income' => ['email_sent' => 1]]);

            // usado para fazer login no site com o bypass, NAO ALTERAR!!!
            $hash = base64_encode($mensalidade['Customer']['codigo_associado']);
            $hash = rawurlencode($hash);

            if ('' != $mensalidade['Customer']['email']) {
                $dados = ['viewVars' => ['nome_fantasia' => $mensalidade['Customer']['nome_secundario'],
                    'email' => $mensalidade['Customer']['email'],
                    'cnpj' => $mensalidade['Customer']['documento'],
                    'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                    'codigo_associado_base' => base64_encode($mensalidade['Customer']['codigo_associado']),
                    'link' => Configure::read('Areadoassociado.link').'users/bypass_login/'.$hash.'/?em_aberto',
                ],
                    'template' => 'pague_fatura',
                    'layout' => 'new_layout',
                    'subject' => 'BeRH - Comunicado importante',
                    'config' => 'fatura',
                ];

                if (!$this->Email->send($dados)) {
                    $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => 'alert alert-danger']);
                    //$this->redirect(['action' => 'mensalidade/'.$id]);
                }
            }
        }
    }

    public function enviar_sms_bloqueado($id)
    {
        $this->autoRender = false;

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.billing_id' => $id, 'Income.check_cobranca' => 0, 'Income.sms_sent' => 0, 'Income.status_id' => 15, 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.sms_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' AND i.sms_sent = 0');
        }

        foreach ($data as $mensalidade) {
            $this->send_all_sms($mensalidade, 'blocked_notification');

            $this->Income->id = $mensalidade['Income']['id'];
            $billing = $this->Income->read();
            $this->Income->save(['Income' => ['sms_sent' => 1]]);
        }
    }

    public function teste_email_boleto()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $data = $this->Income->find('all', ['conditions' => ['Income.id' => [806330, 806331, 806332]], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            // usado para fazer login no site com o bypass, NAO ALTERAR!!!
            $hash = base64_encode($mensalidade['Customer']['codigo_associado']);
            $hash = rawurlencode($hash);

            if ('' != $mensalidade['Customer']['email']) {
                $dados = ['viewVars' => ['nome_fantasia' => $mensalidade['Customer']['nome_secundario'],
                    'email' => $mensalidade['Customer']['email'],
                    'cnpj' => $mensalidade['Customer']['documento'],
                    'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                    'codigo_associado_base' => base64_encode($mensalidade['Customer']['codigo_associado']),
                    'link' => Configure::read('Areadoassociado.link').'users/bypass_login/'.$hash.'/?em_aberto',
                ],
                    'template' => 'boleto',
                    'layout' => 'new_layout',
                    'subject' => 'BeRH - Distribuidor Autorizado Serasa Experian',
                    'config' => 'fatura',
                ];

                if (!$this->Email->send($dados)) {
                    die('nao foi');
                }
            }
        }
        die('foi');
    }

    public function enviar_email_boleto($id)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.billing_id' => $id, 'Income.check_cobranca' => 0, 'Income.email_sent' => 0, 'Income.status_id' => 15, 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.email_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' AND i.email_sent = 0');
        }

        foreach ($data as $mensalidade) {
            // usado para fazer login no site com o bypass, NAO ALTERAR!!!
            $hash = base64_encode($mensalidade['Customer']['codigo_associado']);
            $hash = rawurlencode($hash);

            if ('' != $mensalidade['Customer']['email']) {
                $dados = ['viewVars' => ['nome_fantasia' => $mensalidade['Customer']['nome_secundario'],
                    'email' => $mensalidade['Customer']['email'],
                    'cnpj' => $mensalidade['Customer']['documento'],
                    'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                    'codigo_associado_base' => base64_encode($mensalidade['Customer']['codigo_associado']),
                    'link' => Configure::read('Areadoassociado.link').'users/bypass_login/'.$hash.'/?em_aberto',
                ],
                    'template' => 'boleto',
                    'layout' => 'new_layout',
                    'subject' => 'BeRH - Distribuidor Autorizado Serasa Experian',
                    'config' => 'fatura',
                ];

                if (!$this->Email->send($dados)) {
                    $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => 'alert alert-danger']);
                    //$this->redirect(['action' => 'mensalidade/'.$id]);
                }

                $this->Income->id = $mensalidade['Income']['id'];
                $billing = $this->Income->read();
                $this->Income->save(['Income' => ['email_sent' => 1]]);
            }
        }
    }

    public function enviar_sms_boleto($id, $msg_type = 'new_billet')
    {
        $this->autoRender = false;

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.billing_id' => $id, 'Income.check_cobranca' => 0, 'Income.sms_sent' => 0, 'Income.status_id' => 15, 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.sms_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' AND i.sms_sent = 0');
        }

        foreach ($data as $mensalidade) {
            $this->send_all_sms($mensalidade, $msg_type);

            $this->Income->id = $mensalidade['Income']['id'];
            $billing = $this->Income->read();
            $this->Income->save(['Income' => ['sms_sent' => 1]]);

            echo 'enviado para '.$mensalidade['Customer']['nome_primario'].' - '.$mensalidade['Customer']['celular'].'<br>';
        }
    }

    public function enviar_email_boleto_payment_not_found()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.status_id' => [15, 19], 'Income.check_cobranca' => 0, 'Income.email_sent' => 0, 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'group' => ['Customer.id'], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.email_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' AND i.email_sent = 0');
        }

        foreach ($data as $mensalidade) {
            // usado para fazer login no site com o bypass, NAO ALTERAR!!!
            $hash = base64_encode($mensalidade['Customer']['codigo_associado']);
            $hash = rawurlencode($hash);

            if ('' != $mensalidade['Customer']['email']) {
                $dados = ['viewVars' => ['nome_fantasia' => $mensalidade['Customer']['nome_secundario'],
                    'email' => $mensalidade['Customer']['email'],
                    'cnpj' => $mensalidade['Customer']['documento'],
                    'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                    'codigo_associado_base' => base64_encode($mensalidade['Customer']['codigo_associado']),
                    'link' => Configure::read('Areadoassociado.link').'users/bypass_login/'.$hash.'/?em_aberto',
                ],
                    'template' => 'payment_not_found',
                    'layout' => 'new_layout',
                    'subject' => 'BeRH - Distribuidor Autorizado Serasa Experian',
                    'config' => 'fatura',
                ];

                if (!$this->Email->send($dados)) {
                    $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => 'alert alert-danger']);
                    //$this->redirect(['action' => 'mensalidade/'.$id]);
                }

                $this->Income->id = $mensalidade['Income']['id'];
                $billing = $this->Income->read();
                $this->Income->save(['Income' => ['email_sent' => 1]]);
            }
        }
    }

    public function enviar_sms_boleto_payment_not_found()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $this->autoRender = false;

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.status_id' => 15, 'Income.check_cobranca' => 0, 'Income.sms_sent' => 0, 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'group' => ['Customer.id'], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.sms_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' and i.sms_sent = 0');
        }

        foreach ($data as $mensalidade) {
            $this->send_all_sms($mensalidade, 'not_found_payment');

            $this->Income->id = $mensalidade['Income']['id'];
            $billing = $this->Income->read();
            $this->Income->save(['Income' => ['sms_sent' => 1]]);

            echo 'enviado para '.$mensalidade['Customer']['nome_primario'].' - '.$mensalidade['Customer']['celular'].'<br>';
        }
    }

    public function enviar_comunicado_extrajudicial()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.status_id' => [15, 19], 'Income.check_cobranca' => 0, 'Income.vencimento <' => date('Y-m-d'), 'Income.email_sent' => 0, 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'group' => ['Customer.id'], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.email_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' and i.email_sent = 0');
        }

        foreach ($data as $mensalidade) {
            $this->Income->id = $mensalidade['Income']['id'];
            $billing = $this->Income->read();
            $this->Income->save(['Income' => ['email_sent' => 1]]);

            // usado para fazer login no site com o bypass, NAO ALTERAR!!!
            $hash = base64_encode($mensalidade['Customer']['codigo_associado']);
            $hash = rawurlencode($hash);

            $dados = ['viewVars' => ['email' => $mensalidade['Customer']['email'],
                'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                'codigo_associado_base' => base64_encode($mensalidade['Customer']['codigo_associado']),
                'link' => Configure::read('Areadoassociado.link').'users/bypass_login/'.$hash.'/?em_aberto',
            ],
                'template' => 'extrajudicial',
                'layout' => 'simple',
                'subject' => 'BeRH- NOTIFICAÇÃO EXTRAJUDICIAL',
                'config' => 'fatura',
            ];

            if (!$this->Email->send($dados)) {
                $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => 'alert alert-danger']);
            //$this->redirect(['action' => 'mensalidade/'.$id]);
            } else {
                echo 'enviado para '.$mensalidade['Customer']['email'].' - codigo '.$mensalidade['Customer']['codigo_associado'];
            }
        }
    }

    public function enviar_sms_extrajudicial()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $this->autoRender = false;

        $data = $this->Income->find('all', ['conditions' => ['Income.data_processamento' => date('Y-m-d'), 'Income.status_id' => 15, 'Income.check_cobranca' => 0, 'Income.vencimento <' => date('Y-m-d'), 'Income.sms_sent' => 0, 'Customer.data_cancel' => '1901-01-01', ['not' => ['Customer.status_id' => 5]]], 'group' => ['Customer.id'], 'limit' => 50]);

        foreach ($data as $mensalidade) {
            $this->Income->query('UPDATE incomes i set i.sms_sent = 3 WHERE i.id = '.$mensalidade['Income']['id'].' and i.sms_sent = 0');
        }

        foreach ($data as $mensalidade) {
            $this->send_all_sms($mensalidade, 'extrajudicial');

            $this->Income->id = $mensalidade['Income']['id'];
            $billing = $this->Income->read();
            $this->Income->save(['Income' => ['sms_sent' => 1]]);

            echo 'enviado para '.$mensalidade['Customer']['nome_primario'].' - '.$mensalidade['Customer']['celular'].'<br>';
        }
    }

    public function send_all_sms($mensalidade, $tipo)
    {
        if ('' != $mensalidade['Customer']['celular']) {
            $this->Sms->send($mensalidade['Customer']['celular'], $tipo);
        }

        if ('' != $mensalidade['Customer']['celular1']) {
            $this->Sms->send($mensalidade['Customer']['celular1'], $tipo);
        }

        if ('' != $mensalidade['Customer']['celular2']) {
            $this->Sms->send($mensalidade['Customer']['celular2'], $tipo);
        }

        if ('' != $mensalidade['Customer']['celular3']) {
            $this->Sms->send($mensalidade['Customer']['celular3'], $tipo);
        }

        if ('' != $mensalidade['Customer']['celular4']) {
            $this->Sms->send($mensalidade['Customer']['celular4'], $tipo);
        }

        if ('' != $mensalidade['Customer']['celular5']) {
            $this->Sms->send($mensalidade['Customer']['celular5'], $tipo);
        }
    }

    public function dashboard_index(){
        
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => [], 'or' => []];

        if (isset($_GET['data']) and '' != $_GET['data']) {
            $de = date('Y-m-d', strtotime('01-'.str_replace('/', '-', $_GET['data'])));
            $condition['and'] = array_merge($condition['and'], ['Billing.date_billing' => $de]);
        }

        if (isset($_GET['t']) and '' != $_GET['t']) {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $val_mensalidade = $this->BillingMonthlyPayment->find('all', ['conditions' => ['Customer.data_cancel' => '1901-01-01'], 'group' => 'BillingMonthlyPayment.billing_id', 'fields' => 'BillingMonthlyPayment.billing_id, sum(BillingMonthlyPayment.monthly_value) as valor_total']);

        $manutencao = $this->BillingMonthlyPayment->find('all', ['conditions' => ['Customer.data_cancel' => '1901-01-01'], 'group' => 'BillingMonthlyPayment.billing_id', 'fields' => 'BillingMonthlyPayment.billing_id, sum(PefinMaintenance.value) as valor_total']);

        $valor_desconto = $this->BillingMonthlyPayment->find('all', ['conditions' => ['Customer.data_cancel' => '1901-01-01'], 'group' => 'BillingMonthlyPayment.billing_id', 'fields' => 'BillingMonthlyPayment.billing_id, sum(round((BillingMonthlyPayment.desconto/100)*BillingMonthlyPayment.monthly_value_total,2)) as valor_total']);

        $data = $this->Paginator->paginate('Billing', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $this->set(compact('status', 'data', 'val_mensalidade', 'manutencao', 'valor_desconto'));

    }

    public function dashboard($id){
        $this->Permission->check(7, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $this->Billing->id = $id;
        $faturamento = $this->Billing->read();

        $condition = ['and' => ['BillingMonthlyPayment.billing_id' => $id, 'Customer.data_cancel' => '1901-01-01'], 'or' => []];

        // indicadores
        $total_clientes = $this->BillingMonthlyPayment->find('count', ['conditions' => $condition]);
        $valor_mensal = $this->BillingMonthlyPayment->find('first', ['conditions' => $condition, 'fields' => 'sum(BillingMonthlyPayment.monthly_value) as total']);
        $valor_pago = $this->BillingMonthlyPayment->find('first', ['conditions' => array_merge($condition['and'], ['BillingMonthlyPayment.status_id' => 9]), 'fields' => 'sum(BillingMonthlyPayment.monthly_value) as total']);
        $valor_a_pagar = $this->BillingMonthlyPayment->find('first', ['conditions' => array_merge($condition['and'], ['BillingMonthlyPayment.status_id' => 8]), 'fields' => 'sum(BillingMonthlyPayment.monthly_value) as total']);
        $valor_manutencao = $this->BillingMonthlyPayment->find('first', ['conditions' => $condition, 'fields' => 'sum(PefinMaintenance.value) as total']);
        
        $valor_desconto = $this->BillingMonthlyPayment->find('first', ['conditions' => $condition, 'fields' => 'sum(round((BillingMonthlyPayment.desconto/100)*BillingMonthlyPayment.monthly_value_total,2)) as total']);

        $valor_total = (float) $valor_mensal[0]['total'] + (float) $valor_manutencao[0]['total'];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => '%'.$_GET['q'].'%', 'Customer.nome_secundario LIKE' => '%'.$_GET['q'].'%', 'Customer.codigo_associado LIKE' => '%'.$_GET['q'].'%']);
        }

        $qtde_email_restante = $this->Income->find('count', ['conditions' => ['Income.billing_id' => $id, 'Income.email_sent' => 0, 'Customer.data_cancel' => '1901-01-01', 'Customer.email !=' => '']]);

        $data = $this->Paginator->paginate('BillingMonthlyPayment', $condition);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 5], 'order' => 'Status.name']);

        $action = 'Faturamento';
        $breadcrumb = ['Financeiro' => '', 'Faturamento' => '', $faturamento['Billing']['date_billing_index'] => '', 'Dashboard' => ''];
        $this->set(compact('data', 'action', 'id', 'total_clientes', 'valor_mensal', 'valor_total', 'status', 'valor_manutencao', 'valor_pago', 'valor_a_pagar', 'faturamento', 'qtde_email_restante', 'valor_desconto', 'valor_meproteja', 'breadcrumb'));

    }


    public function get_ranking_produtos(){
        $this->autoRender = false;

        $page = $_GET['page'];
        $billing_id = $_GET['billing_id'];
        $limit = 10;

        $sql = $this->Product->queryRanking($billing_id, $page, $limit);

        $result = $this->Product->query($sql);

        $arr = [];
        foreach ($result as $res) {
            $arr[] = ['produto' => $res[0]['produto'], 'valor_consumo' => number_format($res[0]['valor_consumo'], 2, ',', '.')];
        }

        $tot = $this->Product->countRanking($billing_id);

        $total = $tot[0][0]['tot'];
        $pages = ceil($total / $limit);
        $isLast = $pages == $page;

        return json_encode(['result' => $arr, 'last' => $isLast]);
    }

    public function get_ranking_parceiros(){
        $this->autoRender = false;

        $page = $_GET['page'];
        $billing_id = $_GET['billing_id'];
        $limit = 10;

        $sql = $this->Customer->rankingPartners($billing_id, $page, $limit);

        $result = $this->Customer->query($sql);

        $arr = [];
        foreach ($result as $res) {
            $arr[] = ['razao_social' => $res['r']['razao_social'], 'totalFaturamento' => number_format($res[0]['totalFaturamento'], 2, ',', '.')];
        }

        $tot = $this->Customer->countPartners($billing_id);

        $total = $tot[0][0]['tot'];
        $pages = ceil($total / $limit);
        $isLast = $pages == $page;

        return json_encode(['result' => $arr, 'last' => $isLast]);
    }

    public function get_ranking_clientes(){
        $this->autoRender = false;

        $page = $_GET['page'];
        $billing_id = $_GET['billing_id'];
        $limit = 10;

        $sql = $this->Customer->rankingCustomers($billing_id, $page, $limit);

        $result = $this->Customer->query($sql);

        $arr = [];
        foreach ($result as $res) {
            $arr[] = ['razao_social' => $res['r']['revenda'], 'totalFaturamento' => number_format($res[0]['totalFaturamento'], 2, ',', '.')];
        }

        $tot = $this->Customer->countCustomers($billing_id);

        $total = $tot[0][0]['tot'];
        $pages = ceil($total / $limit);
        $isLast = $pages == $page;

        return json_encode(['result' => $arr, 'last' => $isLast]);
    }
}
