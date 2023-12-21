<?php
class SuppliersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Supplier', 'Status','BankCode','BankAccountType', 'Docsupplier'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Supplier.id' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(9, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Supplier.id LIKE' => "%".$_GET['q']."%",'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%", 'Supplier.razao_social LIKE' => "%".$_GET['q']."%", 'Supplier.documento LIKE' => "%".$_GET['q']."%"]);
        }
        

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Supplier', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(9, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Supplier->create();
            if ($this->Supplier->validates()) {
                $this->request->data['Supplier']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Supplier->save($this->request->data)) {
                    $this->Flash->set(__('O fornecedor foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/'.$this->Supplier->id]);
                } else {
                    $this->Flash->set(__('O fornecedor não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O fornecedor não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $banks = $this->BankCode->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Novo fornecedor' => ''];
        $this->set(compact('statuses', 'action', 'breadcrumb', 'banks', 'bank_account_type'));
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(9, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Supplier->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Supplier->validates();
            $this->request->data['Supplier']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Supplier->save($this->request->data)) {
                $this->Flash->set(__('O fornecedor foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('O fornecedor não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Supplier->validationErrors;
        $this->request->data = $this->Supplier->read();
        $this->Supplier->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $banks = $this->BankCode->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Alterar fornecedor' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb','banks','bank_account_type'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(9, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Supplier->id = $id;
        $this->request->data = $this->Supplier->read();

        $this->request->data['Supplier']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Supplier']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Supplier->save($this->request->data)) {
            $this->Flash->set(__('O fornecedor foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
     /*********************
                DOCUMENTOS
     **********************/
    public function documents($id)
    {
        $this->Permission->check(11, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['Supplier.id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Docsupplier.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $this->Supplier->id = $id;
        $cliente = $this->Supplier->read();

        $action = 'Documentos';

       $data = $this->Paginator->paginate('Docsupplier', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            $cliente['Supplier']['nome_secundario'] => ['controller' => 'suppliers', 'action' => 'edit', $id],
            'Documentos' => '',
        ];
        $this->set(compact('status', 'data', 'id', 'action', 'breadcrumb'));
    }

    public function add_document($id)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $this->Docsupplier->create();
            if ($this->Docsupplier->validates()) {
                $this->request->data['Docsupplier']['user_creator_id'] = CakeSession::read('Auth.User.id');
                if ($this->Docsupplier->save($this->request->data)) {
                    $this->Flash->set(__('O documento foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "../suppliers/documents/" . $id]);
                } else {
                    $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
            
        }

        $this->Supplier->id = $id;
        $cliente = $this->Supplier->read();

        $action = 'Documentos';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            $cliente['Supplier']['nome_secundario'] => ['controller' => 'suppliers', 'action' => 'edit', $id],
            'Novo Documento' => '',
        ];
        $this->set("form_action", "../suppliers/add_document/" . $id);
        $this->set(compact('statuses', 'action', 'id', 'breadcrumb'));
    }

    public function edit_document($id, $document_id = null)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Docsupplier->id = $document_id;
        if ($this->request->is(['post', 'put'])) {
            $this->Docsupplier->validates();
            if ($this->request->data['Docsupplier']['file']['name'] == '') {
                unset($this->request->data['Docsupplier']['file']);
            }
            $this->request->data['Docsupplier']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->Docsupplier->save($this->request->data)) {
                $this->Flash->set(__('O documento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'documents/' . $id]);
            } else {
                $this->Flash->set(__('O documento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->Docsupplier->validationErrors;
        $this->request->data = $this->Docsupplier->read();
        $this->Docsupplier->validationErrors = $temp_errors;

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $breadcrumb = [
            $cliente['Supplier']['nome_secundario'] => ['controller' => 'suppliers', 'action' => 'edit', $id],
            'Alterar Documento' => '',
        ];
        $this->set("action", 'Documentos');
        $this->set("form_action", "../suppliers/edit_document/" . $id);
        $this->set(compact('statuses', 'id', 'document_id', 'breadcrumb'));

        $this->render("add_document");
    }


    public function delete_document($supplier_id, $id)
    {
        $this->Permission->check(11, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->Docsupplier->id = $id;
        $this->request->data = $this->Docsupplier->read();

        $this->request->data['Docsupplier']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['Docsupplier']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->Docsupplier->save($this->request->data)) {
            unlink(APP . 'webroot/files/docsupplier/file/' . $this->request->data["Docsupplier"]["id"] . '/' . $this->request->data["Docsupplier"]["file"]);

            $this->Flash->set(__('O documento foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'documents/' . $supplier_id]);
        }
    }

}
