#include <stdio.h>
#include <sys/mman.h>

int main(void)
{
	void *ptr = mmap(NULL, 0x1000, PROT_READ|PROT_WRITE, MAP_SHARED|MAP_ANONYMOUS, -1, 0);
	printf("%p %d\n", ptr, mprotect(ptr, 0x1000, PROT_READ|PROT_WRITE|PROT_EXEC));
	return 0;
}
