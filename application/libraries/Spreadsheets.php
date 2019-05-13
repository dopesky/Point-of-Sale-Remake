<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Spreadsheets {
	private $writer;
	private $spreadsheet;
	private $active_sheet;
	
	private $alphabet;
	private $center_alignment;
    private $titles;

	public function __construct($params){
		$this->spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
		$this->writer = new Xlsx($this->spreadsheet);  /*----- Excel (Xlsx) Object*/

		$this->alphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$this->center_alignment = array(
	        'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
	    );
	    $this->titles = $params['titles'];
	}

	private function set_active_sheet($index){
		$this->spreadsheet->setActiveSheetIndex($index);
		$this->active_sheet = $this->spreadsheet->getActiveSheet();
	}

	private function merge_and_center_cells($range){
		$this->active_sheet->mergeCells($range);
		$this->active_sheet->getStyle($range)->applyFromArray($this->center_alignment);
	}

	public function parse_html($html){
		$wizard = new Html();
		$richtext = $wizard->toRichTextObject($html);
		return $richtext;
	}

	private function add_logo($logo = ''){
		$highest_column = sizeof($this->titles) - 1;
		$image = new Drawing();
		$image->setName('Logo');
		$image->setDescription('Logo');
		$image->setPath(FCPATH.'assets/img/logo.png');
		$image->setHeight(75);
		$image->setCoordinates('C1');
		$image->setWorksheet($this->active_sheet);
	}

	private function create_first_line(){
		$highest_column = $this->alphabet[sizeof($this->titles) - 1];
		$this->active_sheet->getStyle('A2:'.$highest_column.'2')->getFont()->setBold(true)->setSize(13);
		$this->active_sheet->fromArray($this->titles, NULL, 'A2');
	}

	private function modify_sheet($header, $subheader){
		$highest_column = $this->alphabet[sizeof($this->titles)];
		for ($i = 'A'; $i !== $highest_column ; $i++) {
			$this->active_sheet->getColumnDimension($i)->setAutoSize(true);
		}
		$this->active_sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
		$this->active_sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LETTER);
		$this->active_sheet->getPageSetup()->setFitToWidth(1);
		$this->active_sheet->getPageSetup()->setFitToHeight(0);
		$this->active_sheet->getPageSetup()->setHorizontalCentered(true);
		$this->active_sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
		$this->active_sheet->getStyle('A1:'.$this->active_sheet->getHighestColumn().$this->active_sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
		$this->active_sheet->getHeaderFooter()->setOddHeader('&L&B'.get_instance()->time->get_now('d M, Y h:iA').' &C&B'.$header." &R&B".$subheader);
		$this->active_sheet->getHeaderFooter()->setOddFooter('&C&BPage &P of &N');
	}

	private function set_headers($header, $subheader){
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$header."'s ".$subheader.'.xlsx"'); /*-- $filename is  xsl filename ---*/
		header('Cache-Control: max-age=0');

		ob_end_clean();
	}

	public function write_to_excel($data_array = array()){
		$this->set_active_sheet(0);
		//$this->add_logo();
		$this->create_first_line();
		$this->active_sheet->fromArray($data_array, NULL, 'A3');
	}

	public function save($header, $subheader){
		$this->modify_sheet($header, $subheader);
		$this->set_headers($header, $subheader);
		$this->writer->save('php://output', 'xlsx');
	}
}