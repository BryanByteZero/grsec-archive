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
					<h1 class="large-headline">RAP Demonstrates World-First<br />Fully CFI-Hardened OS Kernel</h1>
					<p>Type-based, high-performance, high-security, forward/backward-edge CFI</p>
					<p>February 6, 2017</p>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<p>Today's release of grsecurity&reg; for Linux kernel version 4.9 makes good on our promise of publishing the implementation of the deterministic type-based return check portion of the Reuse Attack Protector (RAP) initially described at H2HC in October 2015.  This release builds upon the indirect call checking published in RAP's initial April 2016 publication in the grsecurity test patch for Linux 4.5.  RAP is our patent-pending and best-in-breed defense mechanism against code reuse attacks.  It is the result of years of research and development into <a href='https://en.wikipedia.org/wiki/Control-flow_integrity'>Control-Flow Integrity</a> (CFI) technologies by PaX.  The version of RAP present in the test patch released to the public today under the GPLv2 is now feature-complete.  As a demonstration of its high security benefit to performance cost ratio, we're also providing the following benchmarks below, where RAP demonstrates a 5.4% performance hit in a &quot;worst-case&quot; kernel-bounded workload.</p>
<p><center><img src='/rap-benchmark.svg' alt="Benchmark demonstrating RAP performance hit of 5.4% vs SSP-All's 5.8%"/></center></p>
<p>The benchmarks above were taken from a GCC 5.4.0-compiled grsecurity-enhanced kernel for Linux 4.9.8 with an Ubuntu 16.04.1 base kernel config on an Intel Core i7-6700 (Skylake) 3.4GHz CPU, hyperthreading disabled with a `du -s` benchmark locked to a single core in single user mode.  A simple patch was applied to the kernel to permit usage of the <a href='http://wiki.osdev.org/Stack_Smashing_Protector'>Stack Smashing Protector</a> (SSP)-All mode.  SSP-All was chosen as a comparison since it was used <a href='https://outflux.net/blog/archives/2014/01/27/fstack-protector-strong/'>for a long time by Chrome OS</a>. SSP in general sees wide use throughout open-sourced software due to its inclusion in GCC and widespread kernel/libc support.  The benchmark was chosen to demonstrate a worst-case performance scenario, where nearly all of the work being performed by the benchmark happens in the protected kernel.  Real-life workloads would see much lower effective impacts.</p>
<p>RAP's deterministic type-based forward and backward-edge CFI checks eked out a performance win against SSP-All despite SSP-All providing only a small fraction of the security benefit of RAP.  In the tests above, RAP protected all returns and indirect function calls in C, as well as all function calls in assembly.  SSP-All only weakly protects returns in C code.  SSP-All is vulnerable to arbitrary-read primitives, which allow one to determine its canary value, and cannot defend against non-linear stack corruption.  Additional implementation details about RAP and competing attempts at solutions are discussed in the FAQ linked below.</p>
<p>The commercial version of RAP provides even better performance, compile-time static analysis, LTO support (which in turn can realize additional performance gains), C++ support, and an additional highly-optimized probabilistic XOR protection on returns.  RAP does not require currently-unavailable hardware to achieve its security goals, giving software publishers the ability to produce software which protects <em>all</em> its users from code reuse attacks like <a href='https://en.wikipedia.org/wiki/Return-oriented_programming'>Return-Oriented Programming</a> (ROP).  A commercial cloud vendor, for instance, would be able to announce their high-security, ROP-proof cloud architecture by integrating RAP.  RAP has proven its scalability and performance in protecting massive, complex codebases like the Chromium browser, the Linux kernel, and the Xen Project Hypervisor.  We're excited to soon be providing this feature-complete version of RAP to stable patch subscribers as well.</p>
<p>For a technical deep-dive into RAP, please read the PaX Team's <a href="https://pax.grsecurity.net/docs/PaXTeam-H2HC15-RAP-RIP-ROP.pdf">H2HC 2015 presentation</a>.  For a less-technical description everyone can understand of the history of code reuse attacks, how RAP works, and why it's a ground-breaking defense, we've prepared a <a href="rap_faq.php">FAQ</a>.  RAP is available commercially today.  Reach us at <i>contact@grsecurity.net</i> for details.</p>
<p>ROP-lessly yours,</p>
<p>Brad Spengler &amp; The PaX Team</p>
<br /><p>Chromium is a registered trademark of Google Inc.<br />Xen Project is a registered trademark of The Linux Foundation. <br />Linux is the registered trademark of Linus Torvalds in the U.S and other countries.</p>
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
