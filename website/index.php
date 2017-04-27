<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>grsecurity</title>

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=5A5zyaR2my">
<link rel="icon" type="image/png" href="/favicon-32x32.png?v=5A5zyaR2my" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png?v=5A5zyaR2my" sizes="16x16">
<link rel="manifest" href="/manifest.json?v=5A5zyaR2my">
<link rel="mask-icon" href="/safari-pinned-tab.svg?v=5A5zyaR2my" color="#344d83">
<link rel="shortcut icon" href="/favicon.ico?v=5A5zyaR2my">
<meta name="theme-color" content="#ffffff">

<link rel='stylesheet' href='/scss/style.css' type='text/css' media='all' />
<script type='text/javascript' src='/js/header.js'></script>

</head>
<body>
<div class="fp-nav-wrap">
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
</div> <!-- .fp-nav-wrap-->
<div id="top" class="slider-wrapper">

    <div id="slider" class="slider">

    	<!--Slide 1 -->
        <div class="slide">
	        <div class="slide-inner">
	        	<img src="img/snowy-mountains.jpg" class="slide-image" alt="snowy mountains">
	            <div class="slide-content-wrap">
	                <div class="slide-content">
	                	<div class="slider-logo"></div>
	                    <h1 class="large-headline">Grsecurity Adds Confidence to Containers</h1>
	                    <p>No security strategy for today's container-based deployments is complete without grsecurity<sup>&reg;</sup>.  Our unmatched defenses add critical hardening to the Linux kernel, a ripe source of vulnerabilities and involved in most container escapes.</p>
	                    <a class="fancy-link" href="features.php">Read more</a>
	                </div>
	            </div>
	        </div><!-- inner -->
		</div><!-- slide -->
		<!--End Slide 1 -->

		<!--Slide 2 -->
		<div class="slide">
	        <div class="slide-inner">
	        	<img src="img/snowy-mountains.jpg" class="slide-image" alt="snowy mountains">
	            <div class="slide-content-wrap">
	                <div class="slide-content">
	                	<div class="slider-logo"></div>
	                    <h1 class="large-headline">Grsecurity Ends Code Reuse Attacks</h1>
	                    <p>RAP is our best-of-breed Control Flow Integrity (CFI) defense against code reuse attacks like ROP.  Its performance, security guarantees, and ability to scale to complex C/C++ codebases of arbitrary size are unmatched.</p>
	                    <a class="fancy-link" href="rap_announce2.php">Read more</a>
	                </div>
	            </div>
	        </div><!-- inner -->
		</div><!-- slide -->
		<!--End Slide 2 -->

		<a class="scroll-down-btn" href="#fp-first-section"></a>

    </div><!-- slider -->
</div>

