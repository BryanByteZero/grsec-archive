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
					<h1 class="large-headline">Passing the Baton: FAQ</h1>
					<p>April 26, 2017</p>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<h3>When will this happen?</h3>
<p>This change is effective today, April 26th 2017.  Public test patches
have been removed from the download area.  4.9 was specifically chosen as the last public release as being the latest upstream LTS kernel will help ease the community transition.</p>
<h3>Why are you doing this?</h3>
<p>We have been providing grsecurity freely for 16 years. Given renewed
interest in security by the Linux community, we are opening our
playground for newcomers to experiment with new ideas. We believe our
future will be shaped by the next generation and this will provide
them with the experience needed to improve Linux kernel security.</p>
<h3>Why are you <em>really</em> doing this?</h3>
<p>We want to focus our efforts on the next generation of security
defenses. Data-only attacks are the last remaining holdout for
memory corruption exploitation and are even more important for the security of the kernel
itself.  As always, we plan to stay well ahead of the exploitation
curve, so we will be tackling the vector holistically, building on top
of the strong security guarantees provided by <a href="/rap_faq.php">RAP</a>, our best-in-breed
defense against code reuse attacks.</p>
<h3>What about PaX?</h3>
<p>As this is a joint decision, there will be no public PaX patches for future kernels.  This is
effective April 26th 2017.</p>
<h3>What new technologies are you working on?</h3>
<p>ARM64, mobile/Android, RAP for stable kernels, KERNSEAL, STRUCTGUARD,
and other next-generation defenses against data-only attacks.  We
will occasionally provide updates on these advances via our <a href="/blog.php">blog</a>.</p>
<h3>I am a grsecurity -stable customer, how does this affect me?</h3>
<p>Services for existing customers remain unaffected.  All active customers
were sent advance notice of this announcement with all
necessary information.</p>
<h3>My business depends on the -test kernel patches, what now?</h3>
<p>We recommend you become a grsecurity commercial subscriber.  Subscribers
may opt-in to gain access to our -beta patches which track the latest
kernel releases. Please <a href="mailto:contact@grsecurity.net">contact</a> Open Source Security Inc.</p>
<h3>I do not want to spend money, what alternatives are there?</h3>
<p>Unfortunately, in contrast to Microsoft's post-Windows XP <a href="https://www.microsoft.com/mscorp/execmail/2002/07-18twc.mspx">Trustworthy
Computing</a> initiative which drastically changed its security trajectory,
the Linux community at large has failed to invest adequately in security over
the past two decades.  Partially due to this, there is no direct alternative to
grsecurity or even any option that provides a substantial fraction of
grsecurity's features or overall benefits.</p>
<p>This <a href="/compare.php">feature matrix</a> shows the differences between existing Linux
kernel security technologies.</p>
<h3>Can the old patches still be used?</h3>
<p>Of course. The GPLv2 license grsecurity is provided under gives all
users the ability to continue using, modifying, and redistributing the
code present in grsecurity.  We will not however maintain an archive of
<a href="https://forums.grsecurity.net/viewtopic.php?f=3&t=2980">old patches</a> on our website.</p>
<h3>Can I continue to use the name grsecurity?</h3>
<p>grsecurity<sup>&reg;</sup> is a registered trademark by Open Source Security Inc. We
will continue to use it in our official work.  We ask that any
community-based ports or additions to the last public official
grsecurity patch not use the grsecurity trademark.  Replacing the
&quot;grsec&quot; uname addition, removing the grsecurity boot logo from the
patch, and removing &quot;grsec&quot; from associated package names at minimum will make this easier and avoid confusion.  All copyright and license
notices must remain intact as required by the GPL.</p>
<h3>Who is Open Source Security Inc.?</h3>
<p>Open Source Security Inc. is the company behind grsecurity.  Initially
founded in Virginia in 2008, we re-incorporated in Pennsylvania in
2015.  We have been working on grsecurity continuously since 2001 and
bring the results of our years of experience in Linux kernel security
to benefit our customers through grsecurity patch subscriptions,
professional support, and custom security development work.  Our team
has been responsible for most of the effective security defenses in use
today on any OS.</p>
<p>Our announcement may be viewed <a href="/passing_the_baton.php">here</a>.</p>
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
