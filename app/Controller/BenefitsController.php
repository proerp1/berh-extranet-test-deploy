<?php
class BenefitsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['Benefit', 'Status', 'Supplier', 'BenefitType', 'CepbrEstado', 'CustomerUserItinerary', 'LogBenefits', 'User'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Supplier.id' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(16, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'Benefit.code LIKE' => "%".$_GET['q']."%", 
                'BenefitType.name LIKE' => "%".$_GET['q']."%", 
                'Benefit.name LIKE' => "%".$_GET['q']."%", 
                'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%"
            ]);
        }

        if (isset($_GET['exportar'])) {
            // $this->ExcelGenerator->gerarExcelFornecedores('fornecedores_', $data);

            // $this->redirect('/private_files/baixar/excel/fornecedores_xlsx');
            $nome = 'Beneficios_' . date('d_m_Y_H_i_s') . '.xlsx';

            $data = $this->Benefit->find('all', [
                'contain' => ['Status'],
                'conditions' => $condition, 
            ]);

            $this->ExcelGenerator->gerarExcelBeneficios($nome, $data);

            $this->redirect("/files/excel/" . $nome);
        }

        $data = $this->Paginator->paginate('Benefit', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Benefit->create();
            if ($this->Benefit->validates()) {
                $this->request->data['Benefit']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Benefit->save($this->request->data)) {
                    $this->Flash->set(__('O Benefício foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->set(__('O Benefício não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O Benefício não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        // $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia'], 'order' => 'Supplier.nome_fantasia']);
        $benefit_types = $this->BenefitType->find('list');
        $states = $this->CepbrEstado->find('list');

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Novo Benefício' => ''];
        $this->set("form_action", "add");
        $this->set(compact('action', 'breadcrumb', 'suppliers', 'benefit_types', 'states'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Benefit->id = $id;
        $antigo = $this->Benefit->read();
        // debug($antigo['Benefit']['unit_price']);die;
        if ($this->request->is(['post', 'put'])) {
            $unit_price = str_replace(',', '.', str_replace('.', '', $this->request->data['Benefit']['unit_price']));

            if($antigo['Benefit']['unit_price']<>$unit_price){
                $dados_log = [
                   
                    'old_value' => $antigo['Benefit']['unit_price'],
                    'benefit_id' => $id,
                    'user_id' => CakeSession::read("Auth.User.id")
                   
                ];
                $this->LogBenefits->save($dados_log);
            }
           
    

            $ShouldUpdateItinerary = $this->request->data['ShouldUpdateItinerary'];
            $this->Benefit->validates();
            $this->request->data['Benefit']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Benefit->save($this->request->data)) {
                if($ShouldUpdateItinerary == '1'){
                    $this->CustomerUserItinerary->unbindModel(
                        ['belongsTo' => ['Benefit', 'CustomerUser']]
                    );
                    // convert to float
                    $this->CustomerUserItinerary->updateAll(
                        ['CustomerUserItinerary.unit_price' => $unit_price],
                        ['CustomerUserItinerary.benefit_id' => $id]
                    );
                }
                $this->Flash->set(__('O Benefício foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O Benefício não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        
        $temp_errors = $this->Benefit->validationErrors;
        $this->request->data = $this->Benefit->read();
        $this->Benefit->validationErrors = $temp_errors;
        
        $suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia'], 'order' => 'Supplier.nome_fantasia']);
        $benefit_types = $this->BenefitType->find('list');
        $states = $this->CepbrEstado->find('list');

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Alterar Benefício' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'suppliers', 'benefit_types', 'states'));
        
        $this->render("add");
    }

    public function log_status($id)
    {
        $this->Permission->check(16, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['LogBenefits.id' => $id], 'or' => []];

        $this->LogBenefits->id = $id;
        $cliente = $this->LogBenefits->read();

        $action = 'Log Status';

       $data = $this->Paginator->paginate('LogBenefits', $condition);
     
        $this->set(compact('data', 'id', 'action'));
    }



}
