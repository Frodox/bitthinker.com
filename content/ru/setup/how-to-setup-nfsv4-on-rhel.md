<!--
Title: Настраиваем доступ к данным с помощью NFSv4
Description: В кратце описывается настройка клиента и сервера для организации общего доступа к данным на основе протокола NFSv4. В качестве примера выступает ОС RHEL.
Tags: NFS, linux, setup
-->


Для организации общего доступа к даным можно использовать такие протоколы, как
http, ftp, ssh, samba, nfs и другие. [NFS](https://ru.wikipedia.org/wiki/Network_File_System "Wikipedia")
-- протокол, специально разработанный для этих задач. Он показывает неплохую
производительность и удобство при работе в Unix системах.
В этой заметке я опишу способ настройки клиента и сервера на примере RHEL<!--cut-here-->.

## draft


### server:

```bash
yum install nfs-utils nfs-libs

** iptables*!!!
/etc/sysconfig/iptables
-A INPUT -p tcp -m state --state NEW,ESTABLISHED -m tcp --dport 2049 -j ACCEPT

chkconfig rpcbind on
chkconfig nfs on

# you can bind subfolders
mkdir -p /export/test

chown -R nfsnobody:nfsnobody /export

vim /etc/exports
/export *(rw,fsid=0,sec=unix,no_subtree_check,async,all_squash,anonuid=65534,anongid=65534)
/export/subfolder *(rw,sec=unix,async,nohide,all_squash,anonuid=65534,anongid=65534)

exportfs -a # after any changes in /etx/exports

/etc/init.d/rpcbind restart
/etc/init.d/nfs restart
```

### client:

```bash

sudo yum install nfs-utils nfs-libs

chkconfig 35 rpcbind on
chkconfig 35 nfs on

/etc/init.d/rpcbind restart
/etc/init.d/nfs restart

$ showmount -e <server_ip>

Export list for <server_ip>:
/export/subfolder *
/export           *

mkdir -p /mnt/nfs
chown -R nfsnobody:nfsnobody /mnt/nfs

reboot

mount -t nfs4 -o rw,soft <server_ip>:/ /mnt/nfs
```

### important

* `rpcbind` on RHEL must be reloaded *before* `nfs` service
* mount `<server_ip>:/`. NFSv4 rely on one virtual root folder for all exports
* set `fsid=0` for *root* of all export dirs
* show more mount opts (also `rzise,vsize`)
* list *all* subfolders that you want to import in `/etc/exports`

### Problems

* big uids : reboot
* file not exist
** setup server as nfsv4 and mount root
** mount as nfs (`-t nfs`)

### iptables solution here:

* http://mylinuxlife.com/setting-up-nfs-on-rhel-6-iptables-firewall-solution/
* http://nixcraft.com/showthread.php/16729-Linux-NFS4-client-unable-to-mount-share/page2?s=e798fd54314a4dc7d33bf1531fabdb9e
* http://www.k-max.name/linux/network-file-system-nfs/

Mount via /etc/fstab:
https://www.centos.org/docs/5/html/5.1/Deployment_Guide/s2-nfs-fstab.html
common mmount ots: https://www.centos.org/docs/5/html/5.1/Deployment_Guide/s1-nfs-client-config-options.html

