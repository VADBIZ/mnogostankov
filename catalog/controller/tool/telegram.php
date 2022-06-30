<?php
class ControllerToolTelegram extends Controller {

    const TELEGRAM_TOKEN = '5478123142:AAH_dNJ9ktu5FzQoUUPmqCziHuBO-a0HGcc';
    const TELEGRAM_CHATID = '-760137528';

	public function index($error) {
		$ch = curl_init('https://api.telegram.org/bot'.self::TELEGRAM_TOKEN.'/sendMessage?chat_id='.self::TELEGRAM_CHATID.'&text='.$error); // URL

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Не возвращать ответ
		curl_exec($ch); // Делаем запрос
		curl_close($ch); // Завершаем сеанс cURL
	}
}
