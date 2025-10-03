<?php

class CustomerAddressController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Customer', 'Status', 'CustomerUser', 'CustomerAddress', 'Log'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($customer_id)
    {
        $this->Permission->check(11, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = [
            'CustomerAddress' => [
                'limit' => 100,
                'order' => ['CustomerAddress.name' => 'asc'],
            ]
        ];

        $condition = ['and' => ['CustomerAddress.customer_id' => $customer_id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerAddress.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $this->Customer->id = $customer_id;
        $cliente = $this->Customer->read();

        $action = 'Endereços';

        $data = $this->Paginator->paginate('CustomerAddress', $condition);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $customer_id],
            'Endereços' => '',
        ];
        $this->set(compact('data', 'customer_id', 'action', 'breadcrumb'));
    }

    public function add($customer_id)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $nameConflict = $this->CustomerAddress->find('count', [
                'conditions' => [
                    'CustomerAddress.name' => $this->request->data['CustomerAddress']['name'],
                    'CustomerAddress.customer_id' => $customer_id,
                ],
            ]);

            $cpfConflict = null;
            if ($this->request->data['CustomerAddress']['customer_user_id']) {
               $cpfConflict = $this->CustomerAddress->find('count', [
                   'conditions' => [
                       'CustomerAddress.zip_code' => $this->request->data['CustomerAddress']['zip_code'],
                       'CustomerAddress.customer_user_id' => $this->request->data['CustomerAddress']['customer_user_id'],
                       'CustomerAddress.customer_id' => $customer_id,
                   ],
               ]);

               if ($cpfConflict) {
                   $this->Flash->set(__('O usuário já tem um endereço com o mesmo CEP. Por favor, valide os dados e tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
                   $this->redirect($this->referer());
               }
            }

            if ($nameConflict) {
                $this->Flash->set(__('Já existe um endereço com o mesmo nome. Por favor, altere o nome e tente novamente.'), ['params' => ['class' => 'alert alert-danger']]);
                $this->redirect($this->referer());
            }

            $this->CustomerAddress->create();
            if ($this->CustomerAddress->validates()) {
                $this->request->data['CustomerAddress']['customer_id'] = $customer_id;
                $this->request->data['CustomerAddress']['user_creator_id'] = CakeSession::read('Auth.User.id');
                if ($this->CustomerAddress->save($this->request->data)) {
                    $this->Flash->set(__('O endereço foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index', $customer_id]);
                } else {
                    $this->Flash->set(__('O endereço não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('O endereço não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $customer_id;
        $cliente = $this->Customer->read();

        $action = 'Endereços';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 24]]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $customer_id],
            'Novo Endereço' => '',
        ];
        $customer_user_ids = $this->CustomerUser->find('list', ['conditions' => ['CustomerUser.customer_id' => $customer_id]]);
        $this->set("form_action", "../customer_address/add/" . $customer_id);
        $this->set(compact('statuses', 'action', 'customer_id', 'breadcrumb', 'customer_user_ids'));
    }

    public function edit($customer_id, $address_id = null)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');

        $this->CustomerAddress->id = $address_id;

        if ($this->request->is(['post', 'put'])) {
            $this->CustomerAddress->validates();
            $this->request->data['CustomerAddress']['user_updated_id'] = CakeSession::read('Auth.User.id');

            $old = $this->CustomerAddress->find('first', ['conditions' => ['CustomerAddress.id' => $address_id]]);

            $dados_log = [
                'old_value' => json_encode($old),
                'new_value' => json_encode($this->request->data),
                'route' => 'customers_address/edit',
                'log_action' => 'Alterou',
                'log_table' => 'CustomerAddress',
                'primary_key' => $address_id,
                'parent_log' => $customer_id,
                'user_type' => 'ADMIN',
                'user_id' => CakeSession::read('Auth.User.id'),
                'message' => 'O endereço do cliente foi alterado com sucesso',
                'log_date' => date('Y-m-d H:i:s'),
                'data_cancel' => '1901-01-01',
                'usuario_data_cancel' => 0,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ];

            if ($this->CustomerAddress->save($this->request->data)) {
                $this->Log->save($dados_log);
                $this->Flash->set(__('O endereço foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index', $customer_id]);
            } else {
                $this->Flash->set(__('O endereço não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->CustomerAddress->validationErrors;
        $this->request->data = $this->CustomerAddress->read();
        $this->CustomerAddress->validationErrors = $temp_errors;

        $cliente = $this->Customer->findById($customer_id);

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 24]]);
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $customer_id],
            'Alterar Endereço' => '',
        ];
        $customer_user_ids = $this->CustomerUser->find('list', ['conditions' => ['CustomerUser.customer_id' => $customer_id]]);
        $this->set("action", 'Endereços');
        $this->set("form_action", "../customer_address/edit/" . $customer_id);
        $this->set(compact('statuses', 'customer_id', 'address_id', 'breadcrumb', 'customer_user_ids'));

        $this->render("add");
    }

    public function delete($customer_id, $address_id)
    {
        $this->Permission->check(11, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->CustomerAddress->id = $address_id;
        $this->request->data = $this->CustomerAddress->read();

        $this->request->data['CustomerAddress']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['CustomerAddress']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->CustomerAddress->save($this->request->data)) {
            $this->Flash->set(__('O endereço foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index', $customer_id]);
        }
    }

    public function list($customer_id) {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $enderecos = $this->CustomerAddress->find('list', ['conditions' => ['CustomerAddress.customer_id' => $customer_id, 'CustomerAddress.status_id' => [114]]]);

        echo json_encode($enderecos);
    }
}