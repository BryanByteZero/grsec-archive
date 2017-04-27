<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity - Recent ARM Security Improvements</title>


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
					<h1 class="large-headline">Recent ARM Security Improvements</h1>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<h3>Introduction</h3>
<p>If you've been following my @grsecurity Twitter account during December and January, then you've been able to see some brief descriptions/logs from my work in ARM LPAE (Large Physical Address Extension), PXN (Privileged Execute-Never), and porting of PaX's KERNEXEC and UDEREF features to ARMv6+ while providing a level of security equivalent to KERNEXEC/UDEREF on i386.</p>
<p>For those not following me on Twitter or not familiar with the above work, I've fully removed writable and executable memory from the kernel and disabled the kernel's ability to accidentally or maliciously access userland memory directly for any purpose.  This provides numerous kernel self-protection benefits in line with other features of PaX and grsecurity and significantly raises the bar for reliable kernel exploitation.</p>
<p>The code is currently available for free in PaX and will be included soon in grsecurity as soon as I complete my 3.8 port. If you're interested in the low level details for curiosity or for copy+pasting into a DARPA CFT report, then read on!</p>
<h3>Recommended reading</h3>
<p>ARM Architecture Reference Manual (ARMv7-A and ARMv7-R edition) 24 July 2012<br /><a href="http://infocenter.arm.com/help/topic/com.arm.doc.ddi0406c/index.html">http://infocenter.arm.com/help/topic/com.arm.doc.ddi0406c/index.html</a><br />(requires registration)<br /> - Application Level Memory Model (A3)<br /> - Virtual Memory System Architecture (VMSA) (B3)<br />  - Short-descriptor translation table format (B3.5)<br />  - Long-descriptor translation table format (B3.6)<br />  - Memory access control (B3.7)<br />  - VMSA memory aborts (B3.12)<br />  - VMSAv7 MMU fault terminology (B3.12.2)<br />    (I use both wordings where appropriate)</p>
<p>H2HC 2012: PaX - Kernel Self-Protection<br /><a href="http://pax.grsecurity.net/docs/PaXTeam-H2HC12-PaX-kernel-self-protection.pdf">http://pax.grsecurity.net/docs/PaXTeam-H2HC12-PaX-kernel-self-protection.pdf</a><br />PaX UDEREF writeup by the PaX Team<br /><a href="http://grsecurity.net/~spender/uderef.txt">http://grsecurity.net/~spender/uderef.txt</a></p>
<p>We'll start with a general architecture-agnostic description of PaX's KERNEXEC and UDEREF features.</p>
<h3>KERNEXEC Introduction</h3>
<p>Though KERNEXEC can naively be thought of as &quot;PAGEEXEC, but for the kernel&quot; -- it involves quite a bit more than this, both conceptually and implementation-wise.  KERNEXEC provides proper write/execute memory separation which also naturally provides for hardware-enforced read-only data.  One interesting addition from KERNEXEC comes from recognizing that many function pointers and other sensitive data only need to be written to prior to init executing.  KERNEXEC provides a __read_only attribute to be used on data objects that causes them to be located in a special section.  This section will be read/write while the kernel initializes and will have its protection changed to read-only before init executes.  This protects these sensitive objects from accidental or malicious direct modification.</p>
<p>As the main goal of KERNEXEC is to prevent the execution of arbitrary code in kernel mode, the ability to execute arbitrary code located in userland obviously violates this.  Thus, KERNEXEC needs to at least be able to prevent such execution in kernel context.  This requirement can be fulfilled in a number of ways.  On architectures like SPARC64, this comes for free.  On newer Intel processors, there's SMEP, and on newer ARM there's PXN.  Without specially-designed hardware support, we have to get a bit more clever.  KERNEXEC on amd64 without SMEP provides a best-effort for this by enforcing the MSB to be set in saved instruction pointers on every 'ret' instruction or in any function pointer use.  If an attacker were to modify the saved instruction pointer or function pointer to point to userland, the MSB being set would cause the address to be non-canonical (amd64 has a 48-bit virtual address space, not 64-bit as commonly mistaken by armchair ASLR optimists), in turn causing a fault that will be caught by PaX.  Some UDEREF implementations are also likely to meet this goal.  As the purpose of UDEREF is to prevent accidental/malicious userland data dereference, you can imagine that preventing translation of userland addresses, for instance, would handle both the cases of instruction fetching and data reads/writes.</p>
<h3>UDEREF Introduction</h3>
<p>UDEREF is much simpler conceptually, given its above mentioned purpose. Difficulty of implementation again varies highly based on the targeted processor.  On SPARC64 we again achieve the UDEREF goal for free. Intel's Haswell line will introduce SMAP, which (if used properly by the kernel) will prevent accidental/malicious direct userland reads/writes.  ARM, as of yet, has no hardware-based UDEREF equivalent.  On amd64 currently, PaX implements a best-effort UDEREF by remapping the userland address space to a non-executable &quot;shadow&quot; region.  This may seem like a horribly expensive operation, but it's made workable by reducing the address space to the point that the entire userland address range can be moved to the shadow region by changing just eight 4th-level page table entries (a cache line's worth).  You'll see later how UDEREF is achieved on ARM without specific hardware support.</p>
<p>Now that the basic concepts are established, there are some issues that arise when creating a basic KERNEXEC implementation that deserve discussion.  There are several occasions where some code or read-only data (perhaps some that we specially protected above) needs to be temporarily modified.  What PaX does is enter a state where it can write to read-only kernel memory.  This is done with a call to pax_open_kernel() prior to the modification, following up with a call to pax_close_kernel().  There exist two problems with this: kernel preemption and interrupts.  Kernel preemption is easy enough to handle without significant performance impact: simply disable it around the protected region.  Disabling interrupts, on the other hand, would cause undesirable performance/latency impact; a different solution is needed here.  KERNEXEC saves the open/close state on any interrupt occurring in kernel mode and then forcibly &quot;closes&quot; the potentially &quot;open&quot; kernel -- removing the ability to modify read-only memory. On interrupt return, the saved state is restored.  In this way, performance/latency is preserved, and we don't give interrupt handlers undue access to read-only memory.</p>
<p>UDEREF works similar to the above, but &quot;opens&quot; and &quot;closes&quot; access to the userland address space within the kernel API for legitimately accessing userland: functions like copy_to_user, copy_from_user, put_user, strnlen_from_user, etc.  On interrupts, access to userland is likewise disabled.</p>
<p>With the generic explanation above and some of the referenced ARM reading, you should hopefully be able to understand what follows ;) I've tried to strike a balance between concision and technical accuracy. If you see an acronym you don't understand/recognize, a search through the ARM reference manual will help; I've highlighted the most useful sections for understanding.</p>
<h3>ARM KERNEXEC implementation</h3>
<p>To implement KERNEXEC, two additional memory types were created: MT_MEMORY_RW and MT_MEMORY_RX.  Memory types (see arch/arm/mm/mmu.c) define different uses for memory and assign to that use a set of permissions for each level of page tables.  Not supporting a pure read (without execute) memory type allows us to merge code and read-only data and reduce memory waste due to section (a 1MB &quot;page&quot;) alignment required for efficient TLB usage.  The MT_MEMORY type was renamed to MT_MEMORY_RWX to make its usage more explicit and to catch new insecure kernel mappings at compile time during forward porting.</p>
<p>To implement pax_open_kernel()/pax_close_kernel() we make use of ARM's support for domains.  Thus, to be able to use kernel modules or make use of certain features that require open/close, LPAE must currently be disabled (see the future work section for more information about this). Of note is that domain support is disabled (see CONFIG_CPU_USE_DOMAINS) in the upstream kernel for recent ARM processors (those supporting VMSAv7) due to issues with speculative fetching of device memory. Linux was using domains in the first place to handle set_fs(KERNEL_DS) as it was adding some additional hardware-enforcement to user-&gt;kernel copies.  The kernel would use ldrt and strt, variants of the load and store instructions which act as if they were executed in unprivileged mode.  This would thus prevent a copy_from_user at the hardware level from being able to use a kernel address as its source.  While this additional hardware-enforcement is somewhat unnecessary for copy_from_user, it would however be useful for __copy_from_user, which omits the access_ok() check for performance reasons.  When in KERNEL_DS mode, using ldrt/sdrt is obviously a problem, since instead of doing user-&gt;kernel copies, kernel-&gt;kernel copies are done and unprivileged loads/stores in this mode would trigger immediate faults.  So when using domain support, the upstream kernel at set_fs(KERNEL_DS) time will switch DOMAIN_KERNEL to DOMAIN_MANAGER, disabling any access checks on all kernel memory.  If an attacker could control a source or destination for one of these copies, he/she could modify read-only kernel code or data.</p>
<p>Allowing kernel-&gt;kernel copy*user to modify read-only memory is clearly not ideal security-wise, so we did away with upstream's use of domains while retaining and (ab)using the existing domain infrastructure.  Since pax_open_kernel()/pax_close_kernel() are used around small blocks of code, sometimes even single lines, the risk of speculative fetches from device memory is minimized/eliminated.  The value of DOMAIN_MANAGER is changed to alias that of DOMAIN_CLIENT and a new DOMAIN_KERNEXEC define is created to match the old DOMAIN_MANAGER value.  Thus we can be reasonably assured that only inside open/close can access checks be ignored on kernel memory.  To further ensure that we don't get into any inconsistent state, BUG_ON()s are inserted to make sure we're not already running with DOMAIN_KERNEL set to DOMAIN_KERNEXEC at open time and that DOMAIN_KERNEL isn't set to DOMAIN_CLIENT at close time. Kernel preemption and interrupts are handled as described in the architecture-agnostic introduction: the DACR value is saved on an interrupt and restored on return.  When re-entering the kernel via interrupt, DOMAIN_KERNEL is reset to DOMAIN_CLIENT, keeping interrupt handlers from having full control over kernel memory.</p>
<p>Two utilities were written to walk the hardware page tables to verify proper removal of pages of memory that were both writable and executable.  Not only are we interested in the immediate protections on a given page of memory, but on all virtual aliases to a given page of physical memory.  The vectors mapping (at 0xffff0000) is one example of such aliasing.  It is mapped as RX for userland, but is readable and writable by the kernel.  This makes it effectively operate as an RWX region of memory.  If this sounds strangely familiar, it should: this was exactly the same kind of situation with vsyscall on amd64 which sgrakkyu famously abused in a remote kernel exploit published in Phrack 64: <a href="http://www.phrack.org/issues.html?issue=64&amp;id=6#article">http://www.phrack.org/issues.html?issue=64&amp;id=6#article</a>.  As with the vsyscall technique, we killed the equivalent technique on ARM by making the kernel's alias of the vectors mapping read-only, requiring the proper pax_open_kernel()/pax_close_kernel() wrapping around writes.</p>
<p>The two tools mentioned above are available at:<br /><a href="http://grsecurity.net/~spender/kmaps-arm-v6.c">http://grsecurity.net/~spender/kmaps-arm-v6.c</a><br /><a href="http://grsecurity.net/~spender/kmaps-arm-lpae.c">http://grsecurity.net/~spender/kmaps-arm-lpae.c</a></p>
<p>During development of KERNEXEC for ARM LPAE, an interesting deficiency of the current upstream kernel was found: when setting up the different memory types, read-only kernel memory types used the (strangely-named) flag combination of: PMD_SECT_APX | PMD_SECT_AP_WRITE for marking sections.  The lack of this combination implied that the section would be writable.  It was found that on LPAE, these flags are defined to 0, and the long descriptor table format that LPAE uses requires a completely different, unimplemented flag.  This means that any kernel memory that was intended to be mapped as read-only on LPAE using sections would in fact be writable.  A flag named PMD_SECT_RDONLY was created to fix this deficiency.</p>
<p>We were also the first to implement PXN on Linux (on !ARM64 at least), both for LPAE and non-LPAE.  We started with LPAE, adding the proper bits to arm/include/asm/pgtable-3level-hwdef.h and making the bit a no-op in arm/include/asm/pgtable-2level-hwdef.h (the file used for !LPAE).  When PXN support was later added for non-LPAE mode (using the short descriptor format), we had to add in runtime detection for PXN via the MMFR0 register (see B4.1.89 of the ARM manual).  This allowed for pmd_populate() and protection_map to set the proper permissions at both page table levels.</p>
<p>An additional detail is the handling of executable ioremapped memory. Currently, the only user of this functionality is the SRAM management code specific to the OMAP platform.  During kernel initialization, a number of functions are copied to SRAM to be executed later.  All copies are performed through the fncpy() API, which is only used by the SRAM initialization code.  We modified the cached and noncached memory types used for executable ioremapping and made them explicitly RX (while renaming them to make this clear and avoid surprises during forward ports).  The fncpy() API was modified to wrap the function copies with the proper pax_open_kernel()/pax_close_kernel() calls.  A call to memset_io() to zero out the soon-to-be-overwritten region of SRAM was also wrapped.  These changes all happen during kernel initialization and thus do not impact runtime performance.</p>
<h3>ARM UDEREF implementation</h3>
<p>When on a system without PXN support, UDEREF can be used to fulfill an essential requirement of KERNEXEC: the inability to execute arbitrary code in kernel mode, regardless of whether the code executed by the kernel exists in the kernel itself or not.  This is similar to how UDEREF relates to KERNEXEC on amd64.</p>
<p>Since we use domain support to implement UDEREF (one of the reasons behind this is mentioned in the future work section) we currently require that LPAE be disabled.  On any entry to the kernel (this includes vector_swi, usr_entry, svc_entry, and inv_entry) we turn off access to userland by setting DOMAIN_USER to DOMAIN_NOACCESS in the DACR (domain access control) register.  The new DACR value is also updated in the cpu_domain field of the current thread_info struct (see arch/arm/include/asm/thread_info.h, domain.h) so that the proper DACR value can be restored in the event of a context switch.  On returning to userland, DOMAIN_USER is set back to DOMAIN_CLIENT, re-enabling normal access to userland memory so that userland code can execute properly.</p>
<p>The userland accessor functions have all been modified to wrap the access with calls to two new functions: pax_open_userland() and pax_close_userland().  Since we don't want to open userland when the accessor is being used to perform a kernel-&gt;kernel copy due to a previous call of set_fs(KERNEL_DS), we will only open up access to userland if get_fs() returns USER_DS.  Access is opened up by setting DOMAIN_USER to DOMAIN_CLIENT.  For forward-safety, all uses of DOMAIN_CLIENT for setting DOMAIN_USER were renamed to DOMAIN_USERCLIENT and aliased to the value of DOMAIN_NOACCESS.  DOMAIN_UDEREF was created to alias the original DOMAIN_CLIENT value.</p>
<p>Like with KERNEXEC, the current DACR value is saved to the stack on kernel re-entry so that, for example, if a copy to userland is interrupted, the DACR will be properly restored when returning to the userland copy so that it can complete successfully.</p>
<p>An important modification is the change of the vectors mapping to be labeled as DOMAIN_KERNEL instead of DOMAIN_USER.  Normal page permissions apply to the mapping, so userland can still access it, however without this change recursive faults would occur on any exception in kernel context when DOMAIN_USER was set to DOMAIN_NOACCESS.</p>
<h3>ASCII Illustration</h3><p>The following table illustrates domain changes at various points in time:</p>
<p><code>|------------------|----------------|----------------|-----------------|<br />|&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; | KERNEXEC-only&nbsp; | UDEREF-only&nbsp; &nbsp; | UDEREF/KERNEXEC |<br />|------------------|----------------|----------------|-----------------|<br />|&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; | USER&nbsp; | KERNEL | USER&nbsp; | KERNEL | USER&nbsp; &nbsp;| KERNEL |<br />|------------------|----------------|----------------|-----------------|<br />| User-&gt;kernel&nbsp; &nbsp; &nbsp;|&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp;1&nbsp; &nbsp; |&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp;|&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp; |<br />| via syscall&nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />|------------------|----------------|----------------|-----------------|<br />| Kernel -&gt; Kernel |&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp;1&nbsp; &nbsp; |&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp;|&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp; |<br />| via interrupt&nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;saves&nbsp; | saves&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; | saves&nbsp; &nbsp; saves&nbsp; |<br />|------------------|----------------|----------------|-----------------|<br />| Kernel -&gt; Kernel |&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp;saved&nbsp; | saved&nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp;| saved&nbsp; &nbsp; saved&nbsp; |<br />| interrupt return |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />|------------------|----------------|----------------|-----------------|<br />| Kernel -&gt; user&nbsp; &nbsp;|&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp;1&nbsp; &nbsp; |&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp;|&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp; |<br />| syscall return&nbsp; &nbsp;|&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />|------------------|----------------|----------------|-----------------|<br />| Within pax_open/ |&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp;3&nbsp; &nbsp; |&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp;|&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 3&nbsp; &nbsp; |<br />| close_kernel()&nbsp; &nbsp;|&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />|------------------|----------------|----------------|-----------------|<br />| In &quot;userland&quot;&nbsp; &nbsp; |&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp;1&nbsp; &nbsp; |&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp;|&nbsp; &nbsp;0&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp; |<br />| accessor&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />| KERNEL_DS&nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />|------------------|----------------|----------------|-----------------|<br />| In userland&nbsp; &nbsp; &nbsp; |&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp;1&nbsp; &nbsp; |&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp;|&nbsp; &nbsp;1&nbsp; &nbsp; &nbsp; &nbsp; 1&nbsp; &nbsp; |<br />| accessor&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />| USER_DS&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|<br />|------------------|----------------|----------------|-----------------|</code></p>
<p>Value legend:<br />0: DOMAIN_NOACCESS (unable to access)<br />1: DOMAIN_CLIENT (normal access checks)<br />2: (reserved by CPU)<br />3: DOMAIN_MANAGER (ignore access checks)<br />saved: DACR restored from saved value on stack<br />saves: saves old DACR value to stack</p>
<h3>Preliminary benchmarks</h3>
<p>NGINX Benchmark 1.0.11 from the Phoronix Test Suite was chosen as a general stress test.  From its description: &quot;This is a test of ab, which is the Apache Benchmark program running against nginx. This test profile measures how many requests per second a given system can sustain when carrying out 500,000 requests with 100 requests being carried out concurrently.&quot;</p>
<p>The results were:<br />3.7.1 with only grsec features enabled: 2861.77 Requests per second<br />3.7.1 with all grsec/PaX features enabled: 2808.17 Requests per second<br />3.7.1 with all grsec/PaX except UDEREF/KERNEXEC: 2853.90 Requests per second</p>
<p>The raw results are available at :<br /><a href="http://grsecurity.net/~spender/arm-bench.txt">http://grsecurity.net/~spender/arm-bench.txt</a></p>
<p>The combination of UDEREF/KERNEXEC shows a 1.6% hit for this test. This may not be relevant however as it's within the standard deviation of results for the test.  In the next section we'll discuss how this performance can be improved even further.</p>
<p>For comparison, the same basic benchmark was used earlier to measure the performance hit of various PaX features on amd64.  Those results are available here:<br /><a href="http://grsecurity.net/~spender/benchmarks.txt">http://grsecurity.net/~spender/benchmarks.txt</a></p>
<h3>Future work (if there's some outside interest in sponsoring future work)</h3>
<p>The domain saving/restoring mechanic occurring at kernel re-entry time should be hardened against stack corruption.  We save a DACR read by using the saved DACR value in current_thread_info()-&gt;cpu_domain, but this could obviously be modified by an attacker, possibly with greater ease than other targets as it's located at a fixed offset from the top of the kernel stack.  Part of this hardening could come from moving the thread_info struct off the kernel stack, as done currently on x86 as part of PaX.  It remains to be seen if and on what processors such a change could happen without impacting performance.</p>
<p>With the above hardening, avoiding &quot;unnecessary&quot; writes to DACR (those where we're writing what should be the current DACR value already) becomes more palatable.  Most DACR writes during nested interrupts and returns from those interrupts could be avoided.</p>
<p>We can harden the __copy*user and other similar routines as PaX does already on x86 by performing range checks on these functions at the expense of a minor performance hit at the few locations that use these routines.</p>
<p>An efficient implementation of pax_open_kernel() and pax_close_kernel() should be investigated on ARM LPAE.  Every attempt should be made for the implementation to match the behavior of the functions on x86 and for !LPAE: ignore read-only access checks on kernel memory for the current CPU only.  Without a per-CPU PGD (as on x86 under PaX) faking an open/close implementation with temporary virtual memory aliases creates insecure race conditions that can be exploited by other CPUs.</p>
<p>Likewise, an efficient UDEREF implementation on ARM LPAE may prove difficult to implement: as LPAE does not support domains, it may be necessary to (ab)use TTBR0 with our existing adjustment in the virtual memory layout to keep all kernel memory in the upper 1GB or 2GB of address space.  This method however requires serious TLB considerations: on !LPAE, TLB entries are tagged with the access rights and domain.  As the domain's permissions are checked through DACR at the time of the TLB entry's use, a switch from DOMAIN_CLIENT to DOMAIN_NOACCESS for DOMAIN_USER doesn't permit any TLB entries to violate the security boundaries established by UDEREF.  On LPAE, however, a transition from TTBR0-enabled (for userland access) to TTBR0-disabled should result in TLB entries for any userland addresses accessed for which subsequent accesses can't be prevented as long as the TLB entry lives.  The reason for this is that the TTBR* registers are only consulted in the event of a TLB miss.  On LPAE, TLB entries are tagged with an ASID (determined based on TTBR* and a flag in TTBCR).  We'd have to temporarily switch to a reserved but appropriate ASID when performing the userland accesses so as not to pollute the TLB for the kernel when operating in the state that should be denied userland access.</p>
<p>In addition to implementing per-CPU PGDs as mentioned above, at least the first level of page tables associated with kernel memory could be made read-only and thus only writable by specific small code wrapped in pax_open_kernel()/pax_close_kernel().  Under LPAE, this kind of protection could be made even more effective through use of the *Table flags (APTable, XNTable, PXNTable) which define whether subsequent page table levels are consulted for certain access permissions.  For instance, by setting XNTable in a second-level entry, we can prevent execution on a 2MB range of memory regardless of whether the third-level page table entries have been modified maliciously in an attempt at permitting execution.</p>
<h3>Support original research</h3>
<p>If you'd like to see future research and free, usable implementations like this, please consider sponsoring grsecurity.  For more information, contact me at <a href="mailto:spender@grsecurity.net">spender@grsecurity.net</a>.
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
