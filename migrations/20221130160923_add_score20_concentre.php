<?php

use Phinx\Migration\AbstractMigration;

class AddScore20Concentre extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $respostas = [
            [
                "respostaID" => 1051,
                "produtoID" => 456,
                "respostaPaiID" => 0,
                "respostaRegistro" => "F900",
                "respostaSubtipo" => "RSHC0",
                "respostaNome" => "Score 2.0",
                "respostaAgruparColunas" => 2,
                "respostaQtdeColunas" => 0,
                "respostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "respostaVisivelCliente" => 1,
                "respostaFlagRestricao" => 2,
                "respostaNumeroOrdem" => 3,
                "respostaInformativo" => "",
            ],
        ];

        $this->table('respostas')->insert($respostas)->save();

        $itensResposta = [
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "1",
                "itemRespostaByte" => "4",
                "itemRespostaNome" => "Registro F900",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "5",
                "itemRespostaByte" => "4",
                "itemRespostaNome" => "Código da Feature Solicitada",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "9",
                "itemRespostaByte" => "3",
                "itemRespostaNome" => "1 - Retorno da informações",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "12",
                "itemRespostaByte" => "8",
                "itemRespostaNome" => "Data da Consulta",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "20",
                "itemRespostaByte" => "8",
                "itemRespostaNome" => "Hora da Consulta",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "28",
                "itemRespostaByte" => "4",
                "itemRespostaNome" => "Define a classe na tabela de score",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "32",
                "itemRespostaByte" => "5",
                "itemRespostaNome" => "Risco",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "37",
                "itemRespostaByte" => "78",
                "itemRespostaNome" => "Mensagem",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
            [
                "respostaID" => 1051,
                "itemRespostaInicio" => "115",
                "itemRespostaByte" => "1",
                "itemRespostaNome" => "Mensagem informativa somente Corporate",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 2,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
        ];

        $this->table('itensResposta')->insert($itensResposta)->save();
    }
}
