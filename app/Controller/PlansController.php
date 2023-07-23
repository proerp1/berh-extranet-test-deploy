<?php
class PlansController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator'];
    public $uses = ['Plan', 'Status', 'PlanProduct', 'Product', 'Customer', 'PlanCustomer'];

    public $paginate = [
        'Plan' => [
            'limit' => 20,
            'order' => ['Status.id' => 'asc', 'Plan.description' => 'asc'],
            'fields' => ['Plan.*', 'Status.*', 'count(PlanCustomerTotal.id) as total_customers'],
            'joins' => [
                [
                    'table' => 'plan_customers',
                    'alias' => 'PlanCustomerTotal',
                    'type' => 'LEFT',
                    'conditions' => ['PlanCustomerTotal.plan_id = Plan.id']
                ],
                [
                    'table' => 'customers',
                    'alias' => 'Customers',
                    'type' => 'LEFT',
                    'conditions' => ['Customers.id = PlanCustomerTotal.customer_id']
                ]
            ],
            'group' => 'Plan.id'
        ],
        'PlanProduct' => ['limit' => 20, 'order' => ['Status.id' => 'asc', 'Product.name' => 'asc']]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(6, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Plan.description LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        if (isset($_GET["s"]) and $_GET["s"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Plan.type' => $_GET['s']]);
        }

        $data = $this->Paginator->paginate('Plan', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Planos';
        $breadcrumb = ['Cadastros' => '', 'Planos' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(6, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Plan->create();
            if ($this->Plan->validates()) {
                $this->request->data['Plan']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Plan->save($this->request->data)) {
                    $this->Flash->set(__('O plano foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/'.$this->Plan->id]);
                } else {
                    $this->Flash->set(__('O plano não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O plano não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Planos';
        $breadcrumb = ['Cadastros' => '', 'Planos' => '', 'Novo plano' => ''];
        $this->set(compact('statuses', 'action', 'breadcrumb'));
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(6, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Plan->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Plan->validates();
            
            if ($this->Plan->save($this->request->data)) {
                $this->Flash->set(__('O plano foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('O plano não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Plan->validationErrors;
        $this->request->data = $this->Plan->read();
        $this->Plan->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Planos';
        $breadcrumb = ['Cadastros' => '', 'Planos' => '', 'Alterar plano' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(6, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Plan->id = $id;
        $this->request->data = $this->Plan->read();

        $this->request->data['Plan']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Plan']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Plan->save($this->request->data)) {
            $this->Flash->set(__('O plano foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function copy($id)
    {
        $currPlan = $this->Plan->find('first', [
            'conditions' => [
                'Plan.id' => $id
            ],
            'recursive' => -1
        ]);
        unset($currPlan['Plan']['id']);
        unset($currPlan['Plan']['created']);

        $currPlan['Plan']['user_creator_id'] = CakeSession::read("Auth.User.id");
        $currPlan['Plan']['description'] = $currPlan['Plan']['description'].' - cópia';

        $this->Plan->create();
        $this->Plan->save($currPlan);

        $currPlanProduct = $this->PlanProduct->find('all', [
            'conditions' => [
                'PlanProduct.plan_id' => $id
            ],
            'recursive' => -1
        ]);

        foreach ($currPlanProduct as $planProduct) {
            unset($planProduct['PlanProduct']['id']);
            unset($planProduct['PlanProduct']['created']);

            $planProduct['PlanProduct']['plan_id'] = $this->Plan->id;
            $planProduct['PlanProduct']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->PlanProduct->create();
            $this->PlanProduct->save($planProduct);
        }

        $this->Flash->set(__('O plano foi clonado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect($this->referer());
    }

    /*********************
                COMPOSITION
    **********************/
        public function composition($id)
        {
            $this->Permission->check(6, "leitura") ? "" : $this->redirect("/not_allowed");
            $this->Paginator->settings = $this->paginate;

            $composicao = ($_GET['composicao'] == 1 ? [1,3] : $_GET['composicao']);

            $condition = ["and" => ['PlanProduct.plan_id' => $id], "or" => []];

            if (!empty($_GET['q'])) {
                $condition['or'] = array_merge($condition['or'], ['Product.name LIKE' => "%".$_GET['q']."%", 'Product.id' => $_GET['q']]);
            }

            if (!empty($_GET['composicao'])) {
                $condition['and'] = array_merge($condition['and'], ['Product.tipo' => $composicao]);
            }

            $this->Plan->id = $id;
            $plano = $this->Plan->read();

            $data = $this->Paginator->paginate('PlanProduct', $condition);

            $produtos_cadastrados = $this->PlanProduct->find('all', ['conditions' => ['PlanProduct.plan_id' => $id]]);

            $ids = '';
            foreach ($produtos_cadastrados as $value) {
                $ids .= $value['Product']['id'] ? $value['Product']['id'].',' : '';
            }
            $ids = substr($ids, 0, -1);
            
            $products = $this->Product->find('list', ['conditions' => ['Product.tipo' => $composicao, 'Product.status_id' => 1, 'Product.id not in ('.($ids != '' ? $ids : 0).')'], 'order' => ['Product.name']]);

            $form_action = '../plans/add_composition';

	        $action = 'Planos';
	        $breadcrumb = ['Cadastros' => '', 'Planos' => '', $plano['Plan']['description'] => '', 'Composição' => ''];
            $this->set(compact('data', 'action', 'id', 'form_action', 'products', 'breadcrumb'));
        }

        public function add_composition()
        {
            $this->Permission->check(6, "escrita") ? "" : $this->redirect("/not_allowed");
            if ($this->request->is(['post', 'put'])) {
                $this->PlanProduct->create();
                if ($this->PlanProduct->validates()) {
                    $this->request->data['PlanProduct']['user_creator_id'] = CakeSession::read("Auth.User.id");
                    if ($this->PlanProduct->save($this->request->data)) {
                        $this->Flash->set(__('Salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                        
                        $this->redirect($this->referer());
                    } else {
                        $this->Flash->set(__('Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                    }
                } else {
                    $this->Flash->set(__('Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            }
        }

        public function delete_composition($plan_id, $id)
        {
            $this->Permission->check(6, "excluir") ? "" : $this->redirect("/not_allowed");
            $this->PlanProduct->id = $id;

            $this->request->data['PlanProduct']['data_cancel'] = date("Y-m-d H:i:s");
            $this->request->data['PlanProduct']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

            if ($this->PlanProduct->save($this->request->data)) {
                $this->Flash->set(__('Excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect($this->referer());
            }
        }

    /*********************
                COMPOSITION
    **********************/

        public function customers($id)
        {
            $this->Permission->check(6, "leitura")? "" : $this->redirect("/not_allowed");
            $this->Paginator->settings = $this->paginate;

            $condition = ["and" => ['PlanCustomer.plan_id' => $id], "or" => []]; //'Customer.status_id in (3,4,6)'

            if (!empty($_GET['q'])) {
                $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.email LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%"]);
            }

            if (!empty($_GET["t"])) {
                $condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['t']]);
            }

            if (isset($_GET['exportar'])) {
                $nome = 'clientes_planos_'.date('d_m_Y_H_i_s').'.xlsx';

                $data = $this->PlanCustomer->find('all', ['conditions' => $condition]);

                $this->ExcelGenerator->gerarExcelPlansCustomers($nome, $data);

                $this->redirect("/files/excel/".$nome);
            }
            
            $data = $this->Paginator->paginate('PlanCustomer', $condition);
            $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

            $this->Plan->id = $id;
            $plano = $this->Plan->read();

            $action = 'Planos';
	        $breadcrumb = ['Cadastros' => '', 'Planos' => '', $plano['Plan']['description'] => '', 'Clientes' => ''];
            $this->set(compact('status', 'data', 'id', 'action', 'breadcrumb'));
        }
}
