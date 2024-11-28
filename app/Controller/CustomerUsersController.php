<?php
App::uses('ItineraryCSVParser', 'Lib');
App::uses('EconomicGroupCSVParser', 'Lib');

use League\Csv\Reader;

class CustomerUsersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'HtmltoPdf', 'ExcelGenerator', 'Robo'];
    public $uses = ['CustomerUser', 'Customer', 'Status', 'CustomerUserAddress', 'CustomerUserVacation', 
                    'CepbrEstado', 'AddressType', 'CustomerDepartment', 'CustomerPosition', 
                    'CustomerUserBankAccount', 'BankAccountType', 'CustomerUserItinerary', 'Benefit',
                    'CSVImport', 'CSVImportLine', 'CostCenter', 'SalaryRange', 'MaritalStatus', 'OrderItem', 'BankCode', 'EconomicGroup','Group'];

                    public $paginate = [
                        'CustomerUserAddress' => [
                            'limit' => 10, 
                            'order' => ['CustomerUserAddress.id' => 'asc']
                        ],
                        'CustomerUser' => [
                            'limit' => 20,
                            'order' => ['CustomerUser.name' => 'asc'],
                            'conditions' => []
                        ],
                        'OrderItem' => [
                            'limit' => 100, 
                            'order' => ['OrderItem.id' => 'asc'],
                            'fields' => ['OrderItem.*', 'CustomerUserItinerary.*', 'Benefit.*', 'Order.*'],
                            'joins' => [
                                [
                                    'table' => 'benefits',
                                    'alias' => 'Benefit',
                                    'type' => 'INNER',
                                    'conditions' => [
                                        'Benefit.id = CustomerUserItinerary.benefit_id'
                                    ]
                                ]
                            ]
                        ],
                        'CustomerUserBankAccount' => [
                            'limit' => 10,
                            'order' => ['CustomerUserBankAccount.id' => 'asc'],
                            'contain' => ['Status', 'BankCode', 'BankAccountType'] // Incluir a associação Status
                        ]
                    ];
                    

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /*******************
                USUARIOS
    ********************/
    public function index_users($id){
        $this->index($id, true);
        $this->render('index');
    }

    public function index($id, $is_admin = false)
    {
        ini_set('pcre.backtrack_limit', '15000000');
        ini_set('memory_limit', '-1');

        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;
    
        $condition = ["and" => ['CustomerUser.customer_id' => $id], "or" => []];
    
        if($is_admin){
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.is_admin' => 1]);
        }
    
        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.cpf LIKE' => "%".$_GET['q']."%",'CustomerUser.name LIKE' => "%".$_GET['q']."%", 'CustomerUser.email LIKE' => "%".$_GET['q']."%"]);
        }
    
        if (!empty($_GET["t"])) {
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.status_id' => $_GET['t']]);
        }
    
        if (!empty($_GET["cc"])) {
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.customer_cost_center_id' => $_GET['cc']]);
        }
    
        if (!empty($_GET["d"])) {
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.customer_departments_id' => $_GET['d']]);
        }
    
        $data = $this->Paginator->paginate('CustomerUser', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1], 'order' => 'Status.name']);
        $cost_centers = $this->CostCenter->find('all', ['conditions' => ['CostCenter.customer_id' => $id]]);
        $departments = $this->CustomerDepartment->find('all', ['conditions' => ['CustomerDepartment.customer_id' => $id]]);
    
        $this->Customer->id = $id;
        $cliente = $this->Customer->read();
    
        $action = $is_admin ? 'Usuários' : 'Beneficiários';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            $action => ''
        ];
    
        // Remova ou comente a linha abaixo
        // $this->ExcelGenerator->gerarExcelBeneficiario('RelatorioBeneficiario', $data);
    
        // $this->redirect('/private_files/baixar/excel/RelatorioBeneficiario_xlsx');
        $this->set(compact('data', 'action', 'id', 'status', 'breadcrumb', 'is_admin', 'cost_centers', 'departments'));
    }
    public function generate_excel_report($id)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(90);
        ini_set('max_execution_time', '-1'); 
    
        $data = $this->CustomerUser->find('all', [
            'conditions' => ['CustomerUser.customer_id' => $id],
            'joins' => [
                [
                    'table' => 'customer_users_economic_groups',
                    'alias' => 'CustomerUserEconomicGroup',
                    'type' => 'LEFT',
                    'conditions' => [
                        'CustomerUser.id = CustomerUserEconomicGroup.customer_user_id'
                    ]
                ],
                [
                    'table' => 'economic_groups',
                    'alias' => 'EconomicGroup',
                    'type' => 'LEFT',
                    'conditions' => [
                        'EconomicGroup.id = CustomerUserEconomicGroup.economic_group_id'
                    ]
                ],
                [
                    'table' => 'customer_positions',  
                    'alias' => 'CustomerPosition',    
                    'type' => 'LEFT',                 
                    'conditions' => [
                        'CustomerPosition.id = CustomerUser.customer_positions_id' 
                    ]
                ],
                [
                    'table' => 'salary_ranges', 
                    'alias' => 'SalaryRange', 
                    'type' => 'LEFT', 
                    'conditions' => [
                        'SalaryRange.id = CustomerUser.customer_salary_id'
                    ]
                ],
                [
                    'table' => 'marital_statuses', 
                    'alias' => 'MaritalStatus',
                    'type' => 'LEFT',
                    'conditions' => [
                        'MaritalStatus.id = CustomerUser.marital_status_id'
                    ]
                ],
                // Add other joins as necessary
            ],
            'fields' => [
                'CustomerDepartment.name',
                'CustomerPosition.name',
                'CustomerUser.customer_id',
                'CustomerUser.name', 
                'CustomerUser.matricula', 
                'CustomerUser.email', 
                'CustomerUser.tel', 
                'CustomerUser.cel', 
                'CustomerUser.cpf', 
                'CustomerUser.rg', 
                'CustomerUser.emissor_rg', 
                'CustomerUser.emissor_estado', 
                'CustomerUser.nome_mae', 
                'CustomerUser.status_id', 
                'CustomerUser.sexo', 
                'CustomerUser.data_nascimento', 
                'CustomerUser.customer_departments_id', 
                'CustomerUser.customer_positions_id', 
                'CustomerUser.customer_cost_center_id', 
                'CustomerUser.customer_salary_id', 
                'CustomerUser.marital_status_id', 
                'CustomerUser.economic_group_id', 
                'EconomicGroup.name',
                'EconomicGroup.document',
                'CostCenter.name', 
                'SalaryRange.range', 
                'MaritalStatus.status',
            ],
            'group' => ['CustomerUser.id', 'EconomicGroup.id'] 
        ]);
    //debug($data);die;
        $this->ExcelGenerator->gerarExcelBeneficiario('RelatorioBeneficiario', $data);
    
        $this->redirect('/private_files/baixar/excel/RelatorioBeneficiario.xlsx');
    }
    
    public function add_user($id){
        $this->add($id, true);
        $this->render('add');
    }

    public function add($id, $is_admin = false)
    {
        $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");

        $action = $is_admin ? 'Usuário' : 'Beneficiário';

        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUser->create();
            $this->CustomerUser->validates();

            $this->request->data['CustomerUser']['is_admin'] = $is_admin ? 1 : 0;

            $senha = substr(sha1(time()), 0, 6);

            $this->request->data['CustomerUser']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUser']['password'] = $senha;
            if ($this->CustomerUser->save($this->request->data)) {
                if($is_admin && isset($data['CustomerUser']['email'])){
                    $this->envia_email($this->request->data);
                }

                $urlAction = $is_admin ? 'edit_user/'.$id.'/' : 'edit/'.$id.'/';
                $this->Flash->set(__('O '.$action.' foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => $urlAction.$this->CustomerUser->id.'/?'.$this->request->data['query_string']]);
            } else {
                $this->Flash->set(__('O '.$action.' não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $estados = $this->CepbrEstado->find('list');
        $customer_departments = $this->CustomerDepartment->find('list', ['conditions' => ['CustomerDepartment.customer_id' => $id]]);
        $customer_positions = $this->CustomerPosition->find('list', ['conditions' => ['CustomerPosition.customer_id' => $id]]);
        $customer_cost_centers = $this->CostCenter->find('list', ['conditions' => ['CostCenter.customer_id' => $id]]);
        $customer_salaries = $this->SalaryRange->find('list', ['fields' => ['id', 'range']]);
        $marital_statuses = $this->MaritalStatus->find('list', ['fields' => ['id', 'status']]);
        $economicGroups = $this->EconomicGroup->find("list", ["conditions" => ["EconomicGroup.status_id" => 1, 'EconomicGroup.customer_id' => $id]]);

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo '.$action => ''
        ];
        $form_action = $is_admin ? "../customer_users/add/".$id.'/true' : "../customer_users/add/".$id;
        $this->set(compact('statuses', 'action', 'id', 'breadcrumb', 'estados', 'is_admin', 'form_action'));
        $this->set(compact('customer_departments', 'customer_positions', 'customer_cost_centers', 'customer_salaries', 'marital_statuses', 'economicGroups'));
    }

    public function edit_user($id, $user_id){
        $this->edit($id, $user_id, true);
    }

    public function edit($id, $user_id = null, $is_admin = false)
    {
        $this->Permission->check(65, "escrita") ? "" : $this->redirect("/not_allowed");
        $action = $is_admin ? 'Usuário' : 'Beneficiário';

        $this->CustomerUser->id = $user_id;
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['CustomerUser']['user_updated_id'] = CakeSession::read("Auth.User.id");

            if ($this->CustomerUser->save($this->request->data)) {
                $action = $is_admin ? 'index_users/'.$id.'/' : 'index/'.$id.'/';
                
                $this->Flash->set(__('O usuário foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => $action]);
            } else {
                $this->Flash->set(__('O usuário não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CustomerUser->validationErrors;
        $this->request->data = $this->CustomerUser->read();
        $this->CustomerUser->validationErrors = $temp_errors;
            
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();
        
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar '.$action => ''
        ];
        $estados = $this->CepbrEstado->find('list');
        $customer_departments = $this->CustomerDepartment->find('list', ['conditions' => ['CustomerDepartment.customer_id' => $id]]);
        $customer_positions = $this->CustomerPosition->find('list', ['conditions' => ['CustomerPosition.customer_id' => $id]]);
        $customer_cost_centers = $this->CostCenter->find('list', ['conditions' => ['CostCenter.customer_id' => $id]]);
        $customer_salaries = $this->SalaryRange->find('list', ['fields' => ['id', 'range']]);
        $marital_statuses = $this->MaritalStatus->find('list', ['fields' => ['id', 'status']]);
        $economicGroups = $this->EconomicGroup->find("list", ["conditions" => ["EconomicGroup.status_id" => 1, 'EconomicGroup.customer_id' => $id]]);

        // usado para fazer login no site com o bypass, NAO ALTERAR!!!
        $hash = base64_encode($this->request->data['CustomerUser']['email']);
        $this->set('hash', rawurlencode($hash));
        
        $form_action = $is_admin ? "/customer_users/edit/".$id.'/'.$user_id.'/true' : "/customer_users/edit/".$id.'/'.$user_id;
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'breadcrumb', 'estados', 'is_admin', 'form_action'));
        $this->set(compact('customer_departments', 'customer_positions', 'customer_cost_centers', 'customer_salaries', 'marital_statuses', 'economicGroups'));
            
        $this->render("add");
    }

    public function delete_user($customer_id, $id){
        $this->Permission->check(65, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUser->id = $id;
        $this->request->data = $this->CustomerUser->read();

        $this->request->data['CustomerUser']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['CustomerUser']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->CustomerUser->save($this->request->data)) {
            $this->Flash->set(__('O usuário foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'users/'.$customer_id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
        }
    }

    /*******************
                ENDEREÇOS
    ********************/
    public function addresses($id, $user_id){
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['CustomerUserAddress.customer_user_id' => $user_id], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['CustomerUserAddress.address_line LIKE' => "%".$_GET['q']."%", 'CustomerUserAddress.neighborhood LIKE' => "%".$_GET['q']."%", 'CustomerUserAddress.city LIKE' => "%".$_GET['q']."%", 'CustomerUser.cpf LIKE' => "%".$_GET['q']."%", 'CustomerUserAddress.state LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('CustomerUserAddress', $condition);

        $action = 'Endereços';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Endereços' => ''
        ];
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'user_id'));
    }

    public function add_address($id, $user_id)
    {
        $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserAddress->create();
            $this->CustomerUserAddress->validates();

            $this->request->data['CustomerUserAddress']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUserAddress']['customer_id'] = $id;
            $this->request->data['CustomerUserAddress']['customer_user_id'] = $user_id;

            if ($this->CustomerUserAddress->save($this->request->data)) {
                $this->Flash->set(__('O endereço foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'addresses/'.$id.'/'.$user_id]);
            } else {
                $this->Flash->set(__('O endereço não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Beneficiários';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $states = $this->CepbrEstado->find('list');
        $address_type = $this->AddressType->find('list', ['fields' => ['id', 'description']]);
        $estados = $this->CepbrEstado->find('list');
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customer_users', 'action' => 'edit', $id, $user_id],
            'Novo Endereço' => ''
        ];
        // $this->set("form_action", "../customers/add_user/".$id);
        $this->set(compact('statuses', 'action', 'id', 'breadcrumb', 'states', 'user_id', 'address_type', 'estados'));
    }

    public function edit_address($id, $user_id, $add_id)
    {
        $this->Permission->check(11, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserAddress->id = $add_id;
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserAddress->validates();
            $this->request->data['CustomerUserAddress']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->CustomerUserAddress->save($this->request->data)) {
                $this->Flash->set(__('O endereço foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'addresses/'.$id.'/'.$user_id]);
            } else {
                $this->Flash->set(__('O endereço não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CustomerUserAddress->validationErrors;
        $this->request->data = $this->CustomerUserAddress->read();
        $this->CustomerUserAddress->validationErrors = $temp_errors;
            
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Beneficiários';

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Usuário' => ''
        ];
        $estados = $this->CepbrEstado->find('list');
        $address_type = $this->AddressType->find('list', ['fields' => ['id', 'description']]);
        $this->set("form_action", "../customers/edit_user/".$id);
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'breadcrumb', 'estados', 'address_type'));
            
        $this->render("add_address");
    }

    /*******************
                FERIAS
    ********************/
    public function vacations($id, $user_id){
        $this->Permission->check(65, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['CustomerUserVacation.customer_user_id' => $user_id], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['CustomerUserVacation.data_inicio LIKE' => "%".$_GET['q']."%", 'CustomerUserVacation.data_fim LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('CustomerUserVacation', $condition);

        $action = 'Férias';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Férias' => ''
        ];
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'user_id'));
    }

    public function add_vacation($customer_id, $user_id)
    {
        // $this->Permission->check(65, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserVacation->create();
            $this->CustomerUserVacation->validates();

            $this->request->data['CustomerUserVacation']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUserVacation']['customer_id'] = $customer_id;
            $this->request->data['CustomerUserVacation']['customer_user_id'] = $user_id;

            if ($this->CustomerUserVacation->save($this->request->data)) {
                $this->Flash->set(__('As férias foram incluídas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'vacations/' . $customer_id . '/' . $user_id]);
            } else {
                $this->Flash->set(__('As férias não puderam ser salvas, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->Customer->id = $customer_id;
        $cliente = $this->Customer->read();

        $action = 'Férias';

        $states = $this->CepbrEstado->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customer_users', 'action' => 'edit', $user_id],
            'Novas Férias' => ''
        ];
        $this->set(compact('action', 'customer_id', 'breadcrumb', 'user_id'));
    }

    public function edit_vacation($customer_id, $user_id, $id_vacation)
    {
        // $this->Permission->check(65, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserVacation->id = $id_vacation;
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserVacation->validates();
            $this->request->data['CustomerUserVacation']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->CustomerUserVacation->save($this->request->data)) {
                $this->Flash->set(__('As férias foram alteradas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'vacations/' . $user_id]);
            } else {
                $this->Flash->set(__('As férias não puderam ser alteradas, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CustomerUserVacation->validationErrors;
        $this->request->data = $this->CustomerUserVacation->read();
        $this->CustomerUserVacation->validationErrors = $temp_errors;

        $this->Customer->id = $customer_id;
        $cliente = $this->Customer->read();

        $action = 'Férias';

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $this->Customer->id],
            'Alterar Conta Bancária' => ''
        ];

        $this->set("form_action", "../customers/edit_user/" . $user_id);
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'breadcrumb'));

        $this->render("add_vacation");
    }

    public function delete_vacation($customer_id, $user_id, $id)
    {
        // $this->Permission->check(65, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserVacation->id = $id;

        $this->request->data['CustomerUserVacation']['id'] = $id;
        $this->request->data['CustomerUserVacation']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['CustomerUserVacation']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->CustomerUserVacation->save($this->request->data)) {
            $this->Flash->set(__('As férias foram excluidas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'vacations/' . $customer_id . '/' . $user_id . '/?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
        }
    }

     /*******************
                Dados Bancários
    ********************/
    public function bank_info($id, $user_id){
        $this->Permission->check(65, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['CustomerUserBankAccount.customer_user_id' => $user_id], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['BankCode.name LIKE' => "%" . $_GET['q'] . "%", 'BankCode.code LIKE' => "%" . $_GET['q'] . "%", 'CustomerUserBankAccount.acc_number LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.acc_digit LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.branch_number LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.branch_digit LIKE' => "%".$_GET['q']."%"]);
        }
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $data = $this->Paginator->paginate('CustomerUserBankAccount', $condition);

        $action = 'Dados Bancários';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Dados Bancários' => ''
        ];
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'user_id','statuses'));
    }

    public function add_bank_info($id, $user_id)
    {
        $this->Permission->check(65, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserBankAccount->create();
            $this->CustomerUserBankAccount->validates();

            $this->request->data['CustomerUserBankAccount']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUserBankAccount']['customer_id'] = $id;
            $this->request->data['CustomerUserBankAccount']['customer_user_id'] = $user_id;

            if ($this->CustomerUserBankAccount->save($this->request->data)) {
                $this->Flash->set(__('O endereço foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'bank_info/'.$id.'/'.$user_id]);
            } else {
                $this->Flash->set(__('O endereço não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Beneficiários';

        $states = $this->CepbrEstado->find('list');
        $banks = $this->BankCode->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customer_users', 'action' => 'edit', $id, $user_id],
            'Nova Conta Bancária' => ''
        ];
        $this->set(compact('action', 'id', 'breadcrumb', 'states', 'user_id', 'bank_account_type', 'banks','statuses'));
    }

    public function edit_bank_info($id, $user_id, $id_bank)
    {
        $this->Permission->check(65, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserBankAccount->id = $id_bank;
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserBankAccount->validates();
            $this->request->data['CustomerUserBankAccount']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->CustomerUserBankAccount->save($this->request->data)) {
                $this->Flash->set(__('O endereço foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'bank_info/'.$id.'/'.$user_id]);
            } else {
                $this->Flash->set(__('O endereço não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CustomerUserBankAccount->validationErrors;
        $this->request->data = $this->CustomerUserBankAccount->read();
        $this->CustomerUserBankAccount->validationErrors = $temp_errors;
            
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Beneficiários';

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Conta Bancária' => ''
        ];
        $estados = $this->CepbrEstado->find('list');
        $banks = $this->BankCode->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $this->set("form_action", "../customer_users/edit_bank_info/".$id.'/'. $user_id . '/' . $id_bank);
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'breadcrumb', 'estados', 'bank_account_type', 'banks'));
            
        $this->render("add_bank_info");
    }

     /*******************
                Itinerário
    ********************/
    public function itineraries($id, $user_id){
        $this->Permission->check(65, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['CustomerUserItinerary.customer_user_id' => $user_id], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['Benefit.name LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('CustomerUserItinerary', $condition);
        $user = $this->Auth->user();
        $action = 'Dados Bancários';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Dados Bancários' => ''
        ];
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'user_id','user'));
    }

    public function add_itinerary($id, $user_id)
    {
        $this->Permission->check(65, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserItinerary->create();
            $this->CustomerUserItinerary->validates();

            $this->request->data['CustomerUserItinerary']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUserItinerary']['customer_id'] = $id;
            $this->request->data['CustomerUserItinerary']['customer_user_id'] = $user_id;

            if ($this->CustomerUserItinerary->save($this->request->data)) {
                $this->Flash->set(__('O itinerário foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'itineraries/'.$id.'/'.$user_id]);
            } else {
                $this->Flash->set(__('O itinerário não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();
        $action = 'Beneficiários';

        $benefits = $this->Benefit->find('list', ['fields' => ['id', 'complete_name']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customer_users', 'action' => 'edit', $id, $user_id],
            'Novo Benefício' => ''
        ];
        $this->set(compact('action', 'id', 'breadcrumb', 'user_id', 'benefits','statuses'));
    }

    public function edit_itinerary($id, $user_id, $id_itinerary)
    {
        $this->Permission->check(65, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserItinerary->id = $id_itinerary;
        
        // Verifica se o formulário é POST ou PUT
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserItinerary->validates();
            $this->request->data['CustomerUserItinerary']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->CustomerUserItinerary->save($this->request->data)) {
                $this->Flash->set(__('O itinerário foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'itineraries/'.$id.'/'.$user_id]);
            } else {
                $this->Flash->set(__('O itinerário não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
    
        $temp_errors = $this->CustomerUserItinerary->validationErrors;
        $this->request->data = $this->CustomerUserItinerary->read();
        $this->CustomerUserItinerary->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $this->Customer->id = $id;
        $cliente = $this->Customer->read();
        $action = 'Beneficiários';
    
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Itinerário' => ''
        ];
        
        $benefits = $this->Benefit->find('list', ['fields' => ['id', 'complete_name']]);
        $this->set("form_action", "../customers/edit_user/".$id);
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'breadcrumb', 'benefits'));
    
        // Define se o campo deve ser readonly
        $isEdit = true;
        $this->set('isEdit', $isEdit);
    
        $this->render("add_itinerary");
    }
    
    public function delete_itinerary($customer_id, $user_id, $id){
        $this->Permission->check(65, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserItinerary->id = $id;
        $this->request->data = $this->CustomerUserItinerary->read();

        $this->request->data['CustomerUserItinerary']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['CustomerUserItinerary']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->CustomerUserItinerary->save($this->request->data)) {
            $this->Flash->set(__('O usuário foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'itineraries/'.$customer_id.'/'.$user_id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
        }
    }

    public function getBenefitUnitPrice(){
        $this->autoRender = false;

        $benefit_id = $_POST['benefit_id'];

        $benefit = $this->Benefit->find('first', ['conditions' => ['Benefit.id' => $benefit_id]]);

        echo json_encode(['unit_price' => $benefit['Benefit']['unit_price']]);
    }

    /*******************
                Transações
    ********************/

    public function transactions($customer_id, $user_id)
    {
        $action = 'Extrato';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Extrato' => ''
        ];

        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['OrderItem.customer_user_id' => $user_id, 'Order.customer_id' => $customer_id], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['Benefit.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $data = $this->Paginator->paginate('OrderItem', $condition);

        $this->set(compact('action', 'breadcrumb', 'customer_id', 'user_id', 'data'));
    }

    public function envia_email($data)
    {
        $dados = [
            'viewVars' => [
                'nome'  => $data['CustomerUser']['name'],
                'email' => $data['CustomerUser']['email'],
                'username' => $data['CustomerUser']['email'],
                'senha' => $data['CustomerUser']['password'],
                'link'  => 'https://cliente.berh.com.br'
            ],
            'template' => 'nova_senha_usuario_cliente',
            'subject'  => 'Be RH - Acesso Admin - Nova senha',
            'config'   => 'default'
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function reenviar_senha($id, $user_id)
    {
        $this->CustomerUser->id = $user_id;
        $this->CustomerUser->recursive = -1;
        $this->request->data = $this->CustomerUser->read();

        $senha = substr(sha1(time()), 0, 6);

        $this->request->data['CustomerUser']['password'] = $senha;

        if ($this->CustomerUser->save($this->request->data)) {
            $this->envia_email($this->request->data);

            $this->Flash->set(__('Senha reenviada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect($this->referer());
        }
    }

    public function upload_csv()
    {
        if ($this->request->is('post') && !empty($this->request->data['file']['name']) && $this->request->data['file']['type'] == 'text/csv') {
        
            $uploadedFile = $this->request->data['file'];
            $deleteItinerary = $this->request->data['option_itinerary'];

            $csv = new ItineraryCSVParser();
            $ret = $csv->parse($uploadedFile['tmp_name'], $uploadedFile['name'], $this->request->data['customer_id'], CakeSession::read("Auth.User.id"), false, $deleteItinerary);

            $this->saveFile($uploadedFile);

            $this->redirect("/customer_users/csv_import_result/".$this->request->data['customer_id'].'/'.$ret['file_id']);
        } else {
            $this->Flash->set(__('Arquivo Inválido, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }
    }

    public function saveFile($uploadedFile)
    {
        $targetDir = APP."Private/customer_users/";

        $targetFile = $targetDir . basename($uploadedFile['name']);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($uploadedFile['tmp_name'], $targetFile)) {
            return true;
        }

        return false;
    }

    public function update_working_days()
    {
        if ($this->request->is('post') && !empty($this->request->data['file']['name']) && $this->request->data['file']['type'] == 'text/csv') {
        
            $uploadedFile = $this->request->data['file'];
            
            $csv = new ItineraryCSVParser();
            $csv->parse_working_days_update($uploadedFile['tmp_name'], $this->request->data['customer_id']);

            $this->Flash->set(__('Dias úteis atualizados com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect($this->referer());
        } else {
            $this->Flash->set(__('Arquivo Inválido, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }
    }

    public function update_grupo_economico()
    {
        if ($this->request->is('post') && !empty($this->request->data['file']['name']) && $this->request->data['file']['type'] == 'text/csv') {
            $uploadedFile = $this->request->data['file'];
            $csv = new EconomicGroupCSVParser();
            $csv->parse($uploadedFile['tmp_name'], $this->request->data['customer_id']);

            $this->Flash->set(__('Grupo econômico atualizados com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect($this->referer());
        } else {
            $this->Flash->set(__('Arquivo Inválido, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }
    }

    public function csv_import_result($customerId, $fileId){
        $action = 'Resultado Importação';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $customerId],
            'Resultado Importação' => ''
        ];

        $cond = ['CSVImport.id' => $fileId];
        if(isset($_GET['t'])){
            $cond['CSVImportLine.status_id'] = $_GET['t'];
        }

        $this->CSVImport->unbindModel(['hasMany' => ['CSVImportLine']]);
        $csv_file = $this->CSVImport->find('first', ['conditions' => ['CSVImport.id' => $fileId]]);
        $csv_file_lines = $this->CSVImportLine->find('all', ['conditions' => $cond]);

        if($customerId != $csv_file['CSVImport']['customer_id']){
            $this->redirect("/not_allowed");
        }

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 19, 'Status.id != 90'], 'order' => 'Status.name desc']);

        $this->set(compact('csv_file', 'action', 'breadcrumb', 'customerId', 'fileId', 'status', 'csv_file_lines'));
    }

    public function ativa_customer_user($customerId = false, $custUserIds = false)
    {
        $this->autoRender = false;

        $is_multiple = false;
        if($customerId == false || $custUserIds == false){
            $is_multiple = true;
            $customerId = $this->request->data['customerId'];
            $custUserIds = $this->request->data['custUserIds'];
        }

        $this->CustomerUser->updateAll(
            ['CustomerUser.status_id' => 1],
            ['CustomerUser.id' => $custUserIds]
        );

        if($is_multiple){
            echo json_encode(['success' => true]);
        } else {
            $this->redirect('/customer_users/index/' . $customerId);
        }        
    }

    public function inativa_customer_user($customerId = false, $custUserIds = false)
    {
        $this->autoRender = false;

        $is_multiple = false;
        if($customerId == false || $custUserIds == false){
            $is_multiple = true;
            $customerId = $this->request->data['customerId'];
            $custUserIds = $this->request->data['custUserIds'];
        }

        $this->CustomerUser->updateAll(
            ['CustomerUser.status_id' => 2],
            ['CustomerUser.id' => $custUserIds]
        );

        if($is_multiple){
            echo json_encode(['success' => true]);
        } else {
            $this->redirect('/customer_users/index/' . $customerId);
        }        
    }

    public function update_ativar_inativar()
    {
        if ($this->request->is('post') && !empty($this->request->data['file']['name']) && $this->request->data['file']['type'] == 'text/csv') {
            $customerId = $this->request->data['customer_id'];

            $ret = $this->parseCSVAtivarInativar($customerId, $this->request->data['file']['tmp_name']);

            foreach ($ret['data'] as $data) {
                if ($data['customer_user_id'] and $data['status_id'] != 0 and empty($data['benefit_id'])) {
                    $this->CustomerUser->id = $data['customer_user_id'];
                    $this->CustomerUser->save([
                        'CustomerUser' => [
                            'status_id' => $data['status_id']
                        ]
                    ]);
                }
            }

            $this->Flash->set(__('Status atualizados com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect($this->referer());
        } else {
            $this->Flash->set(__('Arquivo Inválido, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }
    }

    private function parseCSVAtivarInativar($customerId, $tmpFile)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $line = 0;
        $data = [];
        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if ($line == 0) {
                    $line++;
                }
                continue;
            }

            $benefit_id = null;
            $benefitCode = null;
            if(isset($row[2])){
                $benefitCode = $row[2];

                $benefit = $this->Benefit->find('first', [
                    'conditions' => [
                        'Benefit.code' => $benefitCode
                    ]
                ]);

                $benefit_id = $benefit ? $benefit['Benefit']['id'] : null;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);            

            $existingUser = $this->CustomerUser->find('first', [
                'conditions' => [
                    "REPLACE(REPLACE(CustomerUser.cpf, '-', ''), '.', '')" => $cpf,
                    'CustomerUser.customer_id' => $customerId,
                ]
            ]);

            $customer_user_id = null;
            if (isset($existingUser['CustomerUser'])) {
                $customer_user_id = $existingUser['CustomerUser']['id'];
            }

            $status_id = 0;
            if (trim(strtolower($row[1])) == 'ativar' || trim(strtolower($row[1])) == 'ativo') {
                $status_id = 1;
            } elseif (trim(strtolower($row[1])) == 'inativar' || trim(strtolower($row[1])) == 'inativo') {
                $status_id = 2;
            }

            $data[] = [
                'customer_user_id' => $customer_user_id,
                'status_id' => $status_id,
                'document' => $row[0],
                'status' => $row[1],
                'benefit_id' => $benefitCode,
            ];

            if($benefit_id && $customer_user_id){
                $this->CustomerUserItinerary->updateAll(
                    ['CustomerUserItinerary.status_id' => $status_id],
                    ['CustomerUserItinerary.customer_user_id' => $customer_user_id, 'CustomerUserItinerary.benefit_id' => $benefit_id]
                );
            }

            $line++;
        }

        return ['data' => $data];
    }

}
