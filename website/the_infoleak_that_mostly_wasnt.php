<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity - The Infoleak that (Mostly) Wasn't</title>


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
					<h1 class="large-headline">The Infoleak that (Mostly) Wasn't</h1>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<p>The following is an analysis of a recently fixed Linux kernel stack infoleak 
vulnerability existing from version 2.6.9 (Oct 2003) when compat-mode syscalls were added for set_mempolicy and mbind.  It was reported
and fixed publicly by Chris Salls On April 7th 2017 with a CC to security@kernel.org at:
<a href="http://www.spinics.net/lists/linux-mm/msg125328.html">http://www.spinics.net/lists/linux-mm/msg125328.html</a>
and merged the following day via commit 
<a href="https://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git/commit/?id=cf01fb9985e8deb25ccf0ea54d916b8871ae0e62">cf01fb9985e8deb25ccf0ea54d916b8871ae0e62</a>.  
It was not marked with any &quot;Fixes:&quot; tag or stable@ CC, yet was backported to
some stable kernels two days later.  Historically, this is typical of upstream's attempts at avoiding
CVE allocation and lowering the chance distros include the fix.</p>

<img width="70%" style="display: block; margin-left: auto; margin-right: auto;" src="/images/mempolicy.png" alt="Screenshot showing vulnerable code">

<p>The vulnerability exists because compat_get_bitmap() could leave the
nodemask on the kernel stack uninitialized in an error condition, and then
instead of erroring out of the syscall immediately, it would proceed to
copy the uninitialized nodemask to userland.</p>

<img width="70%" style="display: block; margin-left: auto; margin-right: auto;" src="/images/getbitmap.png" alt="Screenshot showing compat_get_bitmap() implementation">

<p>Generally with a 32-bit userland and with the limit on the size enforced
prior to calling compat_get_bitmap(), the access_ok() shouldn't be able
to fail.  This means failing the call requires __get_user to fail, which
can be done by providing some unmapped address.  The zeroing behavior of
__get_user isn't a problem for exploitation here because the temporary value gets thrown
away instead of being set into the mask array before returning -EFAULT.</p>

<img width="70%" style="display: block; margin-left: auto; margin-right: auto;" src="/images/compatalloc.png" alt="Screenshot showing ARM64's compat_alloc_user_space implementation">

<p>compat_alloc_user_space() in most cases works by decrementing a copy of
the userland stack pointer by the requested allocation size and returning it.  The image above shows the ARM64-specific implementation of the API.  Since the
leaked data is placed in this &quot;allocation&quot; via copy_to_user, it wouldn't
normally be visible by high-level code -- any subsequent stack usage at
lower depth would clobber the data.  This suggests the vulnerability was likely
found via static analysis or manual inspection by the reporter.  For exploitation 
purposes, using assembly to prime the region and search for leaked data after the syscall
provides more determinism.</p>

<p>The size of the leak depends on the value of CONFIG_NODES_SHIFT.  Values
up to 6 would have 8 bytes of uninitialized stack, other distros setting
this to 10 would have 16 bytes of uninitialized stack.  If it weren't
for the next issue, this would be more than enough to defeat KASLR on
any 64-bit Linux kernel (not to suggest that these kinds of individual
leaks are necessary to defeat KASLR when generic timing attacks are
always feasible).</p>

<p>Unfortunately (for an attacker), only a handful of architectures have wired up support for
the compat versions of the set_mempolicy and mbind syscalls.  Specifically, of recent versions
this includes arm64, sparc64, s390, and mips64.  So the vulnerability
can't be triggered on x86-64 systems.  On the architectures that do have
support for these syscalls, CONFIG_NUMA is also required for the
routines to actually be built into the kernel.  Support for enabling
CONFIG_NUMA on arm64 however was only added relatively recently in
April of 2016, and it's unlikely to be enabled in the few kernel
versions where that support exists.</p>

<p>Assuming we had a rare unicorn of a system where the vulnerable syscalls
could actually be reached, exploitation would proceed as follows:</p>

<p>
<ol>
<li>Run a random syscall without system side effects</li>
<li>Memset the 16 bytes below the stack pointer</li>
<li>Invoke the 32-bit version of set_mempolicy, setting the arguments as:<br>
   mode: anything<br>
   nmask: address of unmapped memory (0xffffffff for instance) as this
        will cause the compat_get_bitmap() call to fail<br>
   maxnode: 0xffffffff (the min_t() operation performed by the compat version
        of the syscall will ensure that MAX_NUMNODES will be chosen and the
        largest possible infoleak will be performed, either 8 or 16 bytes
        in practice)</li>
<li>Inspect the 16 bytes memory below the stack pointer (assuming you did direct
   syscall invocation for step 3) for non-zero quadwords.  With a variety of syscalls in step 1, a kernel
   address is almost surely to be found.</li>
</ol>
</p>

<p>So a 13 year old infoleak that as of yet has no CVE (by design, due to
upstream's handling process) ends up not being very important.  With a
policy that hinders distro adoption of security fixes, however, this story could have
just as likely ended with the opposite result -- and without a blog post like this, one would likely have never
heard about it.</p>

<p>Update: the article claimed no CVE existed at time of publication, but one was indeed assigned <a href="https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2017-7616">CVE-2017-7616</a> on April 10th.  I apologize for this mistake -- in my search for the commit message there was clearly a result on the first page that I must have overlooked at the time.  If you're interested in seeing vulnerabilities fixed without CVEs, there are a plethora of other examples (see <a href="http://seclists.org/oss-sec/2017/q2/76">here</a> for some), just not this specific instance.</p>

<p>Of interesting note is <a href="https://access.redhat.com/security/cve/cve-2017-7616">Red Hat</a> gives this a CVSSv3 score of 5.5 and lists their kernels as affected, when as demonstrated by the analysis above the vulnerable code isn't even accessible on x64 kernels.  This may be a reflection of the fact that the sheer volume of Linux kernel vulnerabilities results in very little exploitation analysis being performed on individiual vulnerabilities these days.</p>

<p>Until next time,</p>
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
