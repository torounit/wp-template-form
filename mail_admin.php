<?php
/**
 *
 * 管理者宛メールテンプレート
 *
 *
 * */


$userHost = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
$userAgent = $_SERVER["HTTP_USER_AGENT"];
$nowDate = date('Y/m/d - H:i:s');



$template = <<<EOT

セミナー内容
{$formContent}

氏名
{$formLastName} {$formFirstName}

フリガナ
{$formKanaLastName} {$formKanaFirstName}


会社名
{$formCompany}

住所
{$formDepartment}

電話番号
{$formTel}


メールアドレス
{$formMail}





--------------------------
ユーザーIP
{$userHost}
UA
{$userAgent}
送信日時
{$nowDate}
EOT;
?>