<!--
Title: How to visualize tcpdump with GraphViz&nbsp;?
Description: Do you know how to visualize tcpdump with graphviz and bash only? No?! I will show you!
Date: 2013/11/07
Tags: bash, tcpdump, makefile, awk
-->

<style>
img#what-we-need-img {
    width: 300px;
}
img#graphviz-simple {
    width: 300px;
}
</style>


Let's assume, you want to visualize your network's map/structure.
How could you get it? Well.. there're plenty ways. For example, you can use
Gephi, Scapy and other tools and scripts. Or you can do it by yourself :)

I'll show you one of such ways - how to visualise tcpdump output
with help of GraphViz and bash only<!--cut-here-->.



## What do we have?
Suppose, all you have - a `tcpdump` log. You could get it from wherever you want,
all you can make it by yourself.



## What do we need?
We need a graph of network:

* PCs (identified by IP) are nodes
* If one PC sended data to anothere - nodes should be connected

We should get something like this:
![Example of network's graph][what-we-need-example]



## A bit of theory

We would use:

* tcpdump
* graphViz
* bash

If you are already familiar with with these technologies, you could skip next 2 chapters ;)



### Tcpdump
`tcpdump` - utility to dump traffic on a network. There're plenty of tutorials how to work with it
([one][tcpdump_man], [two][tcpdump-daniel], [three][tcpdump-dummies]).
I will show only the most basic and common commands.



#### Basic args

* `-i any` : Listen on all interfaces just to see if you're seeing any traffic.
* `-i eth0`: Listen on *eth0* interface.
* `-n` : Don't resolve hostnames.
* `-nn` : Don't resolve hostnames or port names.
* `-X` : Show the packet's contents in both hex and ASCII.
* `-v, -vv, -vvv` : Increase the amount of packet information you get back.
* `-c x` : Only get *x* number of packets and then stop.
* `-S` : Print absolute sequence numbers.
* `-w file.pcap` : save the packet data to a *file.pcap* for later analysis. Data will be saved in binary mode, so you could later get ALL nedeed info from such file.
* `-r file.pcap` : read from a saved packet *file.pcap* rather than to read packets from a network interface. You could also specified any needed options, like `-n, -v, -S` and so on.
* `-t` : Don't print a timestamp on each dump line.



#### Basic usage

1. `tcpdump -nS -i eth0` : see the basics info without many options
2. `tcpdump -nnvvS -i eht0` : see a good amount of traffic, with verbosity and no name help
3. `tcpdump -c 100 -i eht0 -w dump.pcap` : save 100 packages into file *dump.pcap* for later analysis
4. `tcpdump -nntr dump.pcap` : get only desirable information from saved dump file.  
`-nnt` will print smth like: `IP 192.168.1.1.22 > 192.168.1.2.50673: UDP, length 10`.

Look at [man pages][tcpdump_man] for more information ;)



### GraphViz
`graphviz` - rich set of graph drawing tools. Actually, it has deadly simple syntax.  
Just look :

    $ cat simple_graph.dot
    digraph my_first_graph {
        A -> B [ label = "Edge A to B" ];
        B -> C [ label = "Edge B to C" ];
        A [label="Node A"];
        A -> {C ; D}
    }
    
    # compile it:
    cat simple_graph.dot | dot -Tsvg -o simple_graph.svg

And you will get your first graph:
![My First graph with graphviz][graphviz-basis-img]

There are several layout programs for drawing graphs, like

* dot (filter for hierarchical layouts of graphs)
* neato (filter for symmetric layouts of graphs)
* circo (for symmetric layouts of graphs)
* and other


#### Basic args fot `dot`

* `-Txxx` : xxx  is  an unlikely format to convert. Supported formats: `ps, svg, png, gif` and so on.
* `-Klayout` : use diffrenet layout (dot, neato, circo, fdp ...)
* `-o outfile` : output file

There're good official tutorials for beginners
for [`dot`][dot-tutor] and [`neato`][neato-tutor].



