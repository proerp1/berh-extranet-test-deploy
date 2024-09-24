<?php

class PagesController extends AppController{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator',];
    public $uses = ['Comunicado','Status', 'Categoria'];

    public function display() {
        $this->Paginator->settings = ['Comunicado' => [
            'limit' => 100,
            'order' => ['Comunicado.data' => 'desc'],
        ]];

        $data = $this->Paginator->paginate('Comunicado',  [ ['Comunicado.categoria_id' => 5]]);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 5]]);

        $action = 'biblioteca';
        $breadcrumb = ['Configurações' => '', 'biblioteca' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }

	}

?>