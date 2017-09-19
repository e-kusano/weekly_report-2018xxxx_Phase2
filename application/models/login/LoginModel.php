<?php

/**
 * LoginModel
 * @author takanori_gozu
 *
 */
class LoginModel extends CI_Model {

	public function input_check($user_id, $password) {

		$msg = array();

		if ($user_id == '') {
			$msg[] = $this->lang->line('err_required', array('ユーザーID'));
		}

		if ($password == '') {
			$msg[] = $this->lang->line('err_required', array('パスワード'));
		}

		if ($msg != null) return $msg;

		$this->db->select('id, name, reader');
		$this->db->where('user_id', $user_id);
		$this->db->where('password', $password);
		$query = $this->db->get('employee');

		$result = $query->result('array');

		if ($result == null) {
			$msg[] = $this->lang->line('err_not_match', array('ユーザーID', 'パスワード'));
		} else {
			//ID,氏名をセッションへ
			$this->session->set_userdata(array(
											'user_id' => $result[0]['id'],
											'user_name' => $result[0]['name'],
											'reader' => $result[0]['reader']
			));
		}

		return $msg;
	}
}
?>