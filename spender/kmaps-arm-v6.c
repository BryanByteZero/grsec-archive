/*
 * ARM v6+ short descriptor table walker using AP[2:0] access permissions model
 * Rewrite of PaX Team's kmaps.c
 * ./kmaps 40004000 to see all pmds
 */

#include <errno.h>
#include <fcntl.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/mman.h>
#include <sys/stat.h>
#include <sys/types.h>

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

#define PTRS_PER_PTE            256
#define PTRS_PER_PMD            1
#define PTRS_PER_PGD            4096

#define PHYS_MEM_SIZE	(2UL * 1024 * 1024 * 1024)

#define PAGE_SIZE	4096U
#define PAGE_SHIFT	12
#define PER_PAGE(x)	(PAGE_SIZE / sizeof (x))

#define IS_TABLE(x)	(((x) & 3) == 1)
#define IS_LARGE(x)	(!((x) & 2))
#define IS_PRESENT(x)	((x) & 3)
#define PTE_XN		(1UL << 0)
#define LARGE_XN	(1UL << 15)
#define PAGE_TABLE_PXN	(1UL << 2)
#define SECT_PXN	(1UL << 0)
#define SECT_XN		(1UL << 4)
#define SECT_AP2	(1UL << 15)
#define SECT_AP1	(1UL << 11)
#define SECT_AP0	(1UL << 10)
#define SECT_AP_MASK	(SECT_AP2 | SECT_AP1 | SECT_AP0)
#define IS_SECT_URW(x)	(!((x) & SECT_AP2) && ((x) & (SECT_AP0 | SECT_AP1)) == (SECT_AP0 | SECT_AP1))
#define IS_SECT_SRW(x)	(!((x) & SECT_AP2) && ((x) & (SECT_AP0 | SECT_AP1)))
#define IS_SECT_URO(x)	((!((x) & SECT_AP2) && ((x) & (SECT_AP0 | SECT_AP1)) == SECT_AP1) || (((x) & SECT_AP2) && ((x) & SECT_AP1)))
#define IS_SECT_SRO(x)	(((x) & SECT_AP2) && ((x) & (SECT_AP0 | SECT_AP1)))
#define IS_SECT_NOACCESS(x)	(!((x) & SECT_AP_MASK))

#define IS_SUPERSECTION(x) (((x) & 2) && ((x) & (1 << 18)))
#define IS_SECTION(x) 	(((x) & 2) && !((x) & (1 << 18)))
#define EXECUTABLE(x)	(!((x) & PTE_XN))

#define PTE_AP2		(1UL << 9)
#define PTE_AP1		(1UL << 5)
#define PTE_AP0		(1UL << 4)
#define PTE_AP_MASK	(PTE_AP2 | PTE_AP1 | PTE_AP0)

#define IS_URW(x)	(!((x) & PTE_AP2) && ((x) & (PTE_AP0 | PTE_AP1)) == (PTE_AP0 | PTE_AP1))
#define IS_SRW(x)	(!((x) & PTE_AP2) && ((x) & (PTE_AP0 | PTE_AP1)))
#define IS_URO(x)	((!((x) & PTE_AP2) && ((x) & (PTE_AP0 | PTE_AP1)) == PTE_AP1) || (((x) & PTE_AP2) && ((x) & PTE_AP1)))
#define IS_SRO(x)	(((x) & PTE_AP2) && ((x) & (PTE_AP0 | PTE_AP1)))
#define IS_NOACCESS(x)	(!((x) & PTE_AP_MASK))

#define set_small_pfn_bit(mem, pfn) 	do { \
					(mem)[(pfn) / 32] |= (1 << ((pfn) % 32)); \
					} while (0)
#define set_large_pfn_bit(mem, pfn) do { \
					unsigned long __x; \
					for (__x = 0; __x < 16; __x++) { \
						set_small_pfn_bit(mem, (pfn) + __x); \
					} \
				    } while (0)
