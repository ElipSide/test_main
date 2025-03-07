<?
define('SITE_ID', 's2');
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


<?php
 // get products
 $response = file_get_contents('http://guardantapi:5000/main/products-versions/');
 $data_products = json_decode($response, true);
?>
<script>
  // Продукты в localStorage для js-отрисовок версий на вкладке заказы
  const productsResponse = <?= json_encode($data_products)?>;
  const dataProducts = productsResponse.reduce((acc,prod) => {
    return {
      ...acc,
      [prod.id]: prod
    }
  }, {});
  localStorage.setItem('lin-products-versions', JSON.stringify(dataProducts));
</script>

<?php
 // get typed products
 $res = file_get_contents('http://guardantapi:5000/main/products-typed/');
 $products_typed = json_decode($res, true);
?>
<script>
  // Продукты в localStorage для js-отрисовок версий на вкладке заказы (выбор версий и лицензий)
  const products_typed = <?= json_encode($products_typed)?>;
  const dataProductsTyped = products_typed.reduce((acc,prod) => {
    return {
      ...acc,
      [prod.id]: prod
    }
  }, {});
  localStorage.setItem('lin-typed-products', JSON.stringify(dataProductsTyped));
</script>

<script>
    // async function onClickBuy(){
    //   try{
    //     console.log('onClickBuy');
    //     let response = await fetch('/api/create-order-guardant.php', {
    //         method: "POST", // *GET, POST, PUT, DELETE, etc.
    //         mode: "cors", // no-cors, *cors, same-origin
    //         cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    //         credentials: "same-origin", // include, *same-origin, omit
    //         headers: {
    //             "Content-Type": "application/json",
    //             // 'Content-Type': 'application/x-www-form-urlencoded',
    //         },
    //         redirect: "follow", // manual, *follow, error
    //         referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
    //         body: JSON.stringify({  // body data type must match "Content-Type" header
    //         }), 
    //     });
    //     console.log(response);
    //     const json = await response.json();
    //     const result = await JSON.parse(json);
    //     // if (!result?.order){
    //     //   console.log(result?.msg);
    //     // }
    //     console.log(result?.order);
    //   }catch(e){
    //     console.log('Error: ',e);
    //   }finally{
    //     location.reload();
    //   }
    // }

    // async function onClickCreateOrder(){
    //   // check valid
      
    //   const quantity_el = document.getElementById('quantity');
    //   if (!quantity_el.validity.valid || !quantity_el.value){
    //     return;
    //   }
    //   try{
    //     console.log('onClickBuy');
    //     let response = await fetch('/api/create-order-guardant.php', {
    //         method: "POST", // *GET, POST, PUT, DELETE, etc.
    //         mode: "cors", // no-cors, *cors, same-origin
    //         cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    //         credentials: "same-origin", // include, *same-origin, omit
    //         headers: {
    //             "Content-Type": "application/json",
    //             // 'Content-Type': 'application/x-www-form-urlencoded',
    //         },
    //         redirect: "follow", // manual, *follow, error
    //         referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
    //         body: JSON.stringify({ // body data type must match "Content-Type" header
    //           "description": document.getElementById('description').value,
    //           "quantity": quantity_el.value,
    //         }), 
    //     });
    //     console.log(response);
    //     const json = await response.json();
    //     const result = await JSON.parse(json);
    //     // if (!result?.order){
    //     //   console.log(result?.msg);
    //     // }
    //     console.log(result?.order);
    //   }catch(e){
    //     console.log('Error: ',e);
    //   }finally{
    //     location.reload();
    //   }
    // }

    // function onClickShow(element,id){
    //   element.children[0].classList.toggle('flipped');
    //   const items = document.querySelectorAll('.orders__item__licenses');
    //   items[id].classList.toggle('hidden');
    // }
</script>

<script>
  function onPlusMinus(element){
    const type = element.dataset.type;
    const inputElement = element.parentNode.querySelector('input');
    if (type !== 'minus' && type !== 'plus'){
        return;
    }
    inputElement.value = (type === 'plus')
        ? ++inputElement.value
        : (inputElement.value>=2)
        ? --inputElement.value : inputElement.value;
    onInputProductCount(inputElement);
  }
  function onInputProductCount(element) {
    const count = element?.value;
    if(!element){
      return;
    }
    if (!element.checkValidity() || !count || count <= 0){
      element.value = element.min;
    }
    renderTotalPrice();
  }
  function renderTotalPrice() {
    const versionElem = document.querySelector('#select-version');
    const price = JSON.parse(localStorage.getItem('lin-products-versions'))[versionElem.value].price;
    const countElem = document.querySelector('#input-count');
    const count = countElem.value;
    const totalElem = document.querySelector('.lk__right__crorder__total');
    console.log(price, count)
    totalElem.innerHTML = `Стоимость: <span>${(price*count).toFixed([2])}</span> руб.`;
  }

  async function onCreateOrder(e){
    const versionElem = document.querySelector('#select-version');
    const pid = JSON.parse(localStorage.getItem('lin-products-versions'))[versionElem.value].pid;
    const countElem = document.querySelector('#input-count');
    const count = countElem.value;
    console.log(pid,count)
    try{
        let response = await fetch('/api/create-order-pid.php', {
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
                pid,
                count
            }), 
        });
        // console.log(response);
        // console.log(response.body);
        const json = await response.json();
        // console.log(json);
        const result = await JSON.parse(json);
        // console.log(result);
        // window.location.href = result?.confirmation_url || 'http://localhost/cabinet/';
        window.open(result?.confirmation_url || 'http://localhost/cabinet/', '_blank');
    }catch(e){
        console.log('Error: ',e);
        alert('Произошла ошибка');
    }finally{
        // window.location.href = 'http://localhost/cabinet/';
    }
  }

  function onClickCopy(elem){
    const data = elem?.dataset?.copy;
    console.log(data);
    try{
      navigator.clipboard.writeText(data).then(()=>{
        console.log('Данные успешно скопированы');
      }).catch(e => {
        alert(`Не удалось скопировать ключ: ${data}`);
      })
    }catch(e){
      alert(`Не удалось скопировать ключ: ${data}`);
    }
  }
