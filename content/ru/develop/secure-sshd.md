<!--
Title: Защищаем SSH сервер
Description: Как зищитить SSH сервер? Как повысить его защищённость? Какие методы защиты есть? Ответы на эти и другие вопросы читайте в моей заметке.
Date: 2014/10/26
Tags: ssh, sshd, security
-->

В этой короткой заметке я собрал воедино способы повышения защищённости
ssh сервера. Описаны самые основные и простые в исполнении приёмы,
а более сложные лишь указаны для интересующихся читателей<!--cut-here-->

### Основные приёмы

Все действия производятся в конфигурационном файле sshd демона --
`/etc/ssh/sshd_config`. Ниже приведу часть своего конфигурационного файла
с комментариями.

```
	### Network ###

# Используем нестандартный порт (>1024)
port 5679
# Используем только IPv4 соединения
# inet = IPv4, inet6 = IPv6, any = both 
AddressFamily inet
# Можно принимать соединения только с определённых IP-адресов
#ListenAddress 0.0.0.0

# Используем вторую версию протокола, т.к. первая подвержена
# известным уязвимостям
Protocol 2

# Отключаем перенаправление графики (X-сервер) до тех порт
# пока она явно не понадобится вам
X11Forwarding no

# Отключаем Disable TCPKeepAlive и используем ClientAliveInterval вместо этого,
# чтобы предотвратить атаки типа TCP Spoofing
TCPKeepAlive no
# Выкидваем пользователя через 10min (600 sec) неактивности
ClientAliveInterval 600
ClientAliveCountMax 3


	### Key configuration files ###

# HostKeys для протокола версии 2
HostKey /etc/ssh/ssh_host_rsa_key
HostKey /etc/ssh/ssh_host_dsa_key

# Используем непривилегированный процесс для
# обработки входящего от клиента трафика
# sandbox - openSSH >= 5.9 ("yes" - для младших версий)
UsePrivilegeSeparation sandbox


# При изменении этих значений, требует удалить старый ключ
# /etc/ssh/ssh_host_rsa_key{,.pub}, и создать новый
# путём перезапуска sshd.
#
# Время жизни ключа, т.е. через какое время будет сгененрирован новый ключ
# в случае, если предыдущий был использован.
KeyRegenerationInterval 1h
# сила ключа
ServerKeyBits 2048

# Разрешаем авторизацию по публичному ключу
PubkeyAuthentication yes
# Место хранения доверенных ключей в каталоге пользователя
AuthorizedKeysFile      .ssh/authorized_keys


	### Logging ###

# Префикс для syslog
SyslogFacility AUTH
# уровень подробности сообщений для самого sshd
LogLevel INFO

	### Authentication ###

# список разрешённых пользователей
AllowUsers ivan

# ограничиваем время для ввода пароля для ssh-ключа
LoginGraceTime 30s

# запрещаем удалённо входить под учётной записью root
PermitRootLogin no

# Включаем явную проверку прав файлов и директорий с ssh-ключами
StrictModes yes

# Сколько раз переспрашивать пароль при неверном вводе
MaxAuthTries 3

# Запрещаем вход по пустому паролю
PermitEmptyPasswords no

# Запрещаем вход по паролю в принципе
# (Используем публичный/приватный ключ вместо этого)
PasswordAuthentication no

# Отключаем использование "challenge-responce" авторизацию,
# т.к. она бесполезна при использовании ключей
ChallengeResponseAuthentication no

# Так как мы не используем пароль, то и {PAM, login(1)} нам не нужены
UsePAM no
UseLogin no

# Позволяем клиенту передавать лишь определённый набор переменных окружения
# RH BZ#CVE-2014-2532
# ShellShock exploit
AcceptEnv LANG LC_CTYPE LC_NUMERIC LC_TIME LC_COLLATE LC_MONETARY LC_MESSAGES
AcceptEnv LC_PAPER LC_NAME LC_ADDRESS LC_TELEPHONE LC_MEASUREMENT
AcceptEnv LC_IDENTIFICATION LC_ALL
```

Это те параметры, что настраиваются в конфигурационном файле sshd.
После изменения настроек требуется перезапустить сервис sshd.

При использовании авторизации по ключу, ключ требуется **предварительно**
сгенерировать на клиентской машине и скопировать публичный ключ на сервер.
**Пример:**
```
client $ ssh-keygen
client $ cat ~/.ssh/id_rsa.pub | ssh -p 5679 ivan@serverurl.com "cat >> ~/.ssh/authorized_keys"
```

В файле `/var/log/auth.log` будут находиться сообщения от **sshd**. В случае,
если этот файл отсутствует, вам требуется настроить вашу систему логирования.
[Вот здесь][gentoo-syslog] пример для `syslog` и `syslon-ng`.
Я использую `syslog-ng`, и мне потребовалось добавить следующие строки
в файл `/etc/syslog-ng/syslog-ng.conf`:
```
destination authlog { file("/var/log/auth.log"); };
log { source(src); destination(authlog); };
```
и перезапустить сервис `syslog-ng`.


### Если этого мало

Это лишь базовые настройки. Дополнительно можно настроить

* firewall (iptables)
	* [hashlimit](http://habrahabr.ru/post/88461/)
	* [time limit](https://modx.pro/hosting/582-protection-22nd-ssh-server-port/)
* fail2ban
* port knoking
* /etc/hosts.deny

---

### Использованная литература

* [Linux Home Server HOWTO|Chapter 16 - Secure Shell](http://www.brennan.id.au/16-Secure_Shell.html)
* [SSH Server: A more secure configuration](
http://ubuntuforums.org/showthread.php?t=831372)
* [Gentoo handbook|Logging](https://www.gentoo.org/doc/en/security/security-handbook.xml?part=1&chap=3)
* [Arch Linux | syslog-ng](https://wiki.archlinux.org/index.php/syslog-ng)
* [RH bug: openssh: AcceptEnv environment restriction bypass flaw](https://bugzilla.redhat.com/show_bug.cgi?id=CVE-2014-2532)
* [Установка и настройка openssh-сервера в rhel, centos, fedora](http://redhat-club.org/2011/%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D0%B8-%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B0-openssh-%D1%81%D0%B5%D1%80%D0%B2%D0%B5%D1%80%D0%B0-%D0%B2-rhel-centos-fedora)

[gentoo-syslog]: https://www.gentoo.org/doc/en/security/security-handbook.xml?part=1&chap=3
