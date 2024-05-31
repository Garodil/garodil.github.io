<?php
$body = file_get_contents('php://input'); //Получаем в $body json строку
$arr = json_decode($body, true); //Разбираем json запрос на массив в переменную $arr
$data = $arr['callback_query'];
$botId = getenv('botId');
include_once ('tg.class.php'); //Меж дела подключаем наш tg.class.php
include_once ('db.class.php');
//Сразу и создадим этот класс, который будет написан чуть позже
//Сюда пишем токен, который нам выдал бот
//$tg = new tg('000000000:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
$tg = new tg(getenv('token'));
$db = new db();

$sms = $arr['message']['text']; //Получаем текст сообщения, которое нам пришло.
//О структуре этого массива который прилетел нам от телеграмма можно узнать из официальной документации.
//$sms = strval(getenv('group'));
//Сразу и id получим, которому нужно отправлять всё это назад


$tg_id = $arr['message']['chat']['id'];
$tg_name = $arr['message']['chat']['username'];
$group_id = strval(getenv('group'));

if($tg_id == null | $tg_id == $group_id) exit('ok');
//$threadId = $tg->create($tg_name)['result']['message_thread_id'];
//$db->set($tg_id, $threadId);
//$out = $tg->topic($group_id);

if($db->get($tg_id) == null)
{
  $threadId = $tg->create($tg_name)['result']['message_thread_id'];
  $db->set($tg_id, $threadId);
  
}
else $threadId = $db->get($tg_id);

//if (array_search($tg_name, $tg->topic($tg_name), true)[0] != $tg_name)
//{ 
//  $tg->create($tg_name);
//  exit('ok');
//}

//$threadid = $tg->topic($tg_name);

//if($arr['message']['chat']['id'] == strval(getenv('botId')))
//{
//      $tg->forward($group_id, $tg_id, $sms);
//}

if($arr['message']["reply_to_message"] != null)
{
    $boom = explode(' ', $arr['message']['reply_to_message']['text']);
    if($boom[8] !=null) $tg_id = explode('|', $boom[8])[0]; else $tg_id = $boom[3];
    $sms = $arr['message']['text'];
    if($boom[8] != null) $reply_to = explode('|', $boom[8])[1]; else $reply_to = $boom[5];
    $tg->reply($tg_id, $sms, $reply_to);

    //$tg->reply($group_id, $boom, $reply_to);
  
    exit('ok');
}

if ( isset( $data ) )
{
    $tg->reply($group_id, 'Ответь на это сообщение, чтобы ответить на сообщение ' . $data['data'], $arr['message']['message_id'], $data, $threadId);
  console.log("woah");
  exit('ok');
}
 
//Перевернём строку задом-наперёд используя функцию cir_strrev
$button = json_encode(
  array(
  'inline_keyboard' => array(
    array(
      array(
              'text' => 'Ответить',
              'callback_data' => $arr['message']['chat']['id'] . '|' . $arr['message']['message_id'] . ' от @' . $arr['message']['chat']['username'],
            ),
          )
        ),
      )
  );
$encodedMarkup = json_encode($replyButton);
$sms_rev = 'От: ' . '@' . $tg_name . " \n" . 'ID: ' . $tg_id . " \nMessageID: " . $arr['message']['message_id'];

//Используем наш ещё не написанный класс, для отправки сообщения в ответ
//if($tg_id == $group_id) exit('ok');
$tg->forward($group_id, $tg_id, $arr['message']['message_id'], $threadId);
$tg->send($group_id, $button, $sms_rev, $arr['message']['message_id'], $tg_id, $threadId);

exit('ok'); //Обязательно возвращаем "ok", чтобы телеграмм не подумал, что запрос не дошёл
?>