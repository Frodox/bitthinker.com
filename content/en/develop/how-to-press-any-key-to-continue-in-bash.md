<!--
Title: How to implement "Press any key to continue" in bash?
Date: 2013/10/10
Tags: bash, development
-->

If you need a *pause* in your `bash script`, like "PAUSE" does in DOS,
you may implement it with `read` command:

    #!/bin/bash
    read -n 1 -r -s -p "Press any key to continue..." key
<!--cut-here-->

* The `-n 1` &emsp;: specifies that it only waits for a single character.
* The `-r` &emsp;: puts it into raw mode, which is necessary because otherwise, if you press something like backslash, it doesn't register until you hit the next key.
* Tht `-s` &emsp;: Silent mode.  If input is coming from a terminal, characters are not echoed.
* The `-p` &emsp;: specifies the prompt, which must be quoted if it contains spaces. The prompt is displayed only if input is  coming from a terminal.
* The `key` &emsp;: argument is only necessary if you want to know which key they pressed, in which case you can access it through `$key`.
