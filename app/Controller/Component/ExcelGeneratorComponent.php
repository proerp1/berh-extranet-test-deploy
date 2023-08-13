<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelGeneratorComponent extends Component {

	private $template;
	public $templates_list;

	public function __construct(){
		require_once("ExcelTemplate.php");

		$this->templates_list = new ExcelTemplate();
	}

	public function gerarDadosComerciais($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getDadosComerciais($objPHPExcel, $dados);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="'.$nome.'"');
		$objWriter->save('php://output');
	}

	public function gerarExcelCnabLotes($nome, $dados){
		$spreadsheet = new Spreadsheet();

		$this->templates_list->getCnabLotes($spreadsheet, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$writer = new Xlsx($spreadsheet);
		$writer->save($local_salva);
	}

	public function gerarExcelBloqueioDiario($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getBloqueioDiario($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelClientesBloquear($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getClientesBloquear($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelClientes($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getClientes($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelDiarioCobranca($nome, $dados, $valor_cobrado, $exito){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getDiarioCobranca($objPHPExcel, $dados, $valor_cobrado, $exito);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelContasReceber($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getContasReceber($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelOutcome($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getOutcome($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome;

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelFluxo($nome, $dados, $conta){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getFluxo($objPHPExcel, $dados, $conta);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelDespesas($nome, $dados, $conta){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getDespesas($objPHPExcel, $dados, $conta);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelReceitas($nome, $dados, $conta){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getReceitas($objPHPExcel, $dados, $conta);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelCustomers($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getCustomersTemplate($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelRetornoCnab($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getRetornoCnab($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelTww($nome, $dados, $grupo){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getTwwRelatorio($objPHPExcel, $dados, $grupo);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		$objWriter->setDelimiter(';');
				
		header('Content-type: text/csv');
		$local_salva = APP.'webroot/files/excel/'.$nome;
		$objWriter->save($local_salva);
	}

	public function gerarExcelLocaweb($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getLocawebRelatorio($objPHPExcel, $dados);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		$objWriter->setDelimiter(';');
				
		header('Content-type: text/csv');
		$local_salva = APP.'webroot/files/excel/'.$nome;
		$objWriter->save($local_salva);
	}

	public function gerarExcelNegativacao($nome, $dados){
		$objPHPExcel = new PHPExcel();
		
		$errosPefin = ClassRegistry::init('ErrosPefin');

		$this->templates_list->getNegativacaoTemplate($objPHPExcel, $dados, $errosPefin);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelNovaVida($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getNovaVidaTemplate($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelNovaVidaConsolidado($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getNovaVidaConsolidadoTemplate($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelInadimplentes($nome, $dados, $total_valores){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getInadimplentesTemplate($objPHPExcel, $dados, $total_valores);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelClientesCobrados($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getClientesCobradosTemplate($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelClientesExito($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getClientesExitoTemplate($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelFaturamentoRevenda($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getFaturamentoRevendaTemplate($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelFaturamentoHipercheck($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getFaturamentoHipercheckTemplate($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelFaturamento($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getFaturamentoTemplate($objPHPExcel, $dados);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="'.$nome.'"');
		$objWriter->save('php://output');
	}

	public function gerarExcelClientesDesconto($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getClientesDesconto($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelStatusClientes($nome, $dados){	
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getStatusClientes($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelLoginsConsulta($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getLoginsConsulta($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelLogonBlindagem($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getLogonBlindagem($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelPefinDetalhes($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getPefinDetalhes($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelDivisao($nome, $dados, $tipo = 'mes'){
		$objPHPExcel = new PHPExcel();

		if ($tipo = 'todos') {
			$this->templates_list->getDivisaoTodos($objPHPExcel, $dados);
		} else {
			$this->templates_list->getDivisao($objPHPExcel, $dados);
		}

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarExcelPlansCustomers($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getPlansCustomers($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome;

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarBaixaManual($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getBaixaManual($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome;

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}

	public function gerarMovimentacaoStatus($nome, $dados){
		$objPHPExcel = new PHPExcel();

		$this->templates_list->getMovimentacaoStatus($objPHPExcel, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome;

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($local_salva);
	}
}