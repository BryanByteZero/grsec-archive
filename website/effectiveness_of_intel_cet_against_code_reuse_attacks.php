<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity - On the Effectiveness of Intel's CET Against Code Reuse Attacks</title>


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
					<h1 class="large-headline">Close, but No Cigar: On the Effectiveness of Intel's CET Against Code Reuse Attacks</h1>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<p>Intel's recent announcement (<a href='#A1'>[A1]</a>, <a href='#A2'>[A2]</a>) of their hardware support for a form of Control Flow Integrity (CFI) has raised a lot of interest among the expert as well as the popular press. As an interested party we've decided to look at some of the details and analyze the strengths and weaknesses of Intel's Control-flow Enforcement Technology (CET). Note that all the discussion below is based off Intel's published technology preview documents. As no processor with the claimed technology will exist for several years, the details are not complete and may change in small ways prior to production.</p>

<p>Full disclosure: we have a competing production-ready solution to defend against code reuse attacks called RAP, see <a href='#R1'>[R1]</a>, <a href='#R2'>[R2]</a>. RAP isn't tied to any particular CPU architecture or operating system, and it scales to real-life software from Xen to Linux to Chromium with excellent performance.</p>

<p>Following typical CFI schemes (<a href='#P1'>[P1]</a>), CET provides two separate mechanisms to protect indirect control flow transfers: one for forward edges (indirect calls and jumps) and another for backward edges (function returns). As we'll see, they have very different characteristics, so we'll look at them each individually.</p>

<h3>Indirect Branch Tracking</h3>

<p>The forward edge mechanism is called Indirect Branch Tracking (IBT) and is designed to allow only designated code locations as valid targets for indirect calls and jumps (<a href='#N1'>[N1]</a>). This is no different from other approaches in the field. What does differentiate these schemes is their precision, that is, the number of allowed targets at each indirect control transfer instruction. Intuitively, the less locations an attack can target, the less likely that those locations will be useful for something. Without any CFI an attacker can target any executable byte in the program's address space. CFI, ideally, restricts this set to a minimum at each indirect control transfer instruction.</p>

<p>How does CET fare in this regard? Very badly unfortunately as CET implements the weakest form of CFI in that there's only a single class of valid targets. That is, any indirect control transfer can be redirected to any of the designated target locations (similar to what Microsoft's CFG allows). Such simplistic schemes have been proven to be fatally weak by both academic and industry researchers (<a href='#P2'>[P2]</a> <a href='#P3'>[P3]</a> <a href='#P4'>[P4]</a> <a href='#P5'>[P5]</a>).</p>

<p>In contrast, RAP's type hash based classification can create over 30.000 function pointer classes and 47.000 function classes for Chromium (this means among others that thousands of otherwise valid functions cannot be called indirectly at all).</p>

<p>Beyond the design flaw identified above, there are also implementation problems with CET. One of them is related to the fact that the hardware has not one but two state machines to keep track of the IDLE/WAIT_FOR_ENDBRANCH states for user and kernel mode code, respectively. Only one state machine is active at a time depending on the privilege level of the currently executing code; the other state machine is suspended. There is however no mention in the documentation how this hidden state is saved and restored when the privilege boundary is crossed by a system call, interrupts, etc.</p>

<p>This in particular seems to make it impossible for a kernel to switch contexts between threads since it may very well happen that the outgoing thread was interrupted in a different state than what the incoming thread would like to resume in, triggering an instant Control Protection fault upon returning from the kernel to userland. The same problem arises with UNIX style signal delivery and other similar asynchronous userland code invocations. Hopefully this is merely an oversight in the documentation and not the design itself.</p>

<p>Another problem is the support mechanism for compatibility with code that hasn't been recompiled for CET. The Legacy Code Bitmap (LCB) seems to be direct hardware support for Microsoft's CFG scheme and suffers from the same problems as a result identified by earlier research (<a href='#P6'>[P6]</a>, <a href='#P7'>[P7]</a>, <a href='#P8'>[P8]</a>).</p>

<p>Interestingly, this same compatibility mechanism could be used to fix the fatal flaw of the coarse-grained design. Namely, to simulate fine-grained CFI one could create a separate bitmap for each indirect call type and activate it for the call. The implementation would suffer from increased memory usage (one LCB per function pointer type) and it'd also have a large performance impact due to the slow access to the MSR storing the address of the LCB (this would be even worse for userland as the MSR doesn't seem to be writable directly from user mode code). Needless to say, RAP achieves fine-grained forward-edge CFI without this performance impact.</p>

