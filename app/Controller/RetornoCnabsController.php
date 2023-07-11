<?php
App::uses('Bancoob', 'Lib');
class RetornoCnabsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'LerRetornoCnab', 'ExcelGenerator'];
    public $uses = ['RetornoCnab', 'Status', 'TmpRetornoCnab', 'Income', 'ChargesHistory'];

    public $paginate = [
        'RetornoCnab' => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'RetornoCnab.created' => 'desc']],
        'TmpRetornoCnab' => ['limit' => 10, 'order' => ['TmpRetornoCnab.processado' => 'asc', 'TmpRetornoCnab.encontrado' => 'desc'], 'recursive' => 3]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(37, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['RetornoCnab.arquivo LIKE' => "%".$_GET['q']."%", 'RetornoCnab.lote LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('RetornoCnab', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 11]]);

        $action = 'Retorno Sicoob';
        $breadcrumb = ['Financeiro' => '', 'Cnab' => '', 'Retorno Sicoob' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(37, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->RetornoCnab->create();
            $this->RetornoCnab->validates();
            $this->request->data['RetornoCnab']['user_creator_id'] = CakeSession::read("Auth.User.id");
            if ($this->RetornoCnab->save($this->request->data)) {
                $arquivo = APP.'webroot/files/retorno_cnab/arquivo/'.$this->RetornoCnab->id.'/'.$this->request->data['RetornoCnab']['arquivo']['name'];
                
                $Bancoob = new Bancoob();
                $retorno = $Bancoob->processarRetorno($this->RetornoCnab->id, $arquivo);

                $this->Session->setFlash(__('Arquivo importado com sucesso!'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('O arquivo não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 11]]);

        $action = 'Retorno Sicoob';
        $breadcrumb = ['Financeiro' => '', 'Cnab' => '', 'Retorno Sicoob' => ['action' => 'index'], 'Novo' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function detalhes($id)
    {
        $this->Permission->check(37, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $this->RetornoCnab->id = $id;
        $retorno = $this->RetornoCnab->read();

        $condition = ["and" => ['RetornoCnab.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['TmpRetornoCnab.nosso_numero LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('TmpRetornoCnab', $condition);

        $action = 'Retorno - '.date('d/m/Y', strtotime($retorno['RetornoCnab']['data_arquivo']));
        $breadcrumb = ['Financeiro' => '', 'Cnab' => '', 'Retorno Sicoob' => ['action' => 'index'], 'Detalhes' => ''];
        $this->set(compact('id', 'data', 'retorno', 'action', 'breadcrumb'));
    }

    public function gerar_excel($id)
    {
        $this->Permission->check(37, "leitura") ? "" : $this->redirect("/not_allowed");

        $this->RetornoCnab->id = $id;
        $retorno = $this->RetornoCnab->read();

        $dados = $this->TmpRetornoCnab->find('all', ['conditions' => ['RetornoCnab.id' => $id], 'recursive' => 3]);
        $nome = 'contas_cnab_'.date('d_m_Y', strtotime($retorno['RetornoCnab']['data_arquivo']));

        $this->ExcelGenerator->gerarExcelRetornoCnab($nome, $dados);
        $this->redirect("/files/excel/".$nome.".xlsx");
    }

    public function baixar_contas($id)
    {
        $this->Permission->check(37, "escrita") ? "" : $this->redirect("/not_allowed");

        $contas = $this->TmpRetornoCnab->find('all', [
            'conditions' => [
                'RetornoCnab.id' => $id,
                'TmpRetornoCnab.encontrado' => 1,
                'TmpRetornoCnab.erro is null',
                'TmpRetornoCnab.processado' => 2,
                //'TmpRetornoCnab.tipo' => 2 // ocorrencia baixada
                'TmpRetornoCnab.tipo' => 1 // ocorrencia baixada // alterei para o tipo 1 que a ocorrencia é 06 liquidação

            ],
            'fields' => [
                'RetornoCnab.data_pagamento',
                'Income.valor_total',
                'Income.id',
                'TmpRetornoCnab.*',
            ]
        ]);

        $historico = [];
        foreach ($contas as $conta) {
            $this->Income->id = $conta['Income']['id'];
            if ($conta['TmpRetornoCnab']['valor_pago'] >= $conta['Income']['valor_total_nao_formatado']) {
                $status = 17;
            }

            if ($conta['TmpRetornoCnab']['valor_pago'] < $conta['Income']['valor_total_nao_formatado']) {
                $status = 51;
            }

            $data = [
                'Income' => [
                    'status_id' => $status,
                    'valor_pago' => $conta['TmpRetornoCnab']['valor_pago'],
                    'data_pagamento' => $conta['RetornoCnab']['data_pagamento'] != null ? $conta['RetornoCnab']['data_pagamento_nao_formatado'] : $conta['TmpRetornoCnab']['data_pagamento'],
                    'user_updated_id' => CakeSession::read("Auth.User.id"),
                    'retorno_cnab_id' => $conta['TmpRetornoCnab']['id']
                ]
            ];

            $this->Income->save($data, ['validate' => false]);

            $this->TmpRetornoCnab->id = $conta['TmpRetornoCnab']['id'];

            $data_tmp = ['TmpRetornoCnab' => ['processado' => 1, 'user_updated_id' => CakeSession::read("Auth.User.id")]];

            $this->TmpRetornoCnab->save($data_tmp);
            $historico[] = [
                'ChargesHistory' => [
                    'income_id' => $conta['Income']['id'],
                    'text' => 'Retorno do cnab '.$conta['TmpRetornoCnab']['id'].' processado com sucesso',
                    'user_creator_id' => CakeSession::read('Auth.User.id')
                ]
            ];
        }

        if (!empty($historico)) {
            $this->ChargesHistory->saveMany($historico);
        }

        $this->RetornoCnab->id = $id;

        $data_retorno = ['RetornoCnab' => ['data_processamento' => date('Y-m-d H:i:s'), 'status_id' => 40, 'user_updated_id' => CakeSession::read("Auth.User.id")]];

        $this->RetornoCnab->save($data_retorno);

        $this->Session->setFlash(__('Contas baixadas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'index']);
    }

    public function delete($id)
    {
        $this->Permission->check(37, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->RetornoCnab->id = $id;

        $data = ['RetornoCnab' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->RetornoCnab->save($data)) {
            $this->Session->setFlash(__('O arquivo foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
