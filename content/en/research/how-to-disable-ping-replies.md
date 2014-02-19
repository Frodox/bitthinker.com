<!--
Title: Linux: How to disable ping replies?
Date: 2013/10/31
-->

You may want to disable ping replies for many reasons, maybe for a security reason,
or to avoid network congestion. To disable ping replies, you can do it either with help of `iptables` ([allow or block ICMP ping request][1]) or by [setting the kernel variable][2].<!--cut-here--> 

## Temporarily Disable ping replies
To temporarily (will be back after reboot) disable the ping reply, use this command:

    su -
    echo "1" >  /proc/sys/net/ipv4/icmp_echo_ignore_all
    # This instructs the kernel to simply ignore all ping requests
    # 1 -- ignore ping requests
    # 0 -- don't ignore

or

    iptables -A INPUT -p icmp -j DROP

## Permanently Disable ping replies
To disable ping requests permanently, add this line into your `/etc/sysctl.conf` file:

    net.ipv4.icmp_echo_ignore_all = 1

And reload `sysctl`'s policy by `# sysctl -p`.

Or save `iptables` rule by

    # for distros with systemd
    /usr/libexec/iptables.init save

    # for all other distros
    service iptables save

    # univeral way: edit main config by yourself
    vim /etc/sysconfig/iptables

[1]:http://www.cyberciti.biz/faq/howto-drop-block-all-ping-packets/ (Linux disable or drop / block ping packets all together)
[2]:http://www.thegeekstuff.com/2010/07/how-to-disable-ping-replies-in-linux/ (How To Disable Ping Replies in Linux using icmp_echo_ignore_all)
