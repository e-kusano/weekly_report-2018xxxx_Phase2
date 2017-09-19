<?php

/**
 * Controllerのオリジナル継承クラス
 *
 * 基本的にControllerで行う処理は以下
 * ・Viewの呼び出し
 * ・(必要に応じて)Modelのロード、呼び出し
 * ・値の受取、渡し
 * @author takanori_gozu
 *
 */
class MY_Controller extends CI_Controller {

	private $twig;
	public $_data;

	public function __construct() {
		parent::__construct();

		//Twigライブラリをロードする
		$loader = new Twig_Loader_Filesystem('application/views');
		$this->twig = new Twig_Environment($loader, array('cache' => APPPATH.'/cache/twig', 'debug' => true));

		//ログイン認証チェック
		$this->is_logined();

		//base_urlを格納しておく
		$this->set("base_url", base_url());
	}

	/**
	 * ログイン認証チェック
	 */
	public function is_logined() {
		$class = $this->router->fetch_class();
		if (!$this->session->userdata('is_login') &&
			 ($class != "Login" && $class != "Logout")) {
			redirect("Login");
		}
		$this->twig->addGlobal("session", $this->session);
	}

	//setter
	public function set($key, $value) {
		$this->_data[$key] = $value;
	}

	//getter
	public function get($key) {
		return $this->input->post($key);
	}

	/**
	 * エラー情報をセット
	 */
	public function set_err_info($msgs) {
		$this->set("err", "1");
		$this->set("err_msg", $msgs);
	}

	/**
	 * View
	 */
	public function view($template) {
		$view = $this->twig->loadTemplate($template);
		$this->output->set_output($view->render($this->_data));
	}

}
?>