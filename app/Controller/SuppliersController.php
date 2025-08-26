<?php

class SuppliersController extends AppController
{    
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['Supplier', 'Status','BankCode','BankAccountType', 'Docsupplier', 'CustomerSupplierLogin', 'Modalidade', 'Tecnologia', 'TecnologiaVersao', 'SupplierVolumeTier', 'LogSupplier'];

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
            $condition['or'] = array_merge($condition['or'], ['Supplier.id LIKE' => "%".$_GET['q']."%", 'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%", 'Supplier.razao_social LIKE' => "%".$_GET['q']."%", 'Supplier.documento LIKE' => "%".$_GET['q']."%", 'Tecnologia.name LIKE' => "%".$_GET['q']."%"]);
        }
    
        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

         // Filtro de região
        if (isset($_GET['r']) and $_GET['r'] != "") {
            $condition['and'] = array_merge($condition['and'], ['Supplier.regioes' => $_GET['r']]);
        }
        
    
        if (isset($_GET['exportar'])) {
            $nome = 'Fornecedores_' . date('d_m_Y_H_i_s') . '.xlsx';
    
            $data_sup = $this->Supplier->find('all', [
                'contain' => ['Status', 'BankAccountType', 'Tecnologia'], // Incluir a tabela Tecnologia
                'conditions' => $condition,
            ]);
    
            $data_log = $this->CustomerSupplierLogin->find('all');
    
            $this->ExcelGenerator->gerarExcelFornecedores($nome, $data_sup, $data_log);
    
            // Redirecionar para o download
            $this->redirect("/files/excel/".$nome);
        }
    
        // Paginação
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
                
                if (!empty($this->request->data['Supplier']['modalidade_id'])) {
                    $this->request->data['Supplier']['modalidade_id'] = $this->request->data['Supplier']['modalidade_id'];
                }
    
                if (!empty($this->request->data['Supplier']['tecnologia_id'])) {
                    $this->request->data['Supplier']['tecnologia_id'] = $this->request->data['Supplier']['tecnologia_id'];
                }
                
                if ($this->Supplier->save($this->request->data)) {
                    $this->LogSupplier->createLogSupplier($this->Supplier->read());
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
        $this->BankCode->virtualFields = ['name' => "concat(BankCode.name, ' - ', BankCode.code)"];
        $this->BankCode->displayField = 'name';
        $banks = $this->BankCode->find('list');         $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $modalidades = $this->Modalidade->find('list', ['fields' => ['id', 'name']]);
        $tecnologias = $this->Tecnologia->find('list', ['fields' => ['id', 'name']]);
        $versao_creditos = [];
        $versao_cadastros = [];
    
        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Novo fornecedor' => ''];
        
        $this->set(compact('statuses', 'action', 'breadcrumb', 'banks', 'bank_account_type', 'modalidades', 'tecnologias', 'versao_cadastros', 'versao_creditos'));
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
                $this->LogSupplier->createLogSupplier($this->Supplier->read());
                $this->Flash->set(__('O fornecedor foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('O fornecedor não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Supplier->validationErrors;
        $this->request->data = $this->Supplier->read();
        $this->Supplier->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $this->BankCode->virtualFields = ['name' => "concat(BankCode.name, ' - ', BankCode.code)"];
        $this->BankCode->displayField = 'name';
        $banks = $this->BankCode->find('list'); 
        $modalidades = $this->Modalidade->find('list', ['fields' => ['id', 'name']]);
        $tecnologias = $this->Tecnologia->find('list', ['fields' => ['id', 'name']]);

        $versao_conditions = ['TecnologiaVersao.tecnologia_id' => $this->request->data['Supplier']['tecnologia_id']];
        $fields = ['TecnologiaVersao.id', 'TecnologiaVersao.nome'];
        $versao_creditos = $this->TecnologiaVersao->find('list', ['fields' => $fields, 'conditions' => array_merge($versao_conditions, ['TecnologiaVersao.tipo' => 'credito'])]);
        $versao_cadastros = $this->TecnologiaVersao->find('list', ['fields' => $fields, 'conditions' => array_merge($versao_conditions, ['TecnologiaVersao.tipo' => 'cadastro'])]);

        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Alterar fornecedor' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb','banks','bank_account_type', 'modalidades', 'tecnologias', 'versao_cadastros', 'versao_creditos'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(9, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Supplier->id = $id;

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

        $this->Paginator->settings = ['Docsupplier' => [
            'limit' => 100,
            'order' => ['Docsupplier.created' => 'desc'],
            
            ]
        ];
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
        
        $this->set(compact('status', 'data', 'id', 'action'));
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
        
        $this->set("form_action", "../suppliers/add_document/" . $id);
        $this->set(compact('statuses', 'action', 'id'));
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
        
        $this->set("action", 'Documentos');
        $this->set("form_action", "../suppliers/edit_document/" . $id);
        $this->set(compact('statuses', 'id', 'document_id'));

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

    /*********************
            VOLUME TIERS
    **********************/
    public function volume_tiers($id)
    {
        $this->Permission->check(9, 'leitura') ? '' : $this->redirect('/not_allowed');

        $this->Supplier->id = $id;
        $supplier = $this->Supplier->read();
        
        if (!$supplier) {
            $this->Flash->set(__('Fornecedor não encontrado'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action' => 'index']);
        }

        $this->Paginator->settings = ['SupplierVolumeTier' => [
            'limit' => 100,
            'order' => ['SupplierVolumeTier.de_qtd' => 'asc'],
            'contain' => []
        ]];

        $condition = ['and' => ['SupplierVolumeTier.supplier_id' => $id], 'or' => []];

        $data = $this->Paginator->paginate('SupplierVolumeTier', $condition);
        $action = 'Faixas de Volume';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '/suppliers', 'Faixas de Volume' => ''];
        
        $this->set(compact('data', 'id', 'action', 'breadcrumb', 'supplier'));
    }

    public function add_volume_tier($id)
    {
        $this->Permission->check(9, 'escrita') ? '' : $this->redirect('/not_allowed');
        
        $this->Supplier->id = $id;
        $supplier = $this->Supplier->read();
        
        if (!$supplier) {
            $this->Flash->set(__('Fornecedor não encontrado'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post', 'put'])) {
            $this->SupplierVolumeTier->create();
            $this->request->data['SupplierVolumeTier']['supplier_id'] = $id;
            
            // Format data before validation (like other models)
            if (!empty($this->request->data['SupplierVolumeTier']['percentual_repasse'])) {
                $this->request->data['SupplierVolumeTier']['percentual_repasse'] = $this->SupplierVolumeTier->priceFormatBeforeSave($this->request->data['SupplierVolumeTier']['percentual_repasse']);
            }
            if (!empty($this->request->data['SupplierVolumeTier']['valor_fixo'])) {
                $this->request->data['SupplierVolumeTier']['valor_fixo'] = $this->SupplierVolumeTier->priceFormatBeforeSave($this->request->data['SupplierVolumeTier']['valor_fixo']);
            }
            
            // Set data for validation
            $this->SupplierVolumeTier->set($this->request->data);
            
            // Validar se não há sobreposição
            $deQtd = $this->request->data['SupplierVolumeTier']['de_qtd'];
            $ateQtd = $this->request->data['SupplierVolumeTier']['ate_qtd'];
            
            if (!$this->SupplierVolumeTier->validateNoOverlap($id, $deQtd, $ateQtd)) {
                $this->Flash->set(__('Já existe uma faixa que se sobrepõe a esta. Verifique os valores.'), ['params' => ['class' => "alert alert-danger"]]);
            } elseif ($this->SupplierVolumeTier->validates()) {
                $this->request->data['SupplierVolumeTier']['user_creator_id'] = CakeSession::read('Auth.User.id');
                
                if ($this->SupplierVolumeTier->save($this->request->data)) {
                    $this->Flash->set(__('Faixa de volume foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'volume_tiers', $id]);
                } else {
                    $this->Flash->set(__('A faixa de volume não pode ser salva. Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('A faixa de volume não pode ser salva. Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $action = 'Nova Faixa de Volume';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '/suppliers', 'Faixas de Volume' => '/suppliers/volume_tiers/'.$id, 'Nova Faixa' => ''];
        
        $this->set("form_action", "add_volume_tier/" . $id);
        $this->set(compact('action', 'id', 'breadcrumb', 'supplier'));
    }

    public function edit_volume_tier($id, $tier_id = null)
    {
        $this->Permission->check(9, 'escrita') ? '' : $this->redirect('/not_allowed');
        
        $this->SupplierVolumeTier->id = $tier_id;
        $tier = $this->SupplierVolumeTier->read();
        
        if (!$tier || $tier['SupplierVolumeTier']['supplier_id'] != $id) {
            $this->Flash->set(__('Faixa de volume não encontrada'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action' => 'volume_tiers', $id]);
        }

        if ($this->request->is(['post', 'put'])) {
            // Format data before validation (like other models)
            if (!empty($this->request->data['SupplierVolumeTier']['percentual_repasse'])) {
                $this->request->data['SupplierVolumeTier']['percentual_repasse'] = $this->SupplierVolumeTier->priceFormatBeforeSave($this->request->data['SupplierVolumeTier']['percentual_repasse']);
            }
            if (!empty($this->request->data['SupplierVolumeTier']['valor_fixo'])) {
                $this->request->data['SupplierVolumeTier']['valor_fixo'] = $this->SupplierVolumeTier->priceFormatBeforeSave($this->request->data['SupplierVolumeTier']['valor_fixo']);
            }
            
            // Set data for validation
            $this->SupplierVolumeTier->set($this->request->data);
            
            $deQtd = $this->request->data['SupplierVolumeTier']['de_qtd'];
            $ateQtd = $this->request->data['SupplierVolumeTier']['ate_qtd'];
            
            // Validar se não há sobreposição (excluindo o registro atual)
            if (!$this->SupplierVolumeTier->validateNoOverlap($id, $deQtd, $ateQtd, $tier_id)) {
                $this->Flash->set(__('Já existe uma faixa que se sobrepõe a esta. Verifique os valores.'), ['params' => ['class' => "alert alert-danger"]]);
            } elseif ($this->SupplierVolumeTier->validates()) {
                $this->request->data['SupplierVolumeTier']['user_updated_id'] = CakeSession::read('Auth.User.id');
                
                if ($this->SupplierVolumeTier->save($this->request->data)) {
                    $this->Flash->set(__('Faixa de volume foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'volume_tiers', $id]);
                } else {
                    $this->Flash->set(__('A faixa de volume não pode ser alterada. Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('A faixa de volume não pode ser alterada. Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        // Only read the database data when NOT processing a POST request
        if (!$this->request->is(['post', 'put'])) {
            $this->request->data = $this->SupplierVolumeTier->read();
        }

        $this->Supplier->id = $id;
        $supplier = $this->Supplier->read();
        
        $action = 'Editar Faixa de Volume';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '/suppliers', 'Faixas de Volume' => '/suppliers/volume_tiers/'.$id, 'Editar Faixa' => ''];
        
        $this->set("form_action", "edit_volume_tier/" . $id . '/' . $tier_id);
        $this->set(compact('action', 'id', 'tier_id', 'breadcrumb', 'supplier'));
        
        $this->render("add_volume_tier");
    }

    public function delete_volume_tier($id, $tier_id)
    {
        $this->Permission->check(9, 'excluir') ? '' : $this->redirect('/not_allowed');
        
        $this->SupplierVolumeTier->id = $tier_id;
        $tier = $this->SupplierVolumeTier->read();
        
        if (!$tier || $tier['SupplierVolumeTier']['supplier_id'] != $id) {
            $this->Flash->set(__('Faixa de volume não encontrada'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action' => 'volume_tiers', $id]);
        }

        $this->request->data['SupplierVolumeTier']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['SupplierVolumeTier']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->SupplierVolumeTier->save($this->request->data)) {
            $this->Flash->set(__('Faixa de volume foi excluída com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } else {
            $this->Flash->set(__('Erro ao excluir faixa de volume'), ['params' => ['class' => "alert alert-danger"]]);
        }
        
        $this->redirect(['action' => 'volume_tiers', $id]);
    }

}
