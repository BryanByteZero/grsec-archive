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
					<h1 class="large-headline">Frequently Asked Questions About RAP</h1>
				</div>
			</header>

			<section class="bar pull-up blog">
				<div class="wrap">
					<div class="panel">
<h2><span>What are code reuse attacks? Why have they resisted defenses up to now?</span></h2>
<p>To explain this story fully, we need some historical context.  Since the late 1990s, exploitation of memory corruption vulnerabilities had largely used what's generally called &quot;shellcode&quot; to achieve complete control over a compromised application.  In 2000 with the release of PAGEEXEC and MPROTECT by the PaX Team, the use of shellcode (or in more precise words, the introduction of attacker-provided arbitrary code) became impossible.</p>
<p>Years later the rest of the industry mostly caught up (with NX in processors, DEP in Windows, etc), though some OSes still have not fully matched the guarantees provided by MPROTECT in particular and thus are affected by clever bypasses every few years.  Since the introduction of PAGEEXEC back in 2000, another type of exploitation vector became immediately apparent &mdash; what was initially called &quot;ret2libc&quot;, then &quot;borrowed code chunks&quot; and finally the formalized &quot;ROP&quot; &mdash; attackers could, instead of introducing their own code, reuse existing code in the application to achieve the same goal.  For example, the system() function exists in the glibc library, so rather than using shellcode that would execute a shell, an exploit could redirect the program to execute the existing system() function from anywhere in the program to force it to execute a shell on the exploit's behalf.</p>
<p>In 2001 the PaX Team created the now widely known Address Space Layout Randomization (ASLR) as an easily-implemented and inexpensive probabilistic defense against these kinds of attacks, more generically referred to as &quot;code reuse attacks&quot;.  The idea was that since code reuse attacks require some knowledge about the location of the existing code being executed (the address of the system() function for instance), then making it more difficult to find the location of that code in a predictable, reliable way made these attacks more costly and unreliable.  The problem was that, though ASLR was conceptually simple and easy to implement, it had a significant (main) weakness: information leaks.  If an attacker can see or infer where code is located, in many cases the exploit can dynamically adjust itself to continue as if ASLR isn't there.  These kinds of leaks also improve reliability in cases where the contents of the code may not be known beforehand (say with different systems running different versions of the same library).</p>
<p>Since 2003 with the publication of the PaX Team's <a href="https://pax.grsecurity.net/docs/pax-future.txt">pax-future.txt</a>, academia and the rest of the industry have struggled to come up with a solution to the problem.  A formalized version of what was present in pax-future.txt became known as Control Flow Integrity (CFI) two years later in 2005.  Many additional academic papers followed and the industry came up with a number of ad-hoc stopgaps for subsets of the problem that generally have been swiftly defeated and bypassed.  But no one, not even the large teams at Microsoft and Google, had been able to accomplish three things with any of these proposed solutions: make it scale to arbitrary-sized codebases, make it fast, and make it secure enough to consider the entire class of attacks infeasible for good.  This brings us to the Reuse Attack Protector (RAP).</p>
<br/>
<h2><span>What makes RAP so important?</span></h2>
<p>The difficulty of achieving the three goals mentioned above cannot be understated.  Initial CFI implementations and even those in real life use today (like Microsoft's Control Flow Guard or Google's Indirect Function-Call Checks) implement what is called &quot;forward-edge&quot; CFI.  This means that they implement the security checks only when jumping or calling to a particular function, but have no checks for when that function is returning back somewhere.  Though protections like SSP have existed for many years now, they're not a true defense against attacks on the return from a function.  The idea of SSP and similar defenses (like Microsoft's /GS) is that there's a &quot;canary&quot; value located near the return address to be protected.  In the event of some kinds of stack buffer overflows, since the canary is placed between the overflowing buffer and the targeted return address, the canary would be overwritten in the attack.  So much like a canary of the bird type detecting carbon monoxide before it could harm the coal miners, SSP and the like instrument code so that prior to returning from a function, the value of the canary would be checked to make sure no harm had come to the return address.  But these defenses have run into three major problems over the years: 1) it's not always the case that to modify the return address that one needs to overwrite the canary, 2) the canary value can be leaked similar to how we discussed with ASLR, and 3) due to performance considerations and the unoptimized implementation of these defenses, the checks often didn't exist for functions that should have been protected.</p>
<p>RAP has a defined threat model: it operates based on the assumption that an attacker already has the most powerful &quot;exploit primitives&quot; at his or her disposal: the ability to read from and write to anywhere in memory an arbitrary number of times.  Many other proposed solutions to code reuse attacks were not designed with this threat model in mind and so have fallen prey to attacks that don't apply to RAP.  With this realistic threat model, techniques like ASLR and /GS no longer hold up.</p>
<p>Going back to CFI, another problem academics have struggled with is the balance between security and performance.  Many of the same forward-edge CFI implementations are also what are called &quot;coarse-grained&quot; CFI.  That is, given a particular call or jump to a function, the number of functions the CFI implementation would allow an attacker to call is very large: well outside of what would occur during legitimate execution, and in many implementations, any function at all in the program and its libraries.  These &quot;coarse-grained&quot; implementations have been common for two reasons: performance and information limitations.  The more fine-grained the implementation, generally the more expensive the checks at each call or jump site become.  Particularly with C++ applications that would require many of these checks, this could cripple the performance of the application.  Respectively, especially with CFI methods that don't require source code, it was difficult to come up with a classification method that would meet the requirements of limiting the permitted call targets to the smallest set possible without introducing false positives.</p>
<p>Trying to solve this problem led most CFI proposals to run into another issue: scalability.  To classify the functions, these implementations required knowledge of the entire program to be fit in memory all at once through a compilation technique known as Link-Time Optimization (LTO).  For small codebases this isn't an issue, but at the scale of the Linux kernel or the Chromium browser, this poses severe constraints, as Google themselves discovered in their CFI attempts.</p>
<p>Finally, several existing CFI implementations (specifically those developed by Google) each target very specific subcategories of C/C++: some don't protect C function pointers, others only protect virtual calls, and none provide return address protection.  To compare performance of these weaker solutions, you would need to sum their individual performance impacts and add in the cost of return address protection to compare to RAP.</p>
<p>There are several key points to RAP that make it the best defense against code reuse attacks.  It is resistant against all the attacks previously mentioned, even in the face of an attacker with the most valuable memory corruption vulnerability at his or her disposal.  It is implemented at the proper level in the compiler, early enough that the compiler itself can optimize the changes made by RAP to improve performance.  Adding to this, RAP knows when checks should be made and more importantly, when those checks can be eliminated while provably not reducing the security of the defense.  The checks performed at each location by RAP are much faster than competing CFI solutions, which means it doesn't have to reduce coverage to achieve high performance, and it can add even more defenses within its performance budget that competitors cannot match.  Finally, RAP gets as close as possible to classifying which functions a particular call or jump site may use, and has the ability to make use of simple code changes to restrict the groups even further.  It does this in a way that scales to large codebases by not requiring all the information about a program to be in memory at once.</p>
<br/>
<h2><span>How does RAP work?</span></h2>
<p>RAP is implemented as a GCC compiler plugin.  This means you don't need to use a specially modified compiler; you can use whichever version of GCC is provided by your Linux distribution or embedded vendor.  The commercial version of RAP has two components. The first is a deterministic defense limiting both what functions can be called from a given place as well as what locations may be returned to from that function.  The second is a probabilistic defense to help ensure that a function can return not just to a group of various call sites as defined by the first defense, but in fact only to the location from which the function was called.</p>
<p>The first defense makes use of type information from a program and by using a hashing function, can create a set of hashes such that the number of hashes closely resembles the number of possible different types for functions used by the program.  As mentioned earlier, RAP can also make use of simple code changes to increase the granularity even further.  Say multiple functions exist that take a single string argument and don't return a value.  RAP can make use of the ability of C and C++ to give a known type a different name, say &quot;sensitive_string&quot;, to split that group of functions into two while retaining the same exact code semantics.  Since the hashes are based off type information, all the information required by RAP can be obtained within a single compilation unit, rather than needing all the information about the program at once like competing solutions mentioned earlier.</p>
<p>The second defense is more complicated.  On entry to a function, it essentially &quot;encrypts&quot; the address being returned to by the function, prior to any code that could possibly corrupt the return address.  The key used to encrypt the return address is stored in a reserved CPU register, generally ensuring that the key itself should not leak.  The resulting value of encrypting the return address gets saved in a register, but the actual return address in memory is not modified.  On return from the function, the instrumented code will compare whatever return address exists at that point (either legitimate or attacker-modified) to that obtained from decrypting the encrypted return address saved in the other register.  If the two do not match, execution is terminated.  One note of caution: though the encryption key itself is highly resistant to leaking as it shouldn't be stored or spilled into memory, through separate information leaks of two types, it's possible to infer the key.  This is why the deterministic, type-hash-based RAP protection remains in place for returns from functions.  The good news is that in many cases, the encryption key doesn't have to stay the same for the lifetime of the thread, process, or kernel.  In the kernel, for instance, each system call can use a new key.  Likewise on iterations of infinite loops like the kernel's scheduler, a new key can be used.  These can both limit the potential damage posed by information leaks.</p>
<p>This covers RAP from a high level &mdash; of course the reasons for its high performance and security have to do with specific implementation details: instruction encodings, using the fastest instruction sequences possible for checks, complex optimization passes, and knowing how and where to eliminate checks completely without sacrificing security guarantees.</p>
<br/>
<h2><span>What about unaligned instructions?</span></h2>
<p>One corollary of RAP that isn't immediately obvious to many is that the threat of unaligned instructions goes away naturally, without having to implement performance-heavy defenses like forced 16-byte instruction alignment.  The reason becomes clear when you think about an attack in the order in which it would occur: a function pointer or return address becomes modified by an attacker who then points it in the middle of an existing instruction to result in some useful unintentional sequence of instructions.  Since RAP ensures deterministically all locations where a potentially corruptible function pointer or return address is used (what is called &quot;indirect control flow&quot;) can only transfer to legitimate locations, it thus prevents transferring to the middle of instructions or anywhere else that isn't a valid site.  An interesting facet of the type hash encoding also ensures a function can't return to the beginning of a function, nor the opposite case of a call or jump site transferring control to another call or jump site.</p>
<br/>
<h2><span>How does RAP handle shared libraries?</span></h2>
<p>The beauty of RAP's type-hash-based deterministic defense is that it handles shared libraries much more easily than other CFI approaches.  Some other fine-grained CFI proposals have to apply complex, performance-intensive algorithms at runtime (generally at library load time).  Yet other CFI approaches deal with shared libraries by weakening their classifications of functions, thus lowering the security of the defense.  In contrast, since all compilation units agree on the standard for type hash creation, calling a function in a shared library is no different with RAP than making an indirect call to a function in the main executable itself.</p>
<p>RAP can be gradually introduced into a larger codebase.  It's possible to use it in a mode that only emits the type hashes for functions without instrumenting the code with verification checks at call, jump, and return sites.  In this way, the mismatches between function pointer prototypes in library dependencies and the functions they're designed to call (which RAP would detect at compile time) do not need to be fixed up before the application can successfully run.  Of course, indirect control flows occurring in those unfixed libraries would not be protected by RAP with the deterministic type hash defense.</p>
<br/>
<h2><span>How does RAP handle Just-In-Time compilation (JIT)?</span></h2>
<p>Current JIT engines haven't been architected with security in mind.  The most secure method of generating code at runtime is by enforcing the separation of use of the code from the creation of the code.  This can be accomplished by splitting the JIT engine out into a separate process, as done by <a href="http://wenke.gtisc.gatech.edu/papers/sdcg.pdf">SDCG</a>.  Further, the JIT engine would need to be modified to emit hashes usable by RAP and ensure the JIT encoding doesn't allow an attacker enough leeway to control eight consecutive bytes of the JIT output that could be interpreted as a valid RAP hash.  To fake the RAP hash for a C++ virtual call, the attacker would need to control sixteen consecutive bytes.  The normal constant-blinding techniques used by modern JIT implementations are sufficient for this task.</p>
<br/>
<h2><span>What's the deal with the license?</span></h2>
<p>The versions of the GCC compiler which support plugins like RAP are provided under the GPLv3.  Unlike the GPLv2, the GPLv3 allows a copyright holder (in this case the Free Software Foundation) to create special license exceptions.  In creating the GCC plugin support, which allows access to internal GCC headers and APIs, the FSF wanted to avoid there being a market for proprietary GCC plugins being sold that piggy-back off the many years of work of the GCC developers.  The specific exception the FSF came up with is detailed at <a href="http://www.gnu.org/licenses/gcc-exception-3.1.en.html">http://www.gnu.org/licenses/gcc-exception-3.1.en.html</a>.</p>
<p>In the exception, called the &quot;GCC Runtime Library Exception&quot;, it defines a term called &quot;eligible compilation&quot;.  The FSF defines eligible compilation as a binary compiled with a toolchain where each component is licensed with something compatible with GCC's GPLv3 license, where the components include GCC itself as well as any associated GCC plugins.  The exception states that a binary may only be linked against the GCC runtime libraries (libgcc, libstdc++) if the binary was produced through the eligible compilation process.  As the kernel is not linked with the GCC runtime libraries, this exception does not apply, and so the license of the public RAP demo is under the GPLv2.  Since however the GPLv2 is incompatible with GPLv3, then this makes the userland binaries (which do link with the GCC runtime libraries) compiled through a non-eligible compilation process.  Distributing these userland binaries would be illegal and would violate the copyright of the FSF (but not that of the PaX Team).</p>
<p>As sole copyright holder on the RAP plugin itself, the PaX Team is only licensing the full version under a GPLv3 license to commercial customers to permit legal compilation of userland binaries.</p>
<br/>
<p>RAP is available commercially today.  Reach us at <i>contact@grsecurity.net</i> for details.</p>
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
