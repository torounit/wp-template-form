<?php
/**
 *
 * 自動返信メールテンプレート
 *
 *
 * */


$template = <<<EOT
ご連絡いただきありがとうございました。

氏名
{$formLastName} {$formFirstName}

フリガナ
{$formKanaLastName} {$formKanaFirstName}

メールアドレス
{$formMail}

EOT;
?>