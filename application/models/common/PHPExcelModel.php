<?php

/**
 * PHPExcelModel
 * @author takanori_gozu
 *
 */
class PHPExcelModel extends CI_Model {

	private $_excel;
	private $_sheet;
	private $_write;
	private $_type;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 新規Excelファイルを作成
	 */
	public function init($ext = '.xlsx') {
		$this->set_type($ext);
		$this->_excel = new PHPExcel();
	}

	/**
	 * フォーマット用Excelをロード
	 */
	public function load($format_file, $ext = '.xlsx') {
		$this->set_type($ext);
		$path = 'excel/'. $format_file. $ext;
		$this->_excel = PHPExcel_IOFactory::createReader($this->_type)->load($path);
	}

	/**
	 * Excel形式の判定
	 */
	public function set_type($ext) {
		if ($ext == '.xlsx') {
			$this->_type = 'Excel2007';
		} else {
			$this->_type = 'Excel5';
		}
	}

	/**
	 * アクティブシートの設定
	 */
	public function set_sheet($sheet_idx = 0) {
		$this->_excel->setActiveSheetIndex($sheet_idx);
		$this->_sheet = $this->_excel->getActiveSheet();
	}

	/**
	 * シート名
	 */
	public function set_title($title) {
		$this->_sheet->setTitle($title);
	}

	/**
	 * サイズの設定(A4)
	 */
	public function set_pagesize_A4() {
		$this->_sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	}

	/**
	 * サイズの設定(A3)
	 */
	public function set_pagesize_A3() {
		$this->_sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3);
	}

	/**
	 * 罫線印刷の設定
	 */
	public function set_show_grid($flg = true) {
		$this->_sheet->setShowGridlines($flg);
	}

	/**
	 * ページ番号印刷
	 */
	public function set_footer_pager() {
		$this->_sheet->getHeaderFooter()->setOddFooter('Page &P of &N');
	}

	/**
	 * 余白の設定
	 */
	public function set_margin($top = 0, $bot = 0, $left = 0, $right = 0, $head = 0, $foot = 0) {
		$this->_sheet->getPageMargins()->setTop($top);
		$this->_sheet->getPageMargins()->setBottom($bot);
		$this->_sheet->getPageMargins()->setLeft($left);
		$this->_sheet->getPageMargins()->setRight($right);
		$this->_sheet->getPageMargins()->setHeader($head);
		$this->_sheet->getPageMargins()->setFooter($foot);
	}

	/**
	 * 列幅の設定
	 */
	public function set_column_width($column_no, $value) {
		$this->_sheet->getColumnDimension($column_no)->setWidth($value);
	}

	/**
	 * セルの結合
	 */
	public function cell_merge($range) {
		$this->_sheet->mergeCells($range);
	}

	/**
	 * セルの着色
	 */
	public function set_color($range, $color) {
		$this->_sheet->getStyle($range)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($color);
	}

	/**
	 * 罫線
	 */
	public function set_border($range) {
		$this->_sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle( PHPExcel_Style_Border::BORDER_THIN );
	}

	/**
	 * 縦位置中央揃え
	 */
	public function set_vertical_align($range) {
		$this->_sheet->getStyle($range)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	}

	/**
	 * 横位置中央揃え
	 */
	public function set_horizon_align($range) {
		$this->_sheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	/**
	 * セルに値を代入
	 * 数値は文字列に強制設定
	 */
	public function set_cell_value($range, $value) {
		$this->_sheet->setCellValueExplicit($range, $value, PHPExcel_Cell_DataType::TYPE_STRING);
	}

	/**
	 * 保存
	 */
	public function save($file_name) {
		$this->_write = PHPExcel_IOFactory::createWriter($this->_excel, $this->_type);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Transfer-Encoding: binary ");
		header("Content-Disposition: attachment;filename=$file_name");
		header("Cache-Control: max-age=0");
		$this->_write->save("php://output");

		$this->fin();
	}

	/**
	 * メモリの開放
	 */
	public function fin() {
		$this->_excel->disconnectWorksheets();
		unset($this->_write);
		unset($this->_sheet);
		unset($this->_excel);
	}
}
?>