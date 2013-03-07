<?php
/**
 *
 *
 * WordPress Single Page Form
 * @author Toro_Unit
 * @version 0.3
 *
 *
 * */


include_once STYLESHEETPATH."/forms/config.php";

/**
 *
 * Load Mode Template
 *
 * Change include template single.php or single-{$post_type}.php
 *
 **/

Class Load_Mode_Template {

	public $mode = "";
	public $permitModes = array();
	public $post_type = "";

	public function init(){
		add_action( "template_redirect", array(&$this, "template_redirect") );
	}

	public function template_redirect() {

		$mode = $this->mode;

		if( !$mode ){
			return;
		}

		if(is_page()){
			if( empty($this->permitModes) or in_array($mode , $this->permitModes) ){
				include STYLESHEETPATH. "/page-".$mode.".php";
				exit;
			}
		}
		elseif( is_single() ){
			if( "post" == get_post_type() ){
				$post_type = "";
			}else {
				$post_type = "-".get_post_type();
				$this->post_type = $post_type;
			}
			if( empty($this->permitModes) or in_array($mode , $this->permitModes) ){
				if(file_exists( STYLESHEETPATH. "/single".$post_type."-".$mode.".php" )){
					include STYLESHEETPATH. "/single".$post_type."-".$mode.".php";
				}
				else {
					include STYLESHEETPATH. "/single-".$mode.".php";
				}

				exit;
			}
		}
	}
}



/**
 *
 * WP Single Form
 *
 *
 */

Class WP_Single_Form extends Load_Mode_Template {

	public $inputTypes = array();

	public $error = array();

	/**
	 *
	 * メールフォームの処理
	 *
	 * */
	public function form_init(){


		$this->permitModes = array( "confirm", "complete", "error" );

		if(!$_SESSION) {
			session_start();
		}

		//確認画面へ行く
		if($_POST["form_confirm"]) {
			foreach ($this->inputTypes as $key => $validate):
				if(!isset($_POST[$key])) {
					continue;
				}
				$value = $_POST[$key];

				if ($validate == "email") {
					if(!preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $value)){
						$this->error[$key] = "正しいメールアドレスを入力して下さい";
					}
				}

				if ($validate == "tel") {
					if(!preg_match('/^0\d{1,4}-\d{1,4}-\d{4}$/', $value) and !preg_match('/^0\d{9,10}$/', $value)){
						$this->error[$key] = "正しい電話番号を入力して下さい";
					}
				}

				if($validate == "noEmpty") {
					if(!$value) {
						$this->error[$key] = "必須項目です。";
					}
				}

				$_SESSION[$key] = htmlspecialchars($value, ENT_QUOTES);
			endforeach;

			if(empty($this->error)){
				$this->mode = "confirm";
			}
		}

		//送信処理へ
		if($_POST["form_complete"]) {

			$this->mode = "complete";

			if( $this->post_type ) {
				$post_type = $this->post_type."-";
			}

			$formVals = $_SESSION;
			extract($formVals);

			$template = $this->include_mail_template("admin", $post_type, $formVals);

			$adminFrom = "From:" .mb_encode_mimeheader($formLastName." ".$formFirstName) ."<".$formMail.">";
			if( mb_send_mail(ADMIN_TO, ADMIN_SUBJECT, $template,$adminFrom) == FALSE ) {
				$this->mode = "error";
			}

			$template = $this->include_mail_template("reply", $post_type ,$formVals);

			$replyFrom = "From:" .mb_encode_mimeheader(REPLY_FROM_NAME) ."<".REPLY_FROM.">";
			if( mb_send_mail($formMail, REPLY_SUBJECT, $template,$replyFrom) == FALSE ){
				$this->mode = "error";
			}

			if(!empty($_SESSION)){
				session_destroy();
			}
		}

		//編集画面へ戻る
		if($_POST["form_edit"]) {
			$this->mode = "";
		}

		$this->init();
	}

	public function include_mail_template( $type, $post_type , $values) {

		extract($values);

		if(file_exists(STYLESHEETPATH."/forms/".$post_type."mail_".$type.".php")){
			include STYLESHEETPATH."/forms/".$post_type."mail_".$type.".php";
		}else{
			include STYLESHEETPATH."/forms/mail_".$type.".php";
		}

		return $template;
	}

}

?>