/*

  simple reproducer that tests whether we have commit 198214a7ee50375fa71a65e518341980cfd4b2f0 or not
  ^ from Red Hat
  v from me
  modified since PaX disallows writing to read-only areas of memory via 
  ptrace (which /proc/pid/mem uses the same routines of internally)
  and the original reproducer had both strings located in read-only 
  memory causing a "permission denied" error on the write, instead of a 
  correct vulnerable/not vulnerable report
  v from hunger
  fixed 32-bit arch support
 */

#define _LARGEFILE64_SOURCE 
#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>

int main(int argc, char **argv)
{
  char *s = "not vulnerable";
  char *s2 = "vulnerable";

  int fd;
  loff_t r;

  fd = open("/proc/self/mem", O_RDWR);
  if(fd < 0) {
    perror("open");
    goto end;
  }

  if(lseek64(fd, (off64_t)(unsigned long) &s, SEEK_SET) == (off64_t) -1) {
    perror("lseek64");
    goto end;
  }

  if(write(fd, &s2, sizeof(s2)) < 0) {
    perror("write");
  }

end:
  close(fd);
  printf("%s\n", s);
}
