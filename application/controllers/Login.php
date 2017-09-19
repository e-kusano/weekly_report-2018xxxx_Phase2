<?php

/**
 * LoginController
 * @author takanori_gozu
 *
 */
class Login extends MY_Controller {

	/**
	 * Index
	 */
	public function index() {
		$this->session->unset_userdata('is_login');
		$this->view('login/login.html');
	}

	/**
	 * 認証
	 */
	public function validate() {

		$user_id = $this->get('user_id');
		$password = $this->get('password');

		$this->load->model('login/LoginModel', 'model');

		//入力チェック
		$msgs = $this->model->input_check($user_id, $password);

		if ($msgs != null) {
			$this->set_err_info($msgs);
			$this->set('user_id', $user_id);
			$this->view('login/login.html');
			return;
		}

		//ログイン認証データセット
		$this->session->set_userdata(array('is_login' => '1'));

		//メイン画面へ
		redirect('WeeklyReportList');
	}
}
?>