<p>A third problem with IBT is that to mark valid indirect branch targets an otherwise useless instruction must be emitted at the target location which wastes instruction decoding bandwidth at least (and probably more on non-CET capable processors). In contrast, RAP's type hash based marking scheme was specifically designed to avoid this problem thus its only impact is on memory use.</p>

<h3>Shadow Stack</h3>

<p>Let's now look at CET's offering for protecting function returns. This mechanism is based on the well-known concept of shadow stacks that have been (re)invented and implemented many times in the past (<a href='#P9'>[P9]</a>).</p>

<p>Shadow stacks aim to provide secure storage for return addresses that can only be written by call instructions. This ensures that memory corruption bugs cannot be used to divert control flow at function returns, which used to be a widespread exploitation technique since the beginnings of time.</p>

<p>While the shadow stack design is sound as it provides precise enforcement of call/return pairs, implementing it in real life systems faces several problems such as protecting the shadow stack region itself from memory corruption attacks, performance overhead of instructions needed to read from and write to the shadow stack, and compatibility with programming constructs that intentionally violate the strict call/return pairing assumed by the shadow stack design.</p>

<p>Traditional shadow stack implementations all suffer from the problem that they're writable and thus subject to memory corruption themselves. Fixing this by changing memory protection rights on each function call and return is prohibitively expensive thus most designs either assume a weaker threat model or try to hide behind ASLR (which is vulnerable to more powerful threats itself).</p>

<p>Intel's shadow stack design solves the problem of writable shadow stacks by giving hardware support to separate the shadow stack memory from other data and allow only designated instructions to write there. This is a sound design but the particular implementation requires implementors to be careful.</p>

