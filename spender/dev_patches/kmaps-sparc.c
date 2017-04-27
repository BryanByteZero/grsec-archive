/*
 * kmaps: simple page table walker based on /dev/mem
 *
 * author: PaX Team <pageexec@freemail.hu> in 2008.01
 *
 * gcc -W -Wall -pedantic -std=c99 -O2 kmaps.c -o kmaps
 *
 * the only argument is the physical address of the page directory, in hex
 *
 * example usage on a normal amd64: kmaps 201000
 *
 * colors represent the page table level and exec/non-exec (light/normal) status
 */

#include <errno.h>
#include <fcntl.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/mman.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>

#define GREEN		"\033[32m"
#define LIGHTGREEN	"\033[32m\033[1m"
#define YELLOW		"\033[33m"
#define LIGHTYELLOW	"\033[33m\033[1m"
#define RED		"\033[31m"
#define LIGHTRED	"\033[31m\033[1m"
#define BLUE		"\033[34m"
#define LIGHTBLUE	"\033[34m\033[1m"
#define BRIGHT		"\033[m\033[1m"
#define NORMAL		"\033[m"


#define _AC(x,y) x##y


/* PMD_SHIFT determines the size of the area a second-level page
 * table can map
 */

#define PAGE_SHIFT	13

#define PMD_SHIFT       (PAGE_SHIFT + (PAGE_SHIFT-3))
#define PMD_SIZE        (_AC(1,UL) << PMD_SHIFT)
#define PMD_MASK        (~(PMD_SIZE-1))
#define PMD_BITS        (PAGE_SHIFT - 2)

/* PGDIR_SHIFT determines what a third-level page table entry can map */
#define PGDIR_SHIFT     (PAGE_SHIFT + (PAGE_SHIFT-3) + PMD_BITS)
#define PGDIR_SIZE      (_AC(1,UL) << PGDIR_SHIFT)
#define PGDIR_MASK      (~(PGDIR_SIZE-1))
#define PGDIR_BITS      (PAGE_SHIFT - 2)


#define _PAGE_VALID       _AC(0x8000000000000000,UL) /* Valid TTE            */
#define _PAGE_R           _AC(0x8000000000000000,UL) /* Keep ref bit uptodate*/

/* SUN4U pte bits... */
#define _PAGE_SZ4MB_4U    _AC(0x6000000000000000,UL) /* 4MB Page             */
#define _PAGE_SZ512K_4U   _AC(0x4000000000000000,UL) /* 512K Page            */
#define _PAGE_SZ64K_4U    _AC(0x2000000000000000,UL) /* 64K Page             */
#define _PAGE_SZ8K_4U     _AC(0x0000000000000000,UL) /* 8K Page              */
#define _PAGE_NFO_4U      _AC(0x1000000000000000,UL) /* No Fault Only        */
#define _PAGE_IE_4U       _AC(0x0800000000000000,UL) /* Invert Endianness    */
#define _PAGE_SOFT2_4U    _AC(0x07FC000000000000,UL) /* Software bits, set 2 */
#define _PAGE_RES1_4U     _AC(0x0002000000000000,UL) /* Reserved             */
#define _PAGE_SZ32MB_4U   _AC(0x0001000000000000,UL) /* (Panther) 32MB page  */
#define _PAGE_SZ256MB_4U  _AC(0x2001000000000000,UL) /* (Panther) 256MB page */
#define _PAGE_SZALL_4U    _AC(0x6001000000000000,UL) /* All pgsz bits        */
#define _PAGE_SN_4U       _AC(0x0000800000000000,UL) /* (Cheetah) Snoop      */
#define _PAGE_RES2_4U     _AC(0x0000780000000000,UL) /* Reserved             */
#define _PAGE_PADDR_4U    _AC(0x000007FFFFFFE000,UL) /* (Cheetah) pa[42:13]  */
#define _PAGE_SOFT_4U     _AC(0x0000000000001F80,UL) /* Software bits:       */
#define _PAGE_EXEC_4U     _AC(0x0000000000001000,UL) /* Executable SW bit    */
#define _PAGE_MODIFIED_4U _AC(0x0000000000000800,UL) /* Modified (dirty)     */
#define _PAGE_FILE_4U     _AC(0x0000000000000800,UL) /* Pagecache page       */
#define _PAGE_ACCESSED_4U _AC(0x0000000000000400,UL) /* Accessed (ref'd)     */
#define _PAGE_READ_4U     _AC(0x0000000000000200,UL) /* Readable SW Bit      */
#define _PAGE_WRITE_4U    _AC(0x0000000000000100,UL) /* Writable SW Bit      */
#define _PAGE_PRESENT_4U  _AC(0x0000000000000080,UL) /* Present              */
#define _PAGE_L_4U        _AC(0x0000000000000040,UL) /* Locked TTE           */
#define _PAGE_CP_4U       _AC(0x0000000000000020,UL) /* Cacheable in P-Cache */
#define _PAGE_CV_4U       _AC(0x0000000000000010,UL) /* Cacheable in V-Cache */
#define _PAGE_E_4U        _AC(0x0000000000000008,UL) /* side-Effect          */
#define _PAGE_P_4U        _AC(0x0000000000000004,UL) /* Privileged Page      */
#define _PAGE_W_4U        _AC(0x0000000000000002,UL) /* Writable             */


