<?php
require_once('cfg.php');

/**
 * @param array $texts
 * @param int $rows
 * @param bool $inline
 * @param array $urls
 * @return bool|string
 */
function btns(array $texts, int $rows = 0, bool $inline = FALSE, array $urls = []): bool|string
{
    try {
        if ($inline === TRUE) $inline = 'inline_';
        else $inline = '';
        $btns = [[]];
        $c = 0;
        $row = 0;
        foreach ($texts as $item) {
            if ($c == $rows) {
                $btns[] = [];
                $c = 0;
                $row++;
            }
            $key = [
                'text' => $item,
                'callback_data' => 'ok'
            ];
            if (count($urls) > 0) $key['url'] = $urls[array_search($item, $texts)];
            $btns[$row][] = $key;
            $c++;
        }
        return json_encode([
            $inline . 'keyboard' => $btns
        ]);
    } catch (Exception $e) {
        var_dump($e->getTrace());
        return false;
    }
}


/**
 * @param string $method
 * @param array $data
 * @return mixed
 */
function sendToTelegram(string $method, array $data): mixed
{
    try {
        $query = 'https://api.telegram.org/bot' . TOKEN . '/' . $method . '?' . http_build_query($data);
        echo 'Query: ' . $query . PHP_EOL;
        $res = file_get_contents($query);
        return (json_decode($res, 1) ? json_decode($res, 1) : $res);
    } catch (Exception $e) {
        var_dump($e->getTrace());
        return false;
    }
}


/**
 * @param array $ads
 * @param $cat_id
 * @return string
 */
function getAd(array $ads, $cat_id): string
{
    if (count($ads) < 100) {
        $ad = "📞 +998 $ads[5]\n" .
            "🔺 Возраст: $ads[20]\n" .
            "🔺 Рост: $ads[21]\n" .
            "🔺 Вес: $ads[22]\n" .
            "🔺 Грудь: $ads[23]\n" .
            "🔺 Ходка: $ads[35]\n" .
            "🔺 1 час: $ads[9]\n" .
            "🔺 Ночь: $ads[26]\n";
        if (str_contains($ads[24], '0')) $ad = $ad . "✔ У себя\n";
        else $ad = $ad . "❌ У себя\n";
        if (str_contains($ads[24], '1')) $ad = $ad . "✔ Выезд\n";
        else $ad = $ad . "❌ Выезд\n";
        return $ad . 'https://uzchpok.com/a/' . $cat_id . '-' .
            SERVICES_ENG[array_search(SERVICES[array_search($cat_id, SERVICES_IDS)], SERVICES)] .
            '/' . $ads[0];
    } else {
        $ad = "📞 $ads[8]\n" .
            "🔺 Возраст: $ads[13]\n" .
            "🔺 Рост: $ads[14]\n" .
            "🔺 Вес: $ads[15]\n" .
            "🔺 Грудь: $ads[16]\n";
        if ($ads[30] > 0) {
            $ad = $ad . "🔺 Ходка: $ads[100]\n" .
                "🔺 1 час: $ads[30]\n" .
                "🔺 Ночь: $ads[32]\n" .
                "✔ Выезд\n";
            if ($ads[33] > 0)
                $ad = $ad . "✔ У себя\n";
            else
                $ad = $ad . "❌ У себя\n";
        } elseif ($ads[33] > 0) {
            $ad = $ad . "🔺 1 час: $ads[33]\n" .
                "🔺 2 часа: $ads[34]\n" .
                "🔺 Ночь: $ads[35]\n" .
                "✔ У себя\n";
            if ($ads[30] > 0)
                $ad = $ad . "✔ Выезд\n";
            else
                $ad = $ad . "❌ Выезд\n";
        }
        return $ad . 'https://uzpopka.club/link.php?link=' . $ads[0];
    }
}
