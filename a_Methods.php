<?php

declare(strict_types=1);

function curl_multi_exec_All(array $a1_URLs, int $i_TimeOut = 10): array
{
    /**
     * curl_multi_exec многочисленных запросов
     */

    $multiHandle = curl_multi_init();
    $handles     = [];
    $responses   = [];

    // Инициализация отдельных cURL-дескрипторов для каждого URL
    foreach ($a1_URLs as $url) {
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_TIMEOUT, $i_TimeOut);
        curl_multi_add_handle($multiHandle, $handle);
        $handles[] = $handle;
    }

    // Выполнение всех запросов параллельно
    $running = null;
    do {
        curl_multi_exec($multiHandle, $running);
        curl_multi_select($multiHandle);
    } while ($running > 0);

    // Получение ответов и освобождение ресурсов
    foreach ($handles as $handle) {
        $responses[] = curl_multi_getcontent($handle);
        curl_multi_remove_handle($multiHandle, $handle);
        curl_close($handle);
    }

    curl_multi_close($multiHandle);

    return $responses;
}

function curl_multi_exec_All_Test(): void
{

    $timeout = 5;

    // Пример использования
    $urls = [
        'https://example.com',
        'https://example.org',
        'https://example.net',
        'https://example.edu',
        'https://example.gov'
    ];

    // Замер времени начала выполнения
    $startTime = microtime(true);

    // Вызов функции для выполнения запросов
    $responses = curl_multi_exec_All($urls, $timeout);

    // Замер времени окончания выполнения
    $endTime = microtime(true);

    // Вычисление времени выполнения
    $executionTime = $endTime - $startTime;

    // Вывод результатов
    echo "Время выполнения: " . number_format($executionTime, 4) . " секунд\n";
    echo "Количество URL: " . count($urls) . "\n";
    echo "Количество ответов: " . count($responses) . "\n";

    // Проверка ответов
    foreach ($responses as $index => $response) {
        // echo "Ответ от URL {$urls[$index]}: ";
        if ($response) {
            // echo "Успешно (длина: " . strlen($response) . " байт)\n";
        } else {
            echo "Ошибка: " . $urls[$index] . "\n";
        }
    }
}

function a1_Strings_Trim(array $a1_S, int $length = 70): array
{
    /**
     * обрезать строки массива 1-мерного
     */

    return array_map(fn($string) => mb_substr(
        $string,
        0,
        $length,
        'UTF-8'
    ), $a1_S);
}

function curl_multi_exec_All_Good_Bad(array $a1_URLs, array $a1_Responses): array
{
    /**
     * дихотомирует ссылки в массивы "хорошие" и "плохие"
     */

    $goodUrls = [];
    $badUrls  = [];

    // Проходим по всем ссылкам и ответам
    foreach ($a1_URLs as $index => $url) {
        $response = $a1_Responses[$index] ?? null;

        // Проверяем, является ли ответ "хорошим"
        if ($response !== false && !empty($response)) {
            $goodUrls[] = $url;
        } else {
            $badUrls[] = $url;
        }
    }

    return [
        'good' => $goodUrls,
        'bad'  => $badUrls
    ];
}

function curl_multi_exec_All_Good_Bad_Test(): void
{

    $a1_Good_Wanted =  [
        'https://example.com',
        'https://example.org',
        'https://example.net',
        'https://example.edu',
    ];

    $a1_Bad_Wanted = ['https://example.gov'];

    $a1_URLs = array_merge($a1_Bad_Wanted, $a1_Good_Wanted);

    $a1_Responses = curl_multi_exec_All($a1_URLs);

    $a1 = curl_multi_exec_All_Good_Bad($a1_URLs, $a1_Responses);

    $a1_Bad  = $a1['bad'];
    $a1_Good = $a1['good'];

    assert($a1_Bad === $a1_Bad_Wanted);
    assert($a1_Good === $a1_Good_Wanted);

    echo __FUNCTION__ . " пройден\n";
}
