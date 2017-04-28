<!--
Title: [DRAFT] How to create custom CentOS like Linux distro iso
Description: later
Tags: CentOS, Linux, lorax, anaconda, custom distro, hacking
-->

# QuickStart


There are three possible types of iso:
* Regular bootable iso (just like CentOS-7.3-Everything) - I will tell here about this one
* Live-iso (just like Fedora-Workstation-Rawhide) - I would try to do it and tell you in futher posts
* Both bootable & live (just like Ubuntu :-S) - It is my goal. I do not know how to create it now, but I would like to do it

Here is just QuickStart for creating custom bootable iso.
Feel free to send PR, issues and proposes if you don't understand something.

* Run [`lorax`](http://lorax.readthedocs.io/en/latest/intro.html) to get minimal `boot.iso`.
Remember all used arguments, like `-p`, `--volid`!
* mount `boot.iso` and copy all its content
```
setenforce 0
lorax ... "$LORAX_TMP_DIR"
...
buildroot=/build/lorax
mount -o loop,ro "$LORAX_TMP_DIR/images/boot.iso" "$TMP_MOUNT_DIR"
rm -rf "$buildroot"
cp -apr "$TMP_MOUNT_DIR" "$buildroot"
cp -a $LORAX_TMP_DIR/.discinfo "$buildroot"
cp -a $LORAX_TMP_DIR/.treeinfo "$buildroot"
...
# Add all needed rpms into `$buildroot/Packages/` manually
pushd $buildroot
mkdir -p Packages
cp ...mypackages/*.rpm Packages/

# create repo
mkdir repodata
cp ../my-centos-comps.xml repodata/comps-$DISTRO_NAME.xml
createrepo -d -g "repodata/comps-$DISTRO_NAME.xml" .
```
* fix `isolinux/isolinux.cfg` - regular bootloader config
* fix `EFI/BOOT/grub.cfg` - efi bootloader config
* create `product.img` with needed info
  * `custom_kickstart` (ks for interactive installation with some desired defaults)
  * custom `installclasses/custom.py`, with distroname and some fixes
* add GPG-keys for custom packages (or put them inside `centos-release.rpm` (prefered). Do not forget to put them into installed OS then
* add kickstart if you want (and use it in `grub.cfg`, `isolinux.cfg`)
* creating iso. You can take a look into lorax's `x86.tmpl`. In my script it looks like: 
```#!/bin/bash
    genisoimage -U -J -R -T -joliet-long                        \
        -b isolinux/isolinux.bin                                \
        -c isolinux/boot.cat                                    \
        -boot-load-size 4 -boot-info-table -no-emul-boot        \
        -eltorito-alt-boot                                      \
        -e images/efiboot.img -no-emul-boot                     \
        -input-charset utf8                                     \
        -V "$DISTRO_NAME"                                       \
        -sysid "X86_64" \
        -publisher "my complany" \
        -o "$filepath" "$buildroot"
```
*
  * **IMORTANT**: `isolinux/boot.cat` - it is why we need to unpack `boot.iso`, and use it, instead of taking raw lorax-output-root (which contains `images/boot.iso`, which not needed).
  * **IMORTANT**: `-V VOLID` must be same as we use in `lorax --volid` and as we use in `isolinux.cfg` for booting
* `isohybrid --uefi "$filepath"` --- need to make USB bootable after `dd if=myiso.iso of=/dev/sdb`
* `implantisomd5 --supported-iso "$filepath"` 

---

* [Creating the Anaconda boot.iso with lorax](https://www.brianlane.com/creating-the-anaconda-bootiso-with-lorax.html)