#define VA_BITS		44U
#define VA_SIZE		(1ULL << VA_BITS)
#define CANONICALIZE(x)	((((x) | ~(VA_SIZE - 1ULL)) ^ (VA_SIZE / 2)) + (VA_SIZE / 2))
//#define PAGE_SIZE	4096U
#define PAGE_SIZE 	8192U

#define PER_PAGE(x)	(PAGE_SIZE / sizeof (x))
//#define PHYS_MASK	0xFFFFF000U
//#define PHYS_MASK_PAE	0xFFFFFFFFFF000ULL
#define PHYS_MASK	_PAGE_PADDR_4U
//#define PTE_PRESENT	0x01U
#define PTE_PRESENT	_PAGE_VALID

//#define PTE_LARGE	0x80U
#define PTE_LARGE	_PAGE_SZ4MB_4U

//#define PTE_NX		0x8000000000000000ULL
#define PTE_NX		_PAGE_EXEC_4U

#define PTE_EXECUTABLE	(PTE_PRESENT | PTE_NX)
#define PRESENT(x)	((x) & PTE_PRESENT)
#define LARGE(x)	((x) & PTE_LARGE)
#define EXECUTABLE(x)	(((x) & PTE_EXECUTABLE) == PTE_EXECUTABLE)

#define TSB_TAG_INVALID_BIT 46

/* to find an entry in a page-table-directory. */
#define pgd_index(address)      (((address) >> PGDIR_SHIFT) & (PTRS_PER_PGD - 1))
#define pgd_offset(pgd, address) ((pgd) + pgd_index(address))

/* to find an entry in a kernel page-table-directory */
#define pgd_offset_k(address) pgd_offset(&init_mm, address)

/* Find an entry in the second-level page table.. */
#define pmd_offset(pudp, address)       \
        ((pmd_t *) pud_page_vaddr(*(pudp)) + \
         (((address) >> PMD_SHIFT) & (PTRS_PER_PMD-1)))

/* Find an entry in the third-level page table.. */
#define pte_index(dir, address) \
        ((pte_t *) __pmd_page(*(dir)) + \
         ((address >> PAGE_SHIFT) & (PTRS_PER_PTE - 1)))
#define pte_offset_kernel               pte_index
#define pte_offset_map                  pte_index
#define pte_offset_map_nested           pte_index


char *print_flags(char *buf, unsigned long tte)
{
	int size = 0;

	if (tte & _PAGE_SZ64K_4U)
		size = 64;
	else if (tte & _PAGE_SZ512K_4U)
		size = 512;
	else if (tte & _PAGE_SZ4MB_4U)
		size = 4096;
	else
		size = 8;
	

	sprintf(buf, "%s%s%s%s%s%s%s %dk", (tte & _PAGE_READ_4U) ? "R" : "-",
				   (tte & _PAGE_WRITE_4U) ? "W" : "-",
				   (tte & _PAGE_EXEC_4U) ? "X" : "-",
				   (tte & _PAGE_PRESENT_4U) ? "P" : "-",
				   (tte & _PAGE_VALID) ? "V" : "-",
				   (tte & _PAGE_W_4U) ? "w" : "-",
				   (tte & _PAGE_P_4U) ? "p" : "-",
				   size);

	return buf;
}

