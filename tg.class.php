<?php
//Задаём класс
class TG {

    public $token = ''; //Создаём публичную переменную для токена, который нужно отправлять каждый раз при использовании апи тг

    public function __construct($token) {
        $this->token = $token; //Забиваем в переменную токен при конструкте класса
    }
    
    public function create($sender_name )
    {
        $data = array(
            'chat_id' => strval(getenv('group')),
            'name' => $sender_name,
        );

            $out = $this->request('createForumTopic', $data);
            return $out;
    }

    public function send($id, $button, $message, $message_id, $sender, $thread) {   //Задаём публичную функцию send для отправки сообщений
        //Заполняем массив $data инфой, которую мы через api отправим до телеграмма
    $data = array(
      'chat_id'      => $id,
        'information_text' => $message_id . '|' . $sender,
      'text' 	   => $message,
        'reply_markup' => $button,
        'message_thread_id' => $thread,
    );
        //Получаем ответ через функцию отправки до апи, которую создадим ниже
        $out = $this->request('sendMessage', $data);
        //И пусть функция вернёт ответ. Правда в данном примере мы это никак не будем использовать, пусть будет задаток на будущее
        return $out;
    }   

    public function forward($id, $sender, $message_id, $thread)
    {
        $data = array(
            'chat_id'      => $id,
            'from_chat_id' => $sender,
            'message_id'   => $message_id,
            'information_text' => $message_id . '|' . $sender,
            'message_thread_id' => $thread,
        );

        $out = $this->request('forwardMessage', $data);
        return $out;
    }

    public function reply($id, $message, $messageid, $callback = null, $thread)
    {
        $data = array(
            'chat_id'      => $id,
            'text' 	   => $message,
            'reply_to_message_id' => $messageid,
            'information_text' => $callback,
            'message_thread_id' => $thread,
        );

        $out = $this->request('sendMessage', $data);
        return $out;
    }

    public  function request($method, $data = array()) {
        $curl = curl_init(); //мутим курл-мурл в переменную. Для отправки предпочтительнее использовать курл, но можно и через file_get_contents если сервер не поддерживает

    curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token .  '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); //Отправляем через POST
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //Сами данные отправляемые

        $out = json_decode(curl_exec($curl), true); //Получаем результат выполнения, который сразу расшифровываем из JSON'a в массив для удобства

        curl_close($curl); //Закрываем курл

        return $out; //Отправляем ответ в виде массива
    }
}

