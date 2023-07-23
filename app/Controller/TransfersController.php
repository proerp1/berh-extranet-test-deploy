<?php
class TransfersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Transfer', 'Status', 'BankAccount', 'Income', 'Outcome'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Transfer.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(22, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['BankAccountOrigin.name LIKE' => "%".$_GET['q']."%", 'BankAccountDest.name LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Transfer', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 8]]);

        $action = 'Transferência';
        $breadcrumb = ['Financeiro' => '', 'Transferência' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(22, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Transfer->create();
            if ($this->Transfer->validates()) {
                $this->request->data['Transfer']['user_creator_id'] = CakeSession::read("Auth.User.id");
                $this->request->data['Transfer']['status_id'] = 29;
                if ($this->Transfer->save($this->request->data)) {
                    $this->Flash->set(__('A transferência foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->set(__('A transferência não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A transferência não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 8]]);
        $bankAccountOrigins = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);
        $bankAccountDests = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);

        $action = 'Transferência';
        $breadcrumb = ['Financeiro' => '', 'Transferência' => ['action' => 'index'], 'Nova transferência' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'bankAccountOrigins', 'bankAccountDests', 'action', 'breadcrumb'));
    }

    public function aprovar($id)
    {
        $this->Permission->check(22, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Transfer->id = $id;

        $data = ['Transfer' => ['status_id' => 30]];

        if ($this->Transfer->save($data)) {
            $transferencia = $this->Transfer->read();

            $conta_receber = ['Income' => ['bank_account_id' => $transferencia['Transfer']['bank_account_origin_id'],
                'status_id' => 15,
                'name' => $transferencia['Transfer']['observation'],
                'valor_bruto' => $transferencia['Transfer']['value'],
                'valor_total' => $transferencia['Transfer']['value'],
                'parcela' => 1,
                'vencimento' => date('d/m/Y', strtotime('+5days')),
                'user_creator_id' => CakeSession::read("Auth.User.id")
            ]];
            $this->Income->create();
            $this->Income->save($conta_receber);

            $conta_pagar = ['Outcome' => ['bank_account_id' => $transferencia['Transfer']['bank_account_dest_id'],
                'status_id' => 11,
                'name' => $transferencia['Transfer']['observation'],
                'valor_bruto' => $transferencia['Transfer']['value'],
                'valor_total' => $transferencia['Transfer']['value'],
                'parcela' => 1,
                'vencimento' => date('d/m/Y', strtotime('+5days')),
                'user_creator_id' => CakeSession::read("Auth.User.id")
            ]];
            $this->Outcome->create();
            $this->Outcome->save($conta_pagar);

            $this->Flash->set(__('A transferência foi reprovada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function reprovar($id)
    {
        $this->Permission->check(22, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Transfer->id = $id;

        $data = ['Transfer' => ['status_id' => 31]];

        if ($this->Transfer->save($data)) {
            $this->Flash->set(__('A transferência foi reprovada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function edit($id = null)
    {
        $this->Permission->check(22, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Transfer->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Transfer->validates();
            $this->request->data['Transfer']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Transfer->save($this->request->data)) {
                $this->Flash->set(__('A transferência foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('A transferência não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Transfer->validationErrors;
        $this->request->data = $this->Transfer->read();
        $this->Transfer->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 8]]);
        $bankAccountOrigins = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);
        $bankAccountDests = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);

        $action = 'Transferência';
        $breadcrumb = ['Financeiro' => '', 'Transferência' => ['action' => 'index'], 'Alterar transferência' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'bankAccountOrigins', 'bankAccountDests', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(22, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Transfer->id = $id;

        $data = ['Transfer' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->Transfer->save($data)) {
            $this->Flash->set(__('A transferência foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
