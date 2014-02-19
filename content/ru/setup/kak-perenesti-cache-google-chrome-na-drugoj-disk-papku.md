<!--
Title: Как перенести cache Google Chrome на другой диск&nbsp;/&nbsp;папку&nbsp;?
Description: Вы - счастливый обладатель SSD и волнуетесь о своём диске? :) Перенести кэш Google Chrome - правильное решение, и в данной статье я покажу как это сделать.
Date: 2012/10/04
Tags: chrome, hacks
-->

Для управления кэшем в хроме официально доступно всего несколько опции,
причём они являются аргументами для запуска из командной строки (самый простой в реализации,
но не самый удобный в использовании способ):

* `--disk-cache-dir=<путь к папке>` : местоположение кэша
* `--disk-cache-size=<размер в байтах>` : размер кэша
* `--user-data-dir=<путь к папке>` : папка всех пользовательских данных (профили, кэш, ...)

Собственно, всё что нужно - запускать Google Chrome с правильными параметрами<!--cut-here-->.
Всё нижесказанное в равной мере применимо и для **Chromium**.



## Windows

В Windiws 7 хром по умолчанию хранит кэш где-то в районе
`C:\Users\username\AppData\Local\Google\Chrome\User Data\Default\Cache`.
(Для других ОС см. [Источники](!#resources))

Для изменения метоположени кэша **на постоянно**, требуется выполнить следующие шаги:



### Шаг 1. Изменяем ярлык

ПКМ по ярлыку ⇒ *"Свойства"* ⇒ вкладка *"Ярлык"*

![Google Chrome' shortcut settings][chrome-shortcut]

Затем:

1. Создаём где-либо папку для будушего кэша.  
Например, по адресу `"D:\temp\Google_Chrome"`
2. В поле *"Объект"*, через пробел, добавляем полный путь к этой папке как параметр:  
`--disk-cache-dir="D:\temp\Google_Chrome"`

Теперь по адресу `"D:\temp\Google_Chrome"` будет храниться **кэш браузера Google&nbsp;Chrome**.
После перезапуска Chrome в ней появятся папочки *Cache, Media Cache* и т. д.

Чтобы ограничить размер кэша, например, в 300Мб, через пробел добавляем ещё один параметр:

`--disk-cache-size=314572800`  
(`300 * 1024 * 1024 = 314572800` байт)

Если хочется избавиться от кэша совсем - установите его размер в `1` :)

**Но**: если хром - браузер по умолчанию, то при запуске из сторонних приложений,
он по-прежнему будет запускаться с параметрами по умолчанию. Поэтому - переходим к шагу два.



### Шаг 2. Правим реестр

1. Открываем редактор реестра (`Win+R` ⇒ пишем `regedit` ⇒  жмём `Enter`)
2. Переходим по ключу `HKEY_CLASSES_ROOT\ChromeHTML\shell\open\command`
3. Там необходимо найти путь к исполняемому файлу Chrome (команду запуска)  
![Устанавливаем размера кэша в реестре][regedit]
4. Добавляем необходимые параметры после *"...\chrome.exe"* (в кавычках)

Итоговая команда для запуска в реестре будет выглядеть как-то вроде
`"C:\Users\Martin\AppData\Local\Google\Chrome\Application\chrome.exe" --disk-cache-dir="D:\temp\Google_Chrome" --disk-cache-size=314572800 -- "%1"` (для Win7).

Всё.


### Шаг 3. Альтернатива 1

Как вариант, можно не редактировать ярлыки/реестры, а просто поместить символическую ссылку на нужное место вместо старой папки кэша.



### Шаг 3. Альтернатива 2

Так же, вместо редактирования параметров запуска, можно использовать *Политики*:

1. Откройте редактор реестр.
2. Перейдите по ключу `HKEY_LOCAL_MACHINE\SOFTWARE\Policies\Chromium` и 
добавьте **Dword**  `DiskCacheSize` (ПКМ по `Chromium` ⇒  *new* ⇒  *Dword (32-bit value)*).  
Установите значение для размера кэша в байтах.
3. Для установки папки кэша, создайте **String** с именем `DiskCacheDir` и установите значение как полный путь к необходимой папке.



## Linux

Всё аналогично ситуации с ярлыками Windows. Стоит поправить алиас для запуска хрома,
чтобы он вызывался с необходимыми параметрами.

Интересный usecase - хранение кэша в оперативной памяти (tmpfs):  
`$ google-chrome --disk-cache-dir=/tmp/cache`

Гляньте страничку [arch linux](https://wiki.archlinux.org/index.php/Chromium_Tips_and_Tweaks)
про Chromium, чтобы узнать больше интересных вещей :)



## Mac OS X

Гляньте официальную страничку [Chromium](http://www.chromium.org/user-experience/user-data-directory) о User Data.

* * *

### Источники: {#resources}

* [Директории по умолчанию в различных ОС](http://www.chromium.org/user-experience/user-data-directory)
* [How To Change Google Chrome’s Cache Location And Size](http://www.ghacks.net/2010/10/19/how-to-change-google-chromes-cache-location-and-size/)
* [Как перенести cache Google Chrome на другой раздел,папку? Имея систему на SSD](http://productforums.google.com/forum/#!msg/chrome-ru/WxXLK3pjXn8/ZvxHYxUK2s4J)
* [Chromium Tips and Tweaks](https://wiki.archlinux.org/index.php/Chromium_Tips_and_Tweaks)






*[ПКМ]: Правой кнопокй мыши

[chrome-shortcut]: /blog/content/ru/setup/imgs/chrome_shortcat_props.gif
"Свойства ярлыка Google Chrome"

[regedit]: /blog/content/ru/setup/imgs/regedit-500x242.png
"Устанавливаем размера кэша в для Google Chrome в реестре"
