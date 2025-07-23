<?php
class CustomerBenefitCodesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['CustomerBenefitCode', 'Customer'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Permission->check(71, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['CustomerBenefitCode.customer_id' => $id], 'or' => []];

        if (isset($_GET['q']) && $_GET['q'] != '') {
            $query = $_GET['q'];
            $condition['or'] = array_merge($condition['or'], ["Customer.code_be LIKE '%$query%'"]);
            $condition['or'] = array_merge($condition['or'], ["Customer.code_customer LIKE '%$query%'"]);
        }

        if (isset($_GET['excel'])) {
            $dados = $this->CustomerBenefitCode->find('all', ['conditions' => $condition]);

            $nome = 'cliente_de_para_beneficio_' . date('d_m_Y');

            $this->ExcelGenerator->gerarExcelClienteDeParaBeneficios($nome, $dados);
            $this->redirect("/files/excel/" . $nome . ".xlsx");
        }

        $action = 'Clientes';

        $data = $this->Paginator->paginate('CustomerBenefitCode', $condition);

        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Clientes' => ''];
        $this->set('url_novo', "/customer_benefit_codes/add/$id");
        $this->set(compact('data', 'id', 'action', 'breadcrumb'));
    }
    
    public function add($customer_id)
    {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CustomerBenefitCode->create();
            if ($this->CustomerBenefitCode->validates()) {
                $this->request->data['CustomerBenefitCode']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->CustomerBenefitCode->save($this->request->data)) {
                    $benefit_customer_id = $this->CustomerBenefitCode->id;

                    $this->Flash->set(__('A exceção do benefício foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "edit/$customer_id/$benefit_customer_id"]);
                } else {
                    $this->Flash->set(__('A exceção do benefício não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A exceção do benefício não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Nova Exceção' => ''];
        $this->set("form_action", "add/$customer_id");
        $this->set(compact('customer_id', 'action', 'breadcrumb'));
    }

    public function edit($customer_id, $id = null)
    {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerBenefitCode->id = $id;

        if ($this->request->is(['post', 'put'])) {
            $this->CustomerBenefitCode->validates();
            $this->request->data['CustomerBenefitCode']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->CustomerBenefitCode->save($this->request->data)) {
                $this->Flash->set(__('O Benefício foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => "edit/$customer_id/$id"]);
            } else {
                $this->Flash->set(__('O Benefício não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CustomerBenefitCode->validationErrors;
        $this->request->data = $this->CustomerBenefitCode->read();
        $this->CustomerBenefitCode->validationErrors = $temp_errors;

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Alterar Benefício' => ''];
        $this->set("form_action", "edit/$customer_id");
        $this->set(compact('customer_id', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($customer_id, $id)
    {
        $this->Permission->check(71, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->CustomerBenefitCode->id = $id;

        $data = ['CustomerBenefitCode' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->CustomerBenefitCode->save($data)) {
            $this->Flash->set(__('O Benefício foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index/'.$customer_id]);
        }
    }
}
