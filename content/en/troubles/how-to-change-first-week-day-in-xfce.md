<!--
Title: How to fix first day of week in xfce?
Description: In this article I will show you how to easily fix 'first day of week' and other locale issues just from console.
Date: 2014/02/24
Tags: xfce, calendar, locale, troubles
-->

If you want to change the first day of week, actually, it's a problem 
of your locale. So, you have installed/choose a locale, that have first day of week,
for example, Sunday, and all programs and gui-plugins will use it. Let's fix it<!--cut-here-->!


## Detect current locale {#detect-locale}

First of all, you can know current locale by running

```bash
$ locale
LANG=en_US.UTF-8
LC_CTYPE="en_US.UTF-8"
LC_NUMERIC=POSIX
LC_TIME="en_US.UTF-8"
...
```
In my case, locale is `en_US.UTF-8`, and it's first day of a week -- Sunday :(

Actually, `date` and other common utils should format it's output depend on
`LC_TIME` environment variable.



## Method #1

So, first method is pretty simple -- try to run commands with other 
`LC_TIME`-variable, like

```bash
$ LC_TIME="en_US.UTF-8" cal
    February 2014   
Su Mo Tu We Th Fr Sa
                   1
 2  3  4  5  6  7  8
 9 10 11 12 13 14 15
16 17 18 19 20 21 22
23 "24" 25 26 27 28

$ LC_TIME="en_GB.UTF-8" cal
    February 2014   
Mo Tu We Th Fr Sa Su
                1  2
 3  4  5  6  7  8  9
10 11 12 13 14 15 16
17 18 19 20 21 22 23
"24" 25 26 27 28
```
If it works fine for you, just add smth like `LC_TIME="en_GB.UTF-8"` in your
`~/.bashrc` file.

> **NOTE:** you can list all available in your system locales with `locale -a`.
> If some locale isn't installed yet, `LC_TIME` will have no effect.



## Method #2

If it doesn't work, let's try another way. You can just regenerate all locales 
with your own settings. So, after [detecting](#detect-locale) your locale, 
change or add the following lines in the `LC_TIME` section in 
`/usr/share/i18n/locales/<your_locale>`:

```bash
week    7;19971130;7
first_weekday   2
first_workday   2
```

Use `first_weekday 2` -- for Monday, and `first_weekday 1` -- for Sunday. Then just run 

```bash
# locale-gen
```
and reboot your system thus new changes to take effect. Now in all aplications
week should starts from the *correct* day.



---
References:

* [Locale - ArchWiki](https://wiki.archlinux.org/index.php/Locale#Setting_the_first_day_of_the_week)
