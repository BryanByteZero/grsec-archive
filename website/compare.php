<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="NOODP, NOARCHIVE">
<meta name="description" content="grsecurity is an extensive security enhancement to the Linux kernel that defends against a wide range of security threats through intelligent access control, memory corruption-based exploit prevention, and a host of other system hardening that generally require no configuration.">

<title>
grsecurity - Compare</title>


<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=5A5zyaR2my">
<link rel="icon" type="image/png" href="/favicon-32x32.png?v=5A5zyaR2my" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png?v=5A5zyaR2my" sizes="16x16">
<link rel="manifest" href="/manifest.json?v=5A5zyaR2my">
<link rel="mask-icon" href="/safari-pinned-tab.svg?v=5A5zyaR2my" color="#344d83">
<link rel="shortcut icon" href="/favicon.ico?v=5A5zyaR2my">
<meta name="theme-color" content="#ffffff">

<script type='text/javascript' src='/js/header.js'></script>
<link rel='stylesheet' href='/scss/style.css' type='text/css' media='all' />
<link rel="stylesheet" type="text/css" href="/fontawesome/css/font-awesome.min.css" />
</head>
<body>

<header id="top" class="site-header" role="banner">

	<div class="header-content wrap">

		<h1 class="logo">
			<a href="/index.php"><img src="/img/grsecurity.svg" alt="grsecurity"></a>
		</h1>

		
	</div>

</header>

        <main>
                <article>
                        <header class="masthead">
                                <div class="wrap">
					<h1 class="large-headline">&quot;Which is better, grsecurity or SELinux?&quot;</h1>
				</div>
			</header>
			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<p>
We often see questions similar to the above that generally imply an &quot;apples to apples&quot; comparison or suggest that the two are incompatible.  A quick look at our <a href='features.php'>extensive feature set</a> reveals this is far from the truth.  Security is more than just access control, and as has been consistently demonstrated, an easy method of defeating an access control system is through kernel exploitation.  Whether you use containers or SELinux, there exists no project to provide the needed kernel self-protection other than grsecurity.  Further, grsecurity automatically enforces complex experience-driven protections that can't be matched by any access control policy.</p><p>Even if (for whatever reason) you feel SELinux is the pinnacle of mandatory access control, that is still not an argument against using grsecurity or even in fact grsecurity's RBAC system -- unlike the LSMs you're used to, grsecurity and SELinux, or grsecurity and AppArmor, or grsecurity and any other LSM will work together perfectly.  In fact, in addition to all the improvements listed below, grsecurity specifically protects several sensitive data structures involved in SELinux and other LSMs that have made them easy targets for kernel exploit writers.</p><p>A more recent introduction to upstream Linux's public security perception is the late 2015 creation of the Kernel Self-Protection Project (KSPP).  Founded with the worthy goal of improving kernel security, all public evidence of the project shows their efforts have been limited to struggling to understand grsecurity code and upstreaming watered-down versions of a small number of our overall featureset.  These reimplemented features generally lack the comprehensive coverage, security guarantees, and performance of the equivalent features in grsecurity.  Green checkmarks below for KSPP are associated with features implemented directly as a part of KSPP and match the grsecurity counterpart, while the orange minus symbol is reserved for watered-down features that differ significantly in their implementation and security benefits.</p><p>We provide the below feature comparison matrix to illustrate these points.
</p>
<div id="compare_grsec">
<div id="tablemain">
<table id="table_grsec">
<tbody class="">
<tr>
<td class="cat title"></td><td class="title norment"><div class="bigfont">grsecurity</div></td><td class="title norment"><div class="bigfont">SELinux</div></td><td class="title norment"><div class="bigfont">AppArmor</div></td><td class="title norment"><div class="bigfont">KSPP</div></td></tr>
<tr><td class="cat"><div class="lhscat">Mandatory Access Control</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Auditing</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents userland runtime code generation</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment" class="text"><div><p>(dependent on policy)</p></div></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents auto-loading of vulnerable kernel modules by unprivileged users</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment" class="text"><div><p>(dependent on policy)</p></div></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Automatically prevents ptrace-based process snooping</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment" class="text"><div><p>(requires heavy-handed global boolean that prevents normal debugging)</p></div></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Can be used in conjunction with any other LSM</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Reduces the occurrence  of kernel information leaks from a variety of sources</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents arbitrary code execution in the kernel</div></td><td class="norment" class=""><i class="fa fa-check fa-2x green"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents direct access to userland memory from the kernel</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents exploitation of kernel reference counter overflows</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Makes a majority of function pointers and many important data structures in the kernel image read-only</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Hardens userland accessors against malicious use</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-minus-square-o fa-2x yellow"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents many instances of exploitable integer overflows in the kernel</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Adds entropy from a novel source during early boot and runtime</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-minus-square-o fa-2x yellow"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Reduces the lifetime of sensitive data in memory</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Hardens BPF JIT against spray attacks</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Hardens BPF interpreter against malicious corruption</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Randomizes relative offsets of userland thread stacks</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Hardens userland ASLR against information leaks and entropy reduction</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Deters exploit bruteforcing against both userland and the kernel</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Randomizes the layout of sensitive kernel structures</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Automatically prevents webhosting users from causing Apache to access other users' content through symlinks</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents trivial side-channel attacks against administrator terminals</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Automatically converts uses of chroot to hardened jails</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Automatically enforces that execute-only binaries are unreadable</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Enforces consistent multi-threaded task credentials</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Hardens permissions of overly-permissive IPC objects</div></td><td class="norment" class=""><i class="fa fa-check fa-2x green"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Supports Trusted Path Execution, a simple method of preventing users from executing their created binaries</div></td><td class="norment" class=""><i class="fa fa-check fa-2x green"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Can prevent the processing of malicious USB devices inserted at runtime</div></td><td class="norment" class=""><i class="fa fa-check fa-2x green"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents exploitation of kernel stack overflows on 64-bit kernels</div></td><td class="norment" class=""><i class="fa fa-check fa-2x green"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-minus-square-o fa-2x yellow"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Prevents code reuse attacks (ROP, JOP, etc) against the kernel</div></td><td class="norment" class=""><i class="fa fa-check fa-2x green"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment" class=""><i class="fa fa-times fa-2x red"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
<tr><td class="cat"><div class="lhscat">Supports stable Linux kernel versions</div></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-check fa-2x green"></i></td><td class="norment"><i class="fa fa-times fa-2x red"></i></td></tr>
</tbody></table></div></div>
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
