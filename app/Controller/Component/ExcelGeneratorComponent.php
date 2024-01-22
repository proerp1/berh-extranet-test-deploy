<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelGeneratorComponent extends Component
{
    public $templates_list;

    private $template;

    public function __construct()
    {
        require_once 'ExcelTemplate.php';

        $this->templates_list = new ExcelTemplate();
    }

    public function gerarExcelCnabLotes($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getCnabLotes($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelItineraries($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getItinerary($spreadsheet, $dados);

        $local_salva = APP.'Private/excel/'.$nome.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }
    
    public function gerarExcelOrders($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getOrder($spreadsheet, $dados);

        $local_salva = APP.'Private/excel/'.$nome.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarBaixaManual($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getBaixaManual($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelFluxo($nome, $dados, $conta)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getFluxo($spreadsheet, $dados, $conta);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelDespesas($nome, $dados, $conta)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getDespesas($spreadsheet, $dados, $conta);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelClientes($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getClientesRelatorio($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelFornecedores($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getFornecedoresRelatorio($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelBeneficios($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getBeneficioRelatorio($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }


    public function gerarExcelContasReceber($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getContasReceber($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelOutcome($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getOutcome($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }
}
