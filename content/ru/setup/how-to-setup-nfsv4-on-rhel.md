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

chkconfig 35 rpcbind on
chkconfig 35 nfs on

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

mount -t nfs4 -o rw,soft <server_ip>:/ /mnt/nfs
```

### important

* `rpcbind` on RHEL must be reloaded *before* `nfs` service
* mount `<server_ip>:/`. NFSv4 rely on one virtual root folder for all exports
* set `fsid=0` for *root* of all export dirs
* show more mount opts (also `rzise,vsize`)
* list *all* subfolders that you want to import in `/etc/exports`


