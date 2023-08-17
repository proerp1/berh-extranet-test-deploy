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

	public function gerarExcelCnabLotes($nome, $dados){
		$spreadsheet = new Spreadsheet();

		$this->templates_list->getCnabLotes($spreadsheet, $dados);

		$local_salva = APP.'webroot/files/excel/'.$nome.'.xlsx';

		$writer = new Xlsx($spreadsheet);
		$writer->save($local_salva);
	}

	public function gerarExcelItineraries($nome, $dados){
		$spreadsheet = new Spreadsheet();

		$this->templates_list->getItinerary($spreadsheet, $dados);

		$local_salva = APP.'Private/excel/'.$nome.'.xlsx';

		$writer = new Xlsx($spreadsheet);
		$writer->save($local_salva);
	}
}