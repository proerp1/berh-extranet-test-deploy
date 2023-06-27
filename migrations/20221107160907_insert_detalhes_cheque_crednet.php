<?php

use Phinx\Migration\AbstractMigration;

class InsertDetalhesChequeCrednet extends AbstractMigration
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
                "respostaID" => 1050,
                "produtoID" => 451,
                "respostaPaiID" => 0,
                "respostaRegistro" => "N270",
                "respostaSubtipo" => "00",
                "respostaNome" => "Cheque Sem Fundos",
                "respostaAgruparColunas" => 2,
                "respostaQtdeColunas" => 0,
                "respostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "respostaVisivelCliente" => 1,
                "respostaFlagRestricao" => 2,
                "respostaNumeroOrdem" => 48,
                "respostaInformativo" => "",
            ],
        ];

        $this->insert('respostas', $respostas);


        $itensResposta = [
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "1",
                "itemRespostaByte" => "4",
                "itemRespostaNome" => "Tipo de Registro",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "5",
                "itemRespostaByte" => "2",
                "itemRespostaNome" => "SUBTIPO",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "7",
                "itemRespostaByte" => "8",
                "itemRespostaNome" => "Data",
                "itemRespostaObservacao" => "DD/MM/AAAA",
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 4,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "15",
                "itemRespostaByte" => "10",
                "itemRespostaNome" => "Cheque nº",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 10,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "25",
                "itemRespostaByte" => "5",
                "itemRespostaNome" => "Alinea",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 10,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "30",
                "itemRespostaByte" => "5",
                "itemRespostaNome" => "Qtde",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 10,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "35",
                "itemRespostaByte" => "15",
                "itemRespostaNome" => "Valor ",
                "itemRespostaObservacao" => "13 Inteiros e 2 Decimais",
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 2,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "50",
                "itemRespostaByte" => "3",
                "itemRespostaNome" => "Banco",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "53",
                "itemRespostaByte" => "14",
                "itemRespostaNome" => "Banco",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "67",
                "itemRespostaByte" => "4",
                "itemRespostaNome" => "Agencia",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "71",
                "itemRespostaByte" => "30",
                "itemRespostaNome" => "Cidade",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "101",
                "itemRespostaByte" => "2",
                "itemRespostaNome" => "UF",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 1,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => "",
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "103",
                "itemRespostaByte" => "10",
                "itemRespostaNome" => "USO SERASA",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
            [
                "respostaID" => 1050,
                "itemRespostaInicio" => "113",
                "itemRespostaByte" => "3",
                "itemRespostaNome" => "Reservado",
                "itemRespostaObservacao" => null,
                "itemRespostaDataCancel" => "1901-01-01 00:00:00",
                "usuarioIDCancel" => null,
                "itemRespostaVisivelCliente" => 2,
                "itemRespostaMultivalorado" => 1,
                "itemRespostaFormatacao" => 0,
                "itemRespostaMsgPersonalizada" => null,
            ],
        ];

        $this->insert('itensResposta', $itensResposta);
    }
}