#define set_section_pfn_bit(mem, pfn) do { \
					unsigned long __x; \
					for (__x = 0; __x < 256; __x++) { \
						set_small_pfn_bit(mem, (pfn) + __x); \
					} \
				    } while (0)

#define set_l1_pfn_bit(mem, pte) do { \
					unsigned long __pfn = get_l1_pfn(pte); \
					set_section_pfn_bit(mem, __pfn); \
				 } while (0) 
#define set_l2_pfn_bit(mem, pte) do { \
					unsigned long __pfn = get_l2_pfn(pte); \
					if (IS_LARGE(pte)) { \
						set_large_pfn_bit(mem, __pfn); \
					} else { \
						set_small_pfn_bit(mem, __pfn); \
					} \
				 } while (0)

unsigned int *exec_mem;
unsigned int *write_mem;

static unsigned long phys_mask(unsigned long pgd)
{
	unsigned long ret = pgd;
	ret >>= 10;
	ret <<= 10;
	return ret;
}

static unsigned long get_l2_pfn(unsigned long pt)
{
	return pt >> 12;
}

static unsigned long get_l1_pfn(unsigned long pt)
{
	return (pt >> 20) << 8;
}

char * get_l2_attributes(unsigned long pte, char *attrs)
{
	strcpy(attrs, "");

	if (!IS_PRESENT(pte))
		strcat(attrs, "!");
	if (IS_URW(pte)) {
		strcat(attrs, "urw");
		set_l2_pfn_bit(write_mem, pte);
	} else if (IS_URO(pte))
		strcat(attrs, "ur");
	else if (IS_SRW(pte)) {
		strcat(attrs, "srw");
		set_l2_pfn_bit(write_mem, pte);
	} else if (IS_SRO(pte))
		strcat(attrs, "sr");
	if (!IS_LARGE(pte) && !(pte & PTE_XN)) {
		strcat(attrs, "x");
		set_l2_pfn_bit(exec_mem, pte);
	} if (IS_LARGE(pte) && !(pte & LARGE_XN)) {
		strcat(attrs, "x");
		set_l2_pfn_bit(exec_mem, pte);
	}

	return attrs;
}

char * get_l1_attributes(unsigned long pt, char *attrs)
{
	strcpy(attrs, "");

	if (IS_TABLE(pt)) {
		if (pt & PAGE_TABLE_PXN)
			strcat(attrs, "p");
	} else if (IS_SECTION(pt) || IS_SUPERSECTION(pt)) {
		if (IS_SECT_URW(pt)) {
			strcat(attrs, "urw");
			set_l1_pfn_bit(write_mem, pt);
		} else if (IS_SECT_URO(pt))
			strcat(attrs, "ur");
		else if (IS_SECT_SRW(pt)) {
			strcat(attrs, "srw");
			set_l1_pfn_bit(write_mem, pt);
		} else if (IS_SECT_SRO(pt))
			strcat(attrs, "sr");
		if (!(pt & SECT_XN)) {
			strcat(attrs, "x");
			set_l1_pfn_bit(exec_mem, pt);
		}
		if (pt & SECT_PXN)
			strcat(attrs, "p");
	}

	return attrs;
}

static unsigned long dump_pt(int devmem, unsigned long pte, long va)
{
	unsigned long *pt;
	unsigned int i;
	unsigned long buffer[PTRS_PER_PTE];
	char attrs[16];

	if ((off_t)-1 == lseek(devmem, phys_mask(pte), SEEK_SET))
		goto out;
	if (sizeof buffer != read(devmem, &buffer, sizeof buffer))
		goto out;
	pt = buffer;

	for (i = 0; i < PTRS_PER_PTE;) {
		if (IS_PRESENT(pt[i])) {
			if (IS_LARGE(pt[i])) {
				printf("\t\t\t%sLARGE pte: %s %03x %08lx %08lx %08lx\n", EXECUTABLE(pt[i]) ? YELLOW : LIGHTYELLOW, get_l2_attributes(pt[i], (char *)&attrs), i, get_l2_pfn(pt[i]), pt[i], va);
				va += 16 * PAGE_SIZE;
				i += 16;
			} else {
				printf("\t\t\t%spte: %s %03x %08lx %08lx %08lx\n", EXECUTABLE(pt[i]) ? YELLOW : LIGHTYELLOW, get_l2_attributes(pt[i], (char *)&attrs), i, get_l2_pfn(pt[i]), pt[i], va);
				va += PAGE_SIZE;
				i++;
			}
		} else {
			va += PAGE_SIZE;
			i++;
		}
	}

	return va;

out:
	return va + (1024 * 1024);
}