<p>Namely, the way shadow stacks are marked seems to make RELRO and text relocated pages look like shadow stacks as well (they're all read-only but have been written to thus dirty in the last level page table entries). This can become a problem if the actual shadow stack area is ever mapped directly next to such a mapping as overflowing or underflowing the shadow stack may go unnoticed and give rise to an attack. Speaking of which, the current document doesn't say anything about how shadow stack overflows/underflows are handled.</p>

<p>Finally, as already discovered by past implementors (<a href='#P10'>[P10]</a> <a href='#P11'>[P11]</a> <a href='#P12'>[P12]</a> <a href='#P13'>[P13]</a>), shadow stacks cannot be used through compiler modifications only. Each OS has their own exceptional cases that need special handling. On Linux and similar OSes, these exceptional cases include the setjmp/longjmp/makecontext/setcontext set of functions which can violate the assumption that a function will return to its call site. It also includes the default glibc behavior of lazy binding (done for performance reasons) as well as C++ exceptions and asynchronous signal handling.</p>

<h3>Conclusion</h3>

<p>In summary, Intel's CET is mainly a hardware implementation of Microsoft's weak CFI implementation with the addition of a shadow stack. Its use will require the presence of Intel processors that aren't expected to be released for several years. Rather than truly innovating and advancing the state of the art in performance and security guarantees as RAP has, CET merely cements into hardware existing technology known and bypassed by academia and industry that is too weak to protect against the larger class of code reuse attacks. One can't help but notice a striking similarity with Intel's MPX, another software-dependent technology announced with great fanfare a few years ago that failed to live up to its many promises and never reached its intended adoption as the solution to end buffer overflow attacks and exists only as yet another bounds-checking based debugging technology.</p>

<p>In comparison, RAP is architecture-independent, best of breed in performance and security, doesn't require the latest CPU, and gives software developers the powerful ability to easily make the protections from RAP even more fine-grained.</p>

<dl class='footnotes'>
<dt id='A1'>[A1]</dt><dd><a href='http://blogs.intel.com/blog/intel-innovating-stop-cyber-attacks/'>http://blogs.intel.com/blog/intel-innovating-stop-cyber-attacks/</a></dd>
<dt id='A2'>[A2]</dt><dd><a href='http://blogs.intel.com/evangelists/2016/06/09/intel-release-new-technology-specifications-protect-rop-attacks/'>http://blogs.intel.com/evangelists/2016/06/09/intel-release-new-technology-specifications-protect-rop-attacks/</a></dd>
<dt id='R1'>[R1]</dt><dd><a href='https://pax.grsecurity.net/docs/PaXTeam-H2HC15-RAP-RIP-ROP.pdf'>https://pax.grsecurity.net/docs/PaXTeam-H2HC15-RAP-RIP-ROP.pdf</a></dd>
<dt id='R2'>[R2]</dt><dd><a href='https://grsecurity.net/rap_announce.php'>https://grsecurity.net/rap_announce.php</a></dd>
<dt id='N1'>[N1]</dt><dd>Note that in practice indirect calls are the interesting case as the typical use of indirect jumps is to implement high level switch/case constructs where the code addresses and the paths leading to them are already in read-only memory and thus not subject to memory corruption.</dd>
<dt id='P1'>[P1]</dt><dd><a href='https://pax.grsecurity.net/docs/pax-future.txt'>https://pax.grsecurity.net/docs/pax-future.txt</a></dd>
<dt id='P2'>[P2]</dt><dd><a href='https://www.usenix.org/system/files/conference/usenixsecurity14/sec14-paper-davi.pdf'>https://www.usenix.org/system/files/conference/usenixsecurity14/sec14-paper-davi.pdf</a></dd>
<dt id='P3'>[P3]</dt><dd><a href='http://nsl.cs.columbia.edu/projects/minestrone/papers/outofcontrol_oakland14.pdf'>http://nsl.cs.columbia.edu/projects/minestrone/papers/outofcontrol_oakland14.pdf</a></dd>
<dt id='P4'>[P4]</dt><dd><a href='https://people.csail.mit.edu/stelios/papers/jujutsu_ccs15.pdf'>https://people.csail.mit.edu/stelios/papers/jujutsu_ccs15.pdf</a></dd>
<dt id='P5'>[P5]</dt><dd><a href='http://dl.acm.org/citation.cfm?id=2813671'>http://dl.acm.org/citation.cfm?id=2813671</a></dd>
<dt id='P6'>[P6]</dt><dd><a href='https://blog.coresecurity.com/2015/03/25/exploiting-cve-2015-0311-part-ii-bypassing-control-flow-guard-on-windows-8-1-update-3/'>https://blog.coresecurity.com/2015/03/25/exploiting-cve-2015-0311-part-ii-bypassing-control-flow-guard-on-windows-8-1-update-3/</a></dd>
<dt id='P7'>[P7]</dt><dd><a href='https://www.blackhat.com/docs/us-15/materials/us-15-Zhang-Bypass-Control-Flow-Guard-Comprehensively-wp.pdf'>https://www.blackhat.com/docs/us-15/materials/us-15-Zhang-Bypass-Control-Flow-Guard-Comprehensively-wp.pdf</a></dd>
<dt id='P8'>[P8]</dt><dd><a href='http://xlab.tencent.com/en/2015/12/09/bypass-dep-and-cfg-using-jit-compiler-in-charkra-engine/'>http://xlab.tencent.com/en/2015/12/09/bypass-dep-and-cfg-using-jit-compiler-in-charkra-engine/</a></dd>
<dt id='P9'>[P9]</dt><dd><a href='http://www.angelfire.com/sk/stackshield/'>http://www.angelfire.com/sk/stackshield/</a></dd>
<dt id='P10'>[P10]</dt><dd><a href='http://mosermichael.github.io/cstuff/all/projects/2011/06/19/stack-mirror.html'>http://mosermichael.github.io/cstuff/all/projects/2011/06/19/stack-mirror.html</a></dd>
<dt id='P11'>[P11]</dt><dd><a href='https://www.cs.utah.edu/plt/publications/ismm09-rwrf.pdf'>https://www.cs.utah.edu/plt/publications/ismm09-rwrf.pdf</a></dd>
<dt id='P12'>[P12]</dt><dd><a href='http://seclab.cs.sunysb.edu/seclab/pubs/vee14.pdf'>http://seclab.cs.sunysb.edu/seclab/pubs/vee14.pdf</a></dd>
<dt id='P13'>[P13]</dt><dd><a href='https://www.trust.informatik.tu-darmstadt.de/fileadmin/user_upload/Group_TRUST/PubsPDF/ropdefender.pdf'>https://www.trust.informatik.tu-darmstadt.de/fileadmin/user_upload/Group_TRUST/PubsPDF/ropdefender.pdf</a></dd>
</dl>
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
