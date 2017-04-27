<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity - The Truth about Linux 4.6</title>


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
					<h1 class="large-headline">The Truth about Linux 4.6</h1>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<p>As anticipated in public comments, the Linux Foundation is already beginning a campaign to rewrite history and mislead Linux users.  Their latest PR release can be found at: <a href="https://www.linux.com/news/greg-kh-update-linux-kernel-46-next-week-new-security-features" class="postlink">https://www.linux.com/news/greg-kh-update-linux-kernel-46-next-week-new-security-features</a>, which I encourage you to read so you can see the spin and misleading (and just plain factually incorrect) information presented. If you've read any of our blog posts before or are familiar with our work, you'll know we always say &quot;the details matter&quot; and are very careful not to exaggerate claims about features beyond their realistic security expectations (see for instance <a href="https://en.wikibooks.org/wiki/Grsecurity/The_RBAC_System#Limitations_of_Any_Access_Control_System" class="postlink">our discussion of access control systems in the grsecurity wiki</a>).  In a few weeks I will be keynoting at the <a href="https://www.sstic.org" class="postlink">SSTIC</a> conference in France, where a theme of my keynote involves how little critical thinking occurs in this industry and how that results in companies and users making poor security decisions.  So let's take a critical eye to this latest PR spin and actually educate about the &quot;security improvements&quot; to Linux 4.6.</p>
