#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>

void usage(void)
{
	printf("Usage: vmlinuz_to_vmlinux_gz <vmlinuz> <vmlinux.gz>\n");
	exit(0);
}

int main(int argc, char *argv[])
{
	FILE *fin, *fout;
	long fsize, i;
	size_t ret;
	char *buf;

	if (argc != 3)
		usage();

	fin = fopen(argv[1], "r");
	if (fin == NULL) {
		fprintf(stderr, "Unable to open %s for reading.\n", argv[1]);
		exit(1);
	}
	fout = fopen(argv[2], "w");
	if (fout == NULL) {
		fprintf(stderr, "Unable to open %s for writing.\n", argv[2]);
		exit(1);
	}

	fseek(fin, 0, SEEK_END);
	fsize = ftell(fin);
	fseek(fin, 0, SEEK_SET);

	buf = malloc(fsize);
	if (buf == NULL) {
		fprintf(stderr, "Unable to allocate %lu bytes.\n", fsize);
		exit(1);
	}

	ret = fread(buf, fsize, 1, fin);
	if (ret != 1) {
		fprintf(stderr, "Unable to read from %s\n", argv[1]);
		exit(1);
	}

	for (i = 0; i < fsize - 4; i++) {
		if (!memcmp(&buf[i], "\x1f\x8b\x08\x00", 4))
			break;
	}

	if (i == (fsize - 4)) {
		fprintf(stderr, "Gzip signature not found in %s\n", argv[1]);
		exit(1);
	}

	ret = fwrite(&buf[i], fsize - i, 1, fout);
	if (ret != 1) {
		fprintf(stderr, "Error writing to %s\n", argv[2]);
		exit(1);
	}

	fclose(fin);
	fclose(fout);

	return 0;
}
