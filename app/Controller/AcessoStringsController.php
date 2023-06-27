<?php
class AcessoStringsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Acesso', 'AcessoFeature', 'Feature', 'PlanCustomer', 'PlanProduct', 'Customer', 'Product'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Acesso.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $acesso = $this->Acesso->find("all", ["conditions" => ["Acesso.customer_id" => $id] ]);
        $acessoFeatures = $this->AcessoFeature->find("all", ["conditions" => ["AcessoFeature.customer_id" => $id] ]);

        $this->PlanCustomer->unbindModel(['belongsTo' => ['Status', 'UsuarioAlteracao', 'PriceTable']]);
        $planCustomer = $this->PlanCustomer->find('first', [
            'fields' => ['Customer.nome_secundario', 'Plan.id'],
            'conditions' => ['PlanCustomer.customer_id' => $id, 'PlanCustomer.status_id' => 1]
        ]);

        $planProduct = $this->PlanProduct->find('first', ['conditions' => ['PlanProduct.plan_id' => $planCustomer['Plan']['id'], 'Product.tipo' => 4], 'fields' => ['group_concat(PlanProduct.product_id) as ids']]);
          
        $produto = $this->Product->find("all", ["conditions" => ["Product.status_id" => 1, 'Product.id' => explode(',', $planProduct[0]['ids'])], "order" => ["Product.name" => "asc"] ]);

        $breadcrumb = [
            $planCustomer['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 
            'Acesso Strings' => ''
        ];
        $this->set("action", "Acesso Strings");
        $this->set("form_action", "../acesso_strings/add_acesso/".$id);
        $this->set(compact("id", "acesso", "acessoFeatures", "produto", "breadcrumb"));
    }

    public function add_acesso($id)
    {
        $this->autoRender = false;

        $acessoProduto = isset($this->request->data["acessoProduto"]) ? $this->request->data["acessoProduto"] : false;
        $acessoFeature = isset($this->request->data["acessoFeature"]) ? $this->request->data["acessoFeature"] : false;

        $this->Acesso->updateAll(
            ['Acesso.data_cancel' => 'current_timestamp()', 'usuario_id_cancel' => CakeSession::read("Auth.User.id")], // set
            ["Acesso.customer_id" => $id] // where
        );

        $this->AcessoFeature->deleteAll(['AcessoFeature.customer_id' => $id], false);

        if ($acessoProduto) {
            foreach ($acessoProduto as $key => $id_produto) {
                $produto = $this->Product->find("first", ["conditions" => ["Product.id" => $id_produto] ]);
                $dataAcesso["Acesso"]["customer_id"] = $id;
                if (isset($produto)) {
                    $dataAcesso["Acesso"]["product_id"] = $produto["Product"]["id"];
                }

                $this->Acesso->create();
                $this->Acesso->save($dataAcesso);
                $acessoID = $this->Acesso->id;

                if (isset($acessoFeature[$id_produto])) {
                    $dataAcessoFeature = [];
                    foreach ($acessoFeature[$id_produto] as  $featureID) {
                        $feature = $this->Feature->find("first", ["conditions" => ["Feature.id" => $featureID]]);
                        $dataAcessoFeature[] = ["AcessoFeature" => ["feature_id" => $feature["Feature"]["id"], "customer_id" => $id, "access_id" => $acessoID]];
                    }

                    $this->AcessoFeature->create();

                    $this->AcessoFeature->saveMany($dataAcessoFeature);
                }
            }
        }

        $this->Session->setFlash(__('Acessos alterados com sucesso'), 'default', ['class' => "alert alert-success text-center"]);
        $this->redirect(['action'=> 'index', $id]);
    }
}
