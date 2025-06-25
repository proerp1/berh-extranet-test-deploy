<?php

class DashboardController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Faq', 'CategoriaFaq'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

public function index()
{
    $this->Permission->check(4, 'leitura') ? '' : $this->redirect('/not_allowed');

    $breadcrumb = ['Dashboard' => '/'];
    $action = 'Principal';

    $categorias = $this->CategoriaFaq->find('all', [
        'fields' => ['CategoriaFaq.id', 'CategoriaFaq.nome'],
        'order' => ['CategoriaFaq.nome' => 'ASC']
    ]);

    foreach ($categorias as $key => &$categoria) {
        $faqs = $this->Faq->find('all', [
            'fields' => ['Faq.id', 'Faq.pergunta', 'Faq.resposta'],
            'conditions' => [
                'Faq.categoria_faq_id' => $categoria['CategoriaFaq']['id'],
                'Faq.sistema_destino IN' => ['sig', 'todos'] // ✅ filtro necessário
            ],
            'order' => ['Faq.id' => 'DESC']
        ]);

        // Remove categoria se não tiver perguntas
        if (empty($faqs)) {
            unset($categorias[$key]);
        } else {
            $categoria['Faqs'] = $faqs;
        }
    }

    $this->set(compact('breadcrumb', 'action', 'categorias'));
}



    public function oportunidade()
    {
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Oportunidades';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function outros()
    {
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Outros';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function resumo()
    {
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Resumo';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function compras()
    {
        $breadcrumb = ['Compras' => '/'];
        $action = 'Compras';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function fornecedores()
    {
        $breadcrumb = ['Fornecedores' => '/'];
        $action = 'Fornecedores';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function expedicao()
    {
        $breadcrumb = ['Expedição' => '/'];
        $action = 'Expedição';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function cliente()
    {
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Cliente';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function orcamentos()
    {
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Orçamentos';

        $this->set(compact('breadcrumb', 'action'));
    }

    public function produto()
    {
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Produto';

        $this->set(compact('breadcrumb', 'action'));
    }
}
