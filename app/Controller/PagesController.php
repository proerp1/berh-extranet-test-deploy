<?php

class PagesController extends AppController{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator',];
    public $uses = ['Comunicado','Status', 'Categoria'];

    private function carrega_pagina_comunicados($categoria_comunicado, $categoria_status) {
        $this->Paginator->settings = ['Comunicado' => [
            'limit' => 100,
            'order' => ['Comunicado.data' => 'desc'],
        ]];

        $data = $this->Paginator->paginate('Comunicado',  [ ['Comunicado.categoria_id' => $categoria_comunicado]]);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => $categoria_status]]);

        $this->set(compact('status', 'data'));
        $this->render('display');
    }

    public function biblioteca() {
        $this->set('page_title', 'Bibliotecas');
        $this->set('page_subtitle', 'Aqui você poderá encontrar todas as Bibliotecas enviadas pela Be RH.');
        $this->set('action', 'Bibliotecas');
        $this->set('breadcrumb', ['Configurações' => '', 'Bibliotecas' => '']);

        $this->carrega_pagina_comunicados(5, 5);
    }

    public function comunicados() {
        $this->set('page_title', 'Comunicados');
        $this->set('page_subtitle', 'Aqui você poderá encontrar todos os Comunicados enviados pela Be RH.');
        $this->set('action', 'Comunicados');
        $this->set('breadcrumb', ['Configurações' => '', 'Comunicados' => '']);

        $this->carrega_pagina_comunicados(1, 1);
    }

    public function documentacao() {
        $this->set('page_title', 'Documentação');
        $this->set('page_subtitle', 'Aqui você poderá encontrar todas as Documentações enviadas pela Be RH.');
        $this->set('action', 'Documentação');
        $this->set('breadcrumb', ['Configurações' => '', 'Documentação' => '']);

        $this->carrega_pagina_comunicados(2, 2);
    }

    public function layout() {
        $this->set('page_title', 'Layouts');
        $this->set('page_subtitle', 'Aqui você poderá encontrar todos os Layouts enviados pela Be RH.');
        $this->set('action', 'Layouts');
        $this->set('breadcrumb', ['Configurações' => '', 'Layouts' => '']);

        $this->carrega_pagina_comunicados(3, 3);
    }

    public function ajuda() {
        $this->set('page_title', 'Ajuda');
        $this->set('page_subtitle', 'Aqui você poderá encontrar todas as Ajuda enviadas pela Be RH.');
        $this->set('action', 'Ajuda');
        $this->set('breadcrumb', ['Configurações' => '', 'Ajuda' => '']);

        $this->carrega_pagina_comunicados(4, 4);
    }
}
?>