</script>

<main class="main-container">
  <div class="l-container lk__main__container">
    <!-- <div class="b-breadcrumbs">
      <a href="/" class="b-breadcrumbs__item">Назад</a>
    </div> -->

    <div class="lk">
      <div class="lk__left">
        <div class="lk__left__title">
          Личный кабинет
        </div>
        <div class="lk__left__user">
          <div class="lk__left__user__image">
          <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="50" fill="#7D95B8"/>
            <path d="M50.0969 70.9986C43.2624 70.9986 36.4197 70.9986 29.5852 70.9986C28.2052 70.9986 26.964 70.6719 25.8944 69.7656C24.6124 68.6796 24 67.2833 24 65.6175C24 58.3257 24 51.0339 24 43.7503C24 40.6801 24 37.6098 24 34.5396C24 32.0655 25.5841 30.0078 27.9766 29.3954C28.393 29.2892 28.8421 29.2565 29.2749 29.2484C31.8633 29.232 34.4518 29.2402 37.0403 29.2484C37.3506 29.2484 37.571 29.1667 37.7833 28.9218C39.2123 27.3376 40.6576 25.7617 42.1111 24.1939C42.2091 24.0878 42.3887 24.0061 42.5275 24.0061C47.5737 23.998 52.6119 23.998 57.6582 24.0061C57.8296 24.0061 58.0419 24.1286 58.1726 24.2593C59.5526 25.7454 60.9162 27.2397 62.2717 28.7503C62.5738 29.0851 62.8759 29.2484 63.3495 29.2484C65.84 29.2239 68.3386 29.2892 70.8291 29.2239C73.6625 29.1504 76.2183 31.4694 76.2101 34.5968C76.1856 44.918 76.202 55.231 76.202 65.5522C76.202 68.7204 73.9402 70.9904 70.7719 70.9904C63.8803 70.9904 56.9886 70.9904 50.0969 70.9904V70.9986ZM63.1372 50.1357C63.1372 42.8929 57.3315 37.0791 50.1132 37.0709C42.9603 37.0709 37.0729 42.9011 37.0729 50.0051C37.0729 57.2969 42.8541 63.1434 50.0643 63.1434C57.3152 63.1434 63.1372 57.354 63.1372 50.1357V50.1357Z" fill="white"/>
            <path d="M58.4484 50.1049C58.4484 54.7429 54.7413 58.4582 50.1115 58.4582C45.4816 58.4582 41.75 54.7429 41.75 50.1212C41.75 45.4914 45.4898 41.7598 50.1359 41.7598C54.7249 41.7598 58.4484 45.4996 58.4484 50.1049Z" fill="#F9E8D6"/>
          </svg>
          </div>
          <div class="lk__left__user__name"><?= $arUser['NAME']; ?> <?= $arUser['LAST_NAME']; ?></div>
        </div>
        <div class="lk__left__settings">
          <div class="b-lk__item">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M10 10C8.625 10 7.44792 9.51042 6.46875 8.53125C5.48958 7.55208 5 6.375 5 5C5 3.625 5.48958 2.44792 6.46875 1.46875C7.44792 0.489583 8.625 0 10 0C11.375 0 12.5521 0.489583 13.5312 1.46875C14.5104 2.44792 15 3.625 15 5C15 6.375 14.5104 7.55208 13.5312 8.53125C12.5521 9.51042 11.375 10 10 10ZM0 20V16.5C0 15.7917 0.1825 15.1408 0.5475 14.5475C0.9125 13.9542 1.39667 13.5008 2 13.1875C3.29167 12.5417 4.60417 12.0575 5.9375 11.735C7.27083 11.4125 8.625 11.2508 10 11.25C11.375 11.25 12.7292 11.4117 14.0625 11.735C15.3958 12.0583 16.7083 12.5425 18 13.1875C18.6042 13.5 19.0887 13.9533 19.4537 14.5475C19.8187 15.1417 20.0008 15.7925 20 16.5V20H0ZM2.5 17.5H17.5V16.5C17.5 16.2708 17.4429 16.0625 17.3287 15.875C17.2146 15.6875 17.0633 15.5417 16.875 15.4375C15.75 14.875 14.6146 14.4533 13.4687 14.1725C12.3229 13.8917 11.1667 13.7508 10 13.75C8.83333 13.75 7.67708 13.8908 6.53125 14.1725C5.38542 14.4542 4.25 14.8758 3.125 15.4375C2.9375 15.5417 2.78625 15.6875 2.67125 15.875C2.55625 16.0625 2.49917 16.2708 2.5 16.5V17.5ZM10 7.5C10.6875 7.5 11.2762 7.25542 11.7662 6.76625C12.2562 6.27708 12.5008 5.68833 12.5 5C12.5 4.3125 12.2554 3.72417 11.7662 3.235C11.2771 2.74583 10.6883 2.50083 10 2.5C9.3125 2.5 8.72417 2.745 8.235 3.235C7.74583 3.725 7.50083 4.31333 7.5 5C7.5 5.6875 7.745 6.27625 8.235 6.76625C8.725 7.25625 9.31333 7.50083 10 7.5Z" fill="white"/>
            </svg>
            <span>Имя:</span>
            <input type="text" class="b-lk__input input input_lk_js" data-input="NAME"
              value="<?= $arUser['NAME'] ?>" id="lk_first_name_input" >
          </div>
          <div class="b-lk__item">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M10 10C8.625 10 7.44792 9.51042 6.46875 8.53125C5.48958 7.55208 5 6.375 5 5C5 3.625 5.48958 2.44792 6.46875 1.46875C7.44792 0.489583 8.625 0 10 0C11.375 0 12.5521 0.489583 13.5312 1.46875C14.5104 2.44792 15 3.625 15 5C15 6.375 14.5104 7.55208 13.5312 8.53125C12.5521 9.51042 11.375 10 10 10ZM0 20V16.5C0 15.7917 0.1825 15.1408 0.5475 14.5475C0.9125 13.9542 1.39667 13.5008 2 13.1875C3.29167 12.5417 4.60417 12.0575 5.9375 11.735C7.27083 11.4125 8.625 11.2508 10 11.25C11.375 11.25 12.7292 11.4117 14.0625 11.735C15.3958 12.0583 16.7083 12.5425 18 13.1875C18.6042 13.5 19.0887 13.9533 19.4537 14.5475C19.8187 15.1417 20.0008 15.7925 20 16.5V20H0ZM2.5 17.5H17.5V16.5C17.5 16.2708 17.4429 16.0625 17.3287 15.875C17.2146 15.6875 17.0633 15.5417 16.875 15.4375C15.75 14.875 14.6146 14.4533 13.4687 14.1725C12.3229 13.8917 11.1667 13.7508 10 13.75C8.83333 13.75 7.67708 13.8908 6.53125 14.1725C5.38542 14.4542 4.25 14.8758 3.125 15.4375C2.9375 15.5417 2.78625 15.6875 2.67125 15.875C2.55625 16.0625 2.49917 16.2708 2.5 16.5V17.5ZM10 7.5C10.6875 7.5 11.2762 7.25542 11.7662 6.76625C12.2562 6.27708 12.5008 5.68833 12.5 5C12.5 4.3125 12.2554 3.72417 11.7662 3.235C11.2771 2.74583 10.6883 2.50083 10 2.5C9.3125 2.5 8.72417 2.745 8.235 3.235C7.74583 3.725 7.50083 4.31333 7.5 5C7.5 5.6875 7.745 6.27625 8.235 6.76625C8.725 7.25625 9.31333 7.50083 10 7.5Z" fill="white"/>
            </svg>
            <span>Фамилия:</span>
              <input type="text" class="b-lk__input input input_lk_js" data-input="LAST_NAME"
              value="<?= $arUser['LAST_NAME'] ?>" id="lk_second_name_input">
          </div>
          <div class="b-lk__item">
            <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 16C1.45 16 0.979333 15.8043 0.588 15.413C0.196667 15.0217 0.000666667 14.5507 0 14V2C0 1.45 0.196 0.979333 0.588 0.588C0.98 0.196666 1.45067 0.000666667 2 0H18C18.55 0 19.021 0.196 19.413 0.588C19.805 0.98 20.0007 1.45067 20 2V14C20 14.55 19.8043 15.021 19.413 15.413C19.0217 15.805 18.5507 16.0007 18 16H2ZM10 9L2 4V14H18V4L10 9ZM10 7L18 2H2L10 7ZM2 4V2V14V4Z" fill="white"/>
            </svg>
            <span>E-mail:</span>
            <input type="email" class="b-lk__input input input_lk_js" data-input="EMAIL"
              value="<?= $arUser['EMAIL'] ?>">
          </div>
          <div class="b-lk__item">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17.6 19.1674V20.5149C17.6 20.8266 17.4944 21.088 17.2832 21.2992C17.072 21.5104 16.8109 21.6157 16.5 21.6149C16.1883 21.6149 15.9273 21.5097 15.7168 21.2992C15.5063 21.0888 15.4007 20.8273 15.4 20.5149V17.0499C15.4 16.5916 15.5606 16.2022 15.8818 15.8817C16.203 15.5613 16.5924 15.4007 17.05 15.3999H20.515C20.8267 15.3999 21.0881 15.5055 21.2993 15.7167C21.5105 15.9279 21.6157 16.189 21.615 16.4999C21.615 16.8116 21.5098 17.073 21.2993 17.2842C21.0888 17.4954 20.8274 17.6007 20.515 17.5999H19.14L21.615 20.0749C21.8167 20.2766 21.9175 20.5289 21.9175 20.8317C21.9175 21.1346 21.8167 21.3957 21.615 21.6149C21.395 21.8349 21.1339 21.9449 20.8318 21.9449C20.5297 21.9449 20.2682 21.8349 20.0475 21.6149L17.6 19.1674ZM11 21.9999C9.47833 21.9999 8.04833 21.711 6.71 21.1331C5.37167 20.5553 4.2075 19.7717 3.2175 18.7824C2.2275 17.7924 1.44393 16.6283 0.8668 15.2899C0.289667 13.9516 0.000733333 12.5216 0 11C0 9.4783 0.288933 8.04831 0.8668 6.70998C1.44467 5.37165 2.22823 4.20749 3.2175 3.21749C4.2075 2.22749 5.37167 1.44393 6.71 0.866797C8.04833 0.289666 9.47833 0.000733331 11 0C12.5217 0 13.9517 0.288932 15.29 0.866797C16.6283 1.44466 17.7925 2.22823 18.7825 3.21749C19.7725 4.20749 20.5564 5.37165 21.1343 6.70998C21.7122 8.04831 22.0007 9.4783 22 11C22 11.1833 21.9956 11.385 21.9868 11.605C21.978 11.825 21.9641 12.0266 21.945 12.21C21.9083 12.5216 21.78 12.7647 21.56 12.9393C21.34 13.1138 21.065 13.2007 20.735 13.2C20.4417 13.2 20.1942 13.0716 19.9925 12.815C19.7908 12.5583 19.7083 12.2833 19.745 11.99C19.7817 11.8066 19.8 11.6416 19.8 11.495V11C19.8 10.6333 19.7769 10.2666 19.7307 9.89997C19.6845 9.5333 19.6159 9.16663 19.525 8.79997H15.785C15.84 9.16663 15.8814 9.5333 15.9093 9.89997C15.9372 10.2666 15.9507 10.6333 15.95 11V11.5918C15.95 11.8022 15.9408 11.9991 15.9225 12.1825C15.8858 12.4941 15.7575 12.7416 15.5375 12.925C15.3175 13.1083 15.0517 13.2 14.74 13.2C14.4467 13.2 14.1948 13.0808 13.9843 12.8425C13.7738 12.6041 13.6866 12.3383 13.7225 12.045C13.7408 11.8616 13.75 11.6875 13.75 11.5225V11C13.75 10.6333 13.7364 10.2666 13.7093 9.89997C13.6822 9.5333 13.6407 9.16663 13.585 8.79997H8.415C8.36 9.16663 8.31893 9.5333 8.2918 9.89997C8.26467 10.2666 8.25073 10.6333 8.25 11C8.25 11.3666 8.26393 11.7333 8.2918 12.1C8.31967 12.4666 8.36073 12.8333 8.415 13.2H11C11.3117 13.2 11.5731 13.3056 11.7843 13.5168C11.9955 13.728 12.1007 13.989 12.1 14.2999C12.1 14.6116 11.9944 14.873 11.7832 15.0842C11.572 15.2954 11.3109 15.4007 11 15.3999H8.91C9.13 16.1883 9.41417 16.9447 9.7625 17.6692C10.1108 18.3938 10.5233 19.0857 11 19.7449C11.1833 19.7449 11.3667 19.7497 11.55 19.7592C11.7333 19.7688 11.9167 19.764 12.1 19.7449C12.4117 19.7083 12.6683 19.7864 12.87 19.9792C13.0717 20.1721 13.1725 20.424 13.1725 20.7349C13.1725 21.0649 13.09 21.3399 12.925 21.5599C12.76 21.7799 12.5217 21.9083 12.21 21.9449C12.0267 21.9633 11.825 21.9772 11.605 21.9867C11.385 21.9963 11.1833 22.0007 11 21.9999ZM2.475 13.2H6.215C6.16 12.8333 6.11893 12.4666 6.0918 12.1C6.06467 11.7333 6.05073 11.3666 6.05 11C6.05 10.6333 6.06393 10.2666 6.0918 9.89997C6.11967 9.5333 6.16073 9.16663 6.215 8.79997H2.475C2.38333 9.16663 2.31477 9.5333 2.2693 9.89997C2.22383 10.2666 2.20073 10.6333 2.2 11C2.2 11.3666 2.2231 11.7333 2.2693 12.1C2.3155 12.4666 2.38407 12.8333 2.475 13.2ZM8.14 19.3049C7.81 18.6816 7.52107 18.0447 7.2732 17.3942C7.02533 16.7438 6.81927 16.079 6.655 15.3999H3.41C3.94167 16.3349 4.61083 17.1372 5.4175 17.8067C6.22417 18.4763 7.13167 18.9757 8.14 19.3049ZM3.41 6.59998H6.655C6.82 5.92165 7.02643 5.25725 7.2743 4.60678C7.52217 3.95632 7.81073 3.31905 8.14 2.69499C7.13167 3.02499 6.22417 3.52475 5.4175 4.19429C4.61083 4.86382 3.94167 5.66571 3.41 6.59998ZM8.91 6.59998H13.09C12.87 5.81165 12.5858 5.05558 12.2375 4.33178C11.8892 3.60799 11.4767 2.91572 11 2.25499C10.5233 2.91499 10.1108 3.60725 9.7625 4.33178C9.41417 5.05632 9.13 5.81238 8.91 6.59998ZM15.345 6.59998H18.59C18.0583 5.66498 17.3892 4.86308 16.5825 4.19429C15.7758 3.52549 14.8683 3.02572 13.86 2.69499C14.19 3.31832 14.4789 3.95559 14.7268 4.60678C14.9747 5.25798 15.1807 5.92238 15.345 6.59998Z" fill="white"/>
            </svg>
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
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17.6 19.1674V20.5149C17.6 20.8266 17.4944 21.088 17.2832 21.2992C17.072 21.5104 16.8109 21.6157 16.5 21.6149C16.1883 21.6149 15.9273 21.5097 15.7168 21.2992C15.5063 21.0888 15.4007 20.8273 15.4 20.5149V17.0499C15.4 16.5916 15.5606 16.2022 15.8818 15.8817C16.203 15.5613 16.5924 15.4007 17.05 15.3999H20.515C20.8267 15.3999 21.0881 15.5055 21.2993 15.7167C21.5105 15.9279 21.6157 16.189 21.615 16.4999C21.615 16.8116 21.5098 17.073 21.2993 17.2842C21.0888 17.4954 20.8274 17.6007 20.515 17.5999H19.14L21.615 20.0749C21.8167 20.2766 21.9175 20.5289 21.9175 20.8317C21.9175 21.1346 21.8167 21.3957 21.615 21.6149C21.395 21.8349 21.1339 21.9449 20.8318 21.9449C20.5297 21.9449 20.2682 21.8349 20.0475 21.6149L17.6 19.1674ZM11 21.9999C9.47833 21.9999 8.04833 21.711 6.71 21.1331C5.37167 20.5553 4.2075 19.7717 3.2175 18.7824C2.2275 17.7924 1.44393 16.6283 0.8668 15.2899C0.289667 13.9516 0.000733333 12.5216 0 11C0 9.4783 0.288933 8.04831 0.8668 6.70998C1.44467 5.37165 2.22823 4.20749 3.2175 3.21749C4.2075 2.22749 5.37167 1.44393 6.71 0.866797C8.04833 0.289666 9.47833 0.000733331 11 0C12.5217 0 13.9517 0.288932 15.29 0.866797C16.6283 1.44466 17.7925 2.22823 18.7825 3.21749C19.7725 4.20749 20.5564 5.37165 21.1343 6.70998C21.7122 8.04831 22.0007 9.4783 22 11C22 11.1833 21.9956 11.385 21.9868 11.605C21.978 11.825 21.9641 12.0266 21.945 12.21C21.9083 12.5216 21.78 12.7647 21.56 12.9393C21.34 13.1138 21.065 13.2007 20.735 13.2C20.4417 13.2 20.1942 13.0716 19.9925 12.815C19.7908 12.5583 19.7083 12.2833 19.745 11.99C19.7817 11.8066 19.8 11.6416 19.8 11.495V11C19.8 10.6333 19.7769 10.2666 19.7307 9.89997C19.6845 9.5333 19.6159 9.16663 19.525 8.79997H15.785C15.84 9.16663 15.8814 9.5333 15.9093 9.89997C15.9372 10.2666 15.9507 10.6333 15.95 11V11.5918C15.95 11.8022 15.9408 11.9991 15.9225 12.1825C15.8858 12.4941 15.7575 12.7416 15.5375 12.925C15.3175 13.1083 15.0517 13.2 14.74 13.2C14.4467 13.2 14.1948 13.0808 13.9843 12.8425C13.7738 12.6041 13.6866 12.3383 13.7225 12.045C13.7408 11.8616 13.75 11.6875 13.75 11.5225V11C13.75 10.6333 13.7364 10.2666 13.7093 9.89997C13.6822 9.5333 13.6407 9.16663 13.585 8.79997H8.415C8.36 9.16663 8.31893 9.5333 8.2918 9.89997C8.26467 10.2666 8.25073 10.6333 8.25 11C8.25 11.3666 8.26393 11.7333 8.2918 12.1C8.31967 12.4666 8.36073 12.8333 8.415 13.2H11C11.3117 13.2 11.5731 13.3056 11.7843 13.5168C11.9955 13.728 12.1007 13.989 12.1 14.2999C12.1 14.6116 11.9944 14.873 11.7832 15.0842C11.572 15.2954 11.3109 15.4007 11 15.3999H8.91C9.13 16.1883 9.41417 16.9447 9.7625 17.6692C10.1108 18.3938 10.5233 19.0857 11 19.7449C11.1833 19.7449 11.3667 19.7497 11.55 19.7592C11.7333 19.7688 11.9167 19.764 12.1 19.7449C12.4117 19.7083 12.6683 19.7864 12.87 19.9792C13.0717 20.1721 13.1725 20.424 13.1725 20.7349C13.1725 21.0649 13.09 21.3399 12.925 21.5599C12.76 21.7799 12.5217 21.9083 12.21 21.9449C12.0267 21.9633 11.825 21.9772 11.605 21.9867C11.385 21.9963 11.1833 22.0007 11 21.9999ZM2.475 13.2H6.215C6.16 12.8333 6.11893 12.4666 6.0918 12.1C6.06467 11.7333 6.05073 11.3666 6.05 11C6.05 10.6333 6.06393 10.2666 6.0918 9.89997C6.11967 9.5333 6.16073 9.16663 6.215 8.79997H2.475C2.38333 9.16663 2.31477 9.5333 2.2693 9.89997C2.22383 10.2666 2.20073 10.6333 2.2 11C2.2 11.3666 2.2231 11.7333 2.2693 12.1C2.3155 12.4666 2.38407 12.8333 2.475 13.2ZM8.14 19.3049C7.81 18.6816 7.52107 18.0447 7.2732 17.3942C7.02533 16.7438 6.81927 16.079 6.655 15.3999H3.41C3.94167 16.3349 4.61083 17.1372 5.4175 17.8067C6.22417 18.4763 7.13167 18.9757 8.14 19.3049ZM3.41 6.59998H6.655C6.82 5.92165 7.02643 5.25725 7.2743 4.60678C7.52217 3.95632 7.81073 3.31905 8.14 2.69499C7.13167 3.02499 6.22417 3.52475 5.4175 4.19429C4.61083 4.86382 3.94167 5.66571 3.41 6.59998ZM8.91 6.59998H13.09C12.87 5.81165 12.5858 5.05558 12.2375 4.33178C11.8892 3.60799 11.4767 2.91572 11 2.25499C10.5233 2.91499 10.1108 3.60725 9.7625 4.33178C9.41417 5.05632 9.13 5.81238 8.91 6.59998ZM15.345 6.59998H18.59C18.0583 5.66498 17.3892 4.86308 16.5825 4.19429C15.7758 3.52549 14.8683 3.02572 13.86 2.69499C14.19 3.31832 14.4789 3.95559 14.7268 4.60678C14.9747 5.25798 15.1807 5.92238 15.345 6.59998Z" fill="white"/>
            </svg>
            <span>Город:</span>
            <input type="text" class="b-lk__input input input_lk_js" data-input="PERSONAL_CITY"
              value="<?= $arUser['PERSONAL_CITY'] ?>">
          </div>
          <div class="b-lk__item">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M10 10C8.625 10 7.44792 9.51042 6.46875 8.53125C5.48958 7.55208 5 6.375 5 5C5 3.625 5.48958 2.44792 6.46875 1.46875C7.44792 0.489583 8.625 0 10 0C11.375 0 12.5521 0.489583 13.5312 1.46875C14.5104 2.44792 15 3.625 15 5C15 6.375 14.5104 7.55208 13.5312 8.53125C12.5521 9.51042 11.375 10 10 10ZM0 20V16.5C0 15.7917 0.1825 15.1408 0.5475 14.5475C0.9125 13.9542 1.39667 13.5008 2 13.1875C3.29167 12.5417 4.60417 12.0575 5.9375 11.735C7.27083 11.4125 8.625 11.2508 10 11.25C11.375 11.25 12.7292 11.4117 14.0625 11.735C15.3958 12.0583 16.7083 12.5425 18 13.1875C18.6042 13.5 19.0887 13.9533 19.4537 14.5475C19.8187 15.1417 20.0008 15.7925 20 16.5V20H0ZM2.5 17.5H17.5V16.5C17.5 16.2708 17.4429 16.0625 17.3287 15.875C17.2146 15.6875 17.0633 15.5417 16.875 15.4375C15.75 14.875 14.6146 14.4533 13.4687 14.1725C12.3229 13.8917 11.1667 13.7508 10 13.75C8.83333 13.75 7.67708 13.8908 6.53125 14.1725C5.38542 14.4542 4.25 14.8758 3.125 15.4375C2.9375 15.5417 2.78625 15.6875 2.67125 15.875C2.55625 16.0625 2.49917 16.2708 2.5 16.5V17.5ZM10 7.5C10.6875 7.5 11.2762 7.25542 11.7662 6.76625C12.2562 6.27708 12.5008 5.68833 12.5 5C12.5 4.3125 12.2554 3.72417 11.7662 3.235C11.2771 2.74583 10.6883 2.50083 10 2.5C9.3125 2.5 8.72417 2.745 8.235 3.235C7.74583 3.725 7.50083 4.31333 7.5 5C7.5 5.6875 7.745 6.27625 8.235 6.76625C8.725 7.25625 9.31333 7.50083 10 7.5Z" fill="white"/>
            </svg>
            <span>Организация:</span>
            <input type="text" class="b-lk__input input input_lk_js" data-input="WORK_COMPANY"
              value="<?= $arUser['WORK_COMPANY'] ?>">
          </div>
          <div class="b-lk__item">
            <svg class="w60px" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1.16667 20C0.836111 20 0.559222 19.8871 0.336 19.6612C0.112778 19.4353 0.000777778 19.1561 0 18.8235C0 18.4902 0.112 18.211 0.336 17.9859C0.56 17.7608 0.836889 17.6478 1.16667 17.6471H12.8333C13.1639 17.6471 13.4412 17.76 13.6652 17.9859C13.8892 18.2118 14.0008 18.491 14 18.8235C14 19.1569 13.888 19.4365 13.664 19.6623C13.44 19.8882 13.1631 20.0008 12.8333 20H1.16667ZM15.1667 11.7647C13.5528 11.7647 12.1773 11.191 11.0402 10.0435C9.90306 8.89608 9.33411 7.50902 9.33333 5.88235C9.33333 4.2549 9.90228 2.86784 11.0402 1.72118C12.1781 0.574509 13.5536 0.000784314 15.1667 0C16.7806 0 18.1564 0.573725 19.2943 1.72118C20.4322 2.86863 21.0008 4.25569 21 5.88235C21 7.5098 20.4311 8.89725 19.2932 10.0447C18.1553 11.1922 16.7798 11.7655 15.1667 11.7647ZM1.16667 10.5882C0.836111 10.5882 0.559222 10.4753 0.336 10.2494C0.112778 10.0235 0.000777778 9.74431 0 9.41176C0 9.07843 0.112 8.79921 0.336 8.57412C0.56 8.34902 0.836889 8.23608 1.16667 8.23529H6.62083C6.95139 8.23529 7.22867 8.34823 7.45267 8.57412C7.67667 8.8 7.78828 9.07921 7.7875 9.41176C7.7875 9.72549 7.6755 10 7.4515 10.2353C7.2275 10.4706 6.95061 10.5882 6.62083 10.5882H1.16667ZM1.16667 15.2941C0.836111 15.2941 0.559222 15.1812 0.336 14.9553C0.112778 14.7294 0.000777778 14.4502 0 14.1176C0 13.7843 0.112 13.5051 0.336 13.28C0.56 13.0549 0.836889 12.942 1.16667 12.9412H10.4125C10.7431 12.9412 11.0203 13.0541 11.2443 13.28C11.4683 13.5059 11.5799 13.7851 11.5792 14.1176C11.5792 14.4314 11.4722 14.7059 11.2583 14.9412C11.0444 15.1765 10.7625 15.2941 10.4125 15.2941H1.16667ZM15.1667 9.41176C15.3222 9.41176 15.4583 9.35294 15.575 9.23529C15.6917 9.11765 15.75 8.98039 15.75 8.82353V5.29412C15.75 5.13725 15.6917 5 15.575 4.88235C15.4583 4.7647 15.3222 4.70588 15.1667 4.70588C15.0111 4.70588 14.875 4.7647 14.7583 4.88235C14.6417 5 14.5833 5.13725 14.5833 5.29412V8.82353C14.5833 8.98039 14.6417 9.11765 14.7583 9.23529C14.875 9.35294 15.0111 9.41176 15.1667 9.41176ZM15.1667 3.52941C15.3222 3.52941 15.4583 3.47059 15.575 3.35294C15.6917 3.23529 15.75 3.09804 15.75 2.94118C15.75 2.78431 15.6917 2.64706 15.575 2.52941C15.4583 2.41176 15.3222 2.35294 15.1667 2.35294C15.0111 2.35294 14.875 2.41176 14.7583 2.52941C14.6417 2.64706 14.5833 2.78431 14.5833 2.94118C14.5833 3.09804 14.6417 3.23529 14.7583 3.35294C14.875 3.47059 15.0111 3.52941 15.1667 3.52941Z" fill="white"/>
            </svg>
            <span>Сфера деятельности:</span>
            <input type="text" class="b-lk__input input input_lk_js" data-input="WORK_POSITION" autocomplete="new-password"
              value="<?= $arUser['WORK_POSITION'] ?> ">
          </div>

          <? if ($arUser["UF_NEWS"] == 1) {
            $on_status = '';
            $off_status = 'style="display:none"';
          } else {
            $on_status = 'style="display:none"';
            $off_status = '';
          } ?>
          <div class="lk__left__settings__sub">
            <a href="#" class="b-lk__list-item mailing_js sub1" <?=$on_status?> onclick="return false" data-type="Y" data-code="UF_NEWS">
              <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17 1.5H5C3.067 1.5 1.5 3.067 1.5 5V17C1.5 18.933 3.067 20.5 5 20.5H17C18.933 20.5 20.5 18.933 20.5 17V5C20.5 3.067 18.933 1.5 17 1.5ZM5 0C2.23858 0 0 2.23858 0 5V17C0 19.7614 2.23858 22 5 22H17C19.7614 22 22 19.7614 22 17V5C22 2.23858 19.7614 0 17 0H5ZM5.61118 10.3486C5.27363 9.91149 5.35436 9.28349 5.79149 8.94595C6.22862 8.60841 6.85662 8.68914 7.19416 9.12626L10.6732 13.6317L16.3208 4.84132C16.6193 4.37667 17.238 4.242 17.7027 4.54053C18.1673 4.83906 18.302 5.45774 18.0034 5.92239L11.648 15.8144C11.5109 16.0278 11.3063 16.1716 11.08 16.2358C10.6746 16.4002 10.1937 16.283 9.9132 15.9199L5.61118 10.3486Z" fill="white"/>
              </svg>
            </a>
            <a href="#" class="b-lk__button-sub mailing_js sub2" <?=$off_status?> onclick="return false" data-type="N" data-code="UF_NEWS">
              <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0.75" y="0.75" width="20.5" height="20.5" rx="4.25" stroke="white" stroke-width="1.5"/>
              </svg>  
            </a>
            <span>Подписка на новости</span>
          </div>

          <? if ($arUser["UF_MAILING"] == 1) {
            $on_status = '';
            $off_status = 'style="display:none"';
          } else {
            $on_status = 'style="display:none"';
            $off_status = '';
          } ?>
          <div class="lk__left__settings__sub">
            <a href="#" class="b-lk__list-item mailing_js sub3" <?=$on_status?> onclick="return false" data-type="Y" data-code="UF_MAILING">
              <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17 1.5H5C3.067 1.5 1.5 3.067 1.5 5V17C1.5 18.933 3.067 20.5 5 20.5H17C18.933 20.5 20.5 18.933 20.5 17V5C20.5 3.067 18.933 1.5 17 1.5ZM5 0C2.23858 0 0 2.23858 0 5V17C0 19.7614 2.23858 22 5 22H17C19.7614 22 22 19.7614 22 17V5C22 2.23858 19.7614 0 17 0H5ZM5.61118 10.3486C5.27363 9.91149 5.35436 9.28349 5.79149 8.94595C6.22862 8.60841 6.85662 8.68914 7.19416 9.12626L10.6732 13.6317L16.3208 4.84132C16.6193 4.37667 17.238 4.242 17.7027 4.54053C18.1673 4.83906 18.302 5.45774 18.0034 5.92239L11.648 15.8144C11.5109 16.0278 11.3063 16.1716 11.08 16.2358C10.6746 16.4002 10.1937 16.283 9.9132 15.9199L5.61118 10.3486Z" fill="white"/>
              </svg>
            </a>
            <a href="#" class="b-lk__button-sub mailing_js sub4" <?=$off_status?> onclick="return false" data-type="N" data-code="UF_MAILING">
              <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0.75" y="0.75" width="20.5" height="20.5" rx="4.25" stroke="white" stroke-width="1.5"/>
              </svg>  
            </a>
            <span>Подписка на рассылки</span>
          </div>

          <div class="b-lk__item b-lk__item_without-edit">
            <input type="password" placeholder="Новый пароль" name="password" class="b-lk__input b-lk__input_modified input new_password no-bg-img" value="">
            <input type="hidden" name="user_id" value="<?=$arUser['ID'];?>">
          </div>
          <div class="b-lk__item nobgimg-input">
            <input type="password" placeholder="Еще раз новый пароль" name="confirmPassword" class="b-lk__input b-lk__input_modified input new_password_copy no-bg-img" value="">
          </div>
          <!--b-lk__button-pas_active добавляем одно состояние-->
          <!--b-lk__button-pas_saved добавляем другое состояние-->
          <button class="b-lk__button-pas button button_sm lin7-btn-pass">
            <span class="b-lk__button-pas-text1">Изменить пароль</span>
            <span class="b-lk__button-pas-text2">Пароль изменен!</span>
            <img class="icon-lock" src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-lock-white.svg" alt="">
            <img class="icon-tic" src="<?= SITE_TEMPLATE_PATH ?>/assets/html/images/dist/icon-tick-white.svg" alt="">
          </button>
          </div>
        </div>
      <div class="lk__right">
        <div class="lk__right__title">
          Заказы
        </div>

        <div class="lin7__order">
          <h2 class="lin7__order__title">Оформить заказ</h2>
          <div class="lin7__order__select">
              <span>Версия</span>
              <select name="version" id="select-version" onchange="onSelectVersion(this)">
              <?php foreach ($products_typed as $type): ?>
                  <option value="<?=$type['id']?>"><?=$type['title']?></option>';
              <? endforeach; ?>
              </select>
          </div>
          <?php foreach ($products_typed as $id=>$type): ?>
              <? if ($id == 0): ?>
                  <div id="block-license-count-<?=$type['id']?>" class="lin7__order__select select_license_count">
              <? else: ?>
                  <div id="block-license-count-<?=$type['id']?>" class="lin7__order__select select_license_count hide">
              <?endif;?>
                  <span>Количество лицензий</span>
                  <select name="version" id="select-license-count-<?=$type['id']?>" onchange="onSelectLicense()">
                  <?php foreach ($type['guardant'] as $product): ?>
                      <option value="<?= $product['countLicenses']?>"><?= $product['countLicenses']?></option>
                  <? endforeach; ?>
                  </select>
              </div>
          <? endforeach; ?>
          <div class="lin7__order__invoice">
              <input id="cart_1" type="radio" name="invoice" checked>
              <label for="cart_1">Оплата картой</label>
              <input id="cart_2" type="radio" name="invoice">
              <label for="cart_2">Выписать счёт</label>
          </div>
          <div class="lin7__order__total"><i>Стоимость:</i> <b><span><?=$products_typed[0]['guardant'][0]['price']?></span></b> руб.</div>
          <button id="btn-buy-order" class="lin7__order__btn" onclick="onCreateOrder()">
              <span>Купить лицензию</span>
          </button>
      </div>

        <!-- <div class="lk__right__crorder">
          <div class="lk__right__crorder__select">
            <span>Версия</span>
            <select name="version" id="select-version" onchange="renderTotalPrice()">
              <?php
              foreach ($data_products as $product){
                echo '<option value="'.$product['id'].'">'.$product['title'].'</option>';
              }
              ?>
            </select>
          </div>
          <div class="lk__right__crorder__count">
            <span>Количество лицензий</span>
            <div class="lk__right__crorder__count__input">
              <input onchange="onInputProductCount(this)" id="input-count" type="number" min="1" step="1"  value="1">
              <span onclick="onPlusMinus(this)" data-type="minus" class="lk__right__crorder__count__input__minus" data-id=${productId}>
                -
              </span>
              <span onclick="onPlusMinus(this)" data-type="plus" class="lk__right__crorder__count__input__plus" data-id=${productId}>
                +
              </span>
            </div>
          </div>
          <div class="lk__right__crorder__total">
            Стоимость <span><?= $data_products[0]['price']?></span> руб. 
          </div>
          <button id="btn-create-order" onclick="onCreateOrder()">
            <span>Оформить новый заказ</span>
          </button>
        </div> -->
        <div class="lk__right__orders">
          <div class="lk__right__orders__title">
            Мои заказы
          </div>
          <div class="lk__right__orders__items">
          <?php
          // var_dump($decoded_orders);
          if ($decoded_orders['isError']){
            echo '
            <div class="orders__item">
              <div class="orders__item__title">Ошибка получения заказов</div>
            </div>';
            return;
          }
          for($i = 0; $i<sizeof($decoded_orders); $i++){
            echo '
            <div class="orders__item">
              <div class="orders__item__title">Заказ № ' .  $decoded_orders[$i]['_id'] . '</div>
              <div class="orders__item__description">Описание заказа: ' . $decoded_orders[$i]['description'] . ' </div>';
              if (!empty($decoded_orders[$i]['serials'])){
                echo '
                <button onclick="onClickCopy(this)" id="' .  $decoded_orders[$i]['_id'] . '" data-copy="' .  $decoded_orders[$i]['serials'][0]["serialstring"] . '">
                  Копировать ключ лицензии
                </button>';
              }else{
                echo '
                <div class="orders__item__payment"> 
                  <span>Заказ не оплачен</span>
                </div>';
              }
              echo '
            </div>
            ';
          }
          ?>
          </div>
        </div>
        
        </div>
      </div>
    </div>
  </div>
