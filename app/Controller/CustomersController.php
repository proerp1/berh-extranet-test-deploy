<?php

class CustomersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'HtmltoPdf', 'ExcelGenerator', 'Robo'];
    public $uses = ['Customer', 'Status', 'Franquia', 'Seller', 'PlanCustomer', 'Plan', 'PriceTable', 'LoginConsulta', 'Document', 'ActivityArea', 'CustomerUser', 'Income', 'Resale', 'CustomerDiscount', 'Product', 'CustomerDiscountsProduct', 'Log', 'Order', 'OrderItem', 'MovimentacaoCredor', 'EconomicGroup', 'CustomerFile','Proposal','CustomerGeLog', 'CustomerAddress', 'LogCustomer'];

    public $paginate = [
        'Customer' => [
            'limit' => 10,
            'contain' => ['Status', 'Resale', 'PlanoAtivo', 'Seller'],
            'order' => ['Customer.codigo_associado' => 'asc'],
            'group' => 'Customer.id',
            'fields' => [
                'Customer.codigo_associado',
                'Customer.nome_secundario',
                'Customer.nome_secundario',
                'Customer.documento',
                'Customer.cidade',
                'Customer.estado',
                'Customer.id',
                'Customer.id',
                'Customer.telefone1',
                'Customer.email',
                'Customer.observacao',
                'Customer.responsavel',
                'Customer.emitir_nota_fiscal',
                'Customer.flag_gestao_economico',
                'Customer.condicao_pagamento',
                'Customer.prazo',
                'Customer.desc_condicao_pagamento',
                'Status.*',
                'Resale.nome_fantasia',
                'Seller.name',
                'PlanoAtivo.plan_id',
            ],
        ],
        'PlanCustomer'                      => ['limit' => 10, 'order' => ['PlanCustomer.status_id' => 'asc']],
        'LoginConsulta'                     => ['limit' => 10, 'order' => ['LoginConsulta.status_id' => 'asc']],
        'CustomerGeLog'                     => ['limit' => 10, 'order' => ['CustomerGeLog.created' => 'desc']],        
        'Document'                          => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'Document.name' => 'asc']],
        'CadastroPefin'                     => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'CadastroPefin.nome' => 'asc'], 'recursive' => 2],
        'MovimentacaoCredor'                => ['limit' => 10, 'order' => ['MovimentacaoCredor.created' => 'desc']],
        'CustomerUser'                      => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'CustomerUser.name' => 'asc']]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /*******************
                CLIENTES
     ********************/
    public function index()
    {
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");

        $condition = ['and' => ['Customer.cod_franquia' => CakeSession::read('Auth.User.resales')], 'or' => []];
        
        if (!$this->Permission->check(80, "leitura")) {
            $condition['and'] = array_merge($condition['and'], ['Customer.seller_id' => CakeSession::read('Auth.User.id')]);
        }
        
        if (!empty($_GET['c'])) {
            $condition['and'] = array_merge($condition['and'], ["EXISTS (SELECT 1 FROM customer_users u WHERE u.customer_id = Customer.id AND u.data_cancel = '1901-01-01 00:00:00' AND (u.name LIKE '%".$_GET['c']."%' OR u.cpf LIKE '%".$_GET['c']."%' ))"]);
        }

        if (isset($_GET['logon'])) {
            $joins = [
                'fields' => ['Status.*', 'Customer.*', 'LoginConsulta.*'],
                'joins' => [
                    [
                        'table' => 'login_consulta',
                        'alias' => 'LoginConsulta',
                        'type' => 'LEFT',
                        'conditions' => ['LoginConsulta.customer_id = Customer.id', 'LoginConsulta.data_cancel' => '1901-01-01 00:00:00']
                    ]
                ]
            ];

            $this->paginate['Customer'] = array_merge($this->paginate['Customer'], $joins);

            if (!empty($_GET['q'])) {
                $condition['or'] = array_merge($condition['or'], ['LoginConsulta.login LIKE' => "%" . $_GET['q'] . "%"]);
            }
        } else {
            if (!empty($_GET['q'])) {
                $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%", 'Customer.nome_secundario LIKE' => "%" . $_GET['q'] . "%", 'Customer.email LIKE' => "%" . $_GET['q'] . "%", 'Customer.documento LIKE' => "%" . $_GET['q'] . "%", 'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%", 'Customer.celular LIKE' => "%" . $_GET['q'] . "%", 'Customer.celular1 LIKE' => "%" . $_GET['q'] . "%", 'Customer.celular2 LIKE' => "%" . $_GET['q'] . "%", 'Customer.celular3 LIKE' => "%" . $_GET['q'] . "%", 'Customer.celular4 LIKE' => "%" . $_GET['q'] . "%", 'Customer.celular5 LIKE' => "%" . $_GET['q'] . "%"]);
            }
        }

        $this->Paginator->settings = $this->paginate;

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['t']]);
        }

        if (!empty($_GET['f'])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.cod_franquia' => $_GET['f']]);
        }

        if (!empty($_GET['cond_pag'])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.condicao_pagamento' => $_GET['cond_pag']]);
        }

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        if ($get_de != '' and $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], ['Customer.created >=' => $de . ' 00:00:00', 'Customer.created <=' => $ate . ' 23:59:59']);
        }

        $data = [];
        if (isset($_GET['logon'])) {
            if (!empty($_GET['q'])) {
                $data = $this->Paginator->paginate('Customer', $condition);
            }
        } else {
            $data = $this->Paginator->paginate('Customer', $condition);
        }

        if (isset($_GET['exportar'])) {
            $nome = 'clientes' . date('d_m_Y_H_i_s') . '.xlsx';

            $data = $this->Customer->find('all', [
                'contain' => ['Resale', 'Status', 'Seller', 'Proposal'],
                'conditions' => $condition,
            ]);

            $this->ExcelGenerator->gerarExcelClientes($nome, $data);

            $this->redirect("/files/excel/" . $nome);
        }

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);


        $codFranquias = $this->Resale->find('all', ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], ['order' => 'Resale.nome_fantasia']]);
        $action = 'Clientes';
        $this->set(compact('status', 'data', 'codFranquias', 'action'));
    }

    public function add()
    {
        $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
        
        if ($this->request->is(['post', 'put'])) {
            $this->Customer->create();

            if (empty($this->request->data['Customer']['desconto'])) {
                $this->request->data['Customer']['desconto'] = 0;
            }

            if ($this->Customer->validates()) {
                $last_code = $this->Customer->query('SELECT * from customers Customer where Customer.codigo_associado is not null order by Customer.id desc limit 1');
                if (empty($last_code[0])) {
                    $code = 1;
                } else {
                    $code = $last_code[0]['Customer']['codigo_associado'] + 1;
                }

                $this->request->data['Customer']['user_creator_id'] = CakeSession::read('Auth.User.id');
                $this->request->data['Customer']['codigo_associado'] = $code;
                $this->request->data['Customer']['status_id'] = 3;
                $this->request->data['Customer']['tipo_credor'] = 'C';
                $this->request->data['Customer']['created'] = date('Y-m-d H:i:s');

                if ($this->request->data['Customer']['condicao_pagamento'] == 1) {
                    $this->request->data['Customer']['prazo'] = null;
                }

                if ($this->Customer->save($this->request->data)) {
                    $this->LogCustomer->logCustomer($this->Customer->read());
                    $id = $this->Customer->id;
                    /*
                    $customer_user = ['CustomerUser' => ['name' => $this->request->data['Customer']['nome_primario'],
                        'email' => $this->request->data['Customer']['email'],
                        'username' => $this->request->data['Customer']['email'],
                        'customer_id' => $id,
                        'password' => $this->request->data['Customer']['senha'],
                        'main_user' => 1
                    ]];

                                $this->CustomerUser->save($customer_user, ['validate' => false]);
                    */

                    $this->Flash->set(__('O cliente foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect("/customers/edit/" . $id);
                } else {
                    $this->Flash->set(__('O cliente não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('O cliente não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $activityAreas = $this->ActivityArea->find('list', ['conditions' => ['ActivityArea.status_id' => 1], 'order' => 'ActivityArea.name']);
        $sellers = $this->Seller->find('list', ['conditions' => ['Seller.status_id' => 1], 'order' => 'Seller.name']);
        $codFranquias = $this->Resale->find('list', ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read('Auth.User.resales')], ['order' => 'Resale.nome_fantasia']]);
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2, 'not' => ['Status.id' => 6]], 'order' => 'Status.name']);
        $senha = substr(sha1(time()), 0, 6);

        $is_admin = CakeSession::read("Auth.User.Group.name") == 'Administrador';

        $this->set('action', 'Novo Cliente');
        $this->set('form_action', 'add');
        $this->set(compact('statuses', 'senha', 'codFranquias', 'activityAreas', 'sellers', 'is_admin'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Customer->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['Customer']['user_updated_id'] = CakeSession::read('Auth.User.id');
            $this->request->data['Customer']['updated'] = date('Y-m-d H:i:s');

            $log_old_value = $this->request->data['log_old_value'];
            unset($this->request->data['log_old_value']);

            $dados_log = [
                'old_value' => $log_old_value,
                'new_value' => json_encode($this->request->data),
                'route' => 'customers/edit',
                'log_action' => 'Alterou',
                'log_table' => 'Customer',
                'primary_key' => $id,
                'parent_log' => 0,
                'user_type' => 'ADMIN',
                'user_id' => CakeSession::read('Auth.User.id'),
                'message' => 'O cliente foi alterado com sucesso',
                'log_date' => date('Y-m-d H:i:s'),
                'data_cancel' => '1901-01-01',
                'usuario_data_cancel' => 0,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ];

            $dados_ge_log = [
                'customer_id' => $id,
                'flag_gestao_economico' => $this->request->data['Customer']['flag_gestao_economico'],
                'porcentagem_margem_seguranca' => $this->request->data['Customer']['porcentagem_margem_seguranca'],
                'qtde_minina_diaria' => $this->request->data['Customer']['qtde_minina_diaria'],
                'tipo_ge' => $this->request->data['Customer']['tipo_ge'],
                'created' => date('Y-m-d H:i:s'),
                'user_creator_id' => CakeSession::read('Auth.User.id'),
            ];

            $customer_old = $this->Customer->find('first', [
                'conditions' => ['Customer.id' => $id],
                'fields' => [
                    'Customer.flag_gestao_economico',
                    'Customer.porcentagem_margem_seguranca',
                    'Customer.qtde_minina_diaria',
                    'Customer.tipo_ge'
                ]
            ]);

            $fields_ge = ['flag_gestao_economico', 'porcentagem_margem_seguranca', 'qtde_minina_diaria', 'tipo_ge'];
            $alter_ge = false;

            if ($this->request->data['Customer']['condicao_pagamento'] == 1) {
                $this->request->data['Customer']['prazo'] = null;
            }

            foreach ($fields_ge as $field) {
                $val_old = $customer_old['Customer'][$field];
                $val_new = $this->request->data['Customer'][$field];
                if ((string)$val_old !== (string)$val_new) {
                    $alter_ge = true;
                    break;
                }
            }

            if ($this->Customer->save($this->request->data)) {
                $this->LogCustomer->createLogCustomer($this->Customer->read());

                $this->Log->save($dados_log);

                if ($alter_ge) {
                    $this->CustomerGeLog->save($dados_ge_log);
                }

                $this->Flash->set(__('O cliente foi alterado com sucesso'), ['params' => ['class' => 'alert alert-success']]);

                $this->redirect("/customers/edit/" . $id);
            } else {
                $mensagem = '';
                foreach ($this->Customer->validationErrors as $key => $value) {
                    $mensagem .= ucfirst($key) . ': ' . implode(', ', $value) . '.<br>';
                }
                $this->Flash->set(__($mensagem), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->Customer->validationErrors;
        $this->request->data = $this->Customer->read();
        $this->Customer->validationErrors = $temp_errors;
        $this->request->data['Customer']['created'] = $this->request->data['Customer']['created'];


        $activityAreas = $this->ActivityArea->find('list', ['conditions' => ['ActivityArea.status_id' => 1], 'order' => 'ActivityArea.name']);
        $sellers = $this->Seller->find('list', ['order' => 'Seller.name']);
        $codFranquias = $this->Resale->find('list', ['conditions' => ['Resale.status_id' => 1], ['order' => 'Resale.nome_fantasia']]);

        if ($this->request->data['Status']['id'] != 6) {
            $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2, 'not' => ['Status.id' => 6]], 'order' => 'Status.name']);
        } else {
            $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
        }

        $is_admin = CakeSession::read("Auth.User.Group.name") == 'Administrador';

        // usado para fazer login no site com o bypass, NAO ALTERAR!!!
        $hash = base64_encode($this->request->data['Customer']['codigo_associado']);
        $this->set('hash', rawurlencode($hash));

        $this->set('canEditNfseObs', $this->Permission->check(84, "escrita"));
        $this->set('action', $this->request->data['Customer']['nome_secundario']);
        $this->set('form_action', 'edit');
        $this->set(compact('statuses', 'id', 'codFranquias', 'activityAreas', 'sellers', 'is_admin'));

        $this->render("add");
    }

    public function get($customer_id) {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $cliente = $this->Customer->find('first', ['conditions' => ['Customer.id' => $customer_id]]);

        echo json_encode($cliente);
    }

    public function delete($id)
    {
        $this->Permission->check(3, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->Customer->id = $id;

        $data = ['Customer' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->Customer->save($data)) {
            $this->Flash->set(__('O cliente foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function duplicate($id)
    {
        $this->autoRender = false;
        $this->Permission->check(52, 'escrita') ? '' : $this->redirect('/not_allowed');

        $last_code = $this->Customer->query('SELECT * from customers Customer where Customer.codigo_associado is not null order by Customer.id desc limit 1');
        if (empty($last_code[0])) {
            $code = 1;
        } else {
            $code = $last_code[0]['Customer']['codigo_associado'] + 1;
        }

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $antigo = $this->Customer->read();
        unset($antigo['Customer']['id'], $antigo['Customer']['created'], $antigo['Customer']['user_creator_id'], $antigo['Customer']['updated'], $antigo['Customer']['user_updated_id']);

        $antigo['Customer']['user_creator_id'] = CakeSession::read('Auth.User.id');
        $antigo['Customer']['codigo_associado'] = $code;
        $antigo['Customer']['status_id'] = 3;
        $antigo['Customer']['tipo_credor'] = 'C';

        $this->Customer->create();
        if ($this->Customer->save($antigo, false)) {
            $id = $this->Customer->id;

            $customer_user = ['CustomerUser' => [
                'name' => $antigo['Customer']['nome_primario'],
                'email' => $antigo['Customer']['email'],
                'username' => $antigo['Customer']['email'],
                'customer_id' => $id,
                'password' => $antigo['Customer']['senha'],
                'main_user' => 1,
            ]];

            $this->CustomerUser->save($customer_user, ['validate' => false]);

            $this->Flash->set(__('O cliente foi duplicado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } else {
            $mensagem = '';
            foreach ($this->Customer->validationErrors as $key => $value) {
                $mensagem .= ucfirst($key) . ': ' . implode(', ', $value) . '.<br>';
            }

            $this->Flash->set(__($mensagem), ['params' => ['class' => 'alert alert-danger']]);
        }

        $this->redirect(['action' => 'index']);
    }

    public function find_sellers()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $sellers = $this->Seller->find("all", ['conditions' => ['Seller.status_id' => 1, 'Seller.resale_id' => $_POST['resale_id']], 'order' => ['Seller.name' => "asc"]]);

        echo json_encode($sellers);
    }

    public function check_income()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $contas = $this->Income->find('count', ['conditions' => ['Income.customer_id' => $_POST['id'], 'Income.status_id' => [15, 19]]]);

        echo json_encode($contas);
    }

    public function check_documento()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $contas = $this->Customer->find('count', ['conditions' => ['Customer.id !=' => $_POST['id'], 'Customer.documento' => $_POST['doc']]]);

        echo json_encode($contas);
    }

    /*****************
                PLANOS
     ******************/

    public function plans($id)
    {
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['Customer.id' => $id], 'or' => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['Plan.description LIKE' => "%" . $_GET['q'] . "%", 'PlanCustomer.mensalidade LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['PlanCustomer.status_id' => $_GET['t']]);
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Planos';

        $data = $this->Paginator->paginate('PlanCustomer', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $plano = $this->PlanCustomer->find('count', ['conditions' => ['PlanCustomer.customer_id' => $id, 'PlanCustomer.status_id' => 1]]);

        $breadcrumb = [$cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 'Planos' => ''];
        $this->set(compact('status', 'data', 'action', 'id', 'plano', 'breadcrumb'));
    }

    public function add_plan($id)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['PlanCustomer']['user_creator_id'] = CakeSession::read('Auth.User.id');

            $this->PlanCustomer->create();
            if ($this->PlanCustomer->save($this->request->data)) {
                $this->Flash->set(__('O plano foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'plans/' . $id]);
            } else {
                $this->Flash->set(__('O plano não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Planos';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $this->Plan->unbindModel(['hasMany' => ['PlanProduct', 'PlanCustomer']], false);
        $plans = $this->Plan->find('all', ['conditions' => ['Plan.status_id' => 1], 'order' => 'Plan.description']);
        $priceTables = $this->PriceTable->find('list', ['conditions' => ['PriceTable.status_id' => 1]]);

        $cancelarPlano = $this->Permission->check(59, 'escrita');

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo Plano' => ''
        ];
        $this->set("form_action", "../customers/add_plan/" . $id);
        $this->set(compact('statuses', 'action', 'plans', 'priceTables', 'id', 'cancelarPlano', 'breadcrumb'));
    }

    public function edit_plan($id, $plan_id = null)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->PlanCustomer->id = $plan_id;

        if ($this->request->is(['post', 'put'])) {
            $this->request->data['PlanCustomer']['user_updated_id'] = CakeSession::read('Auth.User.id');

            $log_old_value = $this->request->data['log_old_value'];
            unset($this->request->data['log_old_value']);

            $log_old_value = $this->request->data["log_old_value"];
            unset($this->request->data["log_old_value"]);

            $dados_log = [
                'old_value' => $log_old_value,
                'new_value' => json_encode($this->request->data),
                'route' => 'customers/edit_plan',
                'log_action' => 'Alterou',
                'log_table' => 'PlanCustomer',
                'primary_key' => $plan_id,
                'parent_log' => $id,
                'user_type' => 'ADMIN',
                'user_id' => CakeSession::read('Auth.User.id'),
                'message' => 'O plano foi alterado com sucesso',
                'log_date' => date('Y-m-d H:i:s'),
                'data_cancel' => '1901-01-01',
                'usuario_data_cancel' => 0,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ];

            if ($this->PlanCustomer->save($this->request->data)) {
                $this->Log->save($dados_log);
                $this->Flash->set(__('O plano foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'plans/' . $id]);
            } else {
                $this->Flash->set(__('O plano não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->PlanCustomer->validationErrors;
        $this->request->data = $this->PlanCustomer->read();
        $this->PlanCustomer->validationErrors = $temp_errors;

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Planos';

        $plano = $this->PlanCustomer->find('first', ['conditions' => ['PlanCustomer.customer_id' => $id, 'PlanCustomer.status_id' => 1]]);

        if ($plano != null && $plan_id != $plano['PlanCustomer']['id']) {
            $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1, 'Status.id' => 2]]);
        } else {
            $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        }

        $this->Plan->unbindModel(['hasMany' => ['PlanProduct', 'PlanCustomer']], false);
        $plans = $this->Plan->find('all', ['conditions' => ['Plan.status_id' => 1], 'order' => 'Plan.description']);
        $priceTables = $this->PriceTable->find('list', ['conditions' => ['PriceTable.status_id' => 1]]);

        $cancelarPlano = $this->Permission->check(59, 'escrita');

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Plano' => '',
        ];
        $this->set("form_action", "../customers/edit_plan/" . $id);
        $this->set(compact('statuses', 'action', 'plans', 'priceTables', 'id', 'plan_id', 'cancelarPlano', 'breadcrumb', 'action'));

        $this->render("add_plan");
    }

    public function delete_plan($customer_id, $id)
    {
        $this->Permission->check(3, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->PlanCustomer->id = $id;

        $data = ['PlanCustomer' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->PlanCustomer->save($data)) {
            $this->Flash->set(__('O plano foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect($this->referer());
        }
    }

    public function update_status($status, $plan_id)
    {
        $this->PlanCustomer->id = $plan_id;

        $data = ['PlanCustomer' => ['status_id' => $status, 'user_updated_id' => CakeSession::read('Auth.User.id')]];

        if ($this->PlanCustomer->save($data)) {
            $this->Flash->set(__('Status alterado com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect($this->referer());
        }
    }

    public function get_plan_value()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $this->Plan->id = $_POST['id'];
        $plano = $this->Plan->read();

        echo json_encode($plano['Plan']['value']);
    }

    /*************************
                LOGIN CONSULTA
     **************************/

    public function login_consulta($id)
    {
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['Customer.id' => $id], 'or' => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['LoginConsulta.login LIKE' => "%" . $_GET['q'] . "%", 'LoginConsulta.senha LIKE' => "%" . $_GET['q'] . "%", 'LoginConsulta.descricao LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        if (!empty($_GET['tipo'])) {
            $condition['and'] = array_merge($condition['and'], ['LoginConsulta.tipo' => $_GET['tipo']]);
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Logins de Consulta';

        $data = $this->Paginator->paginate('LoginConsulta', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $usuarios = $this->CustomerUser->find('all', ['conditions' => ['CustomerUser.customer_id' => $id, 'CustomerUser.status_id' => 1]]);
        $usuarios_json = [];
        foreach ($usuarios as $key => $u) {
            $usuarios_json[] = [
                'id' => $u['CustomerUser']['id'],
                'email' => $u['CustomerUser']['email'],
                'cpf' => $u['CustomerUser']['cpf'],
                'filial' => $u['CustomerUser']['filial']
            ];
        }

        $tel = str_replace([' ', '-', '(', ')'], '', $cliente['Customer']['telefone1']);
        $ddd = substr($tel, 0, 2);
        $tel = substr($tel, 2);

        $tipos = [
            ['id' => '1', 'name' => 'Manual'],
            ['id' => '2', 'name' => 'Robô']
        ];

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Logins de Consulta' => '',
        ];
        $this->set(compact('status', 'data', 'action', 'id', 'usuarios', 'usuarios_json', 'cliente', 'ddd', 'tel', 'tipos', 'breadcrumb'));
    }

    public function add_login_consulta($id)
    {
        $this->Customer->id = $id;
        $customer = $this->Customer->read();

        $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->LoginConsulta->create();

            if ($this->LoginConsulta->validates()) {
                $senha = substr(sha1(time()), 0, 6);
                $this->request->data['LoginConsulta']['senha'] = $senha;
                $this->request->data['LoginConsulta']['user_creator_id'] = CakeSession::read('Auth.User.id');

                if ($this->request->data['LoginConsulta']['login_blindado'] == 0) {
                    $this->request->data['LoginConsulta']['login_blindado'] = 2;
                }

                if ($this->LoginConsulta->save($this->request->data)) {
                    $this->Flash->set(__('O login foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'login_consulta/' . $id]);
                } else {
                    var_dump($this->LoginConsulta);
                    die;
                    $this->Flash->set(__('O login não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O login não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Logins de Consulta';

        /*if ($cliente['LoginConsulta']['id'] != null) {
            $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1, 'Status.id' => 2]]);
        } else {*/
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        // }

        $plans = $this->Plan->find('list', ['conditions' => ['Plan.status_id' => 1], 'order' => 'Plan.description']);
        $priceTables = $this->PriceTable->find('list', ['conditions' => ['PriceTable.status_id' => 1]]);

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo login' => '',
        ];

        $this->set("form_action", "../customers/add_login_consulta/" . $id);
        $this->set(compact('statuses', 'action', 'plans', 'priceTables', 'id', 'breadcrumb', 'action'));
    }

    public function edit_login_consulta($id, $login_id = null)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->LoginConsulta->id = $login_id;

        if ($this->request->is(['post', 'put'])) {
            $this->request->data['LoginConsulta']['login_blindado'] = 2;
            $this->request->data['LoginConsulta']['user_updated_id'] = CakeSession::read('Auth.User.id');

            if ($this->LoginConsulta->save($this->request->data)) {
                $this->Flash->set(__('O login foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'login_consulta/' . $id]);
            } else {
                $this->Flash->set(__('O login não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->LoginConsulta->validationErrors;
        $this->request->data = $this->LoginConsulta->read();
        $this->LoginConsulta->validationErrors = $temp_errors;

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Logins de Consulta';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $usuarios = $this->CustomerUser->find('all', ['conditions' => ['CustomerUser.customer_id' => $id, 'CustomerUser.status_id' => 1]]);
        $usuarios_json = [];
        foreach ($usuarios as $key => $u) {
            $usuarios_json[] = ['id' => $u['CustomerUser']['id'], 'email' => $u['CustomerUser']['email'], 'cpf' => $u['CustomerUser']['cpf']];
        }

        $tel = str_replace([' ', '-', '(', ')'], '', $cliente['Customer']['telefone1']);
        $ddd = substr($tel, 0, 2);
        $tel = substr($tel, 2);

        $plans = $this->Plan->find('list', ['conditions' => ['Plan.status_id' => 1], 'order' => 'Plan.description']);
        $priceTables = $this->PriceTable->find('list', ['conditions' => ['PriceTable.status_id' => 1]]);

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar login' => '',
        ];
        $this->set("form_action", "../customers/edit_login_consulta/" . $id);
        $this->set(compact('statuses', 'action', 'plans', 'priceTables', 'id', 'login_id', 'tel', 'ddd', 'usuarios', 'usuarios_json', 'cliente', 'breadcrumb'));

        $this->render("add_login_consulta");
    }

    public function reenviar_senha_consulta($customer_id, $id)
    {
        $this->autoRender = false;
        $this->LoginConsulta->id = $id;
        $this->request->data = $this->LoginConsulta->read();

        $senha = substr(sha1(time()), 0, 6);
        $this->request->data['LoginConsulta']['senha'] = $senha;

        if ($this->LoginConsulta->save($this->request->data)) {
            $this->envia_email_consulta($this->request->data);
            $this->Flash->set(__('Senha reenviada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'edit_login_consulta/' . $customer_id . '/' . $id]);
        }
    }

    public function envia_email_consulta($data)
    {
        $dados = [
            'viewVars' => [
                'nome' => $data['Customer']['nome_primario'],
                'email' => $data['Customer']['email'],
                'username' => $data['LoginConsulta']['login'],
                'senha' => $data['LoginConsulta']['senha'],
                'link' => 'https://cliente.berh.com.br',
            ],
            'template' => 'nova_senha_login_consulta',
            'subject' => 'BeRH - Nova senha',
            'config' => 'default',
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => 'alert alert-danger']]);
        }
    }

    public function delete_login_consulta($customer_id, $id)
    {
        $this->Permission->check(3, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->LoginConsulta->id = $id;

        $data = ['LoginConsulta' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->LoginConsulta->save($data)) {
            $this->Flash->set(__('O login foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect($this->referer());
        }
    }

    public function salva_usuarios()
    {
        $customers = $this->Customer->query("SELECT c.id, c.nome_primario, c.email, c.senha
            FROM customers c
            LEFT JOIN customer_users cu ON cu.customer_id = c.id AND cu.data_cancel = '1901-01-01'
            WHERE cu.id IS NULL 
            AND c.status_id IN (3,4)
            AND c.data_cancel = '1901-01-01'
            limit 5
            ");

        foreach ($customers as $customer) {
            $senha = substr(sha1(time()), 0, 6);

            $customer_user = [
                'CustomerUser' => [
                    'name' => $customer['c']['nome_primario'],
                    'email' => $customer['c']['email'],
                    'username' => $customer['c']['email'],
                    'customer_id' => $customer['c']['id'],
                    'password' => $senha,
                    'main_user' => 1,
                ],
            ];
            print $customer['c']['email'] . " - " . $customer['c']['nome_primario'] . "</br>";

            $this->CustomerUser->create();
            $this->CustomerUser->save($customer_user, ['validate' => false]);

            $this->envia_email($customer_user);
        }
    }

    public function envia_email($data)
    {
        $dados = [
            'viewVars' => [
                'nome' => $data['CustomerUser']['name'],
                'email' => $data['CustomerUser']['email'],
                'username' => $data['CustomerUser']['email'],
                'senha' => $data['CustomerUser']['password'],
                'link' => 'https://cliente.berh.com.br',
            ],
            'template' => 'nova_senha_usuario_cliente',
            'subject' => 'BeRH - Nova senha',
            'config' => 'default',
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => 'alert alert-danger']]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function reenviar_senha($id, $user_id)
    {
        $this->CustomerUser->id = $user_id;
        $this->request->data = $this->CustomerUser->read();

        $senha = substr(sha1(time()), 0, 6);

        $this->request->data['CustomerUser']['password'] = $senha;

        if ($this->CustomerUser->save($this->request->data)) {
            $this->envia_email($this->request->data);

            $this->Flash->set(__('Senha reenviada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect("/customers/users/" . $id);
        }
    }


    public function boletos($id)
    {
        // $this->Permission->check(16, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = [
            'Order' => [
                'limit' => 10,
                'order' => ['Order.created' => 'desc'],
                'group' => 'Order.id',
                'contain' => [
                    'Status', 
                    'Customer', 
                    'Income.id',
                    'Income.vencimento',
                ],
            ]
        ];

        $condition = ["and" => [
            'Order.customer_id' => $id,
            //'Order.status_id' => 84,
        ], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $data = $this->Paginator->paginate('Order', $condition);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Pedido';
        $breadcrumb = [$cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'boletos', $id],
        'Boletos' => '',];
        $this->set(compact('data', 'action', 'breadcrumb', 'id'));
    }

    /*********************
                DOCUMENTOS
     **********************/
    public function documents($id)
    {
        $this->Permission->check(11, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = ['Document' => [
            'limit' => 100,
            'order' => ['Document.created' => 'desc'],
            
            ]
        ];

        $condition = ['and' => ['Customer.id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Document.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Documentos';

        $data = $this->Paginator->paginate('Document', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Documentos' => '',
        ];
        $this->set(compact('status', 'data', 'id', 'action', 'breadcrumb'));
    }

    public function add_document($id)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $this->Document->create();
            if ($this->Document->validates()) {
                $this->request->data['Document']['user_creator_id'] = CakeSession::read('Auth.User.id');
                if ($this->Document->save($this->request->data)) {
                    $this->Flash->set(__('O documento foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'documents/' . $id]);
                } else {
                    $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Documentos';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo Documento' => '',
        ];
        $this->set("form_action", "../customers/add_document/" . $id);
        $this->set(compact('statuses', 'action', 'id', 'breadcrumb'));
    }

    public function edit_document($id, $document_id = null)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');

        $this->Document->id = $document_id;

        if ($this->request->is(['post', 'put'])) {
            $this->Document->validates();
            if ($this->request->data['Document']['file']['name'] == '') {
                unset($this->request->data['Document']['file']);
            }
            $this->request->data['Document']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->Document->save($this->request->data)) {
                $this->Flash->set(__('O documento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'documents/' . $id]);
            } else {
                $this->Flash->set(__('O documento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->Document->validationErrors;
        $this->request->data = $this->Document->read();
        $this->Document->validationErrors = $temp_errors;

        $cliente = $this->Customer->findById($id);

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Documento' => '',
        ];
        $this->set("action", 'Documentos');
        $this->set("form_action", "../customers/edit_document/" . $id);
        $this->set(compact('statuses', 'id', 'document_id', 'breadcrumb'));

        $this->render("add_document");
    }

    public function delete_document($customer_id, $id)
    {
        $this->Permission->check(11, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->Document->id = $id;
        $this->request->data = $this->Document->read();

        $this->request->data['Document']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['Document']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->Document->save($this->request->data)) {
            unlink(APP . 'webroot/files/document/file/' . $this->request->data["Document"]["id"] . '/' . $this->request->data["Document"]["file"]);

            $this->Flash->set(__('O documento foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'documents/' . $customer_id]);
        }
    }

    /***********************
                NEGATIVACOES
     ************************/
    public function negativacoes($id)
    {
        $this->Permission->check(3, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['CadastroPefin.customer_id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CadastroPefin.nome LIKE' => "%" . $_GET['q'] . "%", 'NaturezaOperacao.nome LIKE' => "%" . $_GET['q'] . "%", 'CadastroPefin.coobrigado_nome LIKE' => "%" . $_GET['q'] . "%", 'CadastroPefin.documento LIKE' => "%" . $_GET['q'] . "%", 'CadastroPefin.numero_titulo LIKE' => "%" . $_GET['q'] . "%", 'CadastroPefin.valor LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        if (isset($_GET['f']) and $_GET['f'] == 1) {
            $condition['and'] = array_merge($condition['and'], ['CadastroPefin.customer_flag' => 1]);
        }

        if (isset($_GET['pdf'])) {
            $data = $this->CadastroPefin->find('all', ['conditions' => $condition]);

            $view = new View($this, false);
            $view->layout = false;

            $view->set(['pdf' => true, 'data' => $data]);
            $html = $view->render('pdf_negativacoes');

            $this->HtmltoPdf->convert($html, 'negativacoes');
        }

        if (isset($_GET['excel'])) {
            $dados = $this->CadastroPefin->find('all', ['conditions' => $condition]);

            $nome = 'negativacao_' . date('d_m_Y');

            $this->ExcelGenerator->gerarExcelNegativacao($nome, $dados);
            $this->redirect("/files/excel/" . $nome . ".xlsx");
        }

        $data = $this->Paginator->paginate('CadastroPefin', $condition);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Negativações';

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 7], 'order' => ['Status.name']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Negativações' => '',
        ];
        $this->set(compact('status', 'data', 'id', 'action', 'breadcrumb'));
    }

    public function add_negativacao($id)
    {
        // (CakeSession::read('Auth.User.id') == 1 || CakeSession::read('Auth.User.id') == 6) ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CadastroPefin->create();
            $this->CadastroPefin->validates();
            $this->request->data['CadastroPefin']['user_creator_id'] = CakeSession::read('Auth.User.id');
            $this->request->data['CadastroPefin']['status_id'] = 22;
            $this->request->data['CadastroPefin']['product_id'] = 2; // provisorio
            $this->request->data['CadastroPefin']['customer_id'] = $id;

            $temCoobrigado = $this->request->data['CadastroPefin']['tem_coobrigado'];

            if ($temCoobrigado == 1) {
                unset($this->request->data['CadastroPefin']['tem_coobrigado']);
            }

            // Se a Natureza for "Dividas Cheq" não precisa validar já que os campos não vão aparecer pro usuario
            if ($this->request->data['CadastroPefin']['natureza_operacao_id'] != 23) {
                unset($this->CadastroPefin->validate['num_banco'], $this->CadastroPefin->validate['num_agencia'], $this->CadastroPefin->validate['num_conta_corrente'], $this->CadastroPefin->validate['num_cheque'], $this->CadastroPefin->validate['alinea']);
            } else {
                unset($this->CadastroPefin->validate['nosso_numero'], $this->CadastroPefin->validate['numero_titulo']);
            }

            if ($this->CadastroPefin->save($this->request->data)) {
                if ($temCoobrigado == 1) {
                    $dados_coobrigado = [];
                    foreach ($this->request->data['coobrigado_tipo_pessoa'] as $key => $tipo_pessoa) {
                        $dados_temp['CadastroPefin'] = $this->request->data['CadastroPefin'];
                        $dados_temp['CadastroPefin']['coobrigado_tipo_pessoa'] = $tipo_pessoa;
                        $dados_temp['CadastroPefin']['coobrigado_documento'] = $this->request->data['coobrigado_documento'][$key];
                        $dados_temp['CadastroPefin']['coobrigado_nome'] = $this->request->data['coobrigado_nome'][$key];
                        $dados_temp['CadastroPefin']['tem_coobrigado'] = 1;
                        $dados_temp['CadastroPefin']['principal_id'] = $this->CadastroPefin->id;

                        $dados_coobrigado[] = $dados_temp;
                    }

                    $this->CadastroPefin->create();
                    $this->CadastroPefin->saveMany($dados_coobrigado);
                }

                $this->Flash->set(__('A negativação foi salva com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index']);
            } else {
                $mensagem = '';
                foreach ($this->CadastroPefin->validationErrors as $key => $value) {
                    $mensagem .= ucfirst($key) . ': ' . implode(', ', $value) . '.<br>';
                }
                $this->Flash->set(__($mensagem), ['params' => ['class' => 'alert alert-danger']]);
            }
        }
        $naturezaOperacaos = $this->NaturezaOperacao->find('list', ['order' => ['NaturezaOperacao.nome']]);

        $this->set("action", "Nova negativação");
        $this->set("form_action", "../customers/add_negativacao/" . $id);
        $this->set("acao", "add");
        $this->set(compact('statuses', 'naturezaOperacaos', 'id'));
    }

    public function baixar_negativacoes($id)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $this->CadastroPefin->updateAll(
            ['CadastroPefin.motivo_baixa_id' => 33, 'CadastroPefin.status_id' => 33, 'CadastroPefin.data_solic_baixa' => 'current_timestamp', 'CadastroPefin.user_updated_id' => CakeSession::read('Auth.User.id')],
            ['CadastroPefin.status_id' => 25, 'CadastroPefin.customer_id' => $id, 'CadastroPefin.data_cancel' => '1901-01-01']
        );

        $this->redirect($this->referer());
        $this->Flash->set(__('Negativações baixadas com sucesso'), ['params' => ['class' => 'alert alert-success']]);
    }

    /***********************
                LOG DE STATUS
     ************************/

    public function log_status($id = null)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;
        $this->Customer->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $cliente_antigo = $this->Customer->read();
            $this->request->data['Customer']['user_updated_id'] = CakeSession::read('Auth.User.id');
            $this->request->data['Customer']['updated'] = date('Y-m-d H:i:s');
            if ($this->Customer->save($this->request->data, ['validate' => false])) {
                if ($cliente_antigo['Customer']['status_id'] != $this->request->data['Customer']['status_id']) {
                    $data_movimentacao = ['MovimentacaoCredor' => ['status_id' => $this->request->data['Customer']['status_id'], 'customer_id' => $id, 'user_created_id' => CakeSession::read('Auth.User.id')]];

                    $this->MovimentacaoCredor->create();
                    $this->MovimentacaoCredor->save($data_movimentacao);
                }

                $this->Flash->set(__('O status do cliente foi alterado com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            } else {
                $this->Flash->set(__('O status do cliente não pode ser alterado com sucesso'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->Customer->validationErrors;
        $this->request->data = $this->Customer->read();
        $this->Customer->validationErrors = $temp_errors;

        /*if ($this->request->data['Status']['id'] != 6) {
            $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2, 'not' => ['Status.id' => 6]], 'order' => 'Status.name']);
        } else {
            $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
        }*/
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

        $condition = ['and' => ['MovimentacaoCredor.customer_id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != '') {
            $condition['or'] = array_merge($condition['or'], ['date_format(MovimentacaoCredor.data_movimentacao, "%d/%m/%Y")' => $_GET['q']]);
        }

        if (isset($_GET['s']) and $_GET['s'] != '') {
            $condition['or'] = array_merge($condition['or'], ['MovimentacaoCredor.status_id' => $_GET['s']]);
        }

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        if ($get_de != '' and $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], ['MovimentacaoCredor.created >=' => $de . ' 00:00:00', 'Customer.created <=' => $ate . ' 23:59:59']);
        }

        $data = $this->Paginator->paginate('MovimentacaoCredor', $condition);

        $breadcrumb = [
            $this->request->data['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Log de Status' => '',
        ];
        $this->set('action', 'Log de Status');
        $this->set('form_action', 'log_status');
        $this->set(compact('statuses', 'id', 'data', 'breadcrumb'));

        $this->render("log_status");
    }

    /***********************
                LOG GE
     ************************/
    public function log_ge($id = null)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = array_merge($this->paginate, [
            'order' => ['CustomerGeLog.created' => 'desc'] 
        ]);
        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $condition = ['and' => ['CustomerGeLog.customer_id' => $id], 'or' => []];

        $data = $this->Paginator->paginate('CustomerGeLog', $condition);

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Log GE' => '',
        ];

        $this->set('action', 'Log GE');
        $this->set(compact('id', 'data', 'breadcrumb'));
    }

    /*******************************
                CLIENTE NEGATIVACOES
     ********************************/
    public function negativacoes_cliente($id)
    {
        $this->Permission->check(3, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['CustomerPefin.customer_id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerPefin.nosso_numero LIKE' => "%" . $_GET['q'] . "%", 'NaturezaOperacao.nome' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Negativações';

        $data = $this->Paginator->paginate('CustomerPefin', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 10], 'order' => ['Status.name']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Negativações' => '',
        ];
        $this->set(compact('status', 'data', 'id', 'action', 'breadcrumb'));
    }

    public function add_negativacao_cliente($id)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerPefin->create();
            $this->CustomerPefin->validates();
            $this->request->data['CustomerPefin']['user_creator_id'] = CakeSession::read('Auth.User.id');
            $this->request->data['CustomerPefin']['customer_id'] = $id;

            if ($this->CustomerPefin->save($this->request->data)) {
                $this->Flash->set(__('A negativação foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'negativacoes_cliente/' . $id]);
            } else {
                $this->Flash->set(__('A negativação não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Negativações';
        $naturezaOperacaos = $this->NaturezaOperacao->find('list', ['order' => ['NaturezaOperacao.nome']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Nova Negativação' => '',
        ];
        $this->set("form_action", "../customers/add_negativacao_cliente/" . $id);
        $this->set(compact('statuses', 'action', 'id', 'naturezaOperacaos', 'breadcrumb'));
    }

    public function edit_negativacao_cliente($id, $negativacao_id = null)
    {
        $this->Permission->check(3, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->CustomerPefin->id = $negativacao_id;
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerPefin->validates();
            $this->request->data['CustomerPefin']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->CustomerPefin->save($this->request->data)) {
                $this->Flash->set(__('A negativação foi alterada com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            } else {
                $this->Flash->set(__('A negativação não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->CustomerPefin->validationErrors;
        $this->request->data = $this->CustomerPefin->read();
        $this->CustomerPefin->validationErrors = $temp_errors;

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = $cliente['Customer']['nome_secundario'] . ' - Nova negativação';
        $naturezaOperacaos = $this->NaturezaOperacao->find('list', ['order' => ['NaturezaOperacao.nome']]);

        $this->set("form_action", "../customers/edit_negativacao_cliente/" . $id);
        $this->set(compact('statuses', 'action', 'id', 'naturezaOperacaos', 'negativacao_id'));

        $this->render("add_negativacao_cliente");
    }

    /*******************************
                DESCONTOS
     ********************************/

    public function descontos($id)
    {
        $this->Permission->check(49, 'leitura') ? '' : $this->redirect('/not_allowed');
        $condition = ['and' => ['CustomerDiscount.customer_id' => $id], 'or' => []];

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        if ($get_de != '' and $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], ["CustomerDiscount.expire_date BETWEEN '" . $de . "' AND '" . $ate . "'"]);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['CustomerDiscount.status_id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('CustomerDiscount', $condition);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
        $this->Customer->id = $id;
        $temp_errors = $this->Customer->validationErrors;
        $cliente = $this->Customer->read();
        $this->Customer->validationErrors = $temp_errors;

        $action = 'Descontos';

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Descontos' => '',
        ];
        $this->set(compact('id', 'data', 'status', 'cliente', 'action', 'breadcrumb'));
    }

    public function add_desconto($id)
    {
        $this->Permission->check(49, 'escrita') ? '' : $this->redirect('/not_allowed');

        if ($this->request->is(['post', 'put'])) {
            $this->request->data['CustomerDiscount']['customer_id'] = $id;
            // $disc = str_replace(".", "", $this->request->data["CustomerDiscount"]['discount']);
            // $this->request->data["CustomerDiscount"]['discount'] = str_replace(",", ".", $disc);
            $this->request->data['CustomerDiscount']['user_created_id'] = CakeSession::read('Auth.User.id');

            $this->CustomerDiscount->create();
            if ($this->CustomerDiscount->save($this->request->data)) {
                $this->Flash->set(__('O desconto foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect("/customers/edit_desconto/" . $id . "/" . $this->CustomerDiscount->id);
            } else {
                $this->Flash->set(__('O desconto não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $temp_errors = $this->Customer->validationErrors;
        $cliente = $this->Customer->read();
        $this->Customer->validationErrors = $temp_errors;

        $form_action = "../customers/add_desconto/" . $id;
        $action = 'Descontos';
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $acao = 'add';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo Desconto' => '',
        ];
        $this->set(compact('id', 'data', 'form_action', 'action', 'statuses', 'acao', 'breadcrumb'));
    }

    public function edit_desconto($id, $desconto_id = null)
    {
        $this->Permission->check(49, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($desconto_id == null) {
            exit('Parâmetros não fornecidos');
        }

        $this->CustomerDiscount->id = $desconto_id;
        $mensagem = '';

        if ($this->request->is(['post', 'put'])) {
            $this->request->data['CustomerDiscount']['user_updated_id'] = CakeSession::read('Auth.User.id');
            $this->request->data['CustomerDiscount']['updated'] = date('Y-m-d H:i:s');
            $disc = str_replace('.', '', $this->request->data['CustomerDiscount']['discount']);
            $this->request->data['CustomerDiscount']['discount'] = str_replace(',', '.', $disc);

            if (!empty($this->request->data['product'])) {
                $this->CustomerDiscountsProduct->create();
                $produto['CustomerDiscountsProduct']['customer_discount_id'] = $desconto_id;
                $produto['CustomerDiscountsProduct']['product_id'] = $this->request->data['product'];
                if (!$this->CustomerDiscountsProduct->save($produto)) {
                    foreach ($this->CustomerDiscount->validationErrors as $key => $value) {
                        $mensagem .= ucfirst($key) . ': ' . implode(', ', $value) . '.<br>';
                    }
                }
            }

            if ($this->CustomerDiscount->validates()) {
                if ($this->CustomerDiscount->save($this->request->data)) {
                    $this->Flash->set(__('O cliente foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect("/customers/edit_desconto/" . $id . "/" . $desconto_id);
                } else {
                    foreach ($this->CustomerDiscount->validationErrors as $key => $value) {
                        $mensagem .= ucfirst($key) . ': ' . implode(', ', $value) . '.<br>';
                    }
                    $this->Flash->set(__($mensagem), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('O desconto não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->CustomerDiscount->validationErrors;
        $this->request->data = $this->CustomerDiscount->read();
        $this->CustomerDiscount->validationErrors = $temp_errors;

        $data = $this->CustomerDiscountsProduct->find('all', ['conditions' => ['CustomerDiscountsProduct.customer_discount_id' => $desconto_id], 'order' => ['CustomerDiscountsProduct.product_id' => 'asc']]);

        $ids = '';
        foreach ($data as $value) {
            $ids .= $value['Product']['id'] ? $value['Product']['id'] . ',' : '';
        }
        $ids = substr($ids, 0, -1);

        $produtos = $this->Product->find("all", [
            'conditions' => ["Product.id NOT IN (" . ($ids != '' ? $ids : 0) . ")"],
            "fields" => ["Product.id", "Product.name"],
            "order" => ["Product.name" => "asc"],
            'recursive' => -1
        ]);

        $form_action = "../customers/edit_desconto/" . $id . "/" . $desconto_id;
        $action = 'Descontos';
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $acao = 'edit';

        $breadcrumb = [
            $this->request->data['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Desconto' => '',
        ];
        $this->set(compact('id', 'desconto_id', 'data', 'form_action', 'action', 'statuses', 'acao', 'produtos', 'data', 'breadcrumb'));
        $this->render('add_desconto');
    }

    public function delete_desconto($id, $desconto_id = null)
    {
        if ($desconto_id == null) {
            exit('Parâmetros não fornecidos');
        }
        $this->Permission->check(3, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->CustomerDiscount->id = $desconto_id;

        $data = ['CustomerDiscount' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->CustomerDiscount->save($data)) {
            $this->Flash->set(__('O desconto foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'descontos/' . $id]);
        }
    }

    public function delete_produto_desconto($id, $desconto_id = null, $produto_id = null)
    {
        if ($desconto_id == null || $produto_id == null) {
            exit('Parâmetros não fornecidos');
        }
        $this->Permission->check(3, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->CustomerDiscountsProduct->id = $produto_id;

        $data = ['CustomerDiscountsProduct' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->CustomerDiscountsProduct->save($data)) {
            $this->Flash->set(__('O desconto foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'edit_desconto/' . $id . '/' . $desconto_id]);
        }
    }

    public function create_logon_serasa()
    {
        $params = [
            "email" => $this->request['data']['email'], "cnpj_completo" => $this->request['data']['cnpj'],
            "cnpj_indireto" => $this->request['data']['cnpj'], "filial" => $this->request['data']['filial'],
            "cep" => $this->request['data']['cep'], "numero" => $this->request['data']['numero'],
            "cpf" => $this->request['data']['cpf'], "ddd" => $this->request['data']['ddd'], "tel" => $this->request['data']['tel'],
            "ramal" => $this->request['data']['ramal'], "produtos" => $this->request['data']['produtos'],
            'razao' => $this->request['data']['razao'], 'logradouro' => $this->request['data']['logradouro'],
            'complemento' => $this->request['data']['complemento'], 'bairro' => $this->request['data']['bairro'],
            'cidade' => $this->request['data']['cidade'], 'uf' => $this->request['data']['uf'],
            'txtLogon' => $this->request['data']['txtLogon'],
        ];

        $id = $this->request['data']['id'];

        $msg = $this->Robo->gerar_logon($params);

        $msg = str_replace('O campo Logradouro é obrigatório!', '', $msg);
        $msg = str_replace('CEP não encontrado na base dos correios', '', $msg);
        $msg = str_replace('Já existe um contato cadastrado para esta filial. caso necessário altere o contato existente na filial.', '', $msg);

        $pcs = explode('criado', $msg);
        $login = trim(str_replace('Logon ', '', $pcs[0]));

        $this->LoginConsulta->create();

        $this->request->data['LoginConsulta']['status_id'] = 1;
        $this->request->data['LoginConsulta']['customer_id'] = $id;
        $this->request->data['LoginConsulta']['tipo'] = 2;
        $this->request->data['LoginConsulta']['descricao'] = $login . ' - criado via robô';
        $this->request->data['LoginConsulta']['login'] = $login;
        $this->request->data['LoginConsulta']['customer_user_id'] = $this->request['data']['contato'];
        $this->request->data['LoginConsulta']['filial'] = $this->request['data']['filial'];
        $this->request->data['LoginConsulta']['user_creator_id'] = CakeSession::read('Auth.User.id');

        $this->LoginConsulta->save($this->request->data);

        $this->Flash->set($msg, ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'login_consulta/' . $id]);
    }

    public function delete_logon_serasa()
    {
        $login = $this->request->data['login'];
        $cliente_id = $this->request->data['cliente_id'];
        $id = $this->request->data['id'];

        $params = [
            'client_logon' => $login,
        ];

        $msg = $this->Robo->excluir_logon($params);

        if (strpos($msg, 'COM SUCESSO') !== false) {
            $this->request->data['LoginConsulta']['id'] = $id;
            $this->request->data['LoginConsulta']['status_serasa'] = 3;

            $this->LoginConsulta->save($this->request->data);
        }

        $this->Flash->set($msg, ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'login_consulta/' . $cliente_id]);
    }

    public function act_deact_serasa()
    {
        $login = $this->request->data['login'];
        $cliente_id = $this->request->data['cliente_id'];
        $id = $this->request->data['id'];
        $status_serasa = $this->request->data['status_serasa'];

        $params = [
            'client_logon' => $login,
        ];

        $msg = $this->Robo->activate_deactivate_logon($params);

        if (strpos($msg, 'COM SUCESSO') !== false) {
            $this->request->data['LoginConsulta']['id'] = $id;
            $this->request->data['LoginConsulta']['status_serasa'] = $status_serasa == 1 ? 2 : 1;

            $this->LoginConsulta->save($this->request->data);
        }

        $this->Flash->set($msg, ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'login_consulta/' . $cliente_id]);
    }



    public function reset_senha_logon_serasa()
    {
        $login = $this->request->data['login'];
        $cliente_id = $this->request->data['cliente_id'];

        $params = [
            "email" => $this->request['data']['email'], "cnpj_completo" => $this->request['data']['cnpj'],
            "cnpj_indireto" => $this->request['data']['cnpj'], "filial" => $this->request['data']['hidden_filial'],
            "client_logon" => $login, 'razao' => $this->request['data']['razao']
        ];

        $msg = $this->Robo->reset_password($params);

        $this->Flash->set($msg, ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'login_consulta/' . $cliente_id]);
    }

    public function add_access_logon_serasa()
    {
        $login = $this->request->data['login'];
        $cliente_id = $this->request->data['cliente_id'];

        $params = [
            'client_logon' => $login, 'produtos' => $this->request['data']['produtos'],
        ];

        $msg = $this->Robo->add_access_logon($params);

        $msg_type = 'alert-success';
        if ($msg == '') {
            $msg = 'Erro ao processar requisição';
            $msg_type = 'alert-warning';
        }

        $this->Flash->set($msg, 'default', ['class' => "alert " . $msg_type]);
        $this->redirect(['action' => 'login_consulta/' . $cliente_id]);
    }

    public function remove_access_logon_serasa()
    {
        $login = $this->request->data['login'];
        $cliente_id = $this->request->data['cliente_id'];

        $params = [
            'client_logon' => $login, 'produtos' => $this->request['data']['produtos'],
        ];

        $msg = $this->Robo->remove_access_logon($params);

        $msg_type = 'alert-success';
        if ($msg == '') {
            $msg = 'Erro ao processar requisição';
            $msg_type = 'alert-warning';
        }

        $this->Flash->set($msg, 'default', ['class' => "alert " . $msg_type]);
        $this->redirect(['action' => 'login_consulta/' . $cliente_id]);
    }

    public function simulate_access_serasa()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $params = [
            'cnpj_indireto' => $this->request['data']['cnpj'],
        ];

        $msg = $this->Robo->simulate_filter($params);

        echo json_encode(['message' => $msg]);
    }

    public function reset_blindado_serasa()
    {
        $login = $this->request->data['login'];
        $cliente_id = $this->request->data['cliente_id'];

        $params = [
            'client_logon' => $login,
        ];

        $msg = $this->Robo->reset_shield($params);

        $this->Flash->set($msg, ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'login_consulta/' . $cliente_id]);
    }

    /*********************
            ARQUIVOS
     **********************/
    public function customers_files()
    { 
        ini_set('pcre.backtrack_limit', '15000000');
        ini_set('memory_limit', '-1');
        
        $this->Paginator->settings = ['CustomerFile' => [
            'limit' => 100,
            'order' => ['CustomerFile.created' => 'desc'],
            
            ]
        ];

        $condition = ['and' => ['Customer.cod_franquia' => CakeSession::read('Auth.User.resales')], 'or' => []];
        
        if (!$this->Permission->check(80, "leitura")) {
            $condition['and'] = array_merge($condition['and'], ['Customer.seller_id' => CakeSession::read('Auth.User.id')]);
        }

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerFile.file LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $action = 'Arquivos';

        $data = $this->Paginator->paginate('CustomerFile', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 21]]);

        $this->set(compact('status', 'data', 'action'));
    }

    public function files($id)
    {
        $this->Permission->check(11, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = ['CustomerFile' => [
            'limit' => 100,
            'order' => ['CustomerFile.created' => 'desc'],
            
            ]
        ];

        $condition = ['and' => ['Customer.id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerFile.file LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Arquivos';

        $data = $this->Paginator->paginate('CustomerFile', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 21]]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Arquivos' => '',
        ];
        $this->set(compact('status', 'data', 'id', 'action', 'breadcrumb'));
    }

    public function add_file($id)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerFile->create();
            if ($this->CustomerFile->validates()) {
                $this->request->data['CustomerFile']['user_creator_id'] = CakeSession::read('Auth.User.id');
                if ($this->CustomerFile->save($this->request->data)) {
                    $this->Flash->set(__('O arquivo foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'files/' . $id]);
                } else {
                    $this->Flash->set(__('O arquivo não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('O arquivo não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Arquivos';

        $orders = $this->Order->find('list', [
            'fields' => ['Order.id'], // Ajus' conforme o campo que você deseja exibir
            
        ]);

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 21]]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo Arquivo' => '',
        ];
        $this->set("form_action", "../customers/add_file/" . $id);
        $this->set(compact('statuses', 'action', 'id', 'breadcrumb','orders'));
    }

    public function edit_file($id, $file_id = null)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->CustomerFile->id = $file_id;
    
        // Carregar os dados do arquivo antes do request para checar o status
        $fileData = $this->CustomerFile->read();
    
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerFile->validates();
            $this->request->data['CustomerFile']['user_updated_id'] = CakeSession::read('Auth.User.id');
            
            if ($this->request->data['CustomerFile']['status_id'] == 101 || $this->request->data['CustomerFile']['status_id'] == 102) {
                $this->request->data['CustomerFile']['user_finalizado_id'] = CakeSession::read('Auth.User.id');
                $this->request->data['CustomerFile']['data_finalizacao'] =  date('Y-m-d H:i:s');
            }
    
            if ($this->CustomerFile->save($this->request->data)) {
                $this->Flash->set(__('O arquivo foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'files/' . $id]);
            } else {
                $this->Flash->set(__('O arquivo não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $orders = $this->Order->find('list', [
            'fields' => ['Order.id'], // Ajus' conforme o campo que você deseja exibir
            
        ]);
    
        $temp_errors = $this->CustomerFile->validationErrors;
        $this->request->data = $this->CustomerFile->read();
        $this->CustomerFile->validationErrors = $temp_errors;
    
        $this->Customer->id = $id;
        $cliente = $this->Customer->read();
    
        // Adicionar o status do arquivo atual para ser manipulado na view
        $currentStatus = isset($fileData['CustomerFile']['status_id']) ? $fileData['CustomerFile']['status_id'] : null;
    
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 21]]);
    
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Arquivo' => '',
        ];
        
        $this->set("action", 'Arquivos');
        $this->set("form_action", "../customers/edit_file/" . $id);
        $this->set(compact('statuses', 'id', 'file_id', 'breadcrumb', 'currentStatus','orders'));
    
        $this->render("add_file");
    }
    

    public function delete_file($customer_id, $id)
    {
        $this->Permission->check(11, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->CustomerFile->id = $id;
        $this->request->data = $this->CustomerFile->read();

        $this->request->data['CustomerFile']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['CustomerFile']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->CustomerFile->save($this->request->data)) {
            $this->Flash->set(__('O arquivo foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'files/' . $customer_id]);
        }
    }

    /*********************
            Extrato
     **********************/
    public function extrato($id, $tipo = null)
    {
        $this->Permission->check(67, 'leitura') ? '' : $this->redirect('/not_allowed');

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        if ($tipo == 'grupo_economico') {
            $this->Paginator->settings = [
                'Order' => [
                    'fields' => [
                        'Order.*',
                        'Income.*',
                        'Status.*',
                        'Creator.*',
                        'CustomerCreator.*',
                        'EconomicGroup.*',
                    ],
                    'limit' => 25,
                    'group' => 'EconomicGroup.id',
                    'order' => ['Order.created' => 'asc'],
                ]
            ];
            
            $condition = ['and' => ['Order.customer_id' => $id, 'EconomicGroup.id != ' => null], 'or' => []];
        } else {
            $this->Paginator->settings = [
                'Order' => [
                    'fields' => [
                        'Order.*',
                        'Income.*',
                        'Status.*',
                        'Creator.*',
                        'CustomerCreator.*',
                        'EconomicGroup.*',
                    ],
                    'limit' => 25,
                    'order' => ['Order.created' => 'asc'],
                ]
            ];

            $condition = ['and' => ['Order.customer_id' => $id], 'or' => []];
        }
        
        $data = [];
        $saldo = 0;
    
        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%", 
            ]);
        }
    
        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], [
                'Order.status_id' => $_GET['t']
            ]);
        }
    
        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';
    
        if ($get_de != '' and $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));
    
            $condition['and'] = array_merge($condition['and'], [
                'Order.created between ? and ?' => [$de . ' 00:00:00', $ate . ' 23:59:59']
            ]);
            
            $data = $this->Paginator->paginate('Order', $condition);

            $de_anterior = date('Y-m-d', strtotime('-1 day '.$de));

            $orderDesconto = $this->Order->find('all', ['conditions' => ['Order.customer_id' => $id, "Order.created <= '{$de_anterior}'"], 'fields' => 'SUM(Order.desconto) as valor_desconto']);
            $orderSaldo = $this->Order->find('all', ['conditions' => ['Order.customer_id' => $id, "Order.created <= '{$de_anterior}'"], 'fields' => 'SUM(Order.saldo) as valor_saldo']);

            $saldo = ($orderSaldo[0][0]['valor_saldo'] - $orderDesconto[0][0]['valor_desconto']);

            if (isset($cliente['Customer']['dt_economia_inicial_nao_formatado'])) {
                if ($cliente['Customer']['dt_economia_inicial_nao_formatado'] <= $de_anterior) {
                    $saldo = $cliente['Customer']['economia_inicial_not_formated'];
                }
            }
        }

        $first_order = $this->Order->find('first', ['conditions' => ['Order.customer_id' => $id], 'fields' => 'MIN(Order.created) as data_criacao']);

        $totalOrders = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'fields' => [
                'count(1) as qtde_pedidos',
                "IFNULL(
                    (SELECT COUNT(1)
                        FROM (
                            SELECT COUNT(1), o.customer_id 
                                FROM orders o 
                                    INNER JOIN order_items i ON i.order_id = o.id 
                                    INNER JOIN customer_users c ON c.id = i.customer_user_id 
                                WHERE i.data_cancel = '1901-01-01 00:00:00' 
                                        AND o.data_cancel = '1901-01-01 00:00:00' 
                                GROUP BY c.cpf, o.customer_id 
                        ) rw 
                        WHERE rw.customer_id = Customer.id 
                    ), 
                0) as qtde_order_customers",
                'sum(Order.subtotal) as subtotal',
                'sum(Order.transfer_fee) as transfer_fee',
                'sum(Order.commission_fee) as commission_fee',
                'sum(Order.desconto) as desconto',
                'sum(Order.saldo) as saldo',
                'sum(Order.total) as total',
                'sum(Order.total_saldo) as total_saldo',
                "(SELECT coalesce(sum(b.total), 0) as total_balances 
                    FROM order_balances b 
                        INNER JOIN orders o ON o.id = b.order_id 
                    WHERE o.customer_id = Customer.id 
                            AND b.tipo = 1 
                            AND b.data_cancel = '1901-01-01 00:00:00' 
                            AND o.data_cancel = '1901-01-01 00:00:00' 
                ) as total_balances",
                'sum(Order.tpp_fee) as vl_tpp',
            ],
            'conditions' => $condition,
            'recursive' => -1
        ]);

        $data_orders = $this->Order->find('all', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'fields' => [
                'Order.id',
            ],
            'conditions' => $condition,
            'order' => ['Order.created' => 'asc'],
            'recursive' => -1
        ]);
        
        $total_fee_economia         = 0;
        $total_vl_economia          = 0;
        $total_repasse_economia     = 0;
        $total_diferenca_repasse    = 0;
        $total_bal_ajuste_cred      = 0;
        $total_bal_ajuste_deb       = 0;
        $total_bal_inconsistencia   = 0;
        $total_vlca                 = 0;
        
        if ($data_orders) {
            for ($i = 0; $i < count($data_orders); $i++) {
                $data_extrato = $this->Order->getExtrato($data_orders[$i]["Order"]["id"]);

                $total_fee_economia         += $data_extrato['v_fee_economia'];
                $total_vl_economia          += $data_extrato['v_vl_economia'];
                $total_repasse_economia     += $data_extrato['v_repasse_economia'];
                $total_diferenca_repasse    += $data_extrato['v_diferenca_repasse'];
                $total_bal_ajuste_cred      += $data_extrato['v_total_bal_ajuste_cred'];
                $total_bal_ajuste_deb       += $data_extrato['v_total_bal_ajuste_deb'];
                $total_bal_inconsistencia   += $data_extrato['v_total_bal_inconsistencia'];
                $total_vlca                 += $data_extrato['v_total_vlca'];
            }
        }

        foreach ($data as &$item) {
            $item['Order']['extrato'] = $this->Order->getExtrato($item['Order']['id']);
        }

        unset($item);
    
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

        $action = 'Extrato';

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Extrato' => '',
        ];

        $this->set(compact('id', 'data', 'status' ,'action', 'breadcrumb', 'totalOrders', 'saldo', 'first_order', 'tipo'));
        $this->set(compact('total_fee_economia', 'total_vl_economia', 'total_repasse_economia', 'total_diferenca_repasse', 'total_bal_ajuste_cred', 'total_bal_ajuste_deb', 'total_bal_inconsistencia', 'total_vlca'));
    }
}
