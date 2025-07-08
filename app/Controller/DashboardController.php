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

        // Busca todas as categorias
        $categorias = $this->CategoriaFaq->find('all', [
            'fields' => ['CategoriaFaq.id', 'CategoriaFaq.nome'],
            'order' => ['CategoriaFaq.nome' => 'ASC']
        ]);

        // Para cada categoria, busca suas FAQs
        foreach ($categorias as $key => &$categoria) {
            $faqs = $this->Faq->find('all', [
                'fields' => ['Faq.id', 'Faq.pergunta', 'Faq.resposta', 'Faq.file'],
                'conditions' => [
                    'Faq.categoria_faq_id' => $categoria['CategoriaFaq']['id'],
                    'Faq.sistema_destino IN' => ['sig', 'todos']
                ],
                'order' => ['Faq.id' => 'DESC'],
                'contain' => [
                    'FaqRelacionamento' => [
                        'fields' => ['FaqRelacionamento.id', 'FaqRelacionamento.faq_id', 'FaqRelacionamento.supplier_id'],
                        'Supplier' => [
                            'fields' => ['Supplier.id', 'Supplier.nome_fantasia']
                        ]
                    ]
                ]
            ]);

            if (empty($faqs)) {
                unset($categorias[$key]); // Remove categoria se não tiver FAQs
            } else {
                $categoria['Faqs'] = $faqs;
            }
        }

        $this->set(compact('breadcrumb', 'action', 'categorias'));
    }
public function testeRelacionamentoFaq()
{
    $this->loadModel('FaqRelacionamento');
    $this->FaqRelacionamento->Behaviors->load('Containable');

    $relacionamentos = $this->FaqRelacionamento->find('all', [
        'contain' => ['Supplier'],
        'limit' => 5
    ]);

    debug($relacionamentos);
    exit;
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
