/* gcc -O2 -pedantic -Wall -Wextra -o howard-xorrot howard-xorrot.c */
/* cl /Ox /Wall /W4 howard-xorrot.c */

#include <stdio.h>

static const char header[] = "cookie      after XOR   after ROR   overflow    after ROL   after XOR";

static __inline unsigned long ror(unsigned long val, unsigned long amount)
{
  return (val >> amount) | (val << (8*sizeof val - amount));
}

static __inline unsigned long rol(unsigned long val, unsigned long amount)
{
  return (val << amount) | (val >> (8*sizeof val - amount));
}

static void compute(unsigned long address, unsigned long cookie)
{
  printf("0x%.8lx  0x%.8lx  0x%.8lx  0x%.8lx  0x%.8lx  0x%.8lx\n",
         cookie,
         address ^ cookie,
         ror(address ^ cookie, cookie),
         ror(address ^ cookie, cookie) & ~0xFFUL,
         rol(ror(address ^ cookie, cookie) & ~0xFFUL, cookie),
         rol(ror(address ^ cookie, cookie) & ~0xFFUL, cookie) ^ cookie
        );
}

int main(int argc, char *argv[])
{
  unsigned long address, cookie = 0;

  switch (argc) {
  case 3:
    if (sscanf(argv[2], "%lx", &cookie) != 1) {
      printf("invalid cookie: %s\n", argv[2]);
      return 0;
    }

  case 2:
    if (sscanf(argv[1], "%lx", &address) != 1) {
      printf("invalid address: %s\n", argv[1]);
      return 0;
    }
    break;

  default:
    printf("usage: %s address [cookie]\n", argv[0]);
    return 0;
  }

  puts(header);
  if (argc == 3) {
    compute(address, cookie);
    return 0;
  }

  do {
    for (; !(cookie & 0x20UL); ++cookie)
      compute(address, cookie);
    cookie -= 0x20UL;
    cookie += 0x01000000UL;
  } while (cookie);

  return 0;
}
