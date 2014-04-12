<!--
Title: PHP: Как получить короткую ссылку bit.ly
Description: Вы знаете как получить короткую ссылку с помощью сервиса bit.ly из PHP? Как нет?! Тогда быстренько идите сюда!
Date: 2013/10/27
Tags: php, development
-->

**А Зачем что-то придумывать?!**  
Bit.ly - неплохой "сокращатель ссылок". В сети есть руководства ([раз][1], [два][2]),
по работе с ним из PHP, но так как Bit.ly изменил свой API,
вскоре те методы перестанут работать :)<!--cut-here-->.
Они основаны на отправке запроса с авторизацие по *apiKey*,
а как сказано в [офф. документации][3]:

> ApiKey authentication  
> <span style="color:red;">DEPRECATED</span> - 
> ApiKey authentication is deprecated in favor of OAuth requests.

Т.е. нам предлагают использовать более надёжный способ аутификации - *OAuth2*.
Надёжный то надёжный.. но для этого необходимо:

1. создать у них на сайте приложение
2. затем из php получить *access_token* взамен на какие-то свои данные
3. отправить запрос с используя полученный *access_token*
4. получить в ответ долгожданную короткую ссылку

В общем, всё как у твиттера, и шаги 2-3 обязательны к выполнению каждый раз.

Можно проще?! Можно! 
Внимательно перечитываем документацию и находим:

> If all you're looking to do is shorten links on behalf of a single user or site,
> you can call the bitly API's [/v3/shorten][v3-shorten] method using your generic oauth
> token which you can you can generate [here][4] by confirming your account password
> at the bottom of the page.

То есть: если требуется "сокращатель ссылок" для простенького сайта или 1 пользователя
(имеется ввиду пользователя bit.ly), то у них уже есть специальное приложение,
через которое мы и будем творить добро (если это Ваш случай).



### Кодим

В сухом остатке, для того чтобы прикрутить bit.ly к PHP, требуется:

* Завести аккаунт на bitly.com
* Получить [здесь][4] access_token для дальнейших запросов
* [RTFM][v3-shorten]
* Написать PHP код для отправки и обработки GET запросов

После выполнения всех пунктов, у меня вышел следующий код:

    <?php
    /*
     * Create short url with help of bit.ly
     **/
    // new secure api-url
    define("bitly_url",    'https://api-ssl.bitly.com/v3/');
    define("access_token", 'xxxxxx_your_acess_token_xxxxx');
    define("format", 'txt'); // just for short-url, it's enough
    // if you want to increase request's speed
    define("CACHE_DIR", '/var/www/html/.cache'); 


    /* Get short URL
     * @param : $url : the full URL adress
     * @return: the shortened with bit.ly URL
    ***/
    function get_bitly_short_url($url)
    {
        $connectURL = bitly_url
                    . 'shorten?access_token='
                    . access_token
                    . '&longUrl='
                    . urlencode($url)
                    . '&format='
                    . format;
        return curl_get_result($connectURL);
    }

    /* Get full URL from short one
     * @param : $url : shortened with bit.ly URL
     * @return: expanded URL
    ***/
    function get_bitly_long_url($url)
    {
        $connectURL = bitly_url
                    . 'expand?access_token='
                    . access_token
                    . '&shortUrl='
                    . urlencode($url)
                    . '&format='
                    . format;
        return curl_get_result($connectURL);
    }

    /* Returns a result of request to @$url
     * or $url, if result if empty.
     * Save result to cache to increase speed.
    ***/
    function curl_get_result($url)
    {
        $fn = CACHE_DIR . '/' . md5($url) . '.txt';

        if ( file_exists($fn) ) {
            return file_get_contents($fn);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // time to wait
        $html = curl_exec($ch);
        curl_close($ch);

        @mkdir(CACHE_DIR, 0770, true);
        @unlink($fn);

        if ( !empty($html) ) {
            file_put_contents($fn, "$html");
            return $html;
        }
        else
            return $url;
    }

    /* -------------------- T E S T ------------------------------ */

    // get the short url
    $short_url = get_bitly_short_url('http://google.com');
    echo "Short url: $short_url <br>\n";

    // get the long url from the short one /
    $long_url = get_bitly_long_url($short_url);
    echo "Long url: $long_url";
    ?>

В *исходном варианте*, пример выполнялся ~1,7 сек.  
Если же необходимо, например, генерировать короткую ссылку каждый раз для каждой статьи
при загрузке главной страницы, на которой 100 статей, то к времени загрузки + ~2мин.
Многова-то, не правда ли?

С одной стороны, не нужно так делать - генерировтаь короткую ссылку для каждой статьи
на главной странице :)  
С другой, даже если только для одной статьи при показе, то это + ~1cек,
что тоже может быть критично.

Я попробовал кешировать результаты запросов (как раз код приведённый выше),
и среднее время выполнения примера выше ~ 2.43E-5 сек. На этом я и остановился :)
Так же, Вы можете использовать средства кеширования предоставляемые вашей CMS.

**Note**: Для полноценного использования кеширования из примера выше,
рекомендуется:

* создать и указать правильную папку, где будет храниться кеш;
* проверить, что ваш веб-сервер имеет права для записи в неё.



[1]: http://davidwalsh.name/bitly-api-php (Create Bit.ly Short URLs Using PHP: API Version 3)
[2]: http://www.lornajane.net/posts/2011/shortening-urls-from-php-with-bit-ly (Shortening URLs from PHP with Bit.ly)
[3]: http://dev.bitly.com/authentication.html (Bit.ly API: Authentication)
[v3-shorten]: http://dev.bitly.com/links.html#v3_shorten (Bit.ly API: Links)
[4]: https://bitly.com/a/oauth_apps (Registered OAuth applications)
