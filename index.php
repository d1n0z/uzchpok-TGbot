<?php

require_once('cfg.php');

/**
 * @function mysqli $conn_users
 * @var mysqli $conn_first
 * @var mysqli $conn_second
 */
require_once('utils.php');

//$data = json_decode(file_get_contents('php://input'), TRUE);
$data = json_decode(file_get_contents('test.json'), TRUE);
if (!isset($data)) return;
file_put_contents('data.txt', var_export($data, true));

$data = $data['message'];
$msg = mb_strtolower($data['text'], 'utf-8');
$uid = $data['from']['id'];

switch ($msg) {
    case 'назад':
    case '/start':
        $mthd = 'sendMessage';
        $btns = [];
        foreach (START_BTNS as $item) $btns[] = $item;
        $send_data = [
            'text' => START_MSG,
            'reply_markup' => btns($btns, START_ROWS)
        ];
        break;

    case mb_strtolower(SUPPORT_BTN):
        $mthd = 'sendMessage';
        $btns = [];
        foreach (SUPPORT_BTN_TEXT as $item) $btns[] = $item;
        $urls = [];
        foreach (SUPPORT_URL as $item) $urls[] = $item;
        $send_data = [
            'text' => SUPPORT_MSG,
            'reply_markup' => btns($btns, 0, TRUE, $urls)
        ];
        break;

    case mb_strtolower(TELEGRAM_CHANNEL_BTN):
        $mthd = 'sendPhoto';
        $btns = [TELEGRAM_CHANNEL_BTN_TEXT];
        $urls = [TELEGRAM_CHANNEL_URL];
        $img = 'https://uzdosug.ru/photos/telegram_channel.jpg';
        $send_data = [
            'photo' => $img,
            'caption' => TELEGRAM_CHANNEL_MSG,
            'reply_markup' => btns($btns, 0, TRUE, $urls)
        ];
        break;

    case mb_strtolower(TELEGRAM_CHAT_BTN):
        $mthd = 'sendPhoto';
        $btns = [TELEGRAM_CHAT_BTN_TEXT];
        $urls = [TELEGRAM_CHAT_URL];
        $img = 'https://uzdosug.ru/photos/telegram_chat.jpg';
        $send_data = [
            'photo' => $img,
            'caption' => TELEGRAM_CHAT_MSG,
            'reply_markup' => btns($btns, 0, TRUE, $urls)
        ];
        break;

    case mb_strtolower(SERVICES_BTN_TEXT):
        $mthd = 'sendMessage';
        $btns = [];
        foreach (SERVICES_BTNS as $item) $btns[] = $item;
        $send_data = [
            'text' => SERVICES_MSG,
            'reply_markup' => btns($btns, SERVICES_ROWS)
        ];
        break;

    case 'проститутки':
    case 'массажистки':
    case 'транссексуалки':
    case 'парни для дамы':
    case 'бюджетный':
    case 'сауны и гостиницы':
        /**
         * @var mysqli $conn_users
         * @var mysqli $conn_first
         * @var mysqli $conn_second
         */
        require_once('db.php');
        $cat_id = SERVICES_IDS[array_search($msg, SERVICES)];
        $query = "SELECT * FROM users WHERE uid=$uid";
        $res = $conn_users->query($query)->fetch_row();
        if (!isset($res)) {
            $query = "INSERT INTO users(uid, page, cat) VALUES ($uid,0,$cat_id)";
            $conn_users->query($query);
            $query = "SELECT * FROM users WHERE uid = $uid";
            $res = $conn_users->query($query)->fetch_all()[0];
        }
        $query = "UPDATE users SET page=0, cat=$cat_id WHERE uid=$uid";
        $conn_users->query($query);
        $query = "SELECT * FROM hevfd_adsmanager_adcat WHERE catid!='' AND catid='$cat_id'";
        $res = $conn_first->query($query)->fetch_all();
        if (count($res) > 0) {
            $id = $res[0][0];
            $query = 'SELECT * FROM hevfd_adsmanager_ads WHERE id=' . $id;
            $ads = $conn_first->query($query)->fetch_all();
            $ad = getAd($ads[0], $cat_id);

            try {
                $query = 'SELECT * FROM hevfd_adsmanager_ads WHERE id=' . $id;
                $ads1 = $conn_first->query($query)->fetch_row();

                $img = 'https://uzchpok.com/images/com_adsmanager/contents/' .
                    json_decode($ads1[4], true)[0]['thumbnail'];
            } catch (Exception $e) {
                $img = null;
            }

            $mthd = 'sendPhoto';
            $btns = [STOP_WATCHING_ADS];
            if (count($res) > 1)
                array_unshift($btns, '>>>');
            $send_data = [
                'photo' => $img,
                'caption' => $ad,
                'reply_markup' => btns($btns, 1)
            ];
        } else {
            $mthd = 'sendMessage';
            $btns = [STOP_WATCHING_ADS];
            $send_data = [
                'text' => 'Анкет нет',
                'reply_markup' => btns($btns)
            ];
        }
        break;
    case '>>>':
        /**
         * @var mysqli $conn_users
         * @var mysqli $conn_first
         * @var mysqli $conn_second
         */
        require_once('db.php');
        $query = "SELECT * FROM users WHERE uid='$uid'";
        $user = $conn_users->query($query)->fetch_all()[0];
        $page = $user[1] + 1;
        $cat_id = $user[2];

        $query = 'UPDATE users SET page=' . ($page) . " WHERE uid = $uid";
        $conn_users->query($query);

        $cat = SERVICES[array_search($cat_id, SERVICES_IDS)];
        $cat = mb_strtoupper(mb_substr($cat, 0, 1, 'utf8'), 'utf8') .
            mb_substr($cat, 1, encoding: 'utf8');
        $query = "SELECT * FROM hevfd_adsmanager_adcat WHERE catid!='' AND catid='$cat_id'";
        $res = $conn_first->query($query)->fetch_all();
        $id = $res[$page][0];

        $ids = [];
        foreach ($res as $item) {
            $ids[] = $item[0];
        }

        $query = 'SELECT * FROM hevfd_adsmanager_ads WHERE id IN (' .
            implode(',', array_map('intval', $ids)) . ')';
        $ads1 = $conn_first->query($query)->fetch_all();

        $query = 'SELECT * FROM profiles WHERE category="' . $cat . '"';
        $ads2 = $conn_second->query($query)->fetch_all();

        $ads = array_merge($ads1, $ads2);

        $ad = getAd($ads[$page], $cat_id);

        try {
            if (count(end($ads)) > 100) {
                $query = 'SELECT * FROM hevfd_adsmanager_ads WHERE id=' . $id;
                $ads1 = $conn_first->query($query)->fetch_row();

                $img = 'https://uzchpok.com/images/com_adsmanager/contents/' .
                    json_decode($ads1[4], true)[0]['thumbnail'];
            } else {
                $query = 'SELECT * FROM photo WHERE aid=' . $id;
                $ads1 = $conn_second->query($query)->fetch_row();

                $img = 'https://uzpopka.club/photos/' . $ads1[1];
            }
        } catch (Exception $e) {
            var_dump($e);
            $img = null;
        }

        $mthd = 'sendPhoto';
        $btns = [STOP_WATCHING_ADS];
        if ($page + 1 < count($ads))
            array_unshift($btns, '>>>');
        if ($page > 0)
            array_unshift($btns, '<<<');
        $send_data = [
            'photo' => $img,
            'caption' => $ad,
            'reply_markup' => btns($btns, 2)
        ];
        break;

    case '<<<':
        /**
         * @var mysqli $conn_users
         * @var mysqli $conn_first
         * @var mysqli $conn_second
         */
        require_once('db.php');
        $query = "SELECT * FROM users WHERE uid='$uid'";
        var_dump($query);
        $user = $conn_users->query($query)->fetch_all()[0];
        $page = $user[1] - 1;
        $cat_id = $user[2];

        $query = 'UPDATE users SET page=' . ($page) . " WHERE uid = $uid";
        $conn_users->query($query);

        $cat = SERVICES[array_search($cat_id, SERVICES_IDS)];
        $cat = mb_strtoupper(mb_substr($cat, 0, 1, 'utf8'), 'utf8') .
            mb_substr($cat, 1, encoding: 'utf8');
        $query = "SELECT * FROM hevfd_adsmanager_adcat WHERE catid!='' AND catid='$cat_id'";
        $res = $conn_first->query($query)->fetch_all();
        var_dump($user);
        $id = $res[$page][0];

        $ids = [];
        foreach ($res as $item) {
            $ids[] = $item[0];
        }

        $query = 'SELECT * FROM hevfd_adsmanager_ads WHERE id IN (' .
            implode(',', array_map('intval', $ids)) . ')';
        $ads1 = $conn_first->query($query)->fetch_all();

        $query = 'SELECT * FROM profiles WHERE category="' . $cat . '"';
        $ads2 = $conn_second->query($query)->fetch_all();

        $ads = array_merge($ads1, $ads2);

        $ad = getAd($ads[$page], $cat_id);

        try {
            if (count(end($ads)) > 100) {
                $query = 'SELECT * FROM hevfd_adsmanager_ads WHERE id=' . $id;
                $ads1 = $conn_first->query($query)->fetch_row();

                $img = 'https://uzchpok.com/images/com_adsmanager/contents/' .
                    json_decode($ads1[4], true)[0]['thumbnail'];
            } else {
                $query = 'SELECT * FROM photo WHERE aid=' . $id;
                $ads1 = $conn_second->query($query)->fetch_row();

                $img = 'https://uzpopka.club/photos/' . $ads1[1];
            }
        } catch (Exception $e) {
            var_dump($e);
            $img = null;
        }

        $mthd = 'sendPhoto';
        $btns = [STOP_WATCHING_ADS];
        if ($page + 1 < count($ads))
            array_unshift($btns, '>>>');
        if ($page > 0)
            array_unshift($btns, '<<<');
        $send_data = [
            'photo' => $img,
            'caption' => $ad,
            'reply_markup' => btns($btns, 2)
        ];
        break;

    case mb_strtolower(STOP_WATCHING_ADS):
        /**
         * @var mysqli $conn_users
         * @var mysqli $conn_first
         * @var mysqli $conn_second
         */
        require_once('db.php');
        $query = "UPDATE users SET page=0, cat=0 WHERE uid = $uid";
        $conn_users->query($query);

        $mthd = 'sendMessage';
        $btns = [];
        foreach (START_BTNS as $item) $btns[] = $item;
        $send_data = [
            'text' => START_MSG,
            'reply_markup' => btns($btns, START_ROWS)
        ];
        break;
}

if (isset($mthd)) {
    if (!isset($send_data['chat_id'])) $send_data['chat_id'] = $data['chat']['id'];
    sendToTelegram($mthd, $send_data);
}
