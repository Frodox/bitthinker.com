<!--
Title: How to check in Makefile if some program is running ?
Description: 
Tags: bash, makefile, libreoffice, convert
Date: 2014/04/27
-->

I write my university lections in LyX and draw diagrams in LibreOffice Draw.
So, one day I decided to write a Makefile to covert a batch of odg-diagrams to pdf<!--cut-here-->. 
You may ask me: what is the problem? Well... you can convert odg to pdf in may ways:

* libreoffice --convert-to pdf
* cups-printer
* unoconv

I choose an easiest one:

```bash
$ libreoffice --headless --convert-to pdf "file.odg"
```

It works fine until... there is no other LO instances. There is a [workaround][1] to fix it,
but I decide just to check in Makefile, if any LO instance is running now,
and exit if any.

My resulting [Makefile][makefile] looks like:

```makefile
SOURCES = $(wildcard *.odg)
TARGETS = $(SOURCES:.odg=.pdf)

LIBREOFFICE_PID = $(shell pidof soffice.bin)

%.pdf:%.odg

ifneq ("", "$(LIBREOFFICE_PID)")
	$(error "Please, close all LibreOffice instances ($(LIBREOFFICE_PID)) to continue")
endif
	libreoffice --headless --convert-to pdf "$^"

all: $(TARGETS)

clean:
	rm -f $(TARGETS)
```


* * *

### Resources

1. [Syntax of Conditionals](https://www.gnu.org/software/make/manual/html_node/Conditional-Syntax.html)
2. 
[How can I use soffice from the command line when Quickstarter is running?][1]
3. [convert-to command line parameter](http://ask.libreoffice.org/en/question/2641/convert-to-command-line-parameter/)

[1]:http://ask.libreoffice.org/en/question/14841/how-can-i-use-soffice-from-the-command-line-when-quickstarter-is-running/
[makefile]:/blog/content/en/develop/Makefile-detect-running-program
