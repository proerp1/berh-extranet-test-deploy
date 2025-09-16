<?php

use League\Csv\Reader;

class OrderDescontoController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['OrderDesconto', 'Order'];

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index($id) {
        $this->Permission->check(63, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['OrderDesconto.order_id' => $id], 'or' => []];

        if (isset($_GET['q']) && $_GET['q'] != '') {
            $query = $_GET['q'];
            $condition['or'] = array_merge($condition['or'], ["Customer.code_be LIKE '%$query%'"]);
            $condition['or'] = array_merge($condition['or'], ["Customer.code_customer LIKE '%$query%'"]);
        }

        if (isset($_GET['excel'])) {
            $dados = $this->OrderDesconto->find('all', ['conditions' => $condition]);

            $nome = 'cliente_de_para_beneficio_' . date('d_m_Y');

            $this->ExcelGenerator->gerarExcelClienteDeParaBeneficios($nome, $dados);
            $this->redirect("/files/excel/" . $nome . ".xlsx");
        }

        $action = 'Clientes';

        $data = $this->Paginator->paginate('OrderDesconto', $condition);

        $allIds = $this->OrderDesconto->find('list', ['condition' => $condition, 'fields' => ['OrderDesconto.id']]);

        $breadcrumb = ['Pedidos' => '', 'Descontos' => ''];
        $this->set('url_novo', "/order_desconto/add/$id");
        $this->set('can_bulk_edit', $this->Permission->check(83, 'leitura'));
        $this->set(compact('data', 'id', 'action', 'breadcrumb', 'allIds'));
    }
    
    public function add($order_id) {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");

        if ($this->request->is(['post', 'put'])) {
            $this->OrderDesconto->create();
            if ($this->OrderDesconto->validates()) {
                $this->request->data['OrderDesconto']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->OrderDesconto->save($this->request->data)) {
                    $this->update_order_discount_value($order_id);
                    $discount_id = $this->OrderDesconto->id;

                    $this->Flash->set(__('O desconto foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "edit/$order_id/$discount_id"]);
                } else {
                    $this->Flash->set(__('O desconto não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O desconto não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $action = 'Benefício';
        $breadcrumb = ['Pedidos' => '', 'Descontos' => '', 'Novo Desconto' => ''];
        $this->set("form_action", "../order_desconto/add/$order_id");
        $this->set(compact('order_id', 'action', 'breadcrumb'));
    }

    public function edit($order_id, $discount_id = null) {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->OrderDesconto->id = $discount_id;

        if ($this->request->is(['post', 'put'])) {
            if ($this->OrderDesconto->validates()) {
                $this->request->data['OrderDesconto']['user_updated_id'] = CakeSession::read("Auth.User.id");
                if ($this->OrderDesconto->save($this->request->data)) {
                    $this->update_order_discount_value($order_id);
                    $this->Flash->set(__('O desconto foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "edit/$order_id/$discount_id"]);
                } else {
                    $this->Flash->set(__('O desconto não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O desconto não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->OrderDesconto->validationErrors;
        $this->request->data = $this->OrderDesconto->read();
        $this->OrderDesconto->validationErrors = $temp_errors;

        $action = 'Benefício';
        $breadcrumb = ['Pedidos' => '', 'Descontos' => '', 'Alterar Desconto' => ''];
        $this->set("form_action", "../order_desconto/edit/$order_id");
        $this->set(compact('order_id', 'discount_id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($order_id, $discount_id) {
        $this->Permission->check(63, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->OrderDesconto->id = $discount_id;

        $data = ['OrderDesconto' => ['data_cancel' => date('Y-m-d H:i:s'), 'usuario_id_cancel' => CakeSession::read('Auth.User.id')]];

        if ($this->OrderDesconto->save($data)) {
            $this->update_order_discount_value($order_id);
            $this->Flash->set(__('O desconto foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index/'.$order_id]);
        }
    }

    private function update_order_discount_value($order_id) {
      $descontos = $this->OrderDesconto->find('all', ['conditions' => ['OrderDesconto.order_id' => $order_id]]);
      $soma_descontos = array_reduce($descontos, function ($reduced, $desconto) {
        return $reduced + $desconto['OrderDesconto']['valor_nao_formatado'];
      }, 0);

      $this->Order->id = $order_id;
      $this->Order->save(['Order' => [
        'desconto' => $soma_descontos,
      ]]);
    }
}
