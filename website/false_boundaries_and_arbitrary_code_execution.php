<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity</title>


<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=5A5zyaR2my">
<link rel="icon" type="image/png" href="/favicon-32x32.png?v=5A5zyaR2my" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png?v=5A5zyaR2my" sizes="16x16">
<link rel="manifest" href="/manifest.json?v=5A5zyaR2my">
<link rel="mask-icon" href="/safari-pinned-tab.svg?v=5A5zyaR2my" color="#344d83">
<link rel="shortcut icon" href="/favicon.ico?v=5A5zyaR2my">
<meta name="theme-color" content="#ffffff">

<script type='text/javascript' src='/js/header.js'></script>
<link rel='stylesheet' href='/scss/style.css' type='text/css' media='all' />

</head>
<body>

<header id="top" class="site-header" role="banner">

	<div class="header-content wrap">

		<h1 class="logo">
			<a href="/index.php"><img src="/img/grsecurity.svg" alt="grsecurity"></a>
		</h1>

		<button class="menu-btn">Show Navigation</button>
<div class="nav-wrap">
	<nav class="access" role="navigation" aria-label="Primary" itemscope itemtype="http://www.schema.org/SiteNavigationElement">
		<ul>
			<li itemprop="name"><a href="/index.php" itemprop="url">Home</a></li>
			<li itemprop="name"><a href="/features.php" itemprop="url">Features</a></li>
			<li itemprop="name"><a href="/support.php" itemprop="url">Support</a></li>
			<li itemprop="name"><a href="/papers.php" itemprop="url">Papers</a></li>
			<li itemprop="name"><a href="/blog.php" itemprop="url">Blog</a></li>
			<li><a href="/purchase.php" class="btn" itemprop="url"><span itemprop="name">Purchase</span></a></li>
		</ul>

	</nav>
</div> <!-- .nav-wrap -->

	</div>

