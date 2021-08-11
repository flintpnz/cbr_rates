<?php
/*
 * Автоматическое обновление курсов путем CURL запроса /currency/update в 00:00
 * Необходимо заменить $url
 * Далее добавить задание в крон:
 *
 * docker exec -it cbr_rates_nginx crontab -e
 * 0 0 * * * php /application/public/update.php
 */

$url = 'http://localhost';
$date = date('Y-m-d');

try
{
    $ch = curl_init($url."/currency/update?from=".$date."&to=".$date);
    curl_exec($ch);
    if (curl_error($ch))
        echo curl_error($ch);
    else
        echo "Курсы успешно обновлены";
    curl_close($ch);
}
catch (Throwable $t)
{
    echo $t;
}