</main>

<script> 
  // inputs of main panel
  document.querySelectorAll('.input_lk_js').forEach(function(input) {
    input.addEventListener('change', function() {
        var field = this.getAttribute('data-input');
        var newValue = this.value;
        var userId = <?= $userId ?>;
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/update-user-info.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
            console.log('User info updated successfully');
          }
        };
        xhr.send('userId=' + userId + '&fieldName=' + encodeURIComponent(field) + '&fieldValue=' + encodeURIComponent(newValue));
    });
  });
</script>
<script>
    // // Typed products version
    // function openNav() {
    //     // Открытие корзины
    //     // renderCart();
    //     document.getElementById("myNav").style.width = "100%";
    // }
    // function closeNav() {
    //     // Закрытие корзины
    //     document.getElementById("myNav").style.width = "0%";
    // }
    const hideAllLicenseSelects = () => {
        // Скрывает все select для лицезий
        const selects = document.querySelectorAll('.select_license_count');
        for (select of selects){
            select.classList.add('hide');
        }
        renderTotalPrice()
    }
    const correctLicenseSelects = (selectedTypeId) => {
        hideAllLicenseSelects();
        const licenseSelect = document.getElementById(`block-license-count-${selectedTypeId}`);
        licenseSelect.classList.remove('hide');
    }
    function onSelectVersion(){
        // Настраиваем select для кол-ва лицензий
        const selectedTypeId = document.getElementById("select-version").value;
        console.log
        correctLicenseSelects(selectedTypeId);
    }
    function onSelectLicense(){
        console.log('onSelectLicense')
        renderTotalPrice()
    }
    function renderTotalPrice(){
        const selectedTypeId = document.getElementById("select-version").value;
        const countLicenses = document.getElementById(`select-license-count-${selectedTypeId}`).value;
        const typedProducts = JSON.parse(localStorage.getItem('lin-typed-products'));
        console.log(typedProducts);
        let price = typedProducts[selectedTypeId]['guardant']
                        .filter(prod=>prod.countLicenses == countLicenses)[0]
                        .price;
        if(!price){
            price = '???';
            alert('Ошибка получения стоимости продукта!');
        }
        const priceElement = document.querySelector('.lin7__order__total span');
        priceElement.innerHTML = price;
    }
    // function onclickBuy(el){
    //     // Открывает Nav с параметрами заказа
    //     const typeId = el.dataset.typeid;
    //     const countLicenses = el.dataset.countlicenses;
    //     const versionSelect = document.getElementById("select-version");
    //     versionSelect.value = typeId;
    //     correctLicenseSelects(typeId);
    //     const licenseSelect= document.getElementById(`select-license-count-${typeId}`);
    //     licenseSelect.value = countLicenses;
    //     renderTotalPrice()
    //     openNav();
    // }
    async function onCreateOrder(e){
        try{
            const selectedTypeId = document.getElementById("select-version").value;
            const countLicenses = document.getElementById(`select-license-count-${selectedTypeId}`).value;
            const typedProducts = JSON.parse(localStorage.getItem('lin-typed-products'));
            const pid = typedProducts[selectedTypeId]['guardant']
                            .filter(prod=>prod.countLicenses == countLicenses)[0]
                            .pid
            if (!pid){
                throw new Error('Pid not found');
            }
            let response = await fetch('/api/create-order-pid.php', {
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
                body: JSON.stringify(  // body data type must match "Content-Type" header
                    pid
                ), 
            });
            console.log(response);
            console.log(response.body);
            const json = await response.json();
            console.log(json);
            const result = await JSON.parse(json);
            console.log(result);
            // localStorage.removeItem('lin-cart');
            if (!result?.confirmation_url){
                return alert('Ошибка');
            }
            window.open(result?.confirmation_url || 'http://localhost/cabinet/', '_blank');
            // window.location.href = 'http://localhost/cabinet/';
        }catch(e){
            console.log('Error: ',e);
            alert('Произошла ошибка');
        }finally{
            // window.location.href = 'http://localhost/cabinet/';
        }
    }
</script>


<script>
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Пользователь вернулся на текущую вкладку
        refreshPage();
    }
});
function refreshPage() {
    // Проверка, если страница уже не перезагружается
    if (!window.isRefreshing) {
        window.isRefreshing = true;
        window.location.reload(true); // true = force reload from server
    }
}
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
