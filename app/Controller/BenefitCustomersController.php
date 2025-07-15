<?php
class BenefitCustomersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['Benefit', 'BenefitCustomer', 'Customer'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Permission->check(71, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['BenefitCustomer.benefits_id' => $id], 'or' => []];

        if (isset($_GET['q']) && $_GET['q'] != '') {
            $query = $_GET['q'];
            $condition['and'] = array_merge($condition['and'], ["Customer.nome_primario LIKE '%$query%'"]);
        }

        $action = 'Clientes';

        $data = $this->Paginator->paginate('BenefitCustomer', $condition);

        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Clientes' => ''];
        $this->set('url_novo', "/benefit_customers/add/$id");
        $this->set(compact('data', 'id', 'action', 'breadcrumb'));
    }
    
    public function add($benefit_id)
    {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->BenefitCustomer->create();
            if ($this->BenefitCustomer->validates()) {
                $this->request->data['BenefitCustomer']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->BenefitCustomer->save($this->request->data)) {
                    $benefit_customer_id = $this->BenefitCustomer->id;

                    $this->Flash->set(__('A exceção do benefício foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "edit/$benefit_id/$benefit_customer_id"]);
                } else {
                    $this->Flash->set(__('A exceção do benefício não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A exceção do benefício não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $existingCustomers = $this->BenefitCustomer->find('list', [
            'fields' => ['BenefitCustomer.customer_id'],
            'conditions' => ['BenefitCustomer.benefits_id' => $benefit_id],
        ]);
        $customersConditions = [];
        if ($existingCustomers) {
            $customersConditions['Customer.id NOT IN'] = $existingCustomers;
        }

        $customers = $this->Customer->find('list', [
            'conditions' => $customersConditions,
        ]);

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Nova Exceção' => ''];
        $this->set("form_action", "add/$benefit_id");
        $this->set(compact('benefit_id', 'action', 'breadcrumb', 'customers'));
    }

    public function edit($benefit_id, $id = null)
    {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->BenefitCustomer->id = $id;

        if ($this->request->is(['post', 'put'])) {
            $this->BenefitCustomer->validates();
            $this->request->data['BenefitCustomer']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->BenefitCustomer->save($this->request->data)) {
                $this->Flash->set(__('O Benefício foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => "edit/$benefit_id/$id"]);
            } else {
                $this->Flash->set(__('O Benefício não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->BenefitCustomer->validationErrors;
        $this->request->data = $this->BenefitCustomer->read();
        $this->BenefitCustomer->validationErrors = $temp_errors;

        $customers = $this->Customer->find('list');

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Alterar Benefício' => ''];
        $this->set("form_action", "edit/$benefit_id");
        $this->set(compact('benefit_id', 'id', 'action', 'breadcrumb', 'customers'));
        
        $this->render("add");
    }

    public function delete($benefit_id, $id)
    {
        $this->Permission->check(71, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->BenefitCustomer->id = $id;

        $data = ['BenefitCustomer' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->BenefitCustomer->save($data)) {
            $this->Flash->set(__('O Benefício foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index/'.$benefit_id]);
        }
    }
}
