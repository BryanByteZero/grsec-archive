everyone's installer for sparc is broken for newer sparc machines
(they all generally have the new NMI code which crashes the machine on 
boot)
this boot.img and module tarball will allow you to install Linux on a 
Sun Blade 2500

Procedure is:
Setup rarpd+tftpd per instructions online
download a mini.iso from debian, place it in the appropriate location
symlink to the boot.img per instructions online
boot net from the OK prompt
bail out when you have to select your mirror, in the main menu choose to 
start a shell
mv /lib/modules/<whatever_dir_is_there> /lib/modules/olddir
cd /lib/modules
wget http://grsecurity.net/~spender/new_sparc_netinstall/sparc2621.tgz
tar -zxf sparc2621.tgz
you may need to edit /etc/udeb-sources and change "unstable" to "lenny"
exit the shell
choose your mirror, and continue installation
it'll be able to find the harddrive for the Sun Blade 2500 now

You'll want to find a kernel that boots and install it prior to 
rebooting, otherwise you can bootstrap your new system using the 
installer, mounting your drives and compiling a new kernel that way

A working kernel for a Sun Blade 2500 has been provided
