<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
global $USER;
if (!$USER->IsAuthorized()) {
  LocalRedirect("/");
}

$userId = $USER->GetID();

$res = \Bitrix\Main\UserTable::getList([
  'filter' => ['=ID' => $userId],
  'select' => ['ID', 'PERSONAL_PHOTO', 'PERSONAL_STATE', 'PERSONAL_CITY', 'PERSONAL_COUNTRY', 'EMAIL', 'PERSONAL_NOTES', 'WORK_POSITION', 'WORK_COMPANY', 'LAST_NAME', 'NAME', 'ID', 'UF_MAILING', 'UF_NEWS'],
]);

$arUser = $res->fetch();

if (!$arUser['PERSONAL_COUNTRY']) {
  $user = new CUser;
  $user->Update($userId, ['PERSONAL_COUNTRY' => 1]);
  $arUser['PERSONAL_COUNTRY'] = 1;
}

?>


<!-- Заказы -->
<link rel="stylesheet" href="styles.css">
<?php
function createUserGuardant($user){
  $serviceUrl = 'http://guardantapi:5000/customers';
  $data = [   "customerName"        =>$user['NAME'],
              "customerLastName"    =>$user['LAST_NAME'],
              "customerEmail"       =>$user['EMAIL'],
              "customerCompanyName" =>$user['WORK_COMPANY'],
              "customerDescription" =>'User created by lin-web',
              "customerStatus"      =>0,
              "archived"            =>false,
              "crmId"               =>$user['ID']];
    $headers = [
        'Content-Type: application/json',
        'Accept: */*',
    ];
    $myCurl = curl_init();
    curl_setopt_array($myCurl, [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $serviceUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    curl_exec($myCurl);
    curl_close($myCurl);
}
function getUserGuardant($id){
  $base_url = 'http://guardantapi:5000/';
  $url_get_user = $base_url . 'customer/' . $id ;
  $res = file_get_contents($url_get_user);
  $decoded_res = json_decode($res, true);
  return $customer_guardant = $decoded_res[0];
}
function userGuardant($userBitrix){
  // return user from guardant
  $customer_guardant = getUserGuardant($userBitrix['ID']);
  if (is_null($customer_guardant)){
      createUserGuardant($userBitrix);
      $customer_guardant = getUserGuardant($userBitrix['ID']);
  }
  return $customer_guardant;
}



$customer_guardant = userGuardant($arUser);
// Get orders
// $url_orders = 'http://guardantapi:5000/customer-orders/' . $customer_guardant['_id'];
$url_orders = 'http://guardantapi:5000/main/customer-orders/' . $customer_guardant['_id'];
$data_orders = file_get_contents($url_orders);
$decoded_orders = json_decode($data_orders, true);
?>

<script>
    async function onClickBuy(){
      try{
        console.log('onClickBuy');
        let response = await fetch('/api/create-order-guardant.php', {
            method: "POST", // *GET, POST, PUT, DELETE, etc.
            mode: "cors", // no-cors, *cors, same-origin
            cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
            credentials: "same-origin", // include, *same-origin, omit
            headers: {
                "Content-Type": "application/json",
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            redirect: "follow", // manual, *follow, error
            referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
            body: JSON.stringify({  // body data type must match "Content-Type" header
            }), 
        });
        console.log(response);
        const json = await response.json();
        const result = await JSON.parse(json);
        // if (!result?.order){
        //   console.log(result?.msg);
        // }
        console.log(result?.order);
      }catch(e){
        console.log('Error: ',e);
      }finally{
        location.reload();
      }
    }

    async function onClickCreateOrder(){
      // check valid
      
      const quantity_el = document.getElementById('quantity');
      if (!quantity_el.validity.valid || !quantity_el.value){
        return;
      }
      try{
        console.log('onClickBuy');
        let response = await fetch('/api/create-order-guardant.php', {
            method: "POST", // *GET, POST, PUT, DELETE, etc.
            mode: "cors", // no-cors, *cors, same-origin
            cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
            credentials: "same-origin", // include, *same-origin, omit
            headers: {
                "Content-Type": "application/json",
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            redirect: "follow", // manual, *follow, error
            referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
            body: JSON.stringify({ // body data type must match "Content-Type" header
              "description": document.getElementById('description').value,
              "quantity": quantity_el.value,
            }), 
        });
        console.log(response);
        const json = await response.json();
        const result = await JSON.parse(json);
        // if (!result?.order){
        //   console.log(result?.msg);
        // }
        console.log(result?.order);
      }catch(e){
        console.log('Error: ',e);
      }finally{
        location.reload();
      }
    }

    function onClickShow(element,id){
      element.children[0].classList.toggle('flipped');
      const items = document.querySelectorAll('.orders__item__licenses');
      items[id].classList.toggle('hidden');
    }
</script>



<main class="main-container">
  <div class="l-container">
    <div class="b-breadcrumbs">
      <a href="/" class="b-breadcrumbs__item">Назад</a>
    </div>
    <div class="b-lk">
      <nav class="b-lk__nav">
        <ul>
          <li class="b-lk__nav_active">
            <a href="#">
              <img src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-user-blue.svg" alt=""> 
              <span id="lk_first_name"><?= $arUser['NAME']; ?></span>
              &#160
              <span id="lk_second_name"><?= $arUser['LAST_NAME']; ?></span>
              
            </a>
          </li>
<!--
          <li>
            <a href="lk-projects.html">
              <img src="<?=SITE_TEMPLATE_PATH?>/assets/html/images/dist/icon-projects.svg" alt=""> Мои проекты
            </a>
          </li>
-->
        </ul>
      </nav>
      <input type="hidden" class="user_id" value="<?=$arUser['ID'];?>">
        <div class="b-lk__cols">
          <div class="b-lk__col b-lk-col_lg">
            <div class="b-lk__title">Основная информация</div>
            <div class="b-lk__item">
              <span>Имя:</span>
              <input type="text" class="b-lk__input input input_lk_js" data-input="NAME"
              value="<?= $arUser['NAME'] ?>" id="lk_first_name_input" >
            </div>
            <div class="b-lk__item">
              <span>Фамилия:</span>
              <input type="text" class="b-lk__input input input_lk_js" data-input="LAST_NAME"
              value="<?= $arUser['LAST_NAME'] ?>" id="lk_second_name_input">
            </div>
            <div class="b-lk__item">
              <span>Организация:</span>
              <input type="text" class="b-lk__input input input_lk_js" data-input="WORK_COMPANY"
              value="<?= $arUser['WORK_COMPANY'] ?>">
            </div>
            <div class="b-lk__item">
              <span>Сфера деятельности:</span>
              <input type="text" class="b-lk__input input input_lk_js" data-input="WORK_POSITION"
              value="<?= $arUser['WORK_POSITION'] ?>">
            </div>
            <div class="b-lk__item b-lk__item_modified">
              <textarea class="textarea b-lk__textarea input_lk_js" data-input="PERSONAL_NOTES" name=""
              id="" cols="30" rows="10"
              placeholder="О себе: <?= $arUser['PERSONAL_NOTES'] ?>"></textarea>
            </div>
          </div>
          <div class="b-lk__col b-lk-col_md">
            <div class="b-lk__wrapper-item">
              <div class="b-lk__title">Контакты</div>
              <div class="b-lk__item">
                <span>E-mail:</span>
                <input type="email" class="b-lk__input input input_lk_js" data-input="EMAIL"
                value="<?= $arUser['EMAIL'] ?>">
              </div>
              <div class="b-lk__item">
                <span>Страна:</span>
                <select class="b-lk__input input input_lk_js" id="user_country" 
                  style="border:none;" data-input="PERSONAL_COUNTRY">
                  <?$country_arr = GetCountryArray();?>
                  <?foreach ($country_arr['reference'] as $key => $value):?>
                    <option <?($country_arr['reference_id'][$key] == $arUser['PERSONAL_COUNTRY']) ? 'selected' : '';?> value="<?=$country_arr['reference_id'][$key];?>">
                      <?=$value;?>
                    </option>
                  <?endforeach;?>
                </select>
              </div>
              <div class="b-lk__item">
                <span>Город:</span>
                <input type="text" class="b-lk__input input input_lk_js" data-input="PERSONAL_CITY"
                value="<?= $arUser['PERSONAL_CITY'] ?>">
              </div>
              <div class="b-lk__item">
                <span>Регион:</span>
                <input type="text" class="b-lk__input input input_lk_js" data-input="PERSONAL_STATE"
                value="<?= $arUser['PERSONAL_STATE'] ?>">
              </div>
            </div>

            <div class="b-lk__wrapper-item">
              <div class="b-lk__title">Фото профиля</div>
              <div class="b-lk__upload">
                <?if (CFile::GetPath($arUser['PERSONAL_PHOTO'])):?>
                  <div class="b-lk__upload-img" style="background-image: url(<?=CFile::GetPath($arUser['PERSONAL_PHOTO']);?>)"></div>
                <?else:?>
                  <div class="b-lk__upload-img" style="text-align: center; font-size: 50px; line-height: 100px; color: grey; border:1px solid grey;">
                    <i class="fa fa-camera" aria-hidden="true"></i>
                  </div>
                <?endif;?>
                <div class="b-lk__upload-link">
                  <input name="file" type="file" name="file" id="input__file" class="input__file input_lk_js" data-input="PERSONAL_PHOTO">
                    <label for="input__file" class="input__file-button">
                      <img src="<?=SITE_TEMPLATE_PATH;?>/assets/html/images/dist/icon-edit.svg" alt="">
                    </label>
                </div>
              </div>
            </div>

            <form id="changePassword">
              <div class="b-lk__wrapper-item">
                <div class="b-lk__title">Пароль</div>
<!--
                <div class="b-lk__item b-lk__item_without-edit">
                  <input type="password" placeholder="Старый пароль" class="b-lk__input b-lk__input_modified input last_password" value="">
                </div>
-->
                <div class="b-lk__item b-lk__item_without-edit">
                  <input type="password" placeholder="Новый пароль" name="password" class="b-lk__input b-lk__input_modified input new_password" value="">
                  <input type="hidden" name="user_id" value="<?=$arUser['ID'];?>">
                </div>
                <div class="b-lk__item">
                  <input type="password" placeholder="Еще раз новый пароль" name="confirmPassword" class="b-lk__input b-lk__input_modified input new_password_copy" value="">
                </div>
                <!--b-lk__button-pas_active добавляем одно состояние-->
                <!--b-lk__button-pas_saved добавляем другое состояние-->
                <button class="b-lk__button-pas button button_sm">
                  <span class="b-lk__button-pas-text1">Изменить пароль</span>
                  <span class="b-lk__button-pas-text2">Пароль изменен!</span>
                  <img class="icon-lock" src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-lock-white.svg" alt="">
                  <img class="icon-tic" src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-tick-white.svg" alt="">
                </button>
              </div>
            </form>

            <div class="b-lk__wrapper-item">
              <div class="b-lk__title">Подписки</div>

              <? if ($arUser["UF_NEWS"] == 1) {
                $on_status = '';
                $off_status = 'style="display:none"';
              } else {
                $on_status = 'style="display:none"';
                $off_status = '';
              } ?>
              <div class="b-lk__list">
                <a href="#" class="b-lk__list-item mailing_js sub1" <?=$on_status?> onclick="return false" data-type="Y" data-code="UF_NEWS">
                  Новости
                  <img src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-tick.svg"
                  alt="">
                </a>
              </div>

              <a href="#" class="b-lk__button-sub mailing_js sub2" <?=$off_status?> onclick="return false" data-type="N" data-code="UF_NEWS">
                <span>Новости</span>
                <span>подписаться</span>
              </a>
              <? if ($arUser["UF_MAILING"] == 1) {
                $on_status = '';
                $off_status = 'style="display:none"';
              } else {
                $on_status = 'style="display:none"';
                $off_status = '';
              } ?>

              <div class="b-lk__list">
                <a href="#" class="b-lk__list-item mailing_js sub3" <?=$on_status?> onclick="return false" data-type="Y" data-code="UF_MAILING">
                  Рассылки
                  <img src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-tick.svg"
                  alt="">
                </a>
              </div>

              <a href="#" class="b-lk__button-sub mailing_js sub4" <?=$off_status?> onclick="return false" data-type="N" data-code="UF_MAILING">
                <span>Рассылки</span>
                <span>подписаться</span>
              </a>

              <div class="b-lk__helper-sub">
                Подпишитесь и будьте первыми в курсе событий Light-in-Night
              </div>
              <a href="#" onclick="return false" class="b-lk__link-delete-profile">
                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-delete.svg" alt="">
                удалить профиль
              </a>
            </div>
          </div>

        </div>


      <div class="orders__container">
        <div class="orders__title">Заказы:</div>
        <div>
          <!-- Создать заказ? -->
          <button id="btn-onClickBuy" class="orders__btn__buy" style="margin-bottom: 17px;" onclick="onClickBuy()">Купить лицензию</button>
          
          <div class="orders__create__order">
            <form action="#" method="post" class="order__form">
              <div class="order__form__title">Параметры заказа</div>
              <input name="id" id="id" type="text" class="order__form__item" value="<?php echo $customer_guardant['_id'] ?>" style="display:none;">
              <input name="description" id="description" type="text" class="order__form__item" placeholder="Описание...">
              <input name="quantity" id="quantity" type="number" class="order__form__item" placeholder="Количество лицензий..." min="1" max="10">
              <button class="orders__btn__buy" style="max-width:100%;width: 100%;" onclick="onClickCreateOrder()">Создать заказ</button>
            </form>
          </div>
        </div>
        <div class="orders__title">Мои заказы:</div>
        <div class="orders__items">
          <?php
          for($i = 0; $i<sizeof($decoded_orders); $i++){
            echo '
            <div class="orders__item">
              <div class="orders__item__title">Заказ номер <b>' .  $decoded_orders[$i]['_id'] . '</b></div>
              <div class="orders__item__description">Описание заказа: ' . $decoded_orders[$i]['description'] . ' </div>
              <div class="orders__item__show">
                  <div class="orders__item__show__text">Лицензии:</div>
                  <button class="orders__item__show__btn" onclick="onClickShow(this,' . $i . ')">
                      <span class="">
                      <svg width="10" height="5" viewBox="0 0 70 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M35.3223 37.2012C34.4144 37.3363 33.4563 37.0543 32.7574 36.3553L1.64467 5.24263C0.473099 4.07106 0.473099 2.17157 1.64467 1C2.81624 -0.171585 4.71571 -0.17157 5.88731 1L35.3223 30.435L64.7574 0.999985C65.9289 -0.171585 67.8284 -0.171585 69 0.999985C70.1716 2.17157 70.1716 4.07106 69 5.24263L37.8873 36.3553C37.1884 37.0543 36.2303 37.3363 35.3223 37.2012Z" fill="black"/>
                      </svg>
                      </span>
                  </button>
              </div>
              <div class="orders__item__licenses hidden">';
                if(!empty($decoded_orders[$i]['serials'])){
                  for($j = 0; $j<sizeof($decoded_orders[$i]['serials']); $j++){
                    echo '<div class="orders__item__licenses_license"> ' .  $decoded_orders[$i]['serials'][$j]['serialstring'] . '</div>';
                  }
                }else{
                  echo '<div class="orders__item__lecenses__description">Заказ не оплачен</div>';
                }
                echo '
              </div>
            </div>
            ';
          }
          ?>
        </div>
      </div>


    </div>
  </div>
</main>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
