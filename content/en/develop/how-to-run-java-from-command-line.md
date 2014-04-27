<!--
Title: How to run java from command line?
Description: Java and the Linux Command Line
Date: 2013/11/10
Tags: java, development
-->

This document instructs you on how to use the Linux shell with Java<!--cut-here-->.
Let's assume you have already installed Java and have `java[c]*` commands.



## Compile & Run simple program

If you have simple Hello World in *current directory*,

```bash
$ ls
Hello.java
```

then compiling and runnig is pretty easy:

```bash
$ javac Hello.java
$ ls
Hello.class Hello.java
$ java Hello
Hello, Men!
```

> **NOTE #1**: when you *compile* the src, **include** the `.java` extension!

> **NOTE #2**: be sure to omit a trailing `.class` extension, when execute a program!

If you get an error during executin like

	Exception in thread "main" java.lang.NoClassDefFoundError

try to use

	java -cp . Hello

If it succeed, your java-classpath is set incorrectly.



## Compile and Run a program with package

If you have a project, or java-file from eclipse with package, there is a small trick:  
you should use fully qualified name (with package name and correct directory structure),
otherwise you will get an error

	Exception in thread "main" java.lang.NoClassDefFoundError: Hello (wrong name: com/bitthinker/Hello)
	bla bla blah ...
 
So, to resolve it, let's assume your Hello.java in inside com.bitthinker pachage,
so, you **should** have the following dir-structure:

	/workspace
		/com
			/bitthinker
				Hello.java

Then, the whole trick **if you are in `workspace` folder** is (be patient):

	$ javac com/bitthinker/Hello.java
	$ java  com.bitthinker.Hello
	Hello, Man!


For more information, see Useful links section :)

---

### Useful links:

* [Common Problems (and Their Solutions)](http://docs.oracle.com/javase/tutorial/getStarted/problems/)
* [Java and the Linux Command Line](http://introcs.cs.princeton.edu/java/15inout/linux-cmd.html)
* [Running Java Program from Command Line Linux](http://stackoverflow.com/questions/3692229/running-java-program-from-command-line-linux)
* [Exception in thread “main” java.lang.NoClassDefFoundError: wrong name](http://stackoverflow.com/questions/14520793/exception-in-thread-main-java-lang-noclassdeffounderror-wrong-name)
