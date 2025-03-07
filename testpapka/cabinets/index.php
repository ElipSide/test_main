<?
define('SITE_ID', 's2');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
global $USER;
if (!$USER->IsAuthorized()) {
  LocalRedirect("/");
}
?>
<?
	$APPLICATION->IncludeComponent(
		"bitrix:news.list", 
		"cabinet", 
		false
	);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
