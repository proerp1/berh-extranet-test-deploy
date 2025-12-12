<?php
class ResalesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'ExcelGenerator'];
    public $uses = ['Resale', 'Status', 'Vencimento', 'Seller', 'Customer', 'CustomerUser', 'BankAccount', 'Log'];

    public $paginate = [
        'Resale'			 => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'Resale.nome_fantasia' => 'asc']],
        'Seller'			 => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'Seller.name' => 'asc']],
        'Customer'		 => ['limit' => 10, 'order' => ['Seller.nome_fantasia' => 'asc', 'Customer.nome_secundario' => 'asc']],
        'CustomerUser' => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'CustomerUser.name' => 'asc']]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(10, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Resale.nome_fantasia LIKE' => "%".$_GET['q']."%", 'Resale.razao_social LIKE' => "%".$_GET['q']."%", 'Resale.cnpj LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Resale.tipo' => $_GET['t']]);
        }

        if (isset($_GET["tp"]) and $_GET["tp"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Resale.tipo_pessoa' => $_GET['tp']]);
        }

        if (isset($_GET["s"]) and $_GET["s"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['s']]);
        }

        if (isset($_GET['excel'])) {
            $dados = $this->Resale->find('all', ['conditions' => $condition]);

            $nome = 'canais_' . date('d_m_Y'). '.xlsx';

            $this->ExcelGenerator->gerarExcelCanais($nome, $dados);
            $this->redirect("/files/excel/" . $nome);
        }

        $data = $this->Paginator->paginate('Resale', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Canais';
        $breadcrumb = ['Lista' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(10, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Resale->create();
            if ($this->Resale->validates()) {
                $this->request->data['Resale']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Resale->save($this->request->data)) {
                    $this->Flash->set(__('O parceiro foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/'.$this->Resale->id]);
                } else {
                    $this->Flash->set(__('O parceiro não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O parceiro não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name' => 'asc']]);
        $vencimentos = $this->Vencimento->find('list');

        $this->set("action", "Novo Parceiro");
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'vencimentos', 'bankAccounts'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(10, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Resale->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Resale->validates();

            $log_old_value = $this->request->data['log_old_value'];
            unset($this->request->data['log_old_value']);

            $dados_log = [
                'old_value' => $log_old_value,
                'new_value' => json_encode($this->request->data),
                'route' => 'relases/edit',
                'log_action' => 'Alterou',
                'log_table' => 'Resale',
                'primary_key' => $id,
                'parent_log' => 0,
                'user_type' => 'ADMIN',
                'user_id' => CakeSession::read('Auth.User.id'),
                'message' => 'O canal foi alterado com sucesso',
                'log_date' => date('Y-m-d H:i:s'),
                'data_cancel' => '1901-01-01',
                'usuario_data_cancel' => 0,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ];

            $this->request->data['Resale']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Resale->save($this->request->data)) {
                $this->Log->save($dados_log);
                $this->Flash->set(__('O parceiro foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('O parceiro não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Resale->validationErrors;
        $this->request->data = $this->Resale->read();
        $this->Resale->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name' => 'asc']]);
        $vencimentos = $this->Vencimento->find('list');

        $this->set("action", 'Canal - '.$this->request->data['Resale']['nome_fantasia']);
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'vencimentos', 'bankAccounts'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(10, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Resale->id = $id;
        $this->request->data = $this->Resale->read();

        $this->request->data['Resale']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Resale']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Resale->save($this->request->data)) {
            $this->Flash->set(__('A parceiro foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    /*********************
                EXECUTIVOS
    **********************/
    public function sellers($resale_id)
    {
        $this->Permission->check(10, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Resale.id' => $resale_id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Seller.nome_fantasia LIKE' => "%".$_GET['q']."%", 'Seller.razao_social LIKE' => "%".$_GET['q']."%", 'Seller.documento LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $this->Resale->id = $resale_id;
        $resale = $this->Resale->read();

        $action = 'Executivos';

        $data = $this->Paginator->paginate('Seller', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $breadcrumb = [
            $resale['Resale']['nome_fantasia'] => ['controller' => 'resales', 'action' => 'edit', $resale_id], 
            'Executivos' => ''
        ];
        $this->set(compact('status', 'data', 'resale_id', 'action', 'breadcrumb'));
    }
    
    public function add_seller($resale_id)
    {
        $this->Permission->check(10, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Seller->create();
            if ($this->Seller->validates()) {
                $this->request->data['Seller']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Seller->save($this->request->data)) {
                    $this->Flash->set(__('O executivo foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'sellers/'.$resale_id]);
                } else {
                    $this->Flash->set(__('O executivo não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O executivo não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->Resale->id = $resale_id;
        $resale = $this->Resale->read();

        $action = 'Executivos';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $vencimentos = $this->Vencimento->find('list');
        $breadcrumb = [
            $resale['Resale']['nome_fantasia'] => ['controller' => 'resales', 'action' => 'edit', $resale_id], 
            'Novo Vendedor' => ''
        ];
        $this->set("form_action", "../resales/add_seller/".$resale_id);
        $this->set(compact('statuses', 'vencimentos', 'resale_id', 'action', 'breadcrumb'));
    }

    public function edit_seller($resale_id, $id = null)
    {
        $this->Permission->check(10, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Seller->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Seller->validates();
            $this->request->data['Seller']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Seller->save($this->request->data)) {
                $this->Flash->set(__('O executivo foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'sellers/'.$resale_id]);
            } else {
                $this->Flash->set(__('O executivo não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Seller->validationErrors;
        $this->request->data = $this->Seller->read();
        $this->Seller->validationErrors = $temp_errors;

        $this->Resale->id = $resale_id;
        $resale = $this->Resale->read();

        $action = 'Executivos';
            
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $vencimentos = $this->Vencimento->find('list');
        $breadcrumb = [
            $resale['Resale']['nome_fantasia'] => ['controller' => 'resales', 'action' => 'edit', $resale_id], 
            'Alterar Executivo' => ''
        ];

        $this->set("form_action", "../resales/edit_seller/".$resale_id);
        $this->set(compact('statuses', 'id', 'vencimentos', 'resale_id', 'action', 'breadcrumb'));
            
        $this->render("add_seller");
    }

    public function delete_seller($resale_id, $id)
    {
        $this->Permission->check(10, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Seller->id = $id;
        $this->request->data = $this->Seller->read();

        $this->request->data['Seller']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Seller']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Seller->save($this->request->data)) {
            $this->Flash->set(__('O executivo foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'sellers/'.$resale_id]);
        }
    }

    /*******************
                CARTEIRA
    ********************/

    public function carteira($resale_id)
    {
        $this->Permission->check(10, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Customer.cod_franquia' => $resale_id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.email LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        if (isset($_GET["v"]) and $_GET["v"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Seller.id' => $_GET['v']]);
        }

        $this->Resale->id = $resale_id;
        $resale = $this->Resale->read();

        $action = 'Carteira';

        $data = $this->Paginator->paginate('Customer', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
        $vendedor = $this->Seller->find('all', ['conditions' => ['Status.id' => 1, 'Seller.resale_id' => $resale_id], 'order' => 'Seller.name']);
        $breadcrumb = [
            $resale['Resale']['nome_fantasia'] => ['controller' => 'resales', 'action' => 'edit', $resale_id], 
            'Carteira' => ''
        ];
        $this->set(compact('status', 'vendedor', 'data', 'resale_id', 'action', 'breadcrumb'));
    }

    /*******************
                USUARIOS
    ********************/
    public function users($id)
    {
        $this->Paginator->settings = $this->paginate;

        if ($_GET["tipo"] == 'revenda') {
            $this->Permission->check(28, "leitura") ? "" : $this->redirect("/not_allowed");
            $condition = ["and" => ['Resale.id' => $id, 'CustomerUser.resale' => 1], "or" => []];

            $this->Resale->id = $id;
            $cliente = $this->Resale->read();

            $name = $cliente['Resale']['nome_fantasia'];
            $resale_id = $id;
        } elseif ($_GET["tipo"] == 'vendedor') {
            $this->Permission->check(29, "leitura") ? "" : $this->redirect("/not_allowed");
            $condition = ["and" => ['Seller.id' => $id, 'CustomerUser.seller' => 1], "or" => []];

            $this->Seller->id = $id;
            $cliente = $this->Seller->read();

            $name = $cliente['Seller']['nome_fantasia'];
            $resale_id = $cliente['Seller']['resale_id'];
        }

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%".$_GET['q']."%", 'CustomerUser.email LIKE' => "%".$_GET['q']."%"]);
        }

        if (!empty($_GET["t"])) {
            $condition['and'] = array_merge($condition['and'], ['CustomerUser.status_id' => $_GET['t']]);
        }

        $action = 'Usuários';
        $data = $this->Paginator->paginate('CustomerUser', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1], 'order' => 'Status.name']);
        $breadcrumb = [
            $name => ['controller' => 'resales', 'action' => 'edit', $id], 
            'Usuários' => ''
        ];
        $this->set(compact('data', 'action', 'id', 'status', 'resale_id', 'breadcrumb'));
    }

    public function add_user($id)
    {
        if ($_GET["tipo"] == 'revenda') {
            $this->Permission->check(28, "escrita") ? "" : $this->redirect("/not_allowed");

            $this->Resale->id = $id;
            $cliente = $this->Resale->read();

            $name = $cliente['Resale']['nome_fantasia'];
            $resale_id = $id;
        } elseif ($_GET["tipo"] == 'vendedor') {
            $this->Permission->check(29, "escrita") ? "" : $this->redirect("/not_allowed");

            $this->Seller->id = $id;
            $cliente = $this->Seller->read();

            $name = $cliente['Seller']['nome_fantasia'];
            $resale_id = $cliente['Seller']['resale_id'];
        }

        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUser->create();
            $this->CustomerUser->validates();

            $senha = substr(sha1(time()), 0, 6);

            $this->request->data['CustomerUser']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['CustomerUser']['password'] = $senha;
            $this->request->data['CustomerUser']['username'] = $this->request->data['CustomerUser']['email'];
            if ($this->CustomerUser->save($this->request->data)) {
                $this->envia_email($this->request->data);

                $this->Flash->set(__('O usuário foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'users/'.$id.'/?'.$this->request->data['query_string']]);
            } else {
                $this->Flash->set(__('O usuário não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $action = 'Usuários';
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            $name => ['controller' => 'resales', 'action' => 'edit', $id], 
            'Novo Usuário' => ''
        ];
        $this->set("form_action", "../resales/add_user/".$id."/?".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''));
        $this->set(compact('statuses', 'action', 'id', 'resale_id', 'breadcrumb'));
    }

    public function edit_user($id, $user_id = null)
    {
        if ($_GET["tipo"] == 'revenda') {
            $this->Permission->check(28, "escrita") ? "" : $this->redirect("/not_allowed");

            $this->Resale->id = $id;
            $cliente = $this->Resale->read();

            $name = $cliente['Resale']['nome_fantasia'];
            $resale_id = $id;
        } elseif ($_GET["tipo"] == 'vendedor') {
            $this->Permission->check(29, "escrita") ? "" : $this->redirect("/not_allowed");

            $this->Seller->id = $id;
            $cliente = $this->Seller->read();

            $name = $cliente['Seller']['nome_fantasia'];
            $resale_id = $cliente['Seller']['resale_id'];
        }

        $this->CustomerUser->id = $user_id;
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerUser->validates();
            $this->request->data['CustomerUser']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->CustomerUser->save($this->request->data)) {
                $this->Flash->set(__('O usuário foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'users/'.$id.'/?'.$this->request->data['query_string']]);
            } else {
                $this->Flash->set(__('O usuário não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CustomerUser->validationErrors;
        $this->request->data = $this->CustomerUser->read();
        $this->CustomerUser->validationErrors = $temp_errors;
            
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Usuários';
        $breadcrumb = [
            $name => ['controller' => 'resales', 'action' => 'edit', $id], 
            'Alterar Usuário' => ''
        ];
        $this->set("form_action", "../resales/edit_user/".$id."/".$user_id."/?".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''));
        $this->set(compact('statuses', 'id', 'user_id', 'action', 'resale_id', 'breadcrumb'));
            
        $this->render("add_user");
    }

    public function delete_user($customer_id, $id)
    {
        $this->CustomerUser->id = $id;

        $data = ['CustomerUser' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->CustomerUser->save($data)) {
            $this->Flash->set(__('O usuário foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'users/'.$customer_id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
        }
    }

    public function envia_email($data, $senha)
    {
        $dados = ['viewVars' => ['nome'  => $data['CustomerUser']['name'],
            'email' => $data['CustomerUser']['email'],
            'username' => $data['CustomerUser']['username'],
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
            $this->envia_email($this->request->data);

            $this->Flash->set(__('Senha reenviada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect("/customers/users/".$id);
        }
    }

    public function historico($id) {
        $this->Permission->check(10, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = array_merge($this->paginate, [
            'order' => ['Log.log_date' => 'desc'],
            'joins' => [
                [
                    'table' => 'users',
                    'alias' => 'Creator',
                    'type' => 'INNER',
                    'conditions' => ['Creator.id = Log.user_id']
                ],
            ],
            'fields' => ['Log.*', 'Creator.*']
        ]);
        $this->Resale->id = $id;
        $canal = $this->Resale->read();

        $condition = [
            'and' => [],
            'or' => [
                'and' => ['Log.primary_key' => $id, 'Log.log_table' => 'Resale'],
            ]
        ];

        $data = $this->Paginator->paginate('Log', $condition);

        $breadcrumb = [
            $canal['Resale']['nome_fantasia'] => ['controller' => 'resale', 'action' => 'edit', $id],
            'Histórico Alterações' => '',
        ];

        $this->set('action', 'Histórico Alterações');
        $this->set(compact('id', 'data', 'breadcrumb'));
    }
}
