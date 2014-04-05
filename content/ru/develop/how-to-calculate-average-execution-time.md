<!--
Title: Как посчитать среднее время выполнения программы?
Description: В этой короткой статье я расскажу о возможном способе подсчёта времени выполнения программы.
Tags: php, C, bash, development
Date: 2013/10/26
-->

Протестировать время выполнения новоиспечённой программы/скрипта вполне здравое желание.
Но дьявол в деталях! Всё зависит от того, что именно требуется протестировать :) <!--cut-here-->



## Относительное время 

Для подсчёта относительного времени выполнения, например,
*до* и *после* добавления новой фичи,
можно и bash-скриптом обойтись:

    for i in {1..10}; do
        /usr/bin/time -f %e some_script.sh;
    done 2>&1 | awk '{sum += $1} END {print sum / NR, "sec"}'



### Пояснение

* Цикл `for` запускает `some_script.sh` 10 раз, замеряя время выполнения скрипта с помощью утилиты `/usr/bin/time`.
* Вывод `stderr` цикла перенаправляется в `stdout`, чтобы захватить весь вывод `time`.
* С помощью `awk` суммируем все полученные значения в секундах. Затем делим общую сумму на количество строк(== `NR` в `awk`), и получаем среднее время выполнения скрипта.

P.s. Согласно [официальному мануалу][2], `time` в bash - урезанная версия `/usr/bin/time` :)
Будьте бдительны!



## Абсолютное время

Для подсчёта абсолютного (чистого) времени выполнения программы,
особенно если это не bash-евский скрипт,
требуется что-то пошустрее. Например, можно использовать встроенные в язык средства
(т.е. замерять изнутри). 

Приведу имеющиеся у меня примеры.

### PHP



```php
<?php
// Script start
$time_start = microtime(true);

// some code...

$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
//execution time of the script
echo "<br><b>Total Execution Time:</b> " . $execution_time . ' seconds';
```


### C

```cpp
#include <time.h>
#include <stdio.h>
#include <stdlib.h>

int main()
{
    clock_t begin, end;
    double time_spent;
    begin = clock();

    // some code

    end = clock();
    time_spent = (double)(end - begin) / CLOCKS_PER_SEC;
    printf("%1.2f s\n", time_spent);
}
```

### other

Ну а для других языков - иначе. Есть примеры - присылайте, дополню!

Если же требуется некий *большой* цикл, 
то `perl`, между прочим, раз в 150 быстрее `bash` :) 
Вот, можете сравнить выводы у себя на машине:

    time perl -e 'for($i=0; $i<10000000; $i++) {}'
    # loop crash test: perl vs bash 
    time for i in {1..10000000}; do true; done

---

Пост написан на основе [данного][1] сниппета.

[1]: http://www.bashoneliners.com/oneliners/oneliner/48/
(Calculate the average execution time (of short running scripts) with awk)

[2]: http://man7.org/linux/man-pages/man1/time.1.html (time(1))
