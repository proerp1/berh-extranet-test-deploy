<?php

class DespesasReportController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['BankAccount', 'Outcome', 'Income'];

    public $paginate = [];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(31, 'leitura') ? '' : $this->redirect('/not_allowed');

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        $data = [];
        $conta = [];
        $exportar = false;

        if (!empty($_GET['t']) and $get_de != '' and $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $this->BankAccount->id = $_GET['t'];
            $conta = $this->BankAccount->read();

            $data = $this->Outcome->query("
                SELECT s.name as status, s.label as status_label, b.name, 'conta a pagar' AS tipo, o.vencimento, o.valor_total, '-' AS operador
                 FROM outcomes o
                 INNER JOIN statuses s ON s.id = o.status_id
                 INNER JOIN bank_accounts b ON b.id = o.bank_account_id
                 WHERE o.bank_account_id = ".$_GET['t']." AND o.vencimento BETWEEN '".$de."' AND '".$ate."' 
                 ORDER BY vencimento");

            $exportar = true;
        }

        $conta_bancaria = $this->BankAccount->find('all', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);

        if (isset($_GET['exportar'])) {
            $nome = 'despesas_'.$de.'_'.$ate.'.xlsx';

            $this->ExcelGenerator->gerarExcelDespesas($nome, $data, $conta);
            $this->redirect('/files/excel/'.$nome);
        }

        $action = 'Despesas';
        $this->set(compact('conta_bancaria', 'data', 'conta', 'exportar', 'action'));
    }
}
