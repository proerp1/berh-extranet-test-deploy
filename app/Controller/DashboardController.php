<?php

class DashboardController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(4, 'leitura') ? '' : $this->redirect('/not_allowed');
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Principal';
        /*
        if (CakeSession::read('Auth.User.is_seller')) {
            $this->redirect('/dashboard/comercial');
        }*/

        $this->set(compact('breadcrumb', 'action'));
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