static void dump_pgd(int devmem, off_t pgd)
{
  unsigned long *pt, i, va = 0;
  static unsigned long buffer2[PAGE_SIZE / sizeof(unsigned long)];
  char flags[32];

  pt = mmap(NULL, PAGE_SIZE, PROT_READ, MAP_PRIVATE, devmem, pgd);
  if (-1 == pt) {
    if ((off_t)-1 == lseek(devmem, pgd , SEEK_SET))
      return;
	printf("%d devmem=%d\n", sizeof buffer2, devmem);
    if (sizeof buffer2 != read(devmem, buffer2, sizeof buffer2)) {
      printf("didn't read amount we wanted\n");
      return;
    }
    pt = buffer2;
  }

  for (i = 0; i < (PAGE_SIZE)/sizeof(unsigned long); i++) {
    if (pt[i] & _PAGE_VALID) {
      printf("%sPGD: %03x %016lx phys:%016lx va:%016lx %s%s\n", LIGHTYELLOW, i, pt[i], pt[i] & _PAGE_PADDR_4U, (pt[i] & _PAGE_PADDR_4U) + 0xFFFFF80000000000UL, print_flags(flags, pt[i]), NORMAL);
      fflush(stdout);
      //dump_pgd(devmem, (pt[i+1] & _PAGE_PADDR_4U) + 0xFFFFF80000000000ULL);
    }
  }

  if (pt != buffer2)
    munmap(pt, PAGE_SIZE);
}


static void dump_tsb(int devmem, off_t pgd)
{
  unsigned long *pt, i, va = 0;
  static unsigned long buffer[(32 * 1024) / 8];
  char flags[32];
  int ret;
  off_t off;

  pt = mmap(NULL, 32 * 1024, PROT_READ, MAP_PRIVATE, devmem, pgd);
  if (-1 == pt) {
    off = lseek(devmem, pgd, SEEK_SET);
    printf("seeked to %016lx\n", off);
    if ((off_t)-1 == off)
	return;
	printf("%d devmem=%d\n", sizeof buffer, devmem);
    ret = read(devmem, buffer, sizeof(buffer));
    if (ret != sizeof(buffer)) {
      printf("didn't read amount we wanted, got %d, %s\n", ret, strerror(errno));
      return;
    }
    pt = buffer;
  }

  printf("PRINTING TSB:\n");
  for (i = 0; i < (32 * 1024)/sizeof(unsigned long); i += 2) {
    if (!(pt[i] & (1UL << TSB_TAG_INVALID_BIT))) {
      printf("%sTSB: %03x %016lx phys:%016lx va:%016lx %s tag:%lx%s\n", LIGHTRED, i, pt[i+1], pt[i+1] & _PAGE_PADDR_4U, (pt[i+1] & _PAGE_PADDR_4U) + 0xFFFFF80000000000UL, print_flags(flags, pt[i+1]), pt[i], NORMAL);
      fflush(stdout);
      dump_pgd(devmem, (pt[i+1] & _PAGE_PADDR_4U) + 0xFFFFF80000000000UL);
    }
  }

  if (pt != buffer)
    munmap(pt, 32 * 1024);
}

int main(int argc, char *argv[])
{
  off_t pgd;
  int devmem, pgd_size;

  if (argc != 2) {
    printf("usage: %s <pgd %sPHYSICAL%s address, e.g., %sswapper_pg_dir%s or %sinit_level4_pgt%s>\n",
           argv[0], LIGHTRED, NORMAL, LIGHTYELLOW, NORMAL, LIGHTYELLOW, NORMAL);
    return 1;
  }

  if (1 != sscanf(argv[1], "%lx", &pgd))
    return 2;

  printf("%lx\n", pgd);
  devmem = open("/dev/kmem", O_RDONLY);
  if (-1 == devmem) {
    printf("unable to open /dev/kmem\n");
    return 4;
  }

  pgd += 0xfffff80000000000UL;
  dump_tsb(devmem, pgd);

  close(devmem);
  return 0;
}
