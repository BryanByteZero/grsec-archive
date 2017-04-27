#include <stdio.h>
#include <sys/mman.h>
#include <stdlib.h>

#define THREAD_SIZE 8192
#define MAGIC -0xbeef

void clear_stack(unsigned long sp)
{
	unsigned long *low = (unsigned long *)(sp & ~(THREAD_SIZE - 1));
	unsigned long *high = (unsigned long *)(sp & ~(64 - 1));
	unsigned long *mid;
	int ecx;
	int iters = 0;
	int i;
	unsigned long diff;
	while (high > low) {
		diff = ((unsigned long)high - (unsigned long)low)/sizeof(unsigned long);
		// if we're within 128 bytes, don't bother, just clear starting from the low point
		if (diff <= 128/sizeof(unsigned long)) {
			mid = low;
			break;
		} else {
			mid = low + (diff >> 1);
		}
			
		printf("low=%p mid=%p high=%p\n", low, mid, high);
		for (ecx = 0; ecx < (64/sizeof(unsigned long)); ecx++) {
			if (mid[ecx] != MAGIC)
				break;
		}
		if (ecx == (64/sizeof(unsigned long)))
			low = mid + 64/sizeof(unsigned long);
		else
			high = mid;
		iters++;
	}

	printf("iters: %d, going to memset %d bytes\n", iters, sp - (unsigned long)mid);

	for (i = 0; i < (sp - (unsigned long)mid)/sizeof(unsigned long); i++)
		mid[i] = MAGIC;

	low = (unsigned long *)(sp & ~(THREAD_SIZE - 1));
	high = (unsigned long *)(sp & ~(64 - 1));

	while (low < high) {
		if (*low != MAGIC)
			printf("didn't clear the whole stack\n");
		low++;
	}
			
	return;
}

int main(void)
{
	int i;
	int x;
	int randnum;

	unsigned long *buf = mmap(NULL, 65536, PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);

	buf = (unsigned long *)(((unsigned long)buf + 32768) & ~(THREAD_SIZE - 1));

	srand(getpid());
	
	for (x = 0; x < 1000000; x++) {	
		randnum = (rand() % 8192);
		printf("filling %d bytes with MAGIC\n", randnum);
		for (i = 0; i < randnum/sizeof(unsigned long); i++)
			buf[i] = MAGIC;
		for (; i < 8192/sizeof(unsigned long); i++)
			buf[i] = 2;
	
		clear_stack((unsigned long)buf + 8192 - 10);

	}
}
