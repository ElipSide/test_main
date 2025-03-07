<?
define('SITE_ID', 's2');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("password_recovery");
?><?
$APPLICATION->IncludeComponent(
	"bitrix:main.auth.forgotpasswd",
	"password_recovery",
	Array(
		"AUTH_AUTH_URL" => "",
		"AUTH_REGISTER_URL" => ""
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>