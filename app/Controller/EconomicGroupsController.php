<?php

App::uses('AppController', 'Controller');

class EconomicGroupsController extends AppController
{
    public $components = ['Paginator'];
    public $uses = ['EconomicGroup', 'Status'];

    public function index($id)
    {
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['EconomicGroup.customer_id' => $id], 'or' => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['EconomicGroup.name LIKE' => '%'.$_GET['q'].'%', 'EconomicGroup.razao_social LIKE' => '%'.$_GET['q'].'%']);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['EconomicGroup.status_id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('EconomicGroup', $condition);
        $status = $this->Status->find("all", ["conditions" => ["Status.categoria" => 1]]);

        $action = 'Grupos Econômicos';
        $breadcrumb = [
            'Grupos Econômicos' => '',
        ];
        $this->set(compact('data', 'action', 'id', 'breadcrumb', 'status'));
    }
}
