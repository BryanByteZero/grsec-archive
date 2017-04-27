/* ptracing yourself as anti-debugging? that's so 1992AD
   ./zend_anti_debug /usr/bin/sw-engine-cgi
   don't disable security because some silly objdumpable app
   thinks it's actually preventing RE with this nonsense
*/

#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/mman.h>
#include <sys/stat.h>
#include <fcntl.h>

int main(int argc, char *argv[]) {
	struct stat st;
	unsigned char *buf;
	unsigned char *p;
	int fd;
	int ret;

	if (argc != 2)
		return 1;

	ret = stat(argv[1], &st);
	if (ret) {
		fprintf(stderr, "Unable to stat file.\n");
		return 1;
	}

	if (st.st_size != 13332525) {
		fprintf(stderr, "File size doesn't match.\n");
		return 1;
	}

	fd = open(argv[1], O_RDWR);
	if (fd < 0) {
		fprintf(stderr, "Unable to open file.\n");
		return 1;
	}

	buf = (unsigned char *)mmap(NULL, st.st_size, PROT_READ | 
				    PROT_WRITE, MAP_SHARED, fd, 0);
	if (buf == MAP_FAILED) {
		fprintf(stderr, "Unable to mmap file.\n");
		return 1;
	}

	/* set_new_repository */
	for (p = buf + 0x41e671; p < buf + 0x41e75f; p++)
		*p = '\x90';

	/* zif_of_read_key */
	for (p = buf + 0x41566e; p < buf + 0x4157e2; p++)
		*p = '\x90';

	/* zm_startup_swkey */
	for (p = buf + 0x415125; p < buf + 0x415204; p++)
		*p = '\x90';

	munmap(buf, st.st_size);

	close(fd);

	return 0;
}
