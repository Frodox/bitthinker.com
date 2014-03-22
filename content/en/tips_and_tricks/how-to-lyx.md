<!--
Title: LyX tips and tricks
Description: Most common problems and hacks I have ever faced
Date: 2014/02/25
Tags: LyX, tips and tricks
-->

In this article I will describe interesting hack and possibilities which I use 
from time to time in LyX, like default setting for all code listings,
shortcut for `--Separator--` layout and so on<!--cut-here-->.


### How to set custom settings for all program listings ?

If you exhausted to setup every code-listing you have, 
you can set default parameters for all existed and future ones:

* Go to *Document > Document Settings > Listings*
* Insert settings what you need :]

I have used only `basicstyle={\ttfamily}` -- use *Typewriter* font all the time,
but you can easily find other necessary settings in the 
[listings package doc][listing-package-man] and in the web.



### How to create shortcut for *Separator* layout ?

Actually, shortcuts in LyX is not very clear yet. It can take a long time
to find correct command to set/change a shortcut for it :(
If you type a lot, you may noticed there are shortcuts almost for all 
standard layouts, like *Part, Section, Description* and so on... 
but there is no one for `--Separator--` layout. 
You can fix it easily in the next manner:

1. Go to *Tools -> Preferences -> Editing -> Shortcats*
2. Press *New* button
3. In *Function* field input `command-sequence layout --Separator--;`
4. Press on empty button next to *Delete Key*, and setup some shortcut  
(I use `[Alt+P -]`; i.e. Press `Alt+P`, release, and press minus `-`)
5. Press *Ok* -> *Apply* -> *Save* -> *Close*
6. Try it!



### How to start enumerate list from different number ?

1. Start a numbered list (`[Alt+P E]`)
2. Insert *TeX*-code (`[Ctrl+L]`)
3. Write `[0.]\setcounter{enumi}{0}` (to start list from zero)
4. Exit TeX-block and write elements as usual.

#### What is the magic `[num1.]\setcounter{enumi}{num2}` ?

Well, it's a little hack that says:

* current item should be looked like an item with number **num1**
* set real number of current item to **num2**, so next item will be
numbered as **num2+1** and so on

So, if you use TeX-code like `[10.]\setcounter{enumi}{20}` -- this element will
be marked as **10**, but next element will be marked as **21**.

Of course, you can use this hack for any item in numbered list.



***



#### References:

* [Can I change the paragraph spacing in LyX UI?](http://tex.stackexchange.com/questions/88839/can-i-change-the-paragraph-spacing-in-lyx-ui)



[listing-package-man]:http://texdoc.net/texmf-dist/doc/latex/listings/listings.pdf "The Listings Package"
