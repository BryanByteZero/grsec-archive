#include <stdio.h>
#include <sys/mman.h>
#include <fcntl.h>
#include <unistd.h>
#include <string.h>
#include <stdlib.h>
#include <sys/stat.h>

int main(int argc, char *argv[])
{
	int fd;
	unsigned char *buf;
	struct stat st;
	unsigned char expected[] = { 0x7f, 0x45, 0x4c, 0x46, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0 };
	unsigned char expected2[] = { 0x7f, 0x45, 0x4c, 0x46, 1, 1, 1, 3, 0, 0, 0, 0, 0, 0, 0, 0 };
	unsigned char patch[] = { 0x31, 0xc9, 0x90, 0x90, 0x90, 0x90 };
	int i;
	unsigned int GOTPLT;
	unsigned int expectedaddr;
	int found = 0;
	char newpath[4096];
	if (argc != 3)
		return -1;

	snprintf(newpath, sizeof(newpath)-1, "%s.new", argv[1]);

	GOTPLT = strtoul(argv[2], NULL, 16);

	fd = open(newpath, O_RDWR);
	if (fd < 0)
		return -1;

	fstat(fd, &st);

	buf = mmap(NULL, st.st_size, PROT_READ | PROT_WRITE, MAP_SHARED, fd, 0);

	if (buf == (void *)-1)
		return -1;

	for (i = 0; i < st.st_size - 36; i+=16) {
		if (!memcmp(&buf[i], &expected, 16) && !memcmp(&buf[i+16], &expected2, 16) &&
		    buf[i+32+3] == 0xff) {
			printf("found 'expected'\n");
			found = 1;
			expectedaddr = i + 9;
		}
	}
	if (found == 0) {
		printf("didn't find 'expected'\n");
		goto out;
	}

	/* looking for the lea referencing the zeroed e_ident field around code doing an inlined memcmp
	   nop out the next jnz and set the condition code properly
	*/
	for (i = 5; i < st.st_size - 16; i++) {
		if (buf[i] == 0x8d && buf[i+1] == 0xbb && *(unsigned int *)&buf[i+2] == (expectedaddr - GOTPLT)) {
			while (buf[i] != 0x0f || buf[i+1] != 0x85 || buf[i+4] != 0 || buf[i+5] != 0) {
				if (!memcmp(&buf[i], &patch, 6)) {
					printf("file is already patched!\n");
					goto out;
				}
				i++;
			}

			printf("found drepper's memcmp, patching!\n");
			/* patch jnz with xor ecx, ecx, followed by nops */
			memcpy(&buf[i], &patch, 6);

			munmap(buf, st.st_size);
			close(fd);
			rename(newpath, argv[1]);
			return 0;
		}
	}
	printf("didn't find drepper's memcmp\n");
out:
	munmap(buf, st.st_size);
	close(fd);
	return 0; 
}
