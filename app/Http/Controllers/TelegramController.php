<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Telegram;
use App\User;

class TelegramController extends Controller
{
    public function webhook()
    {
        $updates = Telegram::getWebhookUpdates();
        $message = $updates->getMessage();
        if (!$message) return 'ok';
        $from = $message->getFrom();
        if (!$from) return 'ok';
        $text = $message->getText();
        if (!$text) return 'ok';
        $chat = $message->getChat();
        $chatId = $chat->getId();
        $fromId = $from->getId();
        $messageId = $message->getMessageId();
        $response = [
            'method' => 'sendMessage',
            'chat_id' => $chatId
        ];
        $user = User::where('telegram_id', $fromId)->first();
        if ($user) {
            $response = $this->hasTelegramId($user, $text, $response, $chatId, $messageId);
        } else {
            $response = $this->doesntHaveTelegramId($text, $response, $fromId);
        }
        return response()->json($response);
    }

    public function doesntHaveTelegramId($text, $response, $fromId)
    {
        if ($text == '/start') {
            $response['text'] = 'Введите логин(имя) чекера или админа';
        } else {
            $checker = User::where(['name' => $text])->whereIn('role', ['admin', 'checker'])->first();
            if (!$checker) {
                $response['text'] = 'Чекер или админ с таким именем не найден, введите логин еще раз';
            } elseif ($checker->telegram_id && $checker->telegram_auth) {
                $role = ($checker->role == 'admin') ? 'админ' : 'чекер';
                $response['text'] = "Такой $role уже залогинен, введите другой логин";
            } else {
                $checker->update(['telegram_id' => $fromId]);
                $response['text'] = 'Введите пароль';
                $response['reply_markup'] = ['keyboard' => array(array(array("text" => "Другой чекер/админ"))), "resize_keyboard" => true];
            }
        }
        return $response;
    }

    public function hasTelegramId($user, $text, $response, $chatId, $messageId)
    {
        if (!$user->telegram_auth) {
            if ($text == 'Другой чекер/админ') {
                $user->update(['telegram_id' => NULL]);
                $response['text'] = 'Введите логин(имя) чекера или админа';
            } else {
                $check = Hash::check($text, $user->password);
                if (!$check) {
                    $response['text'] = 'Пароль не верный, попробуйте еще раз';
                } else {
                    $user->update(['telegram_auth' => TRUE]);
                    $partText = ($user->role == 'admin') ? 'ваших чекеров' : 'вашего чекера';
                    $response['text'] = "Вы авторизованы и теперь будете получать уведомления об ошибках $partText. \nСпасибо, что вы с нами )";
                    $reply_markup = Telegram::replyKeyboardMarkup([
                        'keyboard' => array(array(array("text" => "Выйти"))),
                        "resize_keyboard" => true
                    ]);
                    $response['reply_markup'] = $reply_markup;
                    Telegram::sendMessage($response);
                    $response = [
                        'method' => 'deleteMessage',
                        'chat_id' => $chatId,
                        'message_id' => $messageId
                    ];
                }
            }
        } else {
            if ($text == 'Выйти') {
                $user->update([
                    'telegram_id' => NULL,
                    'telegram_auth' => FALSE
                ]);
                $response['text'] = 'Вы успешно вышли';
                $response['reply_markup'] = ['keyboard' => array(array(array("text" => "/start"))), "resize_keyboard" => true];
            } else {
                $response['text'] = 'Чота не пойму чо надо';
            }
        }
        return $response;
    }
}
