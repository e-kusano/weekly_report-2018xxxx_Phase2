<?php

/**
 * WeeklyReportModel
 * @author takanori_gozu
 *
 */
class WeeklyReportModel extends CI_Model {

	/**
	 * 一覧の取得
	 */
	public function get_report_list($search = null) {

		$table1 = 'weekly_report';
		$table2 = 'employee';

		$regist_user_ids = array();

		if ($this->session->userdata('user_name') != $this->lang->line('administrator')) {
			$regist_user_ids = $this->get_regist_user_ids();
		}

		$this->db->select("$table1.id,
				date_format($table1.standard_date,'%Y年%c月%e日') as date,
				$table2.name");

		$this->db->from("$table1");
		$this->db->join("$table2", "$table1.regist_user_id = $table2.id", 'left');

		if ($search != null) {
			foreach ($search as $key => $val) {
				if ($key != 'standard_date_from' && $key != 'standard_date_to') {
					$this->db->like($key, $val);
				}
			}
			if (array_key_exists('standard_date_from', $search)) {
				$this->db->where('standard_date >=', $search['standard_date_from']);
			}
			if (array_key_exists('standard_date_to', $search)) {
				$this->db->where('standard_date <=', $search['standard_date_to']);
			}
		}

		if ($regist_user_ids != null) {
			$this->db->where_in('regist_user_id', $regist_user_ids);
		}

		$this->db->order_by('standard_date', 'desc');
		$query = $this->db->get();

		return $query->result('array');
	}

	private function get_regist_user_ids() {

		$reader = $this->session->userdata('reader');
		if ($reader == '') {
			return $this->session->userdata('user_id');
		}

		$ids = array();
		$query = $this->db->query("select id from employee where division_id in ($reader)");

		foreach($query->result('array') as $row) {
			$ids[] = $row['id'];
		}

		return $ids;
	}

	/**
	 * 月別検索のリストを取得
	 */
	public function get_monthly_map() {

		$this->db->distinct();
		$this->db->select('left(regist_date, 7) as date');
		$this->db->order_by('date', 'desc');

		$query = $this->db->get('weekly_report');

		$list = $query->result('array');

		$arr = array("" => '月を選択');

		for($i = 0; $i < count($list); $i++) {
			$date = $list[$i]['date'];
			$value = date('Y年n月', strtotime($date));
			$arr[$date] = $value;
		}

		return $arr;
	}

	public function get_keyword_map() {

		$arr = array(
			'' => 'キーワードを選択',
			'project_name' => '参画プロジェクト名',
			'work_content' => '作業内容',
			'reflect' => '作業に対する疑問/不明点/反省等',
			'other' => 'その他'
		);

		return $arr;
	}

	/**
	 * 名前検索
	 */
	public function get_name_list() {
		$this->db->select('id, name');
		$this->db->where('name <>', $this->lang->line('administrator'));

		$query = $this->db->get('employee');
		$list = $query->result('array');

		$arr = array("" => '名前を選択');

		for($i = 0; $i < count($list); $i++) {
			$id = $list[$i]['id'];
			$name = $list[$i]['name'];
			$arr[$id] = $name;
		}

		return $arr;
	}

	/**
	 * 詳細の取得
	 */
	public function get_report_detail($id) {

		$table1 = 'weekly_report';
		$table2 = 'employee';

		$this->db->select("$table1.id,
						   date_format($table1.standard_date,'%Y年%c月%e日') as standard_date,
						   $table2.name,
						   $table1.project_name,
						   $table1.work_content,
						   $table1.reflect,
						   $table1.other");

		$this->db->from("$table1");
		$this->db->join("$table2", "$table1.regist_user_id = $table2.id");

		$this->db->where("$table1.id", $id);

		$query = $this->db->get();

		return $query->result('array');
	}

	/**
	 * 入力チェック
	 */
	public function input_check($project_name, $work_content, $reflect) {

		$msgs = array();

		if ($project_name == '') {
			$msgs[] = $this->lang->line('err_required', array($this->lang->line('project_name')));
		}

		if ($work_content == '') {
			$msgs[] = $this->lang->line('err_required', array($this->lang->line('work_content')));
		}

		if ($reflect == '') {
			$msgs[] = $this->lang->line('err_required', array($this->lang->line('reflect')));
		}

		//プロジェクト名は50文字以内
		if (mb_strlen($project_name) > 50) {
			$msg[] = $this->lang->line('err_max_length', array($this->lang->line('project_name'), 50));
		}

		return $msgs;
	}

	/**
	 * 登録処理
	 */
	public function db_regist($user_id, $project_name, $work_content, $reflect, $other, $standard_date) {

		$data = array(
				'project_name' => $project_name,
				'work_content' => $work_content,
				'reflect' => $reflect,
				'other' => $other,
				'regist_user_id' => $user_id,
				'regist_date' => date('Y-m-d'),
				'standard_date' => $standard_date
		);

		$this->db->insert('weekly_report', $data);
	}

	/**
	 * 編集処理
	 */
	public function db_modify($id, $project_name, $work_content, $reflect, $other) {

		$data = array(
				'project_name' => $project_name,
				'work_content' => $work_content,
				'reflect' => $reflect,
				'other' => $other
		);

		$this->db->where('id', $id);
		$this->db->update('weekly_report', $data);
	}

	/**
	 * 検索Mapの生成
	 */
	public function make_search_map($search_col, $search_val, $search_date_from, $search_date_to, $name) {

		$map = array();

		if ($search_col != '') {
			$map[$search_col] = $search_val;
		}

		if ($search_date_from != '') {
			$map['standard_date_from'] = $search_date_from;
		}

		if ($search_date_to != '') {
			$map['standard_date_to'] = $search_date_to;
		}

		if ($name != '') {
			$map['regist_user_id'] = $name;
		}

		return $map;
	}

	/**
	 * Excel出力処理
	 */
	public function excel_output($id) {

		$info = $this->get_report_detail($id);

		$this->load->model('common/PHPExcelModel', 'excel');

		$this->excel->load('weekly_report_format');

		$this->excel->set_sheet();

		//TODO 期間については、基準日(月曜日)から日曜日までで固定？
		$this->excel->set_cell_value('O3', $info[0]['name']);
		$this->excel->set_cell_value('O6', $info[0]['project_name']);
		$this->excel->set_cell_value('A8', $info[0]['work_content']);
		$this->excel->set_cell_value('A20', $info[0]['reflect']);
		$this->excel->set_cell_value('A32', $info[0]['other']);

		//TODO 出力ファイル名の検討
		$this->excel->save('週報.xlsx');
	}
}
?>