<main>
	<section class="fp-first-section" id="fp-first-section">

		<div class="super-wrap">
			<div class="col-1-2 fp-first-section-left">

				<div class="hex-carousel">
					<div class="carousel-cell">
						<div class="hexagon-small grsecurity current" data-content="content-1">
							What is<br>
							grsecurity?
						</div>
					</div>
					<div class="carousel-cell">
					  	<div class="hexagon-small beyond" data-content="content-2">
							Beyond<br>
							access control
						</div>
					</div>
					<div class="carousel-cell">
					  	<div class="hexagon-small mitigate" data-content="content-3">
							Hardens Container<br>
							Isolation
						</div>
					</div>
					<div class="carousel-cell">
					  	<div class="hexagon-small defense" data-content="content-4">
							Defends against<br>
							zero-day
						</div>
					</div>
					<div class="carousel-cell">
					  	<div class="hexagon-small integrate" data-content="content-5">
							Integrates with<br>
							your distribution
						</div>
					</div>
					<div class="carousel-cell">
					  	<div class="hexagon-small track-record" data-content="content-6">
							Has a proven<br>
							track record
						</div>
					</div>
				</div>

				<a class="scroll-down-btn" href="#testimonials"></a>

			</div>

			<div class="col-1-2 hexagon-content-wrap">
				<div class="hexagon-content-carousel">
					<div id="content-1" class="hexagon-content current">
						<h2>What is grsecurity?</h2>
						<p>Grsecurity<sup>&reg;</sup> is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.</p>

						<p>It has been actively developed and maintained for the past 16 years. Commercial support for grsecurity is available through Open Source Security, Inc.</p>

						<a href="purchase.php" class="btn"><span>Get an offer</span></a>
						<span class="last-updated">3.14.79 Last updated: 04/25/17</span>
						<span class="last-updated">4.4.63 Last updated: 04/25/17</span>
					</div>

					<div id="content-3" class="hexagon-content">
						<h2 class="mitigate">Hardens Container Isolation</h2>

						<p>In any kind of shared computing environment, whether it be simple UID separation, OpenVZ, LXC, or Linux-VServer, the most common and often easiest method of full system compromise is through kernel exploitation. No other software exists to mitigate this weakness while maintaining usability and performance.</p>

						<a href="purchase.php" class="btn"><span>Get an offer</span></a>
						<span class="last-updated">3.14.79 Last updated: 04/25/17</span>
						<span class="last-updated">4.4.63 Last updated: 04/25/17</span>
					</div>

					<div id="content-4" class="hexagon-content">
						<h2 class="defense">Defends against zero-day</h2>

						<p>Only grsecurity provides protection against zero-day and other advanced threats that buys administrators valuable time while vulnerability fixes make their way out to distributions and production testing. This is made possible by our focus on eliminating entire bug classes and exploit vectors, rather than the status-quo elimination of individual vulnerabilities.</p>

						<a href="purchase.php" class="btn"><span>Get an offer</span></a>
						<span class="last-updated">3.14.79 Last updated: 04/25/17</span>
						<span class="last-updated">4.4.63 Last updated: 04/25/17</span>
					</div>

					<div id="content-5" class="hexagon-content">
						<h2 class="integrate">Integrates with your distribution</h2>

						<p>Grsecurity confines its changes to the Linux kernel itself, making it possible to use with any distribution or device: embedded, server, or desktop. Use your existing distribution's kernel configuration if you wish and answer a simple series of questions about your use case to optimally configure grsecurity automatically. X86, ARM, or MIPS -- grsecurity has been developed for and used on them all and many more.</p>

						<a href="purchase.php" class="btn"><span>Get an offer</span></a>
						<span class="last-updated">3.14.79 Last updated: 04/25/17</span>
						<span class="last-updated">4.4.63 Last updated: 04/25/17</span>
					</div>

					<div id="content-6" class="hexagon-content">
						<h2 class="track-record">Has a proven track record</h2>

						<p>Grsecurity has been developed and maintained since 2001, from the very first 2.4 Linux kernel to the latest and greatest 4.x. In addition to tracking the latest stable kernel, we provide stable releases for both the 3.14 and 4.4 kernels with additional security backports.</p>

						<p>We stay on top of -- and in many cases drive -- the state of the art in security research. While the security teams of Linux distributions react to the latest widespread exploit simply by fixing the associated vulnerability, we quickly work in addition to close down any new exploit vectors, reduce the chance of similar vulnerabilities, and insert additional roadblocks for ancillary techniques that made the exploit possible or reliable.</p>

						<p>As a result of this extensive approach, it is not uncommon to find in the event of a published exploit, particularly against the kernel, that the exploit's success is prevented by several separate features of grsecurity.</p>

						<a href="purchase.php" class="btn"><span>Get an offer</span></a>
						<span class="last-updated">3.14.79 Last updated: 04/25/17</span>
						<span class="last-updated">4.4.63 Last updated: 04/25/17</span>
					</div>

					<div id="content-2" class="hexagon-content">
						<h2 class="beyond">Beyond Access Control</h2>

						<p>Unlike the LSMs you're used to, grsecurity tackles a wider scope of security problems. While access control has its place, it is incapable of dealing with many real-life security issues, especially in webhosting environments where an attacker can fraudulently purchase local access to the system. To see what you're missing out on by relying on just access control, see our <a class="fancy-link" href='compare.php'>feature comparison matrix</a>.</p>

						<p>A major component of grsecurity is its approach to memory corruption vulnerabilities and their associated exploit vectors. Through partnership with the <a class="fancy-link" href='https://pax.grsecurity.net'>PaX</a> project, creators of ASLR and many other exploit prevention techniques -- some now imitated by Microsoft and Apple, grsecurity makes many attacks technically and economically infeasible by introducing unpredictability and complexity to attempted attacks, while actively responding in ways that deny the attacker another chance.</p>

						<a href="purchase.php" class="btn"><span>Get an offer</span></a>
						<span class="last-updated">3.14.79 Last updated: 04/25/17</span>
						<span class="last-updated">4.4.63 Last updated: 04/25/17</span>
					</div>
				</div><!-- .hexagon-carousel -->
			</div><!-- .hexagon-content-wrap -->
		</div>
	</section>

	<div class="logos-outer-wrap">

		<section class="super-wrap logos-wrap" itemscope itemtype="http://schema.org/Organization">
				<meta itemprop="legalname" content="Open Source Security Inc.">
				<meta itemprop="foundingDate" content="2008">
				<meta itemprop="founder" content="Brad Spengler">
				<meta itemprop="url" content="https://grsecurity.net">
				<meta itemprop="email" content="contact@grsecurity.net">

			<div class="logos-carousel">

				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="Zero Day Initiative">
					<img itemprop="logo" src="img/zdi.png" height='48' width='162' alt="Zero Day Initiative">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="Atomicorp">
					<img itemprop="logo" src="img/atomicorp.png" height='66' width='132' alt="Atomicorp">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="Crowdstrike">
					<img itemprop="logo" src="img/crowdstrike.png" height='37' width='222' alt="Crowdstrike">
				</div>
							<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="DreamHost">
					<img itemprop="logo" src="img/dreamhost.png" height='62' width='392' alt="DreamHost">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="Locaweb">
					<img itemprop="logo" src="img/locaweb.png" height='36' width='160' alt="Locaweb">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="CloudPatch">
					<img itemprop="logo" src="img/cloudpatch.png" height='46' width='208' alt="CloudPatch">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="TEHTRI Security">
					<img itemprop="logo" src="img/tehtri.png" height='42' width='273' alt="TEHTRI Security">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="Exonet">
					<img itemprop="logo" src="img/exonet.png" height='54' width='155' alt="Exonet">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="CCBill">
					<img itemprop="logo" src="img/cwie.png" height='51' width='165' alt="CCBill">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="Skyport Systems">
					<img itemprop="logo" src="img/skyport.png" height='66' width='200' alt="Skyport Systems">
				</div>
				<div itemprop="sponsor" itemscope itemtype="http://schema.org/Organization" class="col-1-6 carousel-cell">
					<meta itemprop="name" content="NBS System">
					<img itemprop="logo" src="img/nbs.png" height='56' width='100' alt="NBS System">
				</div>

			</div>

		</section>

	</div>

	<div class="testimonials-outer-wrap" id="testimonials">
		<section class="wrap testimonials center">
			<h2>Testimonials</h2>

			<div class="testimonials-top">
				<p>When building systems that hold sensitive customer data, no other platform is as trusted by professional security engineers, like those at Immunity, than grsecurity. We have 15 years of experience breaking systems, and grsecurity has 15 years of experience protecting them from people like us.</p>
				<p>A lot of work has been done in the past 17 years on exploit mitigations - some practical, and some effective. Very few mechanisms were both practical and effective. The grsecurity and PaX team have been behind almost all of them.</p>
				<p>The people behind grsecurity/PaX are pioneers in computer security. Your Linux servers are in good hands with them.</p>
				<p>PaX and grsecurity are world class innovators in software security. They have played a pivotal role in creating multiple exploit mitigation technologies that are now considered industry standard.</p>
				<p>During the Bugtraq &quot;golden era&quot; I witnessed first-hand the direct effect
				   of the pioneering research by the grsecurity and PaX team on real world
				   vulnerability exploit feasibility. What was once possible with a simple
				   stack overflow now requires a complex multiple-vulnerability bug chain.</p>
			</div>

			<hr>

			<div class="testimonial-carousel-wrap">
				<div class="testimonial-carousel">
					<div class="carousel-cell">
						<a class="testimonial" href="#">
						<span class="name">Dave Aitel</span><br>
						<span class="company">Immunity, Inc., CEO</span>
						</a>
					</div>

					<div class="carousel-cell">
						<a class="testimonial" href="#">
						<span class="name">Halvar Flake</span><br>
						</a>
					</div>

					<div class="carousel-cell">
						<a class="testimonial" href="#">
						<span class="name">Bruce Dang</span><br>
						</a>
					</div>

					<div class="carousel-cell">
						<a class="testimonial" href="#">
						<span class="name">Matt Miller</span><br>
						</a>
					</div>

					<div class="carousel-cell">
						<a class="testimonial" href="#">
						<span class="name">David Mirza Ahmad</span><br>
						<span class="company">Subgraph, President</span>
						</a>
					</div>

				</div>
			</div>

		</section>
	</div>

	<div class="features-outer-wrap">

		<section class="features center">
			<div class="features-top-section clear">
				<h2>Features</h2>
				<p>Grsecurity provides a full suite of synergistic defenses, from security-enhanced compilation and our world-class memory corruption defenses to access control.</p>
				<a href="features.php" class="btn"><span>Learn More</span></a>
			</div>

			<div class="features-carousel-section">
				<div class="wrap">

				<div class="features-carousel">

						<div class="carousel-cell">

							<div class="big-hex memory">
								<h3>Memory <br>Corruption Defenses</h3>
								<p>We're best known for our memory corruption defenses, whose effectiveness have been repeatedly validated by their eventual adoption in all mainstream operating systems and even processor hardware.</p>

								<a class="fancy-link" href="features.php#memory-corruption-defense">Learn more</a>

							</div>

						</div>

						<div class="carousel-cell">

							<div class="big-hex filesystem-hardening">
								<h3>Filesystem <br>Hardening</h3>
								<p>Our filesystem defenses help isolate users through heavily-hardened chroot jails, preventing webservers from being tricked by symlinks pointing to other users' directories, and much more.</p>

								<a class="fancy-link" href="features.php#filesystem-hardening">Learn more</a>

							</div>

						</div>

						<div class="carousel-cell">

							<div class="big-hex miscellaneous">
								<h3>Miscellaneous <br>Protections</h3>
								<p>We offer a number of unique defenses, like automatically limiting the attack surface of highly-modular kernels without impacting usability.</p>

								<a class="fancy-link" href="features.php#misc-protections">Learn more</a>

							</div>
						</div>

					<div class="carousel-cell">

						<div class="big-hex rbac">
							<h3>RBAC</h3>
							<p>Our Role Based Access Control system auto-learns least privilege policies for an entire system in minutes.  Policy enforcement ensures a secure base and helps eliminate manual policy mistakes.</p>

							<a class="fancy-link" href="features.php#rbac">Learn more</a>

						</div>
					</div>

					<div class="carousel-cell">

						<div class="big-hex plugins">
							<h3>GCC <br>Plugins</h3>
							<p>Defeating ROP, improving entropy on IoT devices, defusing uninitialized stack infoleaks, preventing many exploitable integer overflows: our GCC plugins make it possible.</p>

							<a class="fancy-link" href="features.php#gcc-plugins">Learn more</a>

						</div>

					</div>
				</div>

				</div>
			</div>
		</section>

	</div>

	<div class="latest-news-outer-wrap">

		<div class=" latest-news">
		<h2 class="latest-news-title">Latest News</h2>
		<section class="news-carousel">

				<article class="excerpt carousel-cell">
					<div class="excerpt-img-wrap">
						<span class="excerpt-type">Trending</span>

						<img class="excerpt-img" src="img/trending-header.png" alt="Trending">
					</div>

					<h2 class="excerpt-title">Passing the Baton</h2>

					<a class="fancy-link" href="passing_the_baton.php">Read more</a>

					<div class="excerpt-footer excerpt-meta">
						<span class="byline">By Brad Spengler & PaX Team</span>
						<time datetime="2017-04-26">April, 26 2017</time>
					</div>
				</article>

				<article class="excerpt carousel-cell">
					<div class="excerpt-img-wrap">
						<span class="excerpt-type">Trending</span>

						<img class="excerpt-img" src="img/trending-header.png" alt="Trending">
					</div>

					<h2 class="excerpt-title">New Blog Post: The Infoleak that (Mostly) Wasn't</h2>

					<a class="fancy-link" href="the_infoleak_that_mostly_wasnt.php">Read more</a>

					<div class="excerpt-footer excerpt-meta">
						<span class="byline">By Brad Spengler</span>
						<time datetime="2017-04-23">April, 23 2017</time>
					</div>
				</article>

				<article class="excerpt carousel-cell">
					<span class="excerpt-type">News</span>

					<h2 class="excerpt-title">RAP Demonstrates World-First Fully CFI-Hardened OS Kernel</h2>

					<p>Today's release of grsecurity<sup>&reg;</sup> for Linux kernel version 4.9 makes good on our promise of publishing the implementation of the deterministic type-based return check portion of the Reuse Attack Protector (RAP) initially described at H2HC in October 2015. </p>

					<a class="fancy-link" href="rap_announce2.php">Read more</a>

					<div class="excerpt-footer excerpt-meta">
						<span class="byline">By Brad Spengler & PaX Team</span>
						<time datetime="2017-02-06">February, 6 2017</time>
					</div>
				</article>

				<article class="excerpt carousel-cell">
					<span class="excerpt-type">News</span>
					<h2 class="excerpt-title">RAP is here. Public demo in 4.5 test patch and commercially available today!</h2>

					<p>Today's release of grsecurity<sup>&reg;</sup> for the Linux 4.5 kernel marks an important milestone in the project's history. It is the first kernel to contain RAP, a patent pending defense mechanism against code reuse attacks. RAP is the result of our multi-years research and development in Control Flow Integrity (CFI) technologies by PaX. It ground-breakingly scales to C and C++ code bases of arbitrary sizes and provides best-effort protection against code reuse attacks with minimal performance impact.</p>

					<a class="fancy-link" href="rap_announce.php">Read more</a>

					<div class="excerpt-footer excerpt-meta">
						<span class="byline">By Brad Spengler & PaX Team</span>
						<time datetime="2016-04-28">April, 28 2016</time>
					</div>
				</article>

			</section>
		</div>

	</div>
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