</header>

	<main>
		<article>
			<header class="masthead">
				<div class="wrap">
					<h1 class="large-headline">False Boundaries and Arbitrary Code Execution</h1>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<p>I intend this post to be a sort of reference to the Linux capability system, particularly to point out the false boundaries in place with many of the existing capabilities, as I don't think this topic has been written about in depth before.  I'd also like to highlight this as an example of the importance of the PAGEEXEC/MPROTECT concepts in combination with the removal of arbitrary code execution (and in the future, hardened interpreters).  For those with an elementary-school level of reading comprehension, I am not discussing capabilities when used as part of more elaborate containment models (PID namespaces, special/hardened chroot environments, SELinux, grsecurity's RBAC, SECURE_NOROOT, etc) -- the need for these kinds of things, in addition to preventing arbitrary code execution, is the point of this article.</p>
<p>As of Linux 2.6.37, there are 35 capabilities which exist with the intent to split up the privilege associated with UID 0.  Before the implementation of file capabilities, the capability support was for capability-aware applications that ran with root privilege.  A knowledge of which root privileges were needed allowed the applications to drop any unneeded capabilities.  In Linux 2.6.24, file capabilities were introduced, which allowed for the distribution or the administrator to set the capabilities needed for an application via modification of the application's extended attributes on disk.  The immediate application of file capabilities is to remove the need for suid-root binaries on the system.  It can also be used however to reduce the capabilities used by a normal root-running daemon, by clearing the effective bit in the file capabilities.</p>
<p>File capabilities have yet to be fully utilized in any distro, though Fedora 14 plans to remove setuid binaries through filesystem capabilities: <a href="http://fedoraproject.org/wiki/Features/RemoveSETUID">http://fedoraproject.org/wiki/Features/RemoveSETUID</a> as does Ubuntu: <a href="https://blueprints.launchpad.net/ubuntu/+spec/security-karmic-fscaps">https://blueprints.launchpad.net/ubuntu ... mic-fscaps</a> .  Setting aside the vulnerabilities possibly added by introducing new code into the kernel at critical points in transfer of privilege, there's one major reason why file capabilities are important:  any vulnerability in a root-running application on any normal system (i.e. not a system hardened with grsecurity's RBAC system) that occurs prior to the setuid(NONROOTUID) results in full root privileges, even if some capabilities were dropped while under UID 0.</p>
<p>There are a number of reasons for this:
<ul>
<li>System binaries are mode 0755, owned by root and thus writable by root without any additional capabilities.</li>
<li>Execution of a suid-root binary causes any lowered inheritable/permitted capability sets to be ignored, granting the complete set of capabilities.</li>
<li>If the real UID is 0, a simple execve grants full capabilities.</li>
<li>Modification of the system's disks via their block devices requires no capabilities.</li>
<li>Modification of /proc/sys/kernel/modprobe requires no capabilities (can cause arbitrary code to be inserted into the kernel via a replacement modprobe)</li>
<li>Delaying execution via backdooring a system binary or suid-root binary and waiting for it to be executed will also grant full capabilities.
</li>
</ul>
</p>
<p>Note that the above behavior is constrained by the capability bounding set, which can remove certain capabilities from acquisition .  We'll ignore here that certain other capabilities can be used to modify the running kernel to change this bounding set so that the forbidden capabilities can be re-acquired ;)  Prior to 2.6.25, the bounding set was a system-wide setting.  Starting with 2.6.25, it is not.</p>
<p>The suid-root -&gt; full caps and ruid 0 + execve -&gt; full caps behavior can also be controlled via the SECURE_NOROOT securebits flag, present in Linux &gt;= 2.6.26.  Though this only applies to the process that set the securebits and its children.  See <a href="http://lwn.net/Articles/280279/">http://lwn.net/Articles/280279/</a> for more information.  10 points if you can find more than a handful of users of this in a google code search that aren't the Linux kernel themselves ;)</p>
<p>'man capabilities' for more info and a taste of how complex this whole system is, if this poor introduction hasn't impressed that upon you.  If you're interested in this topic, the appendix of Chris Evans' and Julien Tinnes' presentation &quot;Security in Depth for Linux software&quot; is a good read: <a href="http://www.cr0.org/paper/jt-ce-sid_linux.pdf">http://www.cr0.org/paper/jt-ce-sid_linux.pdf</a> .</p>
<p>As mentioned earlier, there are 35 capabilities currently implemented.  I'll now discuss each capability that is effectively equal to root and a rough description of how each transition is made.  I will try to make a distinction between cases that are generally applicable and those that are situational.  Since we've already established that real uid 0 is equivalent to having full capabilities on any normal system, I'll assume we're a non-root user with only the mentioned capability raised.  If you come up with clever transitions, reply and I'll update this list.</p>
<p><ul><li>CAP_SYS_ADMIN: generic: among many other things (it's a sort of catch-all capability choice), CAP_SYS_ADMIN grants the ability to mount/unmount filesystems.  So you have the ability to bind mount a new filesystem over an existing one to backdoor any binary on the system.  There doesn't appear to be any DAC check for this operation, so the capability itself is sufficient.  CAP_SYS_ADMIN also grants the ability to use the TIOCSTI ioctl against /dev/tty (a tty not owned by us) and inject commands into an administrator's shell that will be executed without any interaction on their part.</li><li>CAP_SYS_TTY_CONFIG: generic: temporarily change the keyboard mapping of an administrator's tty via the KDSETKEYCODE ioctl to cause a different command to be executed than intended  (back in 2.4 this used to only be protected by suser() (essentially a uid == 0 check), except in grsecurity)</li><li>CAP_MKNOD: generic: allows the creation of a block device owned by the non-root user which is the same device as (for instance) the system disk (on grsecurity, the access to the block device would also require CAP_SYS_RAWIO).  This allows for backdooring of any binary on the system.</li><li>CAP_SYS_PTRACE: generic: ptrace a process of any UID which has the capabilities you need, POKETEXT/SETREGS your way to control flow hijacking and the execution of your code under full capabilities.</li><li>CAP_SYS_RAWIO: generic: allows mapping of the NULL page for exploiting the huge number of NULL pointer dereferences in Linux.  CAP_SYS_RAWIO also enables the use of the FIBMAP ioctl, which would potentially allow for exploitation of the kernel via the handling of input it doesn't expect from untrusted sources (see: <a href="http://lkml.indiana.edu/hypermail/linux/kernel/9907.0/0132.html">http://lkml.indiana.edu/hypermail/linux ... /0132.html</a> for the reason for FIBMAP being privileged, and <a href="http://linux.derkeiler.com/Mailing-Lists/Kernel/2007-11/msg07723.html">http://linux.derkeiler.com/Mailing-List ... 07723.html</a> for further discussion).</li><li>CAP_SYS_MODULE: generic: allows to modify the kernel</li><li>CAP_SETFCAP: generic: can set full capabilities on a file, granting full capabilities upon exec</li><li>CAP_FSETID: situational: Can escalate to GID = 0.  If the kernel is compiled as root, some directories/files in the kernel source tree appear to be left by default as writable as GID 0 (on my Ubuntu 10.10 test system).  This could allow for backdooring of the kernel.  On Debian-based systems, you can escalate to GID = staff, which allows backdooring of any binaries in /usr/local (or essentially any other binary run without its full path, as /usr/local generally appears first in $PATH).  As noted by Tim Brown: as GID = staff, one can place a trojan library in /usr/local/lib which after caches are rebuilt will be preferred over one in other library search paths, allowing full control over code running within a non-static suid root binary.</li><li>CAP_SETGID: situational: same bypass as CAP_FSETID.</li><li>CAP_SETUID: generic: can set real uid to 0 and gain full capabilities on exec.  Also can be used to ignore credential checks on unix domain sockets and feed crafted data over assumed-secure channels.  Also can be used as an effective CAP_SYS_PTRACE replacement (you can change to any UID, so you can ptrace any process).</li><li>CAP_DAC_OVERRIDE: generic: same bypass as CAP_DAC_READ_SEARCH, can also modify a non-suid binary executed by root to execute code with full privileges (modifying a suid root binary for you to execute would require CAP_FSETID, as the setuid bit is cleared on modification otherwise; thanks to Eric Paris).  The modprobe sysctl can be modified as mentioned above to execute code with full capabilities.</li><li>CAP_SETPCAP: generic: if the bounded set hasn't been lowered for the current process, any capability can be acquired in child processes through modification of the inheritable set.  This capability allows bypass of the restriction that requires a capability to be in the permitted set for the current process to be inherited by child processes.</li><li>CAP_IPC_OWNER: situational: compromise a privileged user of IPC by being able to exploit the assumption that its IPC is private</li><li>CAP_CHOWN: generic: /etc/shadow, /root/.ssh/* can be stolen or modified via ownership changes, allowing for full root</li><li>CAP_SYS_CHROOT: generic: From Julien Tinnes/Chris Evans: if you have write access to the same filesystem as a suid root binary, set up a chroot environment with a backdoored libc and then execute a hardlinked suid root binary within your chroot and gain full root privileges through your backdoor</li><li>CAP_DAC_READ_SEARCH: generic: /etc/shadow, /root/.ssh/* can be read, allowing for full root</li><li>CAP_SYS_BOOT: generic: from Eric Paris: load up a new kernel to boot with kexec_load</li><li>CAP_AUDIT_CONTROL: generic: from Jon Oberheide: AUDIT_TTY_GET/AUDIT_TTY_SET netlink commands to the audit subsystem allow for logging and retrival of tty i/o, allowing to obtain the root password</li><li>CAP_FOWNER: generic: from Stefan Nordhausen: allows for arbitrary chmod, which in turn allows chmod 777 /etc/shadow to read/modify the root password and gain full access to the system</li></ul><br />That's 19/35 capabilities equivalent to full root, a good start.  In older kernels, this would have been 19/30, nearly two thirds of all capabilities!</p>
<p>Not full root equivalence, but opens up a plethora of attack venues that in real life would very likely lead to root:<br /><ul><li>CAP_KILL: situational: if a network service is run off a non-standard port &gt; 1024, CAP_KILL can be used to target the existing processes, then the non-root user can start a trojaned service in its place with the intent to steal credentials in some cases, or exploit the client.  In combination with CAP_NET_BIND_SERVICE, this can be done on any port.</li><li>CAP_NET_ADMIN: generic: among other things, allows administration of the firewall, which can redirect packets destined for the system's network services to trojaned services with the intent to steal credentials or exploit the client.  Kernels between August 2009 and March 2011 also allowed a user with CAP_NET_ADMIN to load any module in the normal search paths (ex: ifconfig xfs loads the xfs module if present), increasing the kernel's attack surface greatly.</li><li>CAP_NET_RAW: generic: can sniff and redirect any local network service to a trojan, similar to the CAP_NET_ADMIN attack (thanks to the commenter below)</li></ul><br />No transitions known (to this author, yet):<br />CAP_LINUX_IMMUTABLE: note from Stefan Nordhausen:<br /><blockquote class="uncited"><div>CAP_LINUX_IMMUTABLE is interesting. The immutable flag forbids &quot;mv&quot;, but<br />also &quot;chmod&quot; and &quot;chown&quot; (which the chattr manpage does not mention) and<br />redirections via &quot;&gt;&quot; and &quot;&gt;&gt;&quot;. This means that in shell scripts that do<br />not &quot;set -e&quot;, these operations will silently fail and the script<br />continues. Happens very frequently, but this would require some rather<br />special script to be exploitable. E.g. a firewalling framework that<br />dynamically creates a shell script with &quot;iptables&quot; instructions. One could<br />essentially deactivate the firewall in that case.<br /></div></blockquote><br />CAP_NET_BROADCAST<br />CAP_NET_BIND_SERVICE (I was incorrect about being able to bind to a specific interface if the service was already listening on INADDR_ANY)<br />CAP_IPC_LOCK<br />CAP_SYS_PACCT<br />CAP_SYS_NICE: note from Stefan Nordhausen:<br /><blockquote class="uncited"><div>CAP_SYS_NICE should make it much, much easier to launch side channel<br />attacks agains cryptography programs. One can force the victim and the<br />analysis program to be alone together on the same CPU core. Then you can<br />be sure that branch prediction and caching are only affected by the<br />program you want to analyse.<br />Race conditions are also much easier to exploit if priority of the victim<br />can be lowered (=CPU clock speed does not increase under load thanks to<br />power saving) and the attacker has a CPU for himself (with high CPU clock).<br />For CPUs with hyperthreading, a helper program can be put on the second<br />virtual processor (victim is first virtual processor) that further<br />decreases the victim's performance by using CPU and causes L2 cache hit<br />rate to go down.<br /></div></blockquote><br />CAP_SYS_RESOURCE<br />CAP_SYS_TIME<br />CAP_LEASE<br />CAP_AUDIT_WRITE<br />CAP_MAC_OVERRIDE<br />CAP_MAC_ADMIN (might be able to exploit the policy loading code of the MAC system since it will likely assume it to be the highest level of trusted communication)<br />CAP_SYSLOG</p>
<p>I'll use a system of mine as illustration of what real-world applications this affects.  Here's a list of applications that need at least one of the root-equivalent capabilities after startup (even more would need them prior to that point, CAP_NET_BIND_SERVICE especially):</p>
<p>rsyslogd/syslogd (CAP_SYS_ADMIN, CAP_DAC_OVERRIDE, CAP_DAC_READ_SEARCH)<br />cron (CAP_SETUID, CAP_SETGID)<br />login (CAP_SETUID, CAP_SETGID, CAP_FSETID, CAP_CHOWN)<br />cvs (CAP_DAC_OVERRIDE, CAP_SETUID, CAP_FSETID)<br />postfix (CAP_DAC_OVERRIDE, CAP_KILL, CAP_SETUID, CAP_SETGID, CAP_SYS_CHROOT)<br />apache (CAP_KILL, CAP_SETUID, CAP_SETGID)<br />sshd (CAP_KILL, CAP_SYS_TTY_CONFIG, CAP_SETUID, CAP_SETGID, CAP_CHOWN)<br />xinetd (CAP_SETUID, CAP_SETGID)<br />procmail (CAP_SETUID, CAP_SETGID, CAP_DAC_OVERRIDE)</p>
<p>Perhaps surprisingly (at least until we enumerated the root-equivalent capabilities above) this is also the same exact list of applications on my system that require any capabilities at all after startup, excluding applications only run temporarily as an administrator.  In other words, on my system <span style="text-decoration: underline">100% of system services that require any capabilities at runtime operate at full-root equivalence, despite their current or any potential future use of privilege dropping via capabilities</span>.  Note however that the full-root equivalent component is not necessarily exposed to the network (as in the case of proper privilege separation).</p>
<p>So, what does this all have to do with PaX, interpreters, and arbitrary code execution on the filesystem?  Withstanding a bug in the privileged code that would, for instance, allow the mknod()ing of arbitrary devices, none of the things listed above are operations performed normally by privileged applications.  In order to perform these (sometimes complex) actions, the flow of execution needs to be diverted and controlled.  Access control systems strive to remove privilege/capabilities from all programs except those that require it.  Traditionally, they had only been interested in certain interactions with the system, but not with the *cause* of those interactions.  Thus, for an app that needed to exec /bin/sh for some controlled operation, the access control system would see no problem with also allowing it to exec a root shell.  The addition of PaX and the concept of removing arbitrary code execution from processes (and in the future, extending this process further with other techniques) has changed all this in a significant way that, to my knowledge, hasn't been properly articulated elsewhere.</p>
<p>Under a traditional access control system, compromising an application immediately leads to being able to easily and artfully weave the privileges of that process into something usable.  We rarely get nice, simple bugs with no constraints.  Those constraints can make the bug unexploitable or can introduce limitations/requirements on the exploitation end.  These are the constraints just imposed by the bug/application itself, not even considering the constraints imposed by the protections of the system.  It's easy to think about chaining a kernel exploit when the address space of a process is your virtual playground, but what would you do without arbitrary code execution in combination with strict access control, randomized address space layout, and some form of fptr protection/control flow integrity?  What if many of these protections were also present in the kernel you're trying to attack?  How often will such an attack be possible under bug/application constraints?  I'm not sure many have thought/had to think about this.</p>
<p>I get excited every time I see a conference add requirements to their talk selection along the lines of &quot;exploitation presentations must be against grsecurity/PaX&quot; -- but then there never ends up being any presentations of this kind.  Is it just the case that there aren't that many sgrakkyu/twizzes in the world?  Or is everyone too busy with &quot;real work&quot; that &quot;makes money&quot; to publish work in this area? ;)</p>
<p>Reply/email with comments and/or corrections (especially from stealth ;)).</p>
<p>-Brad</p>
					</div><!-- .panel -->
				</div><!-- .wrap -->
			</section>
		</article>
	</main>

    <footer class="colophon" role="contentinfo">
            <div class="upper-footer wrap">
                <a class="back-to-top" href="#top">Back to Top</a>

                <div class="col-1-3 first about">
                    <h4>About grsecurity</h4>
                    <p>grsecurity® is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.</p>
                </div>
                <div class="col-1-3 quick-links">
                    <div class="upper-footer-inner-wrap">
                        <h4>Quick Links</h4>
                        <nav class="footer-menu">
                            <ul>
                               <li><a href="index.php">Home</a></li>
                                <li><a href="features.php">Features</a></li>
                                <li><a href="support.php">Support</a></li>
                                <li><a href="papers.php">Papers</a></li>
                                <li><a href="blog.php">Blog</a></li>
                                <li><a href="download.php">Download</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-1-3 last contact">
                    <div class="upper-footer-inner-wrap">
                        <h4>Get in Touch</h4>
                        <span class="phone"><a href="tel:949-424-7732">949-424-7732</a></span>
                        <span class="email"><a href="mailto:contact@grsecurity.net">contact@grsecurity.net</a></span>
                    </div>
                </div>
            </div>
            <hr>
            <div class="lower-footer wrap">
                <div class="fine-print">
                    <a href="trademark_policy.php"><strong>Trademark Policy</strong></a>
                </div>
                <span class="text">&copy; Open Source Security, Inc 2013-2017.</span><br>
		<span class="text">grsecurity is a registered trademark of Open Source Security, Inc.</span>
		<span class="text">Linux is the registered trademark of Linus Torvalds.</span>

            </div><!-- .lower-footer -->

    </footer><!-- .colophon -->

<script src="/js/webfont.js"></script>
<script src="/js/webfont_load.js"></script>

<script type='text/javascript' src='/js/script.js'></script>

</body>
</html>
