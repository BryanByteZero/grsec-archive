<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity - KASLR: An Exercise in Cargo Cult Security</title>


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
					<h1 class="large-headline">KASLR: An Exercise in Cargo Cult Security</h1>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<p>Since this post about Kernel Address Space Layout Randomization (KASLR) extends beyond a critique of the feature itself and into a commentary on the state of commercial defensive security and how it is evaluated both by the security community and by end-users, I asked the PaX Team to contribute some valuable context to the discussion. As the creator of ASLR in 2001, he shares below some history and motivations for ASLR at the time. His exploit vector classification and ASLR analysis cover important nuances and fundamental truths lost in the barrage of &quot;bypasses&quot; in the industry. I continue later in more depth under the heading &quot;Why KASLR is a Failure&quot;.</p>

<p>Before talking about KASLR it seems high time that we revisited a little history regarding ASLR itself. About 12 years ago PaX had already proved that there was in fact a practical way to prevent code injection attacks, the prevalent exploit technique against memory corruption bugs at the time (and even today thanks to the widespread use of JIT compiler engines). It was also clear that the next step for both sides would be to focus on executing already existing code, albeit in an order not intended by the programmer of the exploited application (the market word for this is ROP/JOP/etc). Much less relevant back then but there was always the possibility to exploit these bugs by merely changing data and without disturbing the program logic directly (data only attacks, <a href='#1'>[1]</a> <a href='#2'>[2]</a>). Foreseeing these future developments in practical exploit techniques made me think whether there was perhaps some general way to prevent them or at least reduce their effectiveness until specific reliable and practical defenses could be developed against the remaining exploit techniques (it was clear that such defenses wouldn't come by as easily as non-executable pages, and alas, in 2013AD nobody has still much to show). In other words, ASLR was always meant to be a temporary measure and its survival for this long speaks much less to its usefulness than our inability to get our collective acts together and develop/deploy actual defenses against the remaining exploit techniques.</p>

<p>In any case, thus was the concept of ASLR born which was originally called (for a whole week maybe ;)) ASR for Address Space Randomization (the first proof of concept implementation did in fact randomize every single mmap call as it was the simplest implementation).</p>

<p>The concept was really simple: by randomizing memory addresses on a per process basis we would turn every otherwise reliable exploit needing hardcoded addresses into a probabilistic one where the chances of success were partially controlled by the defense side. While simple in concept and implementation, ASLR doesn't come without conditions, let's look at the them briefly. For ASLR to be an effective prevention measure the following must hold:</p>

