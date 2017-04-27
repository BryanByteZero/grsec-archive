#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <fcntl.h>
#include <string.h>

int main(int argc, char *argv[])
{
	FILE *f = fopen(argv[1], "rb");
	unsigned char zeroes[64] = { 0 };
	long len;
	unsigned char *buf;
	long i, x;
	unsigned int diff;

	if (f == NULL) {
		printf("Unable to open input file\n");
		return 0;
	}

	fseek(f, 0, SEEK_END);
	len = ftell(f);
	fseek(f, 0, SEEK_SET);

	buf = calloc(1, len);

	if (buf == NULL) {
		printf("Unable to allocate memory.\n");
		return 0;
	}

	fread(buf, 1, len, f);

	fclose(f);

	for (i = 0; i < len - 40; i++) {
		if (memcmp(&buf[i], "\xc7\x44\x24\x08\x01\x00\x00\x00\xc7\x44\x24\x04\x05\x00\x00\x00\x8b\x43\x04\x89\x04\x24\xff\xd7", 24))
			continue;

		printf("SSL option setting found at offset 0x%lx\n", i);

		for (x = i; x < len - 40; x++) {
			if (memcmp(&buf[x], "\x05\x00\x00\x00\x02\x00\x00\x00\x0a\x00\x00\x00", 12))
				continue;

			printf(".text boundary found at offset 0x%lx\n", x);

			buf[x - 64 - 3] = 0x83;
			buf[x - 64 - 2] = 0xec;
			buf[x - 64 - 1] = 0x0c;
			memcpy(&buf[x - 64], &buf[i], 24);
			memcpy(&buf[x - 64 + 24], &buf[i], 24);
			buf[x - 64 + 24 + 12] = 0x09;
			buf[x - 64 + 24 + 24] = 0x83;
			buf[x - 64 + 24 + 24 + 1] = 0xc4;
			buf[x - 64 + 24 + 24 + 2] = 0x0c;
			buf[x - 64 + 24 + 24 + 3] = 0xc3;

			memset(&buf[i], 0x90, 24);
			buf[i] = 0xe8;
			diff = (int)x - 64 - 3 - ((int)i + 5);
			buf[i+1] = diff & 0xff;
			buf[i+2] = (diff >> 8) & 0xff;
			buf[i+3] = (diff >> 16) & 0xff;
			buf[i+4] = (diff >> 24) & 0xff;
			f = fopen(argv[2], "wb");
			if (f == NULL) {
				printf("Unable to create patched file.\n");
				return 0;
			}
			fwrite(buf, 1, len, f);
			fclose(f);
			printf("Successfully patched Pidgin .dll to disable SSL cache\n");
			return 0;
		}
	}

	printf("Unable to patch Pidgin .dll to disable SSL cache\n");

	return 0;
}
