<?php
class ProductAttributesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['ProductAttribute', 'Status', 'Product'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'ProductAttribute.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($productId)
    {
        $this->Permission->check(5, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['ProductAttribute.product_id' => $productId], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['ProductAttribute.name LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('ProductAttribute', $condition);
        $this->Product->id = $productId;
        $product = $this->Product->read();

        $action = $product['Product']['name'].' - Atributos';

        $action = 'Produtos';
        $breadcrumb = ['Cadastros' => '', 'Produtos' => ['controller' => 'products'], $product['Product']['name'] => ['controller' => 'products', 'action' => 'edit', $productId], 'Atributos' => ''];
        $this->set(compact('data', 'product', 'productId', 'action', 'breadcrumb'));
    }
    
    public function add($productId)
    {
        $this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is('post')) {
            $this->request->data['ProductAttribute']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['ProductAttribute']['product_id'] = $productId;
            
            $this->ProductAttribute->create();
            if ($this->ProductAttribute->save($this->request->data)) {
                $this->Session->setFlash(__('O atributo foi salvo com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'index', $productId]);
            } else {
                $this->Session->setFlash(__('O atributo não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $this->Product->id = $productId;
        $product = $this->Product->read();

        $action = 'Produtos';
        $breadcrumb = ['Cadastros' => '', 'Produtos' => ['controller' => 'products'], $product['Product']['name'] => ['controller' => 'products', 'action' => 'edit', $productId], 'Novo atributo' => ''];
        $this->set("form_action", "add/".$productId);
        $this->set(compact('product', 'productId', 'action', 'breadcrumb'));
    }

    public function delete($id)
    {
        $this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->ProductAttribute->id = $id;

        $data['ProductAttribute']['data_cancel'] = date("Y-m-d H:i:s");
        $data['ProductAttribute']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->ProductAttribute->save($data)) {
            $this->Session->setFlash(__('O atributo foi excluido com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect($this->referer());
        }
    }
}
