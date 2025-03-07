<?
define('SITE_ID', 's2');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>

<main>
  <div class="b-breadcrumbs">
    <div class="l-container">
      <?//$APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array());?>
      <p class="lin7-page__route">
        Главная / Контакты
      </p>
    </div>
  </div>

  <div class="b-section">
    <div class="l-container">
      <h1><?$APPLICATION->ShowTitle(false);?></h1>
      <div class="docs__container__top__text">
        <p>Центр Инновационных Разработок МСК БЛ ГРУПП является основным разработчиком специального программного обеспечения, мобильных и web-сервисов для специалистов светотехнической отрасли.</p>
      </div>
      <div class="docs__container__top__text">
        <p>Телефон: <a href="tel:+74957806157">+7 (495) 780-61-57</a></p>
        <p>E-mail: <a href="mailto:support@l-i-n.ru">support@l-i-n.ru</a></p>
        <p>Учебный центр: <a href="mailto:edu@l-i-n.ru">edu@l-i-n.ru</a></p>
        <p>129626, Москва, 1-й Рижский пер., 6</p>
      </div>
      <h3>Наша команда</h3>
      <?
        $APPLICATION->IncludeComponent("bitrix:news.list", "team", array(
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "AJAX_MODE" => "Y",
            "IBLOCK_TYPE" => "news",
            "IBLOCK_ID" => "8",
            "NEWS_COUNT" => "",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "ID",
            "SORT_ORDER2" => "ASC",
            "FILTER_NAME" => "",
            "FIELD_CODE" => array("ID"),
            "PROPERTY_CODE" => array("DESCRIPTION"),
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "SET_TITLE" => "N",
            "SET_BROWSER_TITLE" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_LAST_MODIFIED" => "N",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
            "ADD_SECTIONS_CHAIN" => "Y",
            "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "INCLUDE_SUBSECTIONS" => "Y",
            "CACHE_TYPE" => "N",
            "CACHE_TIME" => "3600",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
            "DISPLAY_TOP_PAGER" => "Y",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => "Новости",
            "PAGER_SHOW_ALWAYS" => "Y",
            "PAGER_TEMPLATE" => "",
            "PAGER_DESC_NUMBERING" => "Y",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "Y",
            "PAGER_BASE_LINK_ENABLE" => "Y",
            "SET_STATUS_404" => "Y",
            "SHOW_404" => "Y",
            "MESSAGE_404" => "",
            "PAGER_BASE_LINK" => "",
            "PAGER_PARAMS_NAME" => "arrPager",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
          )
        );
      ?>
    </div>
  </div>
        <!-- <div class="l-container p0 w100">
            <div class="b-about">
                <div class="b-about__head">
                    <div class="b-about__head-left">
                        <h2 class="title-page">Контакты</h2>
                        <div class="b-about__list">
                            <? // ВЛЮЧАЕМАЯ ОБЛАСТЬ
                            // $APPLICATION->IncludeFile(
                            //     '/include/about/contact.php',
                            //     Array(),
                            //     Array("MODE" => "html", "NAME" => "Контакты")
                            // );
                            ?>
                        </div>
                    </div>
                    <div class="b-about__head-right">
                        <div class="b-about__map yandex_cart_castom">
                            <?
                            // $APPLICATION->IncludeComponent(
                            //     "bitrix:map.yandex.view", 
                            //     ".default", 
                            //     array(
                            //         "INIT_MAP_TYPE" => "MAP",
                            //         "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.80527264853976;s:10:\"yandex_lon\";d:37.64693886301044;s:12:\"yandex_scale\";i:15;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.653356754316874;s:3:\"LAT\";d:55.806648559779376;s:4:\"TEXT\";s:39:\"1-й Рижский переулок, 6\";}}}",
                            //         "MAP_WIDTH" => "760",
                            //         "MAP_HEIGHT" => "312",
                            //         "CONTROLS" => array(
                            //             0 => "SCALELINE",
                            //         ),
                            //         "OPTIONS" => array(
                            //             0 => "ENABLE_SCROLL_ZOOM",
                            //             1 => "ENABLE_DBLCLICK_ZOOM",
                            //             2 => "ENABLE_DRAGGING",
                            //         ),
                            //         "MAP_ID" => "yam_1",
                            //         "COMPONENT_TEMPLATE" => ".default",
                            //         "API_KEY" => ""
                            //     ),
                            //     false
                            // );
                            ?>
                        </div>
                    </div>
                </div>
                <? 
                // $APPLICATION->IncludeComponent("bitrix:news.list", "team", Array(
                //         "DISPLAY_DATE" => "Y",
                //         "DISPLAY_NAME" => "Y",
                //         "DISPLAY_PICTURE" => "Y",
                //         "DISPLAY_PREVIEW_TEXT" => "Y",
                //         "AJAX_MODE" => "Y",
                //         "IBLOCK_TYPE" => "news",
                //         "IBLOCK_ID" => "8",
                //         "NEWS_COUNT" => "",
                //         "SORT_BY1" => "SORT",
                //         "SORT_ORDER1" => "ASC",
                //         "SORT_BY2" => "ID",
                //         "SORT_ORDER2" => "ASC",
                //         "FILTER_NAME" => "",
                //         "FIELD_CODE" => Array("ID"),
                //         "PROPERTY_CODE" => Array("DESCRIPTION"),
                //         "CHECK_DATES" => "Y",
                //         "DETAIL_URL" => "",
                //         "PREVIEW_TRUNCATE_LEN" => "",
                //         "ACTIVE_DATE_FORMAT" => "d.m.Y",
                //         "SET_TITLE" => "N",
                //         "SET_BROWSER_TITLE" => "N",
                //         "SET_META_KEYWORDS" => "N",
                //         "SET_META_DESCRIPTION" => "N",
                //         "SET_LAST_MODIFIED" => "N",
                //         "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
                //         "ADD_SECTIONS_CHAIN" => "Y",
                //         "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
                //         "PARENT_SECTION" => "",
                //         "PARENT_SECTION_CODE" => "",
                //         "INCLUDE_SUBSECTIONS" => "Y",
                //         "CACHE_TYPE" => "N",
                //         "CACHE_TIME" => "3600",
                //         "CACHE_FILTER" => "Y",
                //         "CACHE_GROUPS" => "Y",
                //         "DISPLAY_TOP_PAGER" => "Y",
                //         "DISPLAY_BOTTOM_PAGER" => "Y",
                //         "PAGER_TITLE" => "Новости",
                //         "PAGER_SHOW_ALWAYS" => "Y",
                //         "PAGER_TEMPLATE" => "",
                //         "PAGER_DESC_NUMBERING" => "Y",
                //         "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                //         "PAGER_SHOW_ALL" => "Y",
                //         "PAGER_BASE_LINK_ENABLE" => "Y",
                //         "SET_STATUS_404" => "Y",
                //         "SHOW_404" => "Y",
                //         "MESSAGE_404" => "",
                //         "PAGER_BASE_LINK" => "",
                //         "PAGER_PARAMS_NAME" => "arrPager",
                //         "AJAX_OPTION_JUMP" => "N",
                //         "AJAX_OPTION_STYLE" => "Y",
                //         "AJAX_OPTION_HISTORY" => "N",
                //         "AJAX_OPTION_ADDITIONAL" => ""
                //     )
                // ); 
                ?>
            </div>
        </div> -->
</main>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>