static unsigned long dump_pgd(int devmem, unsigned long pgd, unsigned long va)
{
	unsigned long *pt;
	unsigned int i;
	unsigned long buffer[4096];
	char attrs[16];

	if ((off_t)-1 == lseek(devmem, pgd, SEEK_SET))
		goto out;
	if (sizeof buffer != read(devmem, buffer, sizeof buffer))
		goto out;
	pt = buffer;

	for (i = 0; i < PTRS_PER_PGD; ++i) {
		if (IS_PRESENT(pt[i])) {
			if (IS_TABLE(pt[i])) {
				printf("\t\t%spmd: %s %03x %08lx %08lx %08lx\n", EXECUTABLE(pt[i]) ? GREEN : LIGHTGREEN, get_l1_attributes(pt[i], (char *)&attrs), i, get_l1_pfn(pt[i]), pt[i], va);    
				va = dump_pt(devmem, pt[i], va);
			} else if (IS_SECTION(pt[i])) {
				printf("\t\t%ssection: %s %03x %08lx %08lx %08lx\n", EXECUTABLE(pt[i]) ? GREEN : LIGHTGREEN, get_l1_attributes(pt[i], (char *)&attrs), i, get_l1_pfn(pt[i]), pt[i], va);    
				va += 1024 * 1024;
			} else if (IS_SUPERSECTION(pt[i])) {
				printf("\t\t%ssupersection: %s %03x %08lx %08lx %08lx\n", EXECUTABLE(pt[i]) ? GREEN : LIGHTGREEN, get_l1_attributes(pt[i], (char *)&attrs), i, get_l1_pfn(pt[i]), pt[i], va);    
				va += 16 *1024 * 1024;
			}
		} else {
			va += 1024 * 1024;
		}
	}

	return va;

out:
	return 0xffffffff;
}

int main(int argc, char *argv[])
{
	unsigned long pgd;
	int devmem;
	unsigned long i;

	if (argc != 2) {
		printf("usage: %s <pgd %sPHYSICAL%s address, e.g., %sswapper_pg_dir%s or %sinit_level4_pgt%s>\n",
			argv[0], LIGHTRED, NORMAL, LIGHTYELLOW, NORMAL, LIGHTYELLOW, NORMAL);
		return 1;
	}

	if (1 != sscanf(argv[1], "%lx", &pgd))
		return 2;

	devmem = open("/dev/mem", O_RDONLY);
	if (-1 == devmem) {
		printf("unable to open /dev/mem\n");
		return 4;
	}

	exec_mem = (unsigned int *)calloc(1, PHYS_MEM_SIZE / 8);
	write_mem = (unsigned int *)calloc(1, PHYS_MEM_SIZE / 8);

	dump_pgd(devmem, pgd, 0);

	for (i = 0; i < PHYS_MEM_SIZE / 32; i++) {
		if (exec_mem[i] & write_mem[i]) {
			unsigned int x ;
			for (x = 0; x < 32; x++) {
				if ((exec_mem[i] & write_mem[i]) & (1 << x)) {
					printf("PFN %08lx is WX via alias\n", (i * 32) + x);
				}
			}
		}
	}

	close(devmem);

	return 0;
}
