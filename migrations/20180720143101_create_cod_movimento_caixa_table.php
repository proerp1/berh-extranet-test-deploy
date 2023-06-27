<?php


use Phinx\Migration\AbstractMigration;

class CreateCodMovimentoCaixaTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('caixa_cod_movimentos');
        $table->addColumn('codigo', 'string')
              ->addColumn('nome', 'string')
              ->addColumn('baixar_automatico', 'integer', array('default' => 0))
              ->addIndex(array('codigo'))
              ->create();


        $rows = [
            ['codigo' => '01', 'nome' => 'Solicitação de Impressão de Títulos Confirmada', 'baixar_automatico' => 0],
            ['codigo' => '02', 'nome' => 'Entrada Confirmada', 'baixar_automatico' => 0],
            ['codigo' => '03', 'nome' => 'Entrada Rejeitada', 'baixar_automatico' => 0],
            ['codigo' => '04', 'nome' => 'Transferência de Carteira/Entrada', 'baixar_automatico' => 0],
            ['codigo' => '05', 'nome' => 'Transferência de Carteira/Baixa', 'baixar_automatico' => 1],
            ['codigo' => '06', 'nome' => 'Liquidação', 'baixar_automatico' => 1],
            ['codigo' => '07', 'nome' => 'Confirmação do Recebimento da Instrução de Desconto', 'baixar_automatico' => 0],
            ['codigo' => '08', 'nome' => 'Confirmação do Recebimento do Cancelamento do Desconto', 'baixar_automatico' => 0],
            ['codigo' => '09', 'nome' => 'Baixa', 'baixar_automatico' => 0],
            ['codigo' => '12', 'nome' => 'Confirmação Recebimento Instrução de Abatimento', 'baixar_automatico' => 0],
            ['codigo' => '13', 'nome' => 'Confirmação Recebimento Instrução de Cancelamento Abatimento', 'baixar_automatico' => 0],
            ['codigo' => '14', 'nome' => 'Confirmação Recebimento Instrução Alteração de Vencimento', 'baixar_automatico' => 0],
            ['codigo' => '19', 'nome' => 'Confirmação Recebimento Instrução de Protesto', 'baixar_automatico' => 0],
            ['codigo' => '20', 'nome' => 'Confirmação Recebimento Instrução de Sustação/Cancelamento de Protesto', 'baixar_automatico' => 0],
            ['codigo' => '23', 'nome' => 'Remessa a Cartório', 'baixar_automatico' => 0],
            ['codigo' => '24', 'nome' => 'Retirada de Cartório', 'baixar_automatico' => 0],
            ['codigo' => '25', 'nome' => 'Protestado e Baixado (Baixa por Ter Sido Protestado)', 'baixar_automatico' => 0],
            ['codigo' => '26', 'nome' => 'Instrução Rejeitada', 'baixar_automatico' => 0],
            ['codigo' => '27', 'nome' => 'Confirmação do Pedido de Alteração de Outros Dados', 'baixar_automatico' => 0],
            ['codigo' => '28', 'nome' => 'Débito de Tarifas/Custas', 'baixar_automatico' => 0],
            ['codigo' => '30', 'nome' => 'Alteração de Dados Rejeitada', 'baixar_automatico' => 0],
            ['codigo' => '35', 'nome' => 'Confirmação de Inclusão Banco de Pagador', 'baixar_automatico' => 0],
            ['codigo' => '36', 'nome' => 'Confirmação de Alteração Banco de Pagador', 'baixar_automatico' => 0],
            ['codigo' => '37', 'nome' => 'Confirmação de Exclusão Banco de Pagador', 'baixar_automatico' => 0],
            ['codigo' => '38', 'nome' => 'Emissão de Boletos de Banco de Pagador', 'baixar_automatico' => 0],
            ['codigo' => '39', 'nome' => 'Manutenção de Pagador Rejeitada', 'baixar_automatico' => 0],
            ['codigo' => '40', 'nome' => 'Entrada de Título via Banco de Pagador Rejeitada', 'baixar_automatico' => 0],
            ['codigo' => '41', 'nome' => 'Manutenção de Banco de Pagador Rejeitada', 'baixar_automatico' => 0],
            ['codigo' => '44', 'nome' => 'Estorno de Baixa / Liquidação', 'baixar_automatico' => 0],
            ['codigo' => '45', 'nome' => 'Alteração de Dados', 'baixar_automatico' => 0],
            ['codigo' => '46', 'nome' => 'Liquidação On-line', 'baixar_automatico' => 0],
            ['codigo' => '47', 'nome' => 'Estorno de Liquidação On-line', 'baixar_automatico' => 0],
            ['codigo' => '51', 'nome' => 'Título DDA reconhecido pelo pagador', 'baixar_automatico' => 0],
            ['codigo' => '52', 'nome' => 'Título DDA não reconhecido pelo pagador', 'baixar_automatico' => 0],
            ['codigo' => '53', 'nome' => 'Título DDA recusado pela CIP', 'baixar_automatico' => 0],
            ['codigo' => '61', 'nome' => 'Confirmação de alteração do valor nominal do título', 'baixar_automatico' => 0],
            ['codigo' => '62', 'nome' => 'Confirmação de alteração do valor/percentual mínimo/máximo', 'baixar_automatico' => 0],
        ];

        $this->insert('caixa_cod_movimentos', $rows);

    }
}
