<?php

/**
 * WeeklyReportRegistController
 * @author takanori_gozu
 *
 */
class WeeklyReportRegist extends MY_Controller {

	/**
	 * 新規登録
	 */
	public function regist_input() {
		//デフォルトは直前の月曜日
		$this->set('standard_date', date('Y-m-d', strtotime('last monday', strtotime(date('Y-m-d')))));
		$this->view('report/weekly_report_input.html');
	}

	/**
	 * 週報の登録
	 */
	public function regist() {

		$regist_user_id = $this->session->userdata('user_id');
		$project_name = $this->get('project_name');
		$work_content = $this->get('work_content');
		$reflect = $this->get('reflect');
		$other = $this->get('other');
		$standard_date = $this->get('standard_date');

		$this->load->model('report/WeeklyReportModel', 'model');

		$msg = $this->model->input_check($project_name, $work_content, $reflect);

		if ($msg != null) {
			$this->set_err_info($msg);
			$this->set('project_name', $project_name);
			$this->set('work_content', $work_content);
			$this->set('reflect', $reflect);
			$this->set('other', $other);
			$this->set('standard_date', $standard_date);
			$this->view('report/weekly_report_input.html');
			return;
		}

		$this->model->db_regist($regist_user_id, $project_name, $work_content, $reflect, $other, $standard_date);

		$this->set('regist_msg', $this->lang->line('db_registed'));
		$this->view('report/weekly_report_regist_complete.html');
	}

	/**
	 * 編集
	 */
	public function modify_input($id) {

		$this->load->model('report/WeeklyReportModel', 'model');

		$report_info = $this->model->get_report_detail($id);

		$this->set('id', $report_info[0]['id']);
		$this->set('project_name', $report_info[0]['project_name']);
		$this->set('work_content', $report_info[0]['work_content']);
		$this->set('reflect', $report_info[0]['reflect']);
		$this->set('other', $report_info[0]['other']);

		$this->view('report/weekly_report_input.html');
	}

	public function modify() {

		$id = $this->get('id');
		$project_name = $this->get('project_name');
		$work_content = $this->get('work_content');
		$reflect = $this->get('reflect');
		$other = $this->get('other');

		$this->load->model('report/WeeklyReportModel', 'model');

		$msg = $this->model->input_check($project_name, $work_content, $reflect);

		if ($msg != null) {
			$this->set_err_info($msg);
			$this->set('project_name', $project_name);
			$this->set('work_content', $work_content);
			$this->set('reflect', $reflect);
			$this->set('other', $other);
			$this->view('report/weekly_report_input.html');
			return;
		}

		$this->model->db_modify($id, $project_name, $work_content, $reflect, $other);

		$this->set('regist_msg', $this->lang->line('db_registed'));
		$this->view('report/weekly_report_regist_complete.html');
	}
}
?>