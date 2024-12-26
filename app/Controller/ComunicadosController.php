<?php
class ComunicadosController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email'];
    public $uses = ['Comunicado', 'ComunicadoCliente', 'Permission', 'Status', 'Categoria', 'Customer'];
    
    public $paginate = [
        'Comunicado'            => ['limit' => 100, 'order' => ['Comunicado.data' => 'desc', 'Status.id' => 'asc', 'Comunicado.titulo' => 'asc']],
        'ComunicadoCliente'     => ['limit' => 100, 'order' => ['ComunicadoCliente.created' => 'desc']],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(2, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;
    
        $condition = ["and" => [], "or" => []];
    
        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Comunicado.titulo LIKE' => "%".$_GET['q']."%"]);
        }
    
        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        if (isset($_GET["c"]) && $_GET["c"] != "") {
            $condition['and'][] = ['Comunicado.categoria_id' => intval($_GET["c"])];
        }
        
        
    
        $this->Paginator->settings['order'] = ['Comunicado.data' => 'desc'];
    
        $data = $this->Paginator->paginate('Comunicado', $condition);
        $categorias = $this->Categoria->find('all', [
            'fields' => ['Categoria.id', 'Categoria.name'],
            'order' => ['Categoria.name' => 'ASC']
        ]);        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
    
        $action = 'Comunicados';
        $breadcrumb = ['Configurações' => '', 'Comunicados' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb','categorias'));
    }
    
    public function add()
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Comunicado->create();
            if ($this->Comunicado->validates()) {
                $this->request->data['Comunicado']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Comunicado->save($this->request->data)) {
                    $this->Flash->set(__('O Comunicado foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/'.$this->Comunicado->id]);
                    
                } else {
                    $this->Flash->set(__('O Comunicado não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O Comunicado não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $categorias = $this->Categoria->find('list');
        $action = 'Comunicados';
        $breadcrumb = ['Configurações' => '', 'Comunicados' => '', 'Novo Comunicado' => ''];
        $this->set(compact('categorias', 'action', 'breadcrumb'));
        $this->set("action", "Novo Comunicado");
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Comunicado->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Comunicado->validates();
            if ($this->request->data['Comunicado']['file'] == '') {
                unset($this->request->data['Comunicado']['file']);
            }
            if ($this->Comunicado->save($this->request->data)) {
                $this->Flash->set(__('O Comunicado foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'edit/'.$this->Comunicado->id]);
            } else {
                $this->Flash->set(__('O Comunicado não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Comunicado->validationErrors;
        $this->request->data = $this->Comunicado->read();
        $this->Comunicado->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $categorias = $this->Categoria->find('list');
        $action = 'Comunicados';
        $breadcrumb = ['Configurações' => '', 'Comunicados' => '', 'Alterar Comunicado' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('categorias', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(2, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Comunicado->id = $id;
        $this->request->data = $this->Comunicado->read();

        $this->request->data['Comunicado']['data_cancel'] = date("Y-m-d H:i:s");
         $this->request->data['Comunicado']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");
        

        if ($this->Comunicado->save($this->request->data)) {
            $this->Flash->set(__('O Comunicado foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function clientes($id)
    {
        $this->Permission->check(2, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['ComunicadoCliente.comunicado_id' => $id], "or" => []];

        $data = $this->Paginator->paginate('ComunicadoCliente', $condition);

        $cadastrados = $this->ComunicadoCliente->find('all', ['conditions' => $condition]);
        $ids_cadastrados = [];
        foreach ($cadastrados as $cad) {
            $ids_cadastrados[] = $cad['ComunicadoCliente']['customer_id'];
        }

        $customers = $this->Customer->find("list", ['conditions' => ['Customer.status_id' => 3, 'Customer.enviar_email' => 1, 'not' => ['Customer.id' => $ids_cadastrados]], 'order' => ['Customer.nome_primario' => 'asc']]);

        $customersIds = [];
        foreach ($customers as $comunicado_id => $name) {
            $customersIds[$comunicado_id] = $name;
        }

        $action = 'Clientes';
        $breadcrumb = ['Configurações' => '', 'Comunicados' => '', 'Clientes' => ''];

        $this->set("form_action", "../comunicados/add_cliente/" . $id);
        $this->set(compact('data', 'id', 'action', 'breadcrumb', 'customersIds'));
    }
    
    public function add_cliente($id)
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['ComunicadoCliente']['comunicado_id'] = $id;
            $this->request->data['ComunicadoCliente']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->ComunicadoCliente->create();
            if ($this->ComunicadoCliente->save($this->request->data)) {
                $this->Flash->set(__('O cliente foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'clientes/'.$id]);
            } else {
                $this->Flash->set(__('O cliente não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
    }
    
    public function add_all_clientes($id)
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");

        $condition = ["and" => ['ComunicadoCliente.comunicado_id' => $id], "or" => []];

        $cadastrados = $this->ComunicadoCliente->find('all', ['conditions' => $condition]);
        $ids_cadastrados = [];
        foreach ($cadastrados as $cad) {
            $ids_cadastrados[] = $cad['ComunicadoCliente']['customer_id'];
        }

        $customers = $this->Customer->find("list", ['conditions' => ['Customer.status_id' => 3, 'Customer.enviar_email' => 1, 'not' => ['Customer.id' => $ids_cadastrados]], 'order' => ['Customer.nome_primario' => 'asc']]);

        $status = false;
        foreach ($customers as $customer_id => $customer) {
            $this->request->data['ComunicadoCliente']['comunicado_id'] = $id;
            $this->request->data['ComunicadoCliente']['customer_id'] = $customer_id;
            $this->request->data['ComunicadoCliente']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->ComunicadoCliente->create();
            $this->ComunicadoCliente->save($this->request->data);

            $status = true;
        }

        if ($status) {
            $this->Flash->set(__('Os clientes foram cadastrados com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        }

        $this->redirect(['action' => 'clientes/'.$id]);        
    }

    public function delete_all_clientes()
    {
        $this->autoRender = false;
        
        $id = $this->request->data['comunicadoId'];
        $itemIds = $this->request->data['itemIds'];

        $this->ComunicadoCliente->updateAll(
            ['ComunicadoCliente.data_cancel' => 'CURRENT_DATE', 'ComunicadoCliente.usuario_id_cancel' => CakeSession::read("Auth.User.id")],
            ['ComunicadoCliente.id' => $itemIds]
        );

        $this->Flash->set(__('Os clientes foram excluidos com sucesso'), ['params' => ['class' => "alert alert-success"]]);

        echo json_encode(['success' => true]);
    }

    public function delete_cliente($id, $comunicado_cliente_id)
    {
        $this->Permission->check(2, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->ComunicadoCliente->id = $comunicado_cliente_id;

        $data['ComunicadoCliente']['data_cancel'] = date("Y-m-d H:i:s");
        $data['ComunicadoCliente']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->ComunicadoCliente->save($data)) {
            $this->Flash->set(__('O cliente foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'clientes/'.$id]);
        }
    }

    public function enviar_comunicado($id)
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");

        $comunicado_clientes = $this->ComunicadoCliente->find('all', ['conditions' => ['ComunicadoCliente.comunicado_id' => $id, 'ComunicadoCliente.sent' => null]]);
        
        foreach ($comunicado_clientes as $cliente) {
            if ($cliente['Customer']['status_id'] == 3) {
                $dados = 
                    ['viewVars' => [
                        'nome'  => $cliente['Customer']['nome_primario'],
                        'email' => $cliente['Customer']['email'],
                        'link'  => 'https://cliente.berh.com.br/'
                    ],
                    'template' => 'comunicado_cliente',
                    'subject'  => 'BeRH - Comunicado: '.$cliente['Comunicado']['titulo'].' ',
                    'config'   => 'default'
                ];

                if (!$this->Email->send($dados)) {
                    $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => "alert alert-danger"]]);
                    $this->redirect(['action' => 'clientes/'.$id]);
                } else {
                    $this->ComunicadoCliente->id = $cliente['ComunicadoCliente']['id'];

                    $data['ComunicadoCliente']['sent'] = date("Y-m-d H:i:s");
                    $data['ComunicadoCliente']['user_sent_id'] = CakeSession::read("Auth.User.id");

                    $this->ComunicadoCliente->save($data);
                }
            }
        }

        $this->Flash->set(__('O comunicado foi enviado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'clientes/'.$id]);
    }
}
