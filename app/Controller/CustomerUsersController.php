<?php
App::uses('ItineraryCSVParser', 'Lib');
class CustomerUsersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'HtmltoPdf', 'ExcelGenerator', 'Robo'];
    public $uses = ['CustomerUser', 'Customer', 'Status', 'CustomerUserAddress', 'CustomerUserVacation', 'CepbrEstado', 'AddressType', 'CustomerDepartment', 'CustomerPosition', 'CustomerUserBankAccount', 'BankAccountType', 'CustomerUserItinerary', 'Benefit'];

    public $paginate = [
        'CustomerUserAddress' => ['limit' => 10, 'order' => ['CustomerUserAddress.id' => 'asc']]
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
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['CustomerUser.customer_id' => $id], "or" => []];
        
        if($is_admin){
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.is_admin' => 1]);
        } else {
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.is_admin !=' => 1]);
        }

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%".$_GET['q']."%", 'CustomerUser.email LIKE' => "%".$_GET['q']."%"]);
        }

        if (!empty($_GET["t"])) {
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.status_id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('CustomerUser', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1], 'order' => 'Status.name']);

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = $is_admin ? 'Usuários' : 'Beneficiários';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Beneficiários' => ''
        ];
        $this->set(compact('data', 'action', 'id', 'status', 'breadcrumb', 'is_admin'));
    }

    public function add_user($id){
        $this->add($id, true);
        $this->render('add');
    }

    public function add($id, $is_admin = false)
    {
        $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUser->create();
            $this->CustomerUser->validates();

            $this->request->data['CustomerUser']['is_admin'] = $is_admin ? 1 : 0;

            $senha = substr(sha1(time()), 0, 6);

            $this->request->data['CustomerUser']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUser']['password'] = $senha;
            if ($this->CustomerUser->save($this->request->data)) {
                // $this->envia_email($this->request->data);

                $action = $is_admin ? 'edit_user/'.$id.'/' : 'edit/'.$id.'/';
                $this->Flash->set(__('O usuário foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => $action.$this->CustomerUser->id.'/?'.$this->request->data['query_string']]);
            } else {
                $this->Flash->set(__('O usuário não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Beneficiários';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $estados = $this->CepbrEstado->find('list');
        $departamentos = $this->CustomerDepartment->find('list', ['conditions' => ['CustomerDepartment.customer_id' => $id]]);
        $cargos = $this->CustomerPosition->find('list');
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo Usuário' => ''
        ];
        $form_action = $is_admin ? "../customer_users/add/".$id.'/true' : "../customer_users/add/".$id;
        $this->set(compact('statuses', 'action', 'id', 'breadcrumb', 'estados', 'departamentos', 'cargos', 'is_admin', 'form_action'));
    }

    public function edit_user($id, $user_id){
        $this->edit($id, $user_id, true);
        $this->render('add');
    }

    public function edit($id, $user_id = null, $is_admin = false)
    {
        $this->Permission->check(11, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUser->id = $user_id;
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUser->validates();
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

        $action = 'Beneficiários';

        // usado para fazer login no site com o bypass, NAO ALTERAR!!!
        $hash = base64_encode($this->request->data['CustomerUser']['email']);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar Beneficiário' => ''
        ];
        $estados = $this->CepbrEstado->find('list');
        $departamentos = $this->CustomerDepartment->find('list', ['conditions' => ['CustomerDepartment.customer_id' => $id]]);
        $cargos = $this->CustomerPosition->find('list');

        $this->set('hash', rawurlencode($hash));
        $form_action = $is_admin ? "../customer_users/edit/".$id.'/'.$user_id.'/true' : "../customer_users/edit/".$id.'/'.$user_id;
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'breadcrumb', 'estados', 'departamentos', 'cargos', 'is_admin', 'form_action'));
            
        $this->render("add");
    }

    public function delete_user($customer_id, $id){
        $this->Permission->check(11, "excluir") ? "" : $this->redirect("/not_allowed");
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
            $condition['or'] = array_merge($condition['or'], ['CustomerUserAddress.address_line LIKE' => "%".$_GET['q']."%", 'CustomerUserAddress.neighborhood LIKE' => "%".$_GET['q']."%", 'CustomerUserAddress.city LIKE' => "%".$_GET['q']."%", 'CustomerUserAddress.state LIKE' => "%".$_GET['q']."%"]);
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
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
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

     /*******************
                Dados Bancários
    ********************/
    public function bank_info($id, $user_id){
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['CustomerUserBankAccount.customer_user_id' => $user_id], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['CustomerUserBankAccount.bank_name LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.bank_code LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.acc_number LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.acc_digit LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.branch_number LIKE' => "%".$_GET['q']."%", 'CustomerUserBankAccount.branch_digit LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('CustomerUserBankAccount', $condition);

        $action = 'Dados Bancários';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Dados Bancários' => ''
        ];
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'user_id'));
    }

    public function add_bank_info($id, $user_id)
    {
        $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
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

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Beneficiários';

        $states = $this->CepbrEstado->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customer_users', 'action' => 'edit', $id, $user_id],
            'Nova Conta Bancária' => ''
        ];
        $this->set(compact('action', 'id', 'breadcrumb', 'states', 'user_id', 'bank_account_type'));
    }

    public function edit_bank_info($id, $user_id, $id_bank)
    {
        $this->Permission->check(11, "escrita") ? "" : $this->redirect("/not_allowed");
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
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $this->set("form_action", "../customers/edit_user/".$id);
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'breadcrumb', 'estados', 'bank_account_type'));
            
        $this->render("add_bank_info");
    }

     /*******************
                Itinerário
    ********************/
    public function itineraries($id, $user_id){
        $this->Permission->check(3, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['CustomerUserItinerary.customer_user_id' => $user_id], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['Benefit.name LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('CustomerUserItinerary', $condition);

        $action = 'Dados Bancários';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Dados Bancários' => ''
        ];
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'user_id'));
    }

    public function add_itinerary($id, $user_id)
    {
        $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUserItinerary->create();
            $this->CustomerUserItinerary->validates();

            $this->request->data['CustomerUserItinerary']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUserItinerary']['customer_id'] = $id;
            $this->request->data['CustomerUserItinerary']['customer_user_id'] = $user_id;

            if ($this->CustomerUserItinerary->save($this->request->data)) {
                $this->Flash->set(__('O endereço foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'itineraries/'.$id.'/'.$user_id]);
            } else {
                $this->Flash->set(__('O endereço não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $action = 'Beneficiários';

        $states = $this->CepbrEstado->find('list');
        $benefits = $this->Benefit->find('list', ['fields' => ['id', 'complete_name']]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customer_users', 'action' => 'edit', $id, $user_id],
            'Novo Itinerário' => ''
        ];
        $this->set(compact('action', 'id', 'breadcrumb', 'states', 'user_id', 'benefits'));
    }

    public function edit_itinerary($id, $user_id, $id_itinerary)
    {
        $this->Permission->check(11, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserItinerary->id = $id_itinerary;
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
            
        $this->render("add_itinerary");
    }

    public function delete_itinerary($customer_id, $user_id, $id){
        $this->Permission->check(11, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->CustomerUserItinerary->id = $id;
        $this->request->data = $this->CustomerUserItinerary->read();

        $this->request->data['CustomerUserItinerary']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['CustomerUserItinerary']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->CustomerUserItinerary->save($this->request->data)) {
            $this->Flash->set(__('O usuário foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'itineraries/'.$customer_id.'/'.$user_id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
        }
    }

    /*******************
                Transações
    ********************/

    public function transactions($id, $user_id){
        $action = 'Extrato';
        $breadcrumb = [
            'Beneficiários' => ['controller' => 'customer_users', 'action' => 'index', $this->request->params['pass'][0]],
            'Extrato' => ''
        ];
        $this->set(compact('action', 'breadcrumb', 'id', 'user_id'));
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
                    'main_user' => 1
                ]
            ];
            print $customer['c']['email']." - ".$customer['c']['nome_primario']."</br>";

            $this->CustomerUser->create();
            $this->CustomerUser->save($customer_user, ['validate' => false]);

            // $this->envia_email($customer_user);
        }

    }

    public function envia_email($data)
    {
        $dados = [
            'viewVars' => [
                'nome'  => $data['CustomerUser']['name'],
                'email' => $data['CustomerUser']['email'],
                'username' => $data['CustomerUser']['email'],
                'senha' => $data['CustomerUser']['password'],
                'link'  => 'http://berh.com.br/cliente'
            ],
            'template' => 'nova_senha_usuario_cliente',
            'subject'  => 'BeRH - Nova senha',
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
        $this->request->data = $this->CustomerUser->read();

        $senha = substr(sha1(time()), 0, 6);

        $this->request->data['CustomerUser']['password'] = $senha;
            
        if ($this->CustomerUser->save($this->request->data)) {
            // $this->envia_email($this->request->data);

            $this->Flash->set(__('Senha reenviada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect("/customer_users/index/".$id);
        }
    }

    public function upload_csv(){
        if ($this->request->is('post') && !empty($this->request->data['file'])) {
        
            $uploadedFile = $this->request->data['file'];
            
            $csv = new ItineraryCSVParser();
            $ret = $csv->parse($uploadedFile['tmp_name'], $this->request->data['customer_id']);

            if($ret['has_inner_error'] && isset($ret['rows'])){
                foreach ($ret['rows'] as $row) {
                    $this->Flash->set($row['error'].' | Usuario '.$row['userId']. ' - Benefício ('.$row['benefit_code'].')', ['params' => ['class' => "alert alert-danger"]]);
                }
                $this->redirect("/customer_users/index/".CakeSession::read("Auth.User.customer_id"));
            }

            if($ret['success'] == false && isset($ret['error'])){
                $this->Flash->set($ret['error'], ['params' => ['class' => "alert alert-danger"]]);
                $this->redirect("/customer_users/index/".CakeSession::read("Auth.User.customer_id"));
            }

            debug($ret);die;
        }
    }

}
