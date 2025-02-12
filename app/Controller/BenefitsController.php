<?php
class BenefitsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['Benefit', 'Status', 'Supplier', 'BenefitType', 'CepbrEstado', 'CustomerUserItinerary', 'LogBenefits', 'User'];

    public $paginate = [
        'Benefit' => [
            'limit' => 10,
            'order' => ['Status.id' => 'asc', 'Supplier.id' => 'asc']
        ],
        'LogBenefits' => [
            'limit' => 50,
            'order' => ['LogBenefits.log_date' => 'desc']
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(71, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'Benefit.code LIKE' => "%".$_GET['q']."%", 
                'BenefitType.name LIKE' => "%".$_GET['q']."%", 
                'Benefit.name LIKE' => "%".$_GET['q']."%", 
                'Supplier.documento LIKE' => "%".$_GET['q']."%", 
                'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%"
            ]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['o']]);
        }

        if (isset($_GET["o"]) and $_GET["o"] != "") {
            $condition['and'] = array_merge($condition['and'], ['BenefitType.name' => $_GET['o']]);
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
        $benefitTypes = $this->BenefitType->find('all', [
            'fields' => ['BenefitType.name'],
            'order' => ['BenefitType.name ASC']
        ]);        

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb','benefitTypes'));
    }
    
    public function add()
    {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Benefit->create();
            if ($this->Benefit->validates()) {
                $this->request->data['Benefit']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Benefit->save($this->request->data)) {
                    $id = $this->Benefit->id;

                    $this->Flash->set(__('O Benefício foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/' . $id]);
                } else {
                    $this->Flash->set(__('O Benefício não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O Benefício não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia'], 'order' => 'Supplier.nome_fantasia']);
        $benefit_types = $this->BenefitType->find('list');
        $states = $this->CepbrEstado->find('list');

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Novo Benefício' => ''];
        $this->set("form_action", "add");
        $this->set(compact('action', 'breadcrumb', 'suppliers', 'benefit_types', 'statuses', 'states'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(71, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Benefit->id = $id;
        $benef = $this->Benefit->read();

        if ($this->request->is(['post', 'put'])) {
            $unit_price = str_replace(',', '.', str_replace('.', '', $this->request->data['Benefit']['unit_price']));

            if($benef['Benefit']['unit_price_not_formated'] <> $unit_price){
                $dados_log = [
                    'old_value' => $benef['Benefit']['unit_price'],
                    'de' => $benef['Benefit']['de'],
                    'ate' => $benef['Benefit']['ate'],
                    'benefit_id' => $id,
                    'user_id' => CakeSession::read("Auth.User.id")
                ];

                $this->LogBenefits->save($dados_log);
            }

            // if is_variable is not present set it as 2
            if(!isset($this->request->data['Benefit']['is_variable']) 
                || $this->request->data['Benefit']['is_variable'] == ''
                || $this->request->data['Benefit']['is_variable'] == 0){
                $this->request->data['Benefit']['is_variable'] = 2;
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
                $this->redirect(['action' => 'edit/' . $id]);
            } else {
                $this->Flash->set(__('O Benefício não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
        
        $temp_errors = $this->Benefit->validationErrors;
        $this->request->data = $this->Benefit->read();
        $this->Benefit->validationErrors = $temp_errors;

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia'], 'order' => 'Supplier.nome_fantasia']);
        $benefit_types = $this->BenefitType->find('list');
        $states = $this->CepbrEstado->find('list');

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Alterar Benefício' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'suppliers', 'benefit_types', 'statuses', 'states'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(71, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->Benefit->id = $id;

        $data = ['Benefit' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->Benefit->save($data)) {
            $this->Flash->set(__('O Benefício foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function log_status($id)
    {
        $this->Permission->check(71, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['LogBenefits.benefit_id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'User.name LIKE' => "%".$_GET['q']."%", 
                'LogBenefits.old_value LIKE' => "%".$_GET['q']."%", 
            ]);
        }

        $this->LogBenefits->id = $id;
        $cliente = $this->LogBenefits->read();

        $action = 'Log Status';

        $data = $this->Paginator->paginate('LogBenefits', $condition);

        if (isset($_GET['exportar'])) {
            $nome = 'log_beneficios.xlsx';

            $data = $this->LogBenefits->find('all', [
                'conditions' => $condition,
            ]);

            $this->ExcelGenerator->gerarExcelLogBeneficios($nome, $data);

            $this->redirect("/files/excel/" . $nome);
        }

        $this->set(compact('data', 'id', 'action'));
    }
}
