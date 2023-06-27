<?php
class ContasPagasController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Income', 'Status'];

    public $paginate = [
        'Income' => [
            'limit' => 10,
            'order' => ['Income.created' => 'desc']
        ]
    ];

    public function index()
    {
        $this->Permission->check(33, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ["Income.created" => date("Y-m-d"), "Status.id" => 17], "or" => [] ];

        if (!empty($_GET['q'])) {
            $condition['and'] = array_merge($condition['and'], ["Customer.nome_primario LIKE"  => "%".$_GET["q"]."%" ]);
        }

        $dados = $this->Paginator->paginate("Income", $condition);

        $action = "Contas Pagas - ".date("d/m/Y");
        $breadcrumb = ['Relatórios' => '', $action => ''];
        $this->set(compact("dados", "action", "breadcrumb"));
    }
}
