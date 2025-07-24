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

    public function gerarExcelPedidos($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getPedidosRelatorio($spreadsheet, $dados);

        $local_salva = APP.'Private/excel/'.$nome.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarRelatorioCompras($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getRelatorioCompras($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

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

    public function gerarExcelAtendimentos($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getAtendimento($spreadsheet, $dados);

        $local_salva = APP.'Private/excel/'.$nome.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }
    
    
    public function gerarExcelOrders($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getOrder($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }
    
    public function gerarExcelOrdersprocessamento($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getProcessamento($spreadsheet, $dados);

        $local_salva = APP.'Private/excel/'.$nome.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }
    public function gerarExcelBeneficiario($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getBeneficiario($spreadsheet, $dados);

        $local_salva = APP.'Private/excel/'.$nome.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelProposal($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getProposal($spreadsheet, $dados);

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

        public function gerarExcelFaq ($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getFaq($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelPedidoscustomer($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getPedidosRelatorio($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelPedidoMovimentacoes($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getPedidoMovimentacoes($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelPedidosNfs($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getPedidosNfsRelatorio($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelPedidosBeneficiariosPIX($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getPedidosBeneficiariosPIX($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelFornecedores($nome, $dados_sup, $dados_log)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getFornecedoresRelatorio($spreadsheet, $dados_sup, $dados_log);

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
    
    public function gerarExcelNiboContasReceber($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getNiboContasReceber($spreadsheet, $dados);

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

        public function gerarExcelNibo($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getNibo($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelLogBeneficios($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getLogBeneficiosRelatorio($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }

    public function gerarExcelModeloImportacaoBeneficiarios($nome, $dados)
    {
        $spreadsheet = new Spreadsheet();

        $this->templates_list->getModeloImportacaoBeneficiarios($spreadsheet, $dados);

        $local_salva = APP.'webroot/files/excel/'.$nome;

        $writer = new Xlsx($spreadsheet);
        $writer->save($local_salva);
    }
}
