<?php
App::uses('AppShell', 'Console/Command');
App::uses('ClassRegistry', 'Utility');
App::uses('ZenviaApi', 'Lib');
App::uses('CakeEmail', 'Network/Email');

class NotificaAtrasosShell extends AppShell
{
    public $uses = ['Income'];

    private $dir;

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Notifica clientes com contas vencidas há mais de 5 dias');

        return $parser;
    }

    public function main()
    {
        CakeLog::notice("Começou transmissão");

        $contas = $this->Income->find('all', [
            'fields' => ['Income.id', 'Customer.id', 'Customer.nome_primario', 'Customer.telefone1', 'Customer.email'],
            'conditions' => [
                'Income.status_id IN (16)',
                'Income.vencimento <=' => date('Y-m-d', strtotime('-5 days'))
            ],
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => ['Income.customer_id = Customer.id', 'Customer.data_cancel' => '1901-01-01 00:00:00', 'Customer.cod_franquia' => 1]
                ],
            ],
            'group' => ['Customer.id'],
            'recursive' => -1
        ]);

        foreach ($contas as $conta) {
            /*if ($conta['Customer']['telefone1'] != '') {
                $this->sms($conta['Customer']['telefone1'], 'BeRH:  Ainda nao identificamos o pagamento da fatura, voce pode obter a segunda via por meio do nosso site.');
            }*/
            
            if ($conta['Customer']['email'] != '') {
                $this->email($conta['Customer']['email'], [
                    'nome' => $conta['Customer']['nome_primario']
                ]);
            }
        }

        CakeLog::notice("Fim transmissão");
    }

    public function sms($cel, $msg)
    {
        $num = str_replace([' ', '-', '(', ')'], '', $conta['Customer']['telefone1']);
        $ZenviaApi = new ZenviaApi();

        $ZenviaApi->sendSms('55'.$num, $msg);
    }

    public function email($email, $viewVars)
    {
        $CakeEmail = new CakeEmail();

        $CakeEmail->config('default');

        $CakeEmail->to($email);
        $CakeEmail->subject('BeRH - Cobrança');
        $CakeEmail->viewVars($viewVars);
        $CakeEmail->template('notifica_atrasos', 'meproteja');
        $CakeEmail->emailFormat('html');

        if ($CakeEmail->send()) {
            return true;
        } else {
            return false;
        }
    }
}
