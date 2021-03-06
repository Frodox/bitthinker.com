<!--
Title: Не накладывается patch на rpm пакет - неверный код возврата
Description: Не накладывается patch на rpm пакет -- неверный код возврата из /var/tmp/rpm-tmp
Date: 2013/04/25
Tags: troubles, patch
-->

Если Ваш патч ну никак не хочет накладываться, и вывод примерно следующий (я пытался наложить патч на rpm пакет)<!--cut-here-->:

<pre><code class="shell" title="Недовольный выхлоп rpmbuild -bp SPECS/util.spec">...
Patch #777 (util.patch):
+ /bin/cat /home/user/rpmbuild/SOURCES/util.patch
+ /usr/bin/patch -s -p1 -b --suffix .util-ngpatch --fuzz=0
3 out of 5 hunks FAILED -- saving rejects to file util/util.c.rej
ошибка: Неверный код возврата из /var/tmp/rpm-tmp.UdiqLh (%prep)

Ошибки сборки пакетов:
    Неверный код возврата из /var/tmp/rpm-tmp.UdiqLh (%prep)
</code>
</pre>

То, очевидно, с патчем что-то не так, и накладывается он криво (а точнее, ну совсем не накладывается).  
Причин тому может быть несколько:

* Патч в принципе кривой :)
* Вы пытались модифицировтаь патч в сыром виде,
и теперь он не накладывается из-за неправильного смещения строк и т.п.
* Если Вы перегенирировали патч самостоятельно, то, очевидно, криво :)

При прочих равных, у начинающих разработчиков чаще всего случается как раз последняя ситуация из-за извечной **проблемы с табами и пробелами**.<!--cut-here-->

Т.е., например, в исходном файле используются табы или смешанное форматирование (табы + пробелы),
а ваш редактор (или же Вы сами) заменил все табы на пробелы. Или  же наоборот.  
В итоге получается монструозный патч на весь файл, который и не хочет накладываться.

Для решения проблемы, удостоверьтесь, что форматирование (*indent style*)
исходного файла и изменённого совпадают, и только после этого формируйте и накладывайте патч

*P.s.* Лично у меня ушло довольно продолжительное время, чтобы понять в чём проблема, а затем правильно настроить свой редактор :)

<hr>
На мысль навёл [вот этот][1] топик.

[1]: http://www.ljpoisk.ru/archive/3752689.html (Проблема с patch)
