<?php

class OrderDocumentsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email'];
    public $uses = ['OrderDocument', 'Order', 'Status', 'CustomerUser'];

    public $paginate = [
        'OrderDocument' => [
            'limit' => 10,
            'order' => ['OrderDocument.created_at' => 'desc'],
        ],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['OrderDocument.order_id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != '') {
            $condition['or'] = array_merge($condition['or'], ['OrderDocument.name LIKE' => '%'.$_GET['q'].'%']);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $action = 'Pedido';

        $data = $this->Paginator->paginate('OrderDocument', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            'Cadastros' => ['controller' => 'orders', 'action' => 'edit', $id],
            'Notas fiscais' => '',
        ];
        $this->set(compact('status', 'data', 'id', 'action', 'breadcrumb'));
    }

    public function add($id)
    {
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['OrderDocument']['user_creator_id'] = CakeSession::read('Auth.User.id');

            $this->OrderDocument->create();
            if ($this->OrderDocument->save($this->request->data)) {
                $this->send_mail($id);

                $this->Flash->set(__('O documento foi salvo com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $action = 'Pedido';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            'Cadastros' => ['controller' => 'orders', 'action' => 'edit', $id],
            'Notas fiscais' => '',
            'Nova nota' => '',
        ];
        $this->set('form_action', '../order_documents/add/'.$id);
        $this->set(compact('statuses', 'action', 'id', 'breadcrumb'));
    }

    public function edit($id, $document_id = null)
    {
        $this->OrderDocument->id = $document_id;
        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data['OrderDocument']['file_name']['name'] == '') {
                unset($this->request->data['OrderDocument']['file_name']);
            }
            $this->request->data['OrderDocument']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->OrderDocument->save($this->request->data)) {
                $this->Flash->set(__('O documento foi alterado com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set(__('O documento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->OrderDocument->validationErrors;
        $this->request->data = $this->OrderDocument->read();
        $this->OrderDocument->validationErrors = $temp_errors;

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            'Cadastros' => ['controller' => 'customers', 'action' => 'edit', $id],
            'Notas fiscais' => '',
            'Alterar nota' => '',
        ];
        $this->set('action', 'Pedido');
        $this->set('form_action', '../order_documents/edit/'.$id);
        $this->set(compact('statuses', 'id', 'document_id', 'breadcrumb'));

        $this->render('add');
    }

    public function delete($order_id, $id)
    {
        $this->OrderDocument->id = $id;
        $order = $this->OrderDocument->read();

        $data['OrderDocument']['data_cancel'] = date('Y-m-d H:i:s');
        $data['OrderDocument']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->OrderDocument->save($data)) {
            unlink(APP.'webroot/files/order_document/file_name/'.$order['OrderDocument']['id'].'/'.$order['OrderDocument']['file_name']);

            $this->Flash->set(__('O documento foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index', $order_id]);
        }
    }

    public function send_mail($order_id)
    {
        $this->autoRender = false;
        $this->layout = false;

        $order = $this->Order->find('first', [
            'contain' => ['Customer'],
            'conditions' => ['Order.id' => $order_id],
        ]);

        $users[$order['Customer']['email']] = $order['Customer']['nome_primario'];

        if ($order['Customer']['email1'] != '') {
            $users[$order['Customer']['email1']] = $order['Customer']['nome_primario'];
        }

        $dados = [
            'viewVars' => [
                'tos' => $users,
                'pedido' => $order_id,
                'link' => Configure::read('Areadoassociado.link').'orders/edit/'.$order_id,
            ],
            'template' => 'nota_fiscal_criada',
            'subject' => 'BeRH - Nota Fiscal',
            'config' => 'default',
        ];

        $this->Email->send($dados, true);
    }
}