<p>1. the exploit must have to know addresses from the exploited process (there's a little shade of grey here in that, depending on the situation, knowledge of partial addresses may be enough (think partial or unaligned overwrites, heap spraying), 'address' is meant to cover both the 'full' and 'partial' conditions),</p>

<p>2. the exploit must not be able to discover such addresses (either by having the exploited application leak them or brute force enumeration of all possible addresses)</p>

<p>These are conditions that are not trivially true or false for specific situations, but in practice we can go with a few heuristics:</p>

<p>1. remote attacks: this is the primary protection domain of ASLR by design because if an exploit needs addresses at all, this gives an attacker the least amount of a priori information. It also puts a premium on infoleaking bugs on the attack side and info leak and brute force prevention mechanisms on the defense side.</p>

<p>2. local attacks: defense relying on ASLR here faces several challenges:</p>

<p>- kernel bugs: instead of attacking userland it is often better to attack the kernel (the widespread use of sandboxes often makes this the path of least resistance) where userland ASLR plays a secondary role only. In particular it presents a challenge only if exploiting the kernel bug requires the participation of userland memory, a technique whose lifetime is much reduced now that Intel (Haswell) and ARM (v6+) CPUs allow efficient userland/kernel address space separation.</p>

<p>- information leaks: the sad fact of life is that contemporary operating systems have all by design features that provide address information that an exploit needs and it's almost a whack-a-mole game to try to find and eliminate them. Even with such intentional leaks fixed or at least worked around there's still kernel bugs left that leak either kernel or userland addresses back to the attacker. Eliminating these didn't receive much research yet, the state-of-the-art being grsecurity but there is still much more work in this area to be done.</p>

<p>So how does KASLR relate to all this? First of all, the name itself is a bit unfortunate since what is being randomized is not exactly what happens in userland. For one, userland ASLR leaves no stone unturned so to speak. That is, a proper ASLR implementation would randomize all memory areas and would do so for every new process differently. There is no equivalent of this mechanism for a kernel since once booted, the kernel image does not change its location in memory nor is it practically feasible to re-randomize it on every syscall or some other more coarse-grained boundary. In other words, it's as if in userland we applied ASLR to a single process and kept running it indefinitely in the hope that nothing bad would happen to it. At this rate we could call the long existing relocatable kernel feature in Linux KASLR as well since it's trivial to specify a new randomly chosen load address at boot time there.</p>

<p>Second, the amount of randomization that can be applied to the base address of the kernel image is rather small due to address space size and memory management hardware constraints, a good userland ASLR implementation provides at least twice the entropy of what we can see in KASLR implementations. To balance this deficiency there's usually already some form of inadvertent brute force prevention present in that most kernels usually don't recover from the side effects of failed exploit attempts (Linux and its oops mechanisms being an exception here).</p>

<p>Third and probably the biggest issue for KASLR is its sensitivity to even the smallest amounts of information leaks, the sheer amount of information leaking bugs present in all kernels and the almost complete lack of prevention mechanisms against such leaks.</p>

<p>This situation came about because historically there was no interest until very recently in finding such bugs let alone systematically eliminating them or preventing their effects (except on hardened systems such as grsecurity, recognizing this fact early is the reason why we've been working on this problem space for many years now). Based on our experience with Linux, this will be a long drawn out uphill battle until such bugs are found or at least their effects neutralized in KASLR based kernels.</p>

<h3>Why KASLR is a Failure</h3>

<p>Continuing where the PaX Team left off, it should begin to become clear that ASLR has been taken out of the context of its original design and held on a pedestal as something much more than what it was originally intended as: a temporary fix until more effective measures could be implemented (which have much less to do with difficulty than the lack of resources on our side).</p>

<p>Information leakage comes in various forms. For our purposes here we'll consider two types of leakage: addresses and content, the former being a subset of the latter. The leaks can have spatial locality (say by leaking a string whose null terminator has been overwritten), be constrained in some other way (say due to an incomplete bounds check), or be completely arbitrary. They can also be active (by creating a leak) or passive (e.g. uninitialized struct fields). Fermin J. Serna's 2012 talk, &quot;The info leak era on software exploitation&quot; <a href='#3'>[3]</a> covers lots of background information and common vulnerabilities and techniques as they pertain to userland.</p>

<p>The KASLR implementations of Microsoft and Apple operate in an environment where the kernel is a known entity whose contents can be obtained in a variety of ways. While on a custom-compiled Linux kernel set up properly, both the content and addresses of the kernel image are secret, for Microsoft and Apple the contents of the kernel image are known. To use &quot;ROP&quot; against the kernel, one needs to know not only the address of what one is returning to, but also what exists at that address. So the only &quot;secret&quot; in KASLR is the kernel's base address. It follows from this that any known pointer to the kernel image reveals its base address.</p>

<p>Due to operational constraints, however, the situation is even more dire. iOS KASLR for instance uses only a small 8 bits of entropy. If this weren't enough, the model is even further weakened from what we discussed above as not even a known pointer is needed to reveal the kernel base address. Any pointer to the kernel will reveal its base address via the upper 11 bits (the uppermost three bits are a given). The kernel is mapped aligned to a 2MB boundary. In Stefan Esser's recent iOS presentation <a href='#4'>[4]</a> he called this 2MB alignment &quot;arbitrary&quot; wondering why there was no smaller alignment. This alignment is not arbitrary at all and is in fact a platform and performance-based constraint on ARM. &quot;Sections,&quot; the ARM equivalent of large pages on x86, are implemented in the short mode descriptor format as 2MB first-level page table entries. This is why the iOS kernel as mapped in memory is composed of one 2MB &quot;text&quot; region and one 2MB &quot;data&quot; region -- because a page table entry is needed for each region to express their different memory protections. The kernel is mapped using sections as opposed to normal 4kB pages because it doesn't pollute the TLB with many entries (and potentially other reasons). Don't expect this alignment at the page table level to change. Inside the kernel, leaks will exist in the fixed-address vector mapping until it is relocated via TrustZone extensions.</p>

<p>KASLR has likewise been praised <a href='#5'>[5]</a> out of context on OS X. Though the latest version of OS X supports SMEP on Ivy Bridge processors (the latest generation of Intel Core architecture), no processors are available yet that support SMAP. OS X running with a 64bit kernel does not have the userland/kernel memory separation people have been used to in years past. Though similar memory separation is possible without SMEP/SMAP on the 64bit kernel from the &quot;no_shared_cr3&quot; boot argument (thanks to Tarjei Mandt for pointing me to this), it is unlikely that anyone is running in this mode as it imposes a severe performance hit (upwards of 30%+ with today's TLB architectures and sizes). Since cr3 is swapped on changing between a kernel-only and shared address space, cr3 modifications (and thus implied TLB flushes) occur on kernel entry, kernel exit, and before and after every copy to/from userland. Therefore, without SMEP, arbitrary code execution is trivially done in userland. Without SMAP, crafted data for sensitive APIs or ROP payloads can be easily stored in userland, removing any need for reliable storage addresses in the kernel.</p>

<p>Information leaks are the critical weakness of ASLR, and KASLR amplifies this weakness. Bases are only randomized once at boot, and (at least OS X/iOS's) heap/stack randomization is weak and irrelevant over time. For every usable privilege escalation vulnerability found, at least one usable infoleak will exist. Obvious infoleaks will be fixed by Apple and Microsoft (e.g. NtQuerySystemInformation), but other infoleak sources will prove more difficult to eradicate. Uninitialized structure fields can very easily creep into code (as we've seen time and again in Linux). Improper use of certain string routines like snprintf() can cause infoleaks. Pointers get used as unique IDs <a href='#6'>[6]</a>, pointers are printed. Structure padding often introduces infoleaks, especially on 64bit architectures. An OS that had 32bit kernels only until very recently switching to 64bit might find many infoleaks suddenly appearing due to this structure padding if one were to look. Linux has been struggling with infoleaks for years and even still they're readily found. Mathias Krause found 21 of them recently in the Linux kernel <a href='#7'>[7]</a>, and &quot;3&quot; more even more recently <a href='#8'>[8]</a>. I say &quot;3&quot; because if you look at the first commit, for instance, you'll see 8 infoleaks being fixed in a single file. 13 infoleaks rolled up into one CVE -- tidy. During the writing of this article, even, I discovered a new infoleak in the Linux kernel that would have evaded any kind of manual code analysis. An unsanitized field that turned into a limited local ASLR infoleak was found via PaX's size_overflow plugin recently as well that evaded manual inspection by the &quot;many eyes&quot; for years <a href='#9'>[9]</a>. This vulnerability goes back to Linux 1.3.0 (yes you read that correctly, from 1995 -- 18 years).</p>

<p>Of important note is that large infoleaks via these bugs are rare (and we've taken many steps in grsecurity to further reduce the possibility of leaks through approved copying interfaces). What is not rare are small leaks, large enough to leak pointers. The leaks are often local to the source of the bug. An uninitialized heap struct might leak pointers, but it will never directly leak code from the kernel image. Uninitialized stack entries can be coerced into providing desired pointers from previous syscalls. All these things mean it's much more likely to leak addresses that would reveal all useful &quot;secrets&quot;. These secrets are the translations between addresses and known content, and their discovery enables full scale ROP. It's much less likely that content itself will be leaked in quantities sufficient enough for the same kind of attack. While Halvar's famous quote &quot;you do not find info leaks... you create them&quot; rings true for much userland exploitation, in the kernel you will come to know that it is much easier (and safer) to find info leaks than create them.</p>

<p>We've seen this kind of cargo cult security before <a href='#10'>[10]</a> <a href='#11'>[11]</a>, of copying techniques into a different environment and via a kind of post hoc, ergo propter hoc logic fallacy, assuming the technique in its new environment will provide the same security. The kptr_restrict sysctl currently exists in the upstream Linux kernel and in most modern distros. It was derived from my GRKERNSEC_HIDESYM feature and submitted upstream by Dan Rosenberg <a href='#12'>[12]</a>. The intent of the feature was to not leak kernel pointers to userland via /proc interfaces, symbol lists, etc. While the configuration help for GRKERNSEC_HIDESYM mentioned explicitly three things that needed to hold true for the feature to be useful at all, among them being that the kernel was not compiled by a distribution and that the kernel and associated modules, etc on disk are not visible to unprivileged users, you'll note that nowhere in the commit message or the in-kernel documentation for kptr_restrict is any kind of qualification for its efficacy mentioned. So what do we see as a result of this? Take Ubuntu <a href='#13'>[13]</a>:</p>
<blockquote>When attackers try to develop &quot;run anywhere&quot; exploits for kernel vulnerabilities, they frequently need to know the location of internal kernel structures. By treating kernel addresses as sensitive information, those locations are not visible to regular local users. Starting with Ubuntu 11.04, /proc/sys/kernel/kptr_restrict is set to &quot;1&quot; to block the reporting of known kernel address leaks. Additionally, various files and directories were made readable only by the root user: /boot/vmlinuz*, /boot/System.map*</blockquote>

<p>All of it utterly useless as every one of these files is publicly available to anyone, including the attacker. And so the false security spreads.</p>

<p>KASLR is an easy to understand metaphor. Even non-technical users can make sense of the concept of a moving target being harder to attack. But in this obsession with an acronym outside of any context and consideration of its limitations, we lose sight of the fact that this moving target only moves once and is pretty easy to spot. We forget that the appeal of ASLR was in its cost/benefit ratio, not because of its high benefit, but because of its low cost. A cost which becomes not so low when we consider all the weaknesses (including the side-channel attacks mentioned in the paper that triggered this whole debate <a href='#14'>[14]</a> which have not been covered in this article for various reasons, mostly because I don't want to sway the many optimists that popped up away from their firmly held beliefs that these are the only problems of this kind that can and will be fixed <a href='#15'>[15]</a>). KASLR is more of a marketing tool (much like the focus of the rest of the industry) than a serious addition to defense. Many other strategies exist to deal with the problem KASLR claims to deal with. To use some wording from the PaX Team, the line of reasoning is: we need to do something. KASLR is something. Let's do KASLR. &quot;Make attacks harder&quot; is not a valid description of a defense. Nor is &quot;it's better than nothing&quot; an acceptable excuse in the realm of security. If it is, then we need to give up the facade and admit that these kinds of fundamentally broken pseudo-mitigations are nothing more than obfuscation, designed to give the public presence of security while ensuring the various exploit dealers can still turn a profit off the many weaknesses.</p>

<p>The details matter.</p>

<p>Consider this our &quot;I told you so&quot; that we hope you'll remember in the coming years as KASLR is &quot;broken&quot; time and again. Then again, in this offensive-driven industry, that's where the money is, isn't it?</p>

<dl class='footnotes'>
<dt id='1'>[1]</dt><dd><a href='http://static.usenix.org/event/sec05/tech/full_papers/chen/chen.pdf'>http://static.usenix.org/event/sec05/tech/full_papers/chen/chen.pdf</a></dd>
<dt id='2'>[2]</dt><dd><a href='http://www.cs.princeton.edu/~dpw/papers/yarra-csf11.pdf'>http://www.cs.princeton.edu/~dpw/papers/yarra-csf11.pdf</a></dd>
<dt id='3'>[3]</dt><dd><a href='http://media.blackhat.com/bh-us-12/Briefings/Serna/BH_US_12_Serna_Leak_Era_Slides.pdf'>http://media.blackhat.com/bh-us-12/Briefings/Serna/BH_US_12_Serna_Leak_Era_Slides.pdf</a></dd>
<dt id='4'>[4]</dt><dd><a href='http://www.slideshare.net/i0n1c/csw2013-stefan-esserios6exploitation280dayslater'>http://www.slideshare.net/i0n1c/csw2013-stefan-esserios6exploitation280dayslater</a></dd>
<dt id='5'>[5]</dt><dd><a href='https://twitter.com/aionescu/status/312945665888120832'>https://twitter.com/aionescu/status/312945665888120832</a></dd>
<dt id='6'>[6]</dt><dd><a href='http://lists.apple.com/archives/darwin-kernel/2012/Sep/msg00012.html'>http://lists.apple.com/archives/darwin-kernel/2012/Sep/msg00012.html</a></dd>
<dt id='7'>[7]</dt><dd><a href='http://www.openwall.com/lists/oss-security/2013/03/07/2'>http://www.openwall.com/lists/oss-security/2013/03/07/2</a></dd>
<dt id='8'>[8]</dt><dd><a href='http://seclists.org/oss-sec/2013/q1/693'>http://seclists.org/oss-sec/2013/q1/693</a></dd>
<dt id='9'>[9]</dt><dd><a href='http://forums.grsecurity.net/viewtopic.php?f=7&t=2521'>http://forums.grsecurity.net/viewtopic.php?f=7&t=2521</a></dd>
<dt id='10'>[10]</dt><dd><a href='http://forums.grsecurity.net/viewtopic.php?f=7&t=2574'>http://forums.grsecurity.net/viewtopic.php?f=7&t=2574</a></dd>
<dt id='11'>[11]</dt><dd><a href='https://lkml.org/lkml/2013/3/11/498'>https://lkml.org/lkml/2013/3/11/498</a></dd>
<dt id='12'>[12]</dt><dd><a href='https://lwn.net/Articles/420403/'>https://lwn.net/Articles/420403/</a></dd>
<dt id='13'>[13]</dt><dd><a href='https://wiki.ubuntu.com/Security/Features'>https://wiki.ubuntu.com/Security/Features</a></dd>
<dt id='14'>[14]</dt><dd><a href='http://www.ieee-security.org/TC/SP2013/papers/4977a191.pdf'>http://www.ieee-security.org/TC/SP2013/papers/4977a191.pdf</a></dd>
<dt id='15'>[15]</dt><dd>3a5f33b4af2ffbc27530087979802613fae8ed3ce0ae10e41c44c2877a76605b</dd>
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
