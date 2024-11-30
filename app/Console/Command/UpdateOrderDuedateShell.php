<?php

App::uses('AppShell', 'Console/Command');
class UpdateOrderDuedateShell extends AppShell
{
    public $uses = ['Order'];

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Atualiza o vencimento do pedido "Inicio" e vencido.');

        return $parser;
    }

    public function main()
    {
        $this->out('Começou...');

        $itens = $this->Order->find('all', [
            'conditions' => [
                'Order.status_id' => 83,
                'Order.due_date <' => date('Y-m-d'),
            ],
        ]);

        foreach ($itens as $item) {
            $this->Order->save([
                'Order' => [
                    'id' => $item['Order']['id'],
                    'due_date' => date('d/m/Y', strtotime('+1 days')),
                    'credit_release_date' => date('d/m/Y', strtotime('+6 days')),
                ]
            ]);

            $this->out('Atualizou '.$item['Order']['id']);
        }

        $this->out('Fim.');
    }
}
