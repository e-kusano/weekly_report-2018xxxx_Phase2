<?php

/**
 * WeeklyReportListController
 * @author takanori_gozu
 *
 */
class WeeklyReportList extends MY_Controller {

	public function index() {
		$this->top();
	}

	/**
	 * TOP画面
	 */
	public function top($search = null) {

		$this->load->model('report/WeeklyReportModel', 'model');

		//一覧用
		$this->set('list', $this->model->get_report_list($search));

		$this->set('search_date_from', $this->get('search_date_from'));
		$this->set('search_date_to', $this->get('search_date_to'));

		$keyword_map = $this->model->get_keyword_map();
		$this->set('keyword_map', form_dropdown('search_col', $keyword_map));
		$this->set('search_value', $this->get('search_value'));

		//名前
		if ($this->session->userdata('user_name') == $this->lang->line('administrator')) {
			$this->set('name_list', form_dropdown('name_list', $this->model->get_name_list()));
		}

		$title = $this->lang->line('wrapper_title_all');

		$this->set('wrapper_title', $title);

		$this->view('report/weekly_report_list.html');
	}

	/**
	 * 詳細
	 */
	public function detail($id) {

		$this->load->model('report/WeeklyReportModel', 'model');

		$report_info = $this->model->get_report_detail($id);

		$date = $report_info[0]['standard_date'];
		$name = $report_info[0]['name'];

		$this->set('weekly_report_name', $date. '【'. $name. '】');
		$this->set('report_detail_info', $report_info[0]);

		$this->view('report/weekly_report_detail.html');
	}

	/**
	 * 検索
	 */
	public function search() {

		$search_col = $this->get('search_col');
		$search_val = $this->get('search_value');
		$search_date_from = $this->get('search_date_from');
		$search_date_to = $this->get('search_date_to');
		$name = $this->get('name_list');

		$this->load->model('report/WeeklyReportModel', 'model');

		$search_map = $this->model->make_search_map($search_col, $search_val, $search_date_from, $search_date_to, $name);

		$this->top($search_map);

		$this->view('report/weekly_report_list.html');
	}

	/**
	 * Excel出力
	 */
	public function excel($id) {
		$this->load->model('report/WeeklyReportModel', 'model');
		$this->model->excel_output($id);
	}
}
?>