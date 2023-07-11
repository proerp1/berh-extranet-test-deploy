<?php
class BankTicketsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email'];
    public $uses = ['Customer', 'Status', 'Log', 'ProCgo', 'CustomerBkp', 'CustomerUser', 'TransparencyCategory', 'BankTicket', 'BankAccount', 'Bank'];

    public $paginate = [
        'Customer' => [
            'limit' => 10,
            'order' => ['name' => 'asc']
        ],
        'Log' => [
            'limit' => 10,
            'order' => ['Log.log_date' => 'desc']
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(45, "leitura")? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        $data = $this->Paginator->paginate('BankTicket', $condition);

        $status = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Contas e Boletos';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Contas e Boletos' => '', 'Boletos' => ''];
        $this->set(compact('status', 'action', 'breadcrumb', 'data'));
    }


    public function add($idBank)
    {
        $this->Permission->check(45, "escrita") ? "" : $this->redirect("/not_allowed");

        if ($this->request->is(['post', 'put'])) {
            $this->BankTicket->create();

            if ($this->BankTicket->validates()) {
                $this->request->data['BankTicket']['user_creator_id'] = CakeSession::read("Auth.User.id");
                $this->request->data['BankTicket']['valor_taxa_bancaria'] = $this->request->data['BankTicket']['taxa_bancaria'];
                $this->request->data['BankTicket']['multa_boleto'] = $this->request->data['BankTicket']['multa'];
                $this->request->data['BankTicket']['juros_boleto_dia'] = $this->request->data['BankTicket']['juros'];
                $this->request->data['BankTicket']['bank_account_id'] = $idBank;

                unset($this->request->data['BankTicket']['taxa_bancaria']);
                unset($this->request->data['BankTicket']['multa']);
                unset($this->request->data['BankTicket']['juros']);


                if ($this->BankTicket->save($this->request->data)) {
                    $this->Session->setFlash(__('O registro foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect("/bank_tickets/edit/".$this->BankTicket->id);
                } else {
                    $this->Session->setFlash(__('Registro não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Session->setFlash(__('Registro não pode ser salvo, por favor tente novamente.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $form_action = 'add/'.$idBank;

        $action = 'Contas e Boletos';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Contas e Boletos' => '', 'Boletos' => '', 'Novo boleto' => ''];
        $this->set(compact('statuses', 'form_action', 'idBank', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(45, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->BankTicket->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['BanksTickets']['user_updated_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['BankTicket']['valor_taxa_bancaria'] = $this->request->data['BankTicket']['taxa_bancaria'];
            $this->request->data['BankTicket']['multa_boleto'] = $this->request->data['BankTicket']['multa'];
            $this->request->data['BankTicket']['juros_boleto_dia'] = $this->request->data['BankTicket']['juros'];

            $this->BankTicket->validates();
            
            if ($this->BankTicket->save($this->request->data)) {
                $this->Session->setFlash(__('O fundo foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Session->setFlash(__('O fundo não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->BankTicket->validationErrors;
        $this->request->data = $this->BankTicket->read();
        $this->request->data['BankTicket']['taxa_bancaria'] = $this->request->data['BankTicket']['valor_taxa_bancaria'];
        $this->request->data['BankTicket']['multa'] = $this->request->data['BankTicket']['multa_boleto'];
        $this->request->data['BankTicket']['juros'] = $this->request->data['BankTicket']['juros_boleto_dia'];
        $this->BankTicket->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Contas e Boletos';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Contas e Boletos' => '', 'Boletos' => '', 'Editar boleto' => ''];
        $this->set(compact('statuses', 'action', 'breadcrumb'));
        $this->set("form_action", "edit");
        $this->set("idBank", $this->request->data['BankTicket']['bank_account_id']);
        
        $this->render("add");
    }
    
    public function tickets($id = null)
    {
        $this->Permission->check(45, "leitura")? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;
        $this->BankTicket->id = $id;

        $condition = ["and" => ['BankTicket.bank_account_id' => $this->BankTicket->id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['BankTicket.carteira LIKE' => "%".$_GET['q']."%", 'BankTicket.codigo_cedente LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('BankTicket', $condition);

        $bank = $this->BankAccount->find('first', ['conditions' => ['BankAccount.id' => $this->BankTicket->id]]);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Contas e Boletos';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Contas e Boletos' => '', 'Boletos' => ''];
        $this->set("form_action", "Boletos");
        $this->set('idBank', $this->BankTicket->id);
        $this->set(compact('action', 'breadcrumb', 'data', 'status'));
    }


    public function delete($id)
    {
        $this->Permission->check(45, "leitura")? "" : $this->redirect("/not_allowed");
        $this->BankTicket->id = $id;

        $data = ['BankTicket' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->BankTicket->save($data)) {
            $this->Session->setFlash(__('O associado foi excluido com sucesso'), 'default', ['class' => 'alert alert-success']);
            $this->redirect(['action' => 'index']);
        }
    }
}
