<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity - Features</title>


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

	<main class="features-page">
		<article>
			<header class="masthead">
				<div class="wrap">
					<h1 class="large-headline">Features</h1>

					<ul class="tabs">
						<li class="tab-link current" data-tab="memory-corruption-defense">Memory Corruption Defenses</li>
						<li class="tab-link" data-tab="filesystem-hardening">Filesystem Hardening</li>
						<li class="tab-link" data-tab="misc-protections">Miscellaneous Protections</li>
						<li class="tab-link" data-tab="rbac">RBAC</li>
						<li class="tab-link" data-tab="gcc-plugins">GCC Plugins</li>
					</ul>
				</div>
			</header>

			<section class="bar bar-tabs">
				<ul class="tab-content-wrap">
					<li id="memory-corruption-defense" class="tab-content current">
						<h2 class="bar-title">Memory Corruption Defenses</h2>

						<div class="chart-wrap">
							<div class="wrap">
								<div class="col-1-3 first">
									<h3>Highest performance and most secure ROP defense</h3>

									<p>The result of over four years of research and development,  RAP is grsecurity's complete defense against ROP and all 
									other code reuse attacks. No other technology today comes 
									close to its levels of security, performance, and ability to scale to arbitrary codebase sizes, as evidenced by its use in 
									grsecurity kernels and hardened versions of Chromium.</p>

									<a href="rap_faq.php" class="fancy-link">FAQ About RAP</a><br>
									<a href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Prevent_code_reuse_attacks" class="fancy-link">Configure Help Entry</a>
								</div>

								<div class="col-2-3 last">
									<img src="/rap-benchmark.svg" alt="RAP Benchmark" class="chart">
								</div>
							</div><!-- .wrap -->
						</div><!-- .chart-wrap -->

						<section class="bar">
							<div class="wrap">
								<div class="excerpts">
									<article class="excerpt">
										<h2 class="excerpt-title">Industry-leading ASLR</h2>

										<p>Grsecurity has led the way over the years in providing a 
										proper ASLR implementation that deals with the many ways 
										in which an attacker can influence ASLR or defeat it through 
										system-provided information leaks and entropy reduction. In 
										addition, the number of bits of entropy applied to 
										randomization of each memory region is significantly higher 
										in grsecurity compared to upstream's weaker ASLR 
										implementation.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://forums.grsecurity.net/viewtopic.php?f=7&t=2574">grsecurity unaffected by upstream ASLR weaknesses</a>
											<a class="fancy-link" href="http://eindbazen.net/2012/02/codegate-2012-vuln-500/">2012 blog post about an upstream ASLR disabling technique fixed in 2016</a>
											<a class="fancy-link" href="https://labs.mwrinfosecurity.com/blog/2010/09/02/assessing-the-tux-strength-part-2---into-the-kernel/">Report by MWR Labs</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Harden_ASLR_against_information_leaks_and_entropy_reduction">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Bounds checks on kernel copies to/from userland</h2>

										<p>This feature hardens the functions the Linux kernel uses to copy data to and from user applications. It ensures copies to/from a heap object don't exceed the object's size and that stack copies don't exceed the size of the stack frame. It further prevents modifying or leaking sensitive kernel objects via these functions.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://git.kernel.org/cgit/linux/kernel/git/torvalds/linux.git/commit/?id=42da2f948d949efd0111309f5827bf0298bcc9a4">Large heap infoleak discovered by USERCOPY</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Harden_heap_object_copies_between_kernel_and_userland">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents direct userland access by kernel</h2>

										<p>Through PaX's UDEREF feature, grsecurity forces any userland data access to go through an approved accessor. This prevents exploitation of an entire class of vulnerabilities that includes null pointer dereferences and dereferences of magic values that point into userland (e.g. 0xAAAAAAAA on 32-bit systems).  This feature is provided for x86, x64, and ARM, even on systems without SMAP or PAN support.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://www.nvidia.com/download/driverResults.aspx/73666/en-us">NVIDIA vuln found by UDEREF</a>
											<a class="fancy-link" href="http://git.net/ml/linux.kernel.grsecurity/2006-08/msg00006.html">Vuln in 3rd-party kernel patch found by UDEREF</a>
											<a class="fancy-link" href="https://grsecurity.net/~spender/uderef.txt">i386 UDEREF design</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Prevent_invalid_userland_pointer_dereference">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents userland code execution by kernel</h2>

										<p>PaX's KERNEXEC feature effectively prevents the kernel from executing code in userland through memory corruption.  This feature is provided for x86, x64, and ARM, even on processors that don't support SMEP or PXN."</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Enforce_non-executable_kernel_pages">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents kernel stack overflows on x64</h2>

										<p>While vulnerabilities arising through the improper use of variable-length-arrays (VLAs) and runtime stack allocation are handled automatically with a GCC plugin, grsecurity also provides a feature to prevent exploitation arising from other sources of kernel stack overflows: deep nesting and recursion. On a mainline Linux kernel, a kernel task is free to overflow its stack into adjacent heap objects in order to escalate privilege. Grsecurity places kernel stacks non-contiguously in a separate memory region on 64-bit architectures to avoid any such abuse.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Prevent_kernel_stack_overflows">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Hardened userland memory permissions</h2>

										<p>Though mainline Linux now supports NX and a weaker ASLR, by default it does nothing to prevent the introduction of malicious code into a process. While initial control flow hijacking may occur through ROP, the pattern consistently seen on Windows and other OSes is that the majority of the exploit's payload is performed within allocated RWX memory. Grsecurity eliminates this weakness by default, greatly driving up the costs of exploitation and raising the bar above the capabilities of most attackers.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Restrict_mprotect.28.29">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Random padding between thread stacks</h2>

										<p>Linux distros generally do not compile code with the -fstack-check flag to GCC, making it possible to exploit incorrectly-sized calls to alloca(). By taking advantage of pthread's behavior of allocating quickly-created thread stacks adjacent to each other, the stack of another thread can be reliably modified to achieve exploitation. Randomizing the offset between thread stacks removes the reliability of this technique, generally reducing the exploit to a crash.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://downloads.asterisk.org/pub/security/AST-2012-014.html">Asterisk Security Advisory AST-2012-014</a>
											<a class="fancy-link" href="http://blog.exodusintel.com/2013/01/07/who-was-phone/">Exodus Intelligence remotely exploiting Asterisk vulnerability</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Insert_random_gaps_between_thread_stacks">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Hardened BPF JIT against spray attacks</h2>

										<p>The Linux kernel contains functionality that allows it to generate machine code at runtime to speed up packet filtering and SECCOMP rules. This functionality can be abused by attackers as they are able to both pre-determine the contents of the generated machine code and also fully control certain arbitrary values within that content that permit them to execute arbitrary code through an unintended instruction sequence. Grsecurity uses a technique called "constant blinding" to prevent an attacker from having enough control over the generated machine code to launch a successful attack. Unlike upstream's attempts at resolving this problem, our solution is resistent to leaks of the location and contents of the JIT-generated code.</p>
										<p>In the default, JIT-disabled mode, grsecurity also protects the execution environment against a corrupted interpreter buffer.</p>
										<p>Finally, the use of RAP will prevent JIT spray attacks in general by ensuring that no functions can call, jump, or return to anywhere in the middle of a JIT-compiled BPF filter.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://mainisusuallyafunction.blogspot.com/2012/11/attacking-hardened-linux-systems-with.html">Blog demonstrating BPF JIT spray attack</a>
											<a class="fancy-link" href="https://github.com/01org/jit-spray-poc-for-ksp">Attack against upstream's weak BPF JIT spray defense</a>
											<a class="fancy-link" href="http://www.matasano.com/research/Attacking_Clientside_JIT_Compilers_Paper.pdf">Chris Rolf's paper on attacks on JIT compilers</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Harden_BPF_JIT_against_spray_attacks">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Automatically responds to exploit bruteforcing</h2>

										<p> Even if all system-level infoleak sources and methods of entropy reduction are closed down, there remains the fact that a Linux system is generally unable to prevent bruteforcing of arbitrary network services and suid/sgid binaries. Grsecurity solves this issue by forcing a delay between forks of network services being bruteforced and bans users from executing suid/sgid apps for a period of time if they cause one to crash. Grsecurity takes a similar approach to preventing repeated attempts at exploiting kernel vulnerabilities. After the first detected attempt causing an OOPS message, grsecurity bans that unprivileged user from the system until restart.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Deter_exploit_bruteforcing">Configure Help Entry</a>
										</div>
									</article>

								</div><!-- .excerpts -->
							</div><!-- .wrap -->
						</section>
					</li>
					<li id="filesystem-hardening" class="tab-content">
						<h2 class="bar-title">Filesystem Hardening</h2>

						<section class="bar bar-gray">
							<div class="wrap">
								<div class="excerpts">
									<article class="excerpt">
										<h2 class="excerpt-title">Chroot hardening</h2>

										<p>grsecurity's chroot hardening automatically converts all uses of chroot into real jails with confinement levels equivalent to containers. Processes inside a chroot will not be able to create suid/sgid binaries, see or attack processes outside the chroot jail, mount filesystems, use sensitive capabilities, or modify UNIX domain sockets or shared memory created outside the chroot jail.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Chroot_jail_restrictions">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents users from tricking Apache into accessing other users' files</h2>

										<p>If Apache is configured to allow following of symlinks, it is trivial in most webhosting configurations to force it to reveal sensitive data from other users' webroots. While Apache has a feature that aims to mitigate this risk, it suffers from an unsolvable Time-Of-Check/Time-Of-Use (TOCTOU) race condition. Grsecurity solves this problem by enforcing at the kernel-level that Apache can't follow symlinks owned by one user but pointing to the files of a different user.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Kernel-enforced_SymlinksIfOwnerMatch">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Eliminates side-channel attacks against admin terminals</h2>

										<p>Demonstrating our ability to swiftly respond to new threats, this feature was developed the same day as Vladz' report on a side-channel attack against the /dev/ptmx device. While we immediately handled a more generalized form of the attack, as of over a year later, upstream Linux has still failed to prevent one of the two attack vectors explicitly listed in the original report.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://vladz.devzero.fr/013_ptmx-timing.php">Article on timing attack</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Eliminate_stat.2Fnotify-based_device_sidechannels">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Provides Trusted Path Execution</h2>

										<p>Trusted Path Execution (TPE) is an old and simple concept. It dates back to at least 1998 with route's Phrack 62 article linked below. The goal of TPE is to provide an easily-configurable and generally software compatible method of preventing unprivileged users from executing binaries they create. Grsecurity extends the idea of TPE a bit and resolves some vulnerabilities in the original design in the process (for instance, TPE is not bypassed via ld.so under grsecurity).</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://www.phrack.org/issues.html?issue=52&id=6#article">Initial 1998 Phrack article</a>
											<a class="fancy-link" href="http://www.phrack.org/issues.html?issue=53&id=8">Follow-up Phrack article</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Trusted_Path_Execution_.28TPE.29">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Hide other users' processes for unprivileged users</h2>

										<p>While the upstream kernel now provides a mount option for /proc to hide other unprivileged users' processes, grsecurity goes beyond this by hiding such information by default, hiding additional sources of sensitive information provided by the kernel in /proc, and hiding private network-related information of all users. Not only is the networking information a violation of the privacy of other users on the system, but it has also been useful in the past for TCP hijacking attacks.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://lwn.net/Articles/531090/">Article on TCP hijacking attack made possible by /proc/net counters</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Proc_restrictions">Configure Help Entry</a>
										</div>
									</article>

								</div><!-- .excerpts -->
							</div><!-- .wrap -->
						</section>
					</li>
					<li id="misc-protections" class="tab-content">
						<h2 class="bar-title">Miscellaneous Protections</h2>

						<section class="bar bar-with-pattern">
							<div class="wrap">
								<div class="excerpts">
									<article class="excerpt">
										<h2 class="excerpt-title">Prevents ptrace-based process snooping</h2>

										<p>This feature was introduced to deal with ptrace-based userland rootkits and other malicious process hijacking. Importantly, it preserves the ability of a user to debug his/her own programs through a novel implementation that enforces a process can only attach to its children.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Deter_ptrace-based_process_snooping">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents attackers from auto-loading vulnerable kernel modules</h2>

										<p>Your webhosting server has no need for a protocol used only in cars, or one for HAM radios, but your distro's kernel configuration likely causes modules for these to be built -- useful only for exploiting your system. While Linux distros continue to take a reactive approach (via blacklisting) to vulnerable, rarely-legitimately used modules like these, grsecurity uses a proactive approach that prevents unprivileged users from auto-loading kernel modules. The below list of example exploits for vulnerable and rarely used kernel modules is far from exhaustive, but is provided to serve as demonstration.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://www.vsecurity.com/download/tools/linux-rds-exploit.c">Exploit for rarely-used RDS module</a>
											<a class="fancy-link" href="http://downloads.securityfocus.com/vulnerabilities/exploits/36038-5.c">Exploit for Bluetooth and Appletalk modules</a>
											<a class="fancy-link" href="http://www.exploit-db.com/exploits/926/">Older Bluetooth module exploit</a>
											<a class="fancy-link" href="http://kernelbof.blogspot.com/2009/04/kernel-memory-corruptions-are-not-just.html">SCTP module exploit</a>
											<a class="fancy-link" href="https://grsecurity.net/~spender/sctp.c">PoC for another SCTP module vulnerability</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Harden_module_auto-loading">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents dumping unreadable binaries</h2>

										<p>On a normal distro kernel, it's not possible to allow a user to execute a program without also giving away the full contents of the program's binary image. While direct reads are denied, a user can ptrace themselves and then execute the binary, using ptrace to extract out the entire mapped contents of the binary image -- even if that binary is setuid root. This information leak can be useful in creating reliable exploits against custom-compiled binaries. This weakness was abused by Jason Donenfeld, for example, in his exploit for the /proc/pid/mem kernel vulnerability.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://blog.zx2c4.com/749">Blog post on CVE-2012-0056</a>
											<a class="fancy-link" href="http://git.zx2c4.com/CVE-2012-0056/tree/mempodipper.c">Exploit for CVE-2012-0056</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Require_read_access_to_ptrace_sensitive_binaries">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Enforces consistent multithreaded privileges</h2>

										<p>Though glibc wraps calls to setuid() and setgid() with magic signals that cause other threads in a process to change their credentials as well, other libcs and multithreaded applications in other languages do not do this, leading to unexpected vulnerable results of a thread running as root that the developers believe is running unprivileged. Since it's also conceptually wrong for threads sharing the same address space to be running with radically different privilege, grsecurity enforces glibc's behavior at the kernel level despite what language or libc is involved in userland.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://code.google.com/p/go/issues/detail?id=1435">Google Go and multithreaded credentials</a>
											<a class="fancy-link" href="https://bugs.launchpad.net/qemu/+bug/807893">QEMU privilege escalation vuln caused by multithreaded credentials</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Enforce_consistent_multithreaded_privileges">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Denies access to overly-permissive IPC objects</h2>

										<p>This feature was developed in response to research done by Portcullis Labs who surveyed use of shared memory in Linux software with surprising results -- many were unnecessarily granting all users on the system the ability to read and or write their created shared memory. Since in many cases this can result in security vulnerabilities, grsecurity locks down access to overly-permissive shared memory and other IPC objects in such a way that does not impact normal operations.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://labs.portcullis.co.uk/whitepapers/memory-squatting-attacks-on-system-v-shared-memory/">Portcullis Labs research</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Disallow_access_to_overly-permissive_IPC_objects">Configure Help Entry</a>
										</div>
									</article>

								</div><!-- .excerpts -->
							</div><!-- .wrap -->
						</section>
					</li>
					<li id="rbac" class="tab-content">
						<h2 class="bar-title">RBAC</h2>

						<section class="bar bar-gray">
							<div class="wrap">
								<div class="excerpts">
									<article class="excerpt">
										<h2 class="excerpt-title">Automatic full system policy learning</h2>

										<p>Grsecurity's RBAC has provided the very first learning system that can automatically generate least-privilege full system policies without manual configuration. While the default learning heuristics will provide secure results for most users while predicting future access needs, it also supports a simple human-readable configuration file to drive the policy generation. Have a directory specific to your system that you wish to ensure is protected by policy? A single line in the configuration file will create a security boundary around any process that reads or writes to files in that directory. Users will find that in most cases, full system learning will produce a more secure policy than one created by hand.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="http://secgroup.ext.dsi.unive.it/wp-content/uploads/2012/04/PID2308633-camera.pdf">Academic analysis of security properties of grsecurity's auto-learned policies</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Human-readable policies and logs</h2>

										<p>If you've ever developed SELinux policies, grsecurity's RBAC policies will be a breath of fresh air. Our policies are similar in appearance to those of AppArmor, though more intuitive. Logs display full paths for the violating process and its parent and describes the nature of the violation in an easily-understandable way. You won't need to be an expert on system call names and rummage through logs stuffed into the same restrictive template to determine the reason for a policy violation in grsecurity's RBAC system.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/The_RBAC_System#Policy_Structure">Excerpt of a grsecurity RBAC policy</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Intuitive design</h2>

										<p>The organization of grsecurity's RBAC policies makes intuitive sense. Roles apply to users or groups (with allowance for "special" roles that can be entered with or without authentication). These roles contain a collection of subjects which describe policies for binaries and scripts on the system. Subjects contain a collection of objects, which are the files, capabilities, network sockets, and resources a process is permitted to use. Combined with the human-readable policies, many users find they are able to jump right in to creating meaningful security policies either through full system learning or by using full system learning as a starting example policy.</p>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Automated policy analysis</h2>

										<p>Unlike other access control systems, grsecurity's RBAC was not designed to be an all-permissive framework -- it has the specific purpose of locking down access to the entire system. Because it has a specific goal, it allows us to implement mandatory policy analysis that catches administrator mistakes and prevents an administrator from deploying a policy that would provide a false sense of security. Any errors found in the policy are described with human-readable, meaningful explanations of what kinds of attacks would be possible if the policy were allowed to be loaded (as it would be in other access control systems).</p>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Unconventional features</h2>

										<p>Grsecurity does not use LSM and thus is not constrained by the set of hooks LSM provides. With this freedom it is able to implement a number of unconventional features not possible in any LSM. Grsecurity allows overriding and auto-learning of resource limits on a per-subject basis, provides per-subject limits on the number of times a service can crash in a given time interval, can limit access to roles by IP address, tags policy violation logs with the IP address of the originator of the violation, provides mandatory control over per-subject PaX flags, supports policies on individual scripts run directly, and many more features not available elsewhere.</p>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Stackable with LSM</h2>

										<p>As grsecurity has never used LSM, it does not suffer from a major problem that LSM has been unable to resolve in over 15 years: it does not permit multiple LSMs to be enabled at the same time. Thus while SELinux's default policies cannot be used in conjunction with AppArmor, grsecurity can be used in combination with SELinux, AppArmor, or any other LSM if such a requirement exists.</p>
									</article>
								</div><!-- .excerpts -->
							</div><!-- .wrap -->
						</section>
					</li>
					<li id="gcc-plugins" class="tab-content">
						<h2 class="bar-title">GCC Plugins</h2>

						<section class="bar bar-with-alt-pattern">
							<div class="wrap">
								<div class="excerpts">
									<article class="excerpt">
										<h2 class="excerpt-title">Randomized kernel structure layouts</h2>

										<p>The RANDSTRUCT plugin forces some exploits into requiring additional information leaks to achieve reliability. It does this by randomizing the layout of sensitive selected kernel structures as well as automatically randomizing the layout of all structures comprised purely of function pointers -- a common target for exploits.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Randomize_layout_of_sensitive_kernel_structures">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents integer overflows in size arguments</h2>

										<p>Integer overflows are a common bug class in the kernel. This feature was first realized as a clever macro to deal with overflows occuring in size expressions of kernel memory allocators, but was replaced and greatly expanded in scope by Emese Revfy with what is now our largest GCC plugin. This powerful plugin can detect and prevent exploitation of a wide range of integer overflow and integer truncation bugs that are likely to result in exploitable conditions.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://grsecurity.net/~spender/i915_size_overflow.txt">SIZE_OVERFLOW plugin preventing exploitation of $40,000 Pwnium 3 vulnerability</a>
											<a class="fancy-link" href="https://forums.grsecurity.net/viewtopic.php?f=7&t=3043">Blog post on the SIZE_OVERFLOW plugin</a>
											<a class="fancy-link" href="http://www.openwall.com/lists/oss-security/2013/03/11/8">CVE-2013-0914 discovered by plugin</a>
											<a class="fancy-link" href="http://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE-2013-2141">CVE-2013-2141 discovered by plugin</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Prevent_various_integer_overflows_in_function_size_parameters">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Adds entropy at early boot and runtime</h2>

										<p>Grsecurity provides a feature to help address the problem of entropy starvation at early boot on IoT devices.  The demonstrable effects of entropy starvation at early boot are weak private keys, predictable stack canaries, and more.  PaX's latent entropy plugin modifies the instruction streams of functions called only during boot as well as some specific functions at runtime that perform some unpredictable computations from the perspective of an attacker. These functions are modified to produce a nondeterministic value based on the flow of execution. At several points in the boot process and at runtime, this pseudorandom value is mixed in to the entropy pool.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://grsecurity.net/pipermail/grsecurity/2012-July/001093.html">Initial plugin description</a>
											<a class="fancy-link" href="https://factorable.net/">Research on widespread weak keys in network devices</a>
											<a class="fancy-link" href="http://accepted.100871.net/jni.pdf">Research showing predictable stack canaries on Android due to low entropy</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Generate_some_entropy_during_boot_and_runtime">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Makes read-only sensitive kernel structures</h2>

										<p>For some years, grsecurity had maintained a large patch that systematically constified a few dozen structure types used frequently in the kernel -- collections of function pointers, or so-called "ops structures". This patch, weighing in at over 1MB, became time-consuming to maintain and upstream kernel developers did not accept the patch wholesale (responsive maintainers adopted patches specific to their domain, but many unresponsive maintainers did not). Creation of the CONSTIFY plugin allowed us to generalize the approach and apply it to hundreds of additional structure types, currently causing up to 75% of function pointers in the kernel image to be made read-only.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Automatically_constify_eligible_structures">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Ensures all kernel function pointers point into the kernel</h2>

										<p>This plugin supplements the protection provided by PaX's KERNEXEC feature on the X64 architecture. While on i386 KERNEXEC is able to prevent execution of userland code using segmentation, that is not possible on X64. This plugin forces the upper bit on in all function pointers and return addresses dereferenced by the kernel. This doesn't impact legitimate kernel code, but causes attempts to return to userland code to result in a non-canonical address that will cause a GPF upon access. Modulo attacks on kernel paging structures, this feature is not needed if PaX's UDEREF is enabled.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://lwn.net/Articles/461811/">Plugin discussion</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Return_Address_Instrumentation_Method">Configure Help Entry</a>
										</div>
									</article>

									<article class="excerpt">
										<h2 class="excerpt-title">Prevents leaking of stack data from previous syscalls</h2>

										<p>Information leaks of kernel stack data are the most common type of leaks in the kernel and potentially the most dangerous. The reason for this is that the data arising from an uninitialized structure field can possibly come from data on the kernel stack from a previous system call invocation. By carefully choosing a system call to leave stale data on the stack, an attacker can leak the addresses to a wide number of sensitive kernel structures. The STACKLEAK plugin addresses this issue by clearing the portion of the kernel stack that was used during a system call before returning to userland. This ensures that if any leak of an uninitialized field is possible, it must come from a previously-called function in the current system call. This plugin also prevents dynamic stack-based allocation in the kernel from overflowing the kernel stack, which would normally permit an attacker to corrupt an adjacent memory object in order to exploit the system.</p>

										<div class="excerpt-footer">
											<a class="fancy-link" href="https://pax.grsecurity.net/docs/PaXTeam-H2HC13-PaX-gcc-plugins.pdf">PaX presentation on GCC plugins</a>
											<a class="fancy-link" href="https://en.wikibooks.org/wiki/Grsecurity/Appendix/Grsecurity_and_PaX_Configuration_Options#Sanitize_kernel_stack">Configure Help Entry</a>
										</div>
									</article>
								</div><!-- .wrap -->
							</div><!-- .wrap -->
						</section>
					</li>
				</ul>
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
