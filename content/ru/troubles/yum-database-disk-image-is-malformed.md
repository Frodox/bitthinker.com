<!--
Title: Проблема yum : database disk image is malformed
Description: Проблема yum : database disk image is malformed
Date: 2013/04/09
Tags: troubles, yum
-->

Если при очередной попытке установить что-либо, *yum* выдал Вам примерно следующее<!--cut-here-->:
<pre><code class="bash" title="database disk image is malformed">[leeky@darkstar ~]$ sudo yum install vim
Loaded plugins: fastestmirror, langpacks, presto, refresh-packagekit
Loading mirror speeds from cached hostfile
 * fedora: mirror.netrino.co.uk
 * rpmfusion-nonfree-updates: mirror01.th.ifl.net
 * updates: mirror.netrino.co.uk
Error: database disk image is malformed</code></pre>

Значит, что-то случилось с SQLite базой yum (или её кэшем), и он не доволен её структурой. Ну-с, попробуем всё починить.  
Скорее всего, Вам поможет один из следующих способов:

* `yum clean all` : Почистить весь кэш.
* `yum clean dbcache` : Почистить кэш базы данных.
* Почистим кэш, и заново скачаем мета-данные из repos:
<pre><code class="bash">su -
yum clean metadata
yum clean dbcache
yum makecache</code></pre>

* крайний случай..
<pre><code class="bash">su -
cd /var/libs/
rm -rf yum
mkdir yum

### rebuild database
yum clean metadata
yum clean dbcache
yum makecache</code></pre>

* и ещё вот так можно:
<pre><code class="bash" title="Rebuild rpm database">su -
mv /var/lib/rpm/__db* /tmp
rpm --rebuilddb</code></pre>


* [из ML](http://lists.fedoraproject.org/pipermail/users/2010-January/364817.html)  
`rm -rf /var/lib/yum/history/*`


Если ни один из вышеприведённых способов Вам не помог, попробуйте посмотреть выводы `# yum history new`, и  `# yum check`, ну и, собственно, решить проблему собственными знаниями и опытом, или с помощью поисковых систем :)

---
По материалам [этого топика][2].

[2]:http://forums.fedoraforum.org/showthread.php?t=260195 (Problem with yum; Error: database disk image is malformed)
