/* compile with -m64 and without PIE */

char *path = "/usr/bin/id";
char *blah2[] = { "/usr/bin/id", NULL };

 int main(int argc, char *argv[])
 {

	/* perform X32(not i386) execve */
	asm volatile (
		".intel_syntax noprefix\n"
		"mov rdi, path\n"
		"lea rsi, blah2\n"
		"xor rdx, rdx\n"
		"mov rax, 0x40000208\n"
		"syscall\n"
		".att_syntax noprefix\n"
	);

//	execl("/usr/bin/id", "/usr/bin/id", NULL);

	return 0;
}
