<?php

use League\Csv\Reader;

class CustomerBenefitCodesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['CustomerBenefitCode', 'Customer', 'Benefit'];

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index($id) {
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

        $allIds = $this->CustomerBenefitCode->find('list', ['condition' => $condition, 'fields' => ['CustomerBenefitCode.id']]);

        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Clientes' => ''];
        $this->set('url_novo', "/customer_benefit_codes/add/$id");
        $this->set('can_bulk_edit', $this->Permission->check(83, 'leitura'));
        $this->set(compact('data', 'id', 'action', 'breadcrumb', 'allIds'));
    }
    
    public function add($customer_id) {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");

        if ($this->request->is(['post', 'put'])) {
            $this->CustomerBenefitCode->create();
            if ($this->CustomerBenefitCode->validates()) {
                $benefit = $this->Benefit->find('first', ['conditions' => ['Benefit.id' => $this->request->data['CustomerBenefitCode']['benefit_id']]]);

                $this->request->data['CustomerBenefitCode']['code_be'] = $benefit['Benefit']['code'];
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

        $this->Benefit->virtualFields = ['code_name' => "CONCAT(Benefit.code, ' - ', Benefit.name)"];
        $benefits = $this->Benefit->find('list', ['fields' => ['Benefit.id', 'Benefit.code_name']]);

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Nova Exceção' => ''];
        $this->set("form_action", "add/$customer_id");
        $this->set(compact('customer_id', 'action', 'breadcrumb', 'benefits'));
    }

    public function edit($customer_id, $id = null) {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CustomerBenefitCode->id = $id;

        if ($this->request->is(['post', 'put'])) {
            if ($this->CustomerBenefitCode->validates()) {
                $benefit = $this->Benefit->find('first', ['conditions' => ['Benefit.id' => $this->request->data['CustomerBenefitCode']['benefit_id']]]);

                $this->request->data['CustomerBenefitCode']['code_be'] = $benefit['Benefit']['code'];
                $this->request->data['CustomerBenefitCode']['user_updated_id'] = CakeSession::read("Auth.User.id");
                if ($this->CustomerBenefitCode->save($this->request->data)) {
                    $this->Flash->set(__('A exceção do benefício foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "edit/$customer_id/$id"]);
                } else {
                    $this->Flash->set(__('A exceção do benefício não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A exceção do benefício não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CustomerBenefitCode->validationErrors;
        $this->request->data = $this->CustomerBenefitCode->read();
        $this->CustomerBenefitCode->validationErrors = $temp_errors;

        $this->Benefit->virtualFields = ['code_name' => "CONCAT(Benefit.code, ' - ', Benefit.name)"];
        $benefits = $this->Benefit->find('list', ['fields' => ['Benefit.id', 'Benefit.code_name']]);

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Alterar Benefício' => ''];
        $this->set("form_action", "edit/$customer_id");
        $this->set(compact('customer_id', 'id', 'action', 'breadcrumb', 'benefits'));
        
        $this->render("add");
    }

    public function delete($customer_id, $id) {
        $this->Permission->check(71, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->CustomerBenefitCode->id = $id;

        $data = ['CustomerBenefitCode' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->CustomerBenefitCode->save($data)) {
            $this->Flash->set(__('O Benefício foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index/'.$customer_id]);
        }
    }

    public function delete_all($customer_id) {
        $this->Permission->check(83, 'leitura') ? '' : $this->redirect('/not_allowed');
        if (!isset($_GET['ids']) || !$_GET['ids']) $this->redirect($this->referer());

        $benefitIds = explode(',', $_GET['ids']);

        foreach ($benefitIds as $benefitId) {
            $this->CustomerBenefitCode->id = $benefitId;

            $data = ['CustomerBenefitCode' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];
            $this->CustomerBenefitCode->save($data);
        }

        $this->Flash->set(__('Benefícios excluidos com sucesso'), ['params' => ['class' => 'alert alert-success']]);
        $this->redirect(['action' => 'index/'.$customer_id]);
    }

    public function upload($customerId) {
        $file = file_get_contents($this->request->data['file']['tmp_name'], FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $benefits = $this->Benefit->find('list', ['fields' => ['Benefit.code', 'Benefit.id']]);

        $line = 0;
        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if ($line == 0) {
                    $line++;
                }
                continue;
            }

            $benefitId = $benefits[$row[0]] ?? null;

            $this->CustomerBenefitCode->create();
            $this->CustomerBenefitCode->save([
                'CustomerBenefitCode' => [
                    'customer_id' => $customerId,
                    'code_be' => $row[0],
                    'benefit_id' => $benefitId,
                    'code_customer' => $row[1],
                ],
            ]);

            $line++;
        }

        $this->Flash->set(__('Códigos incluídos com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'index/' . $customerId]);
    }
}