## Let's Code it!
Going back to the original problem, I think you already know what we need to do:

1. Parse a tcpdump log and grep all IPs
2. Put them into file with graphviz-dot-syntax
3. Render an image
4. Profit!




### Parse script
There're some existing solution for this problem, like [this][NTV] or [this][afterglow] one,
but in my case (if you already have a tcpdump log to visualize) they were too complex
or didn't work at all. So, I write it by myself.

I grep IPv4 and put them in a graphviz-dot file format like this:

```bash
grep -E "IP\ " $FILE | awk ' BEGIN { e="(([0-9]{1,3}.){3})([0-9]{1,3}).*" }
{
    printf "\"%s\" -> \"%s\";\n", gensub(e, "\\1\\3", "1", $3), gensub(e, "\\1\\3", "1", $5)
}' >> $TMP 
```

And IPv6:

```bash
grep -E "IP6" $FILE | awk 'BEGIN { e="(([0-9a-fA-F:]{1,5}){1,5}):([0-9a-fA-F]{1,4}).*" }
{
    printf "\"%s\" -> \"%s\";\n", gensub(e, "\\1:\\3", "1", $3), gensub(e, "\\1:\\3", "1", $5)
}' >> $TMP
```

Since there is no sense to post here all the code, you can download the [tcp2graphviz script here][tcp2graphviz-script] (and on [github][tcp2graphviz-github]).



### Render

Now, we have 2 files with IPv4 and IPv6 which we can convert into image/pdf/svg/etcs.
IPv4 one looks like:

```bash
digraph tcpdump_graph_ip4 {
    "10.0.0.50" -> "10.0.0.255";
    "10.0.0.50" -> "192.168.0.57";
    "10.0.0.50" -> "224.0.0.251";
    ...
}
```

You can render it manually, or with help of universal [Makefile][makefile]  
(be careful to copy it as is, because you should use tabs for indents in Makefiles):

```makefile
LAYOUT=circo
SOURCES = $(wildcard *.dot)
TARGETS = $(SOURCES:.dot=.svg)

%.svg:%.dot
    dot -Tsvg -K${LAYOUT} "$^" -o "$@"

all: $(TARGETS)

clean:
	rm -f $(TARGETS)
```

As for me, I just run `make` and... Profit!

One of resulting images (svg; may be useful to open in new tab):
![Map of local network][tcp_all_06]



[what-we-need-example]: /blog/content/en/research/imgs/what-we-need-exmple.svg
"Example of network's graph" {.img #what-we-need-img}

[graphviz-basis-img]: /blog/content/en/research/imgs/graphviz-basic.svg
"Simple graph with graphviz" {.img #graphviz-simple}

[tcp_all_06]: /blog/content/en/research/imgs/06-all-ip4.svg
"Map of my local network.. ;)" {.img}



[tcpdump_man]: http://www.tcpdump.org/tcpdump_man.html (Man page)
[tcpdump-daniel]: http://www.danielmiessler.com/study/tcpdump/ (A tcpdump Primer)
[tcpdump-dummies]: http://www.alexonlinux.com/tcpdump-for-dummies (tcpdump for Dummies)

[dot-tutor]: http://www.graphviz.org/pdf/dotguide.pdf (Drawing graphs with dot)
[neato-tutor]: http://www.graphviz.org/pdf/neatoguide.pdf (Drawing graphs with NEATO)

[NTV]: https://github.com/rpt/Network-Traffic-Visualization/blob/master/doc/USAGE
( Network-Traffic-Visualization)
[afterglow]: https://github.com/zrlram/afterglow (afterglow)

[tcp2graphviz-script]: /blog/content/en/research/imgs/src/tcp2graphviz.sh
"tcp2graphviz script"
[tcp2graphviz-github]: %github%/mephi_labs/tree/master/4th-year/tcpdump_visualisation (tcp2graphviz on github)
[makefile]: /blog/content/en/research/imgs/src/Makefile
"Universal Makefile"