<p>In response to the question &quot;What is the hard truth about Linux kernel security that many people don't want to hear?&quot; Greg KH mentions how Linux users need to be running the latest kernels, citing as evidence a specific &quot;infamous&quot; TTY vulnerability.  Presumably this vulnerability was not <a href="http://www.openwall.com/lists/oss-security/2014/05/05/6" class="postlink">this one</a> for which an <a href="http://www.openwall.com/lists/oss-security/2014/05/12/3" class="postlink">exploit</a> was released within a week of publication of the vulnerability and affected the latest kernels at the time as well as those for the previous five years.  To date, no exploit for that vulnerability has ever been released that would work against a kernel secured with grsecurity.</p>
<p>In describing this vulnerability, Greg says:<blockquote class="uncited">Three years later, somebody realized, &quot;Hey! I can use this and get root!&quot; All of a sudden, this bug that we have fixed years ago had to be backported to really old enterprise kernels, because if you're running those, all of a sudden you had a root, you had to exploit those a long time.</blockquote></p>
<p>As best I've been able to tell, the details of this &quot;infamous&quot; vulnerability have taken on new life through a fish tale.  What Greg seems to be referring to is <a href="http://www.openwall.com/lists/oss-security/2015/05/26/1" class="postlink">this vulnerability</a> (CVE-2015-4170), which for most people wasn't memorable at all, and perhaps only &quot;infamous&quot; because of Greg's response to it, decrying the assignment of CVEs for vulnerabilities that have been implicitly fixed in newer upstream kernels.  From that same thread we can see that all the claims in the quote are false.  The bug couldn't have been used to get root, it caused a DoS-only deadlock (discovered by running a reproducer for the other vulnerability I mentioned above).  The 2 years in the fish tale also suddenly became 3 years.</p>
<p>The real &quot;hard truth&quot; about Linux kernel security is that there's no such thing as a free lunch.  Keeping up to date on the latest upstream kernel will generally net all the bug fixes that have been created thus far, but with it of course brings completely new features, new code, new bugs, and new attack surface.  The majority of vulnerabilities in the Linux kernel are ones that have been released just recently, something any honest person active in kernel development can attest to.  I commented as such back in 2010 on an LWN article about whether kernel vulnerabilities were old or new: <a href="https://lwn.net/Articles/410674/" class="postlink">https://lwn.net/Articles/410674/</a>.  I had claimed at the time that the methodology used in the post, which showed that vulnerabilities lasted on average 5 years before being discovered, had been biased in its conclusions by only relying on CVE statistics.  As I noted in the comment, it was common practice at the time (though it seems to have improved slightly since) that when reporting a recent vulnerability on oss-sec, Red Hat wouldn't request a CVE as it didn't affect any of their distribution kernels, and none would subsequently be allocated.  Comments in reply to that post from Dan Carpenter and others discuss the many other vulnerabilities found that weren't assigned CVEs.</p>
<p>We just saw an example of new changes introducing vulnerabilities recently with eBPF as noted in our previous blog post.  The <a href="http://git.kernel.org/cgit/linux/kernel/git/davem/net.git/commit/?id=8358b02bf67d3a5d8a825070e1aa73f25fb2e4c7" class="postlink">use-after-free vulnerability in eBPF</a> which just recently was allowed to be accessed by unprivileged users, had a published exploit within a week of the bug's public disclosure: <a href="https://bugs.chromium.org/p/project-zero/issues/detail?id=808" class="postlink">https://bugs.chromium.org/p/project-zero/issues/detail?id=808</a>.  Again, grsecurity was unaffected by this vulnerability.  Add to this the hopefully now well-known plethora of vulnerabilities plaguing the introduction of unprivileged user namespaces.</p>
<p>So the real &quot;hard truth&quot; is that Linux is just now starting to play catch-up in the game of kernel self-protection that we've been playing for 13 years.  Finally perhaps they've gotten the message that relying on updates alone doesn't provide any security, even against the least skilled attacker.  You're always one 0day away from complete system compromise using the same old techniques that are known and dead in grsecurity for years.  For all the talk of raising attacker cost, what's the cost to an attacker that downloads a fully-functional public exploit?  Real security defenses make exploitation impossible for relevant bug classes and exploit vectors, and force attacks to more difficult unpublished techniques that are more time-consuming, harder to automate, and harder to perfect in an attacker's test environment.</p>
<p>Later in the article where Greg talks about how companies and users need to use the latest releases so that they get not only the most bugfixes, but also the newest security features, he says:<blockquote class="uncited">The new release that's going to come out next week has write-only protection to all the data structures. If something happens and you can't override a data structure, you can't. All of a sudden we took out a whole class of exploits that a bug could turn into an exploit.</blockquote></p>
<p>On its face, this quote is utter nonsense -- hopefully only terribly misconstrued from Greg's supposed words (maybe the interview was over phone and transcribed poorly -- override vs overwrite etc).  Ignoring all that, what Greg appears to be referring to is the addition of __ro_after_init, a renamed implementation of what in grsecurity and PaX we've had and called __read_only for the past 8 years.  The idea of the feature is to make certain global kernel variables and data-structures read-only after they've been initialized by early kernel init code, protecting them against direct modification at runtime.  A quick grep through our latest patch shows we're using this marking on about 200 global variables and data structures important to the kernel's security.  In the to-be-released Linux 4.6, this feature protects exactly *one* thing, as a common theme seen in upstream security, the bare minimum effort required to claim support for X and publish an entire PR piece about it.  Of course, in cherry-picking a tiny subset of a tiny feature in grsecurity, they fail to mention what it doesn't cover: that it doesn't have support for marking in modules like we do, nor does it allow to mark the many things we do which need to be written after init, but infrequently enough that we are able to make them ambiently read-only.</p>
<p>As seen with other upstream security features, once that claim of support for X is made, little is done to improve it or fix fundamental weaknesses.  KASLR, a failed security mitigation introduced in Linux 3.14, continues to be as irrelevant as ever.  Just in the past week a half dozen or so stack-based information leaks were published by academics at Georgia Tech, each one a KASLR defeat.  The attacks published in academic literature (mentioned in our KASLR blog post prior to the addition of KASLR to Linux) still work today as a generic bypass.  While work continues (currently on its 8th revision) on &quot;improving&quot; KASLR, the improvements are all completely orthogonal to how KASLR can currently be bypassed and will continue to be bypassed.  Yet none of this is giving anyone any pause.  Update: shortly after this post, yet another <a href="http://git.kernel.org/cgit/linux/kernel/git/davem/net.git/commit/?id=31b0b385f69d8d5491a4bca288e25e63f1d945d0" class="postlink">kernel infoleak</a> was posted.  This particular one has been fixed already in grsecurity the same way since August of 2012.</p>
<p>Kees Cook posted about the security features new to Linux 4.6 that the Linux Foundation is making such a big deal out of: <a href="https://plus.google.com/u/0/+KeesCook/posts/adtf8msMKNL" class="postlink">https://plus.google.com/u/0/+KeesCook/posts/adtf8msMKNL</a>.  Through an outlandish amount of effort, KASLR support was added to ARM64 (<a href="https://lwn.net/Articles/673598/" class="postlink">https://lwn.net/Articles/673598/</a>) where it will serve no effective purpose, much like the touted SSP support in the kernel from years ago.  The &quot;memory protection enabled by default&quot; items are simply single-line changes to Kconfig defaults -- no new protections were added.  Any sensible distribution or user would already have had these enabled.  The only remaining item is the __ro_after_init item already discussed.</p>
<p>What can we look forward to in upstream's 4.7 and 4.8?  In the Linux.com article Greg talks about the companies doing Linux kernel security work:<blockquote class="uncited">[W]e've been going through and doing a lot of work. We have people working on a lot of things: taking bits and pieces of the GRSec [sic], the large security patch set, taking them and merging them into the kernel as needed and doing some other work. CII is helping fund that.  A number of the developers working on that are just being funded by the companies that they work for such as Google, Red Hat and Intel. They are doing a lot of work to improve kernel security.</blockquote></p>
<p>Kees' post shows us what all this effort will amount to (if it all lands) around the one year anniversary of the Kernel Self-Protection Project: more KASLR, a fully copy+pasted version of my RANDSTRUCT plugin (I have doubts this will land), and the GCC plugin infrastructure we've had in grsecurity for many years now.  This latter improvement is being done by our own Emese Revfy under a CII grant.  For all the talk of the &quot;lot of work&quot; of Google, Red Hat, and Intel, so far it's been nothing more than just talk.  But as the Linux Foundation is a self-interested trade association, not a charity organization, clearly it wants to push the false message that its members are at the forefront of Linux security.</p>
<p>Don't be fooled by pleasant-sounding PR spin, the details matter.  Companies and users interested in real security for the Linux kernel have been using grsecurity for years and will continue to do so.  If you haven't had the opportunity yet, check out our recently published FAQ on RAP at <a href="https://grsecurity.net/rap_faq.php" class="postlink">https://grsecurity.net/rap_faq.php</a>.  RAP is the strongest and most efficient defense available anywhere today against code reuse attacks.</p>
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
