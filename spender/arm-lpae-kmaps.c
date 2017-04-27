/*
 * ARM LPAE page table walker for new years
 * Not for use in DARPA CFT! LoL
 * Appx value of this code in DARPA monies: $50k
 *
 * Rewrite of PaX Team's kmaps.c
 * ./kmaps 0x40003000 to see all pmds
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

#define PAGE_SIZE	4096U
#define PAGE_SHIFT	12
#define PER_PAGE(x)	(PAGE_SIZE / sizeof (x))
#define PGDIR_SHIFT	30
#define PMD_SHIFT	21
#define PMD_SIZE	(1ULL << PMD_SHIFT)
#define PGDIR_SIZE      (1ULL << PGDIR_SHIFT)
#define PHYS_MASK	(0xFFFFFFFFFFFFF000ULL)
#define PGDIR_MASK      (~((1ULL << PGDIR_SHIFT) - 1))
#define TTBR1_OFFSET       (4096 * (1 + 3))

#define IS_TABLE(x)	((x) & 2)
#define IS_PRESENT(x)	((x) & 1)
#define PTE_PRESENT	0x01U
#define PTE_XN		(1ULL << 54)
#define PTE_PXN		(1ULL << 53)
#define PTE_USER	(1ULL << 6)
#define PTE_READONLY	(1ULL << 7)
#define TABLE_PXN	(1ULL << 59)
#define TABLE_XN	(1ULL << 60)

#define PRESENT(x)	((x) & PTE_PRESENT)
#define EXECUTABLE(x)	(!((x) & PTE_XN))

static unsigned long long phys_mask(unsigned long long pgd)
{
	unsigned long long ret = pgd;
	ret &= (1ULL << 40) - 1;
	ret &= PHYS_MASK;
	return ret;
}

char * get_pte_attributes(unsigned long long pte, char *attrs)
{
	strcpy(attrs, "");

	if (!(pte & PTE_PRESENT))
		strcat(attrs, "!");
	if (pte & PTE_USER)
		strcat(attrs, "u");
	else
		strcat(attrs, "s");
	strcat(attrs, "r");
	if (!(pte & PTE_READONLY))
		strcat(attrs, "w");
	if (!(pte & PTE_XN))
		strcat(attrs, "x");
	if (pte & PTE_PXN)
		strcat(attrs, "p");

	return attrs;
}

char * get_attributes(unsigned long long pt, char *attrs)
{
	strcpy(attrs, "");

	if (IS_TABLE(pt)) {
		if (pt & TABLE_PXN)
			strcpy(attrs, "p");
		if (!(pt & TABLE_XN))
			strcpy(attrs, "x");
	} else
		return get_pte_attributes(pt, attrs);

	return attrs;
}

static unsigned long dump_pt(int devmem, unsigned long long pte, long va)
{
	unsigned long long *pt;
	unsigned int i;
	static unsigned long long buffer[PAGE_SIZE / sizeof(unsigned long long)];
	char attrs[16];

	if (!PRESENT(pte))
		goto out;

	if (!IS_TABLE(pte))
		goto out;

	pt = mmap(NULL, PAGE_SIZE, PROT_READ, MAP_PRIVATE, devmem, phys_mask(pte));
	if (MAP_FAILED == pt) {
		if ((off_t)-1 == lseek(devmem, phys_mask(pte), SEEK_SET))
			goto out;
		if (sizeof buffer != read(devmem, buffer, sizeof buffer))
			goto out;
		pt = buffer;
	}

	for (i = 0; i < PER_PAGE(pte); ++i, va += PAGE_SIZE) {
		if (PRESENT(pt[i]))
			printf("\t\t\t%spte: %s %03x %016llx %08lx\n", EXECUTABLE(pt[i]) ? YELLOW : LIGHTYELLOW, get_pte_attributes(pt[i], (char *)&attrs), i, pt[i], va);
	}

	if (pt != buffer)
		munmap(pt, PAGE_SIZE);

	return va;

out:
	return va + PAGE_SIZE * PER_PAGE(pte);
}

static unsigned long dump_pmd(int devmem, unsigned long long pmd, unsigned long va)
{
	unsigned long long *pt;
	unsigned int i;
	static unsigned long long buffer[PAGE_SIZE / sizeof(unsigned long long)];
	char attrs[16];

	pt = mmap(NULL, PAGE_SIZE, PROT_READ, MAP_PRIVATE, devmem, phys_mask(pmd));
	if (MAP_FAILED == pt) {
		if ((off_t)-1 == lseek(devmem, phys_mask(pmd), SEEK_SET))
			goto out;
		if (sizeof buffer != read(devmem, buffer, sizeof buffer))
			goto out;
		pt = buffer;
	}

	for (i = 0; i < PER_PAGE(pmd); ++i) {
		if (IS_PRESENT(pt[i]))
			printf("\t\t%spmd: %s %03x %016llx %08lx\n", EXECUTABLE(pt[i]) ? GREEN : LIGHTGREEN, get_attributes(pt[i], (char *)&attrs), i, pt[i], va);    
		va = dump_pt(devmem, pt[i], va);
	}

	if (pt != buffer)
		munmap(pt, PAGE_SIZE);

	return va;

out:
	return va + PAGE_SIZE * PER_PAGE(pmd) * PER_PAGE(pmd);
}

static void dump_pgd(int devmem, unsigned long long pgd, unsigned int pgd_size)
{
	unsigned long long *pt;
	unsigned long va = 0;
	unsigned int i;
	static unsigned long long buffer[PAGE_SIZE / sizeof(unsigned long long)];
	char attrs[16];

	pt = mmap(NULL, PAGE_SIZE, PROT_READ, MAP_PRIVATE, devmem, pgd);
	if (MAP_FAILED == pt) {
		if ((off_t)-1 == lseek(devmem, pgd, SEEK_SET))
			return;
		if (sizeof buffer != read(devmem, buffer, sizeof buffer))
			return;
		pt = buffer;
	}

	for (i = 0; i < pgd_size; ++i) {
		if (IS_PRESENT(pt[i])) {
			printf("%spgd: %s %03x %016llx %08lx\n", EXECUTABLE(pt[i]) ? RED : LIGHTRED, get_attributes(pt[i], (char *)&attrs), i, pt[i], va);
			if (IS_TABLE(pt[i]))
				va = dump_pmd(devmem, pt[i], va);
			else
				va += 1 * 1024 * 1024;
		} else
			va += 1 * 1024 * 1024;
	}

	if (pgd & (PAGE_SIZE-1))
		free(pt);
	else if (pt != buffer)
		munmap(pt, PAGE_SIZE);
}

int main(int argc, char *argv[])
{
	unsigned long long pgd;
	int devmem, pgd_size;

	if (argc != 2) {
		printf("usage: %s <pgd %sPHYSICAL%s address, e.g., %sswapper_pg_dir%s or %sinit_level4_pgt%s>\n",
			argv[0], LIGHTRED, NORMAL, LIGHTYELLOW, NORMAL, LIGHTYELLOW, NORMAL);
		return 1;
	}

	if (1 != sscanf(argv[1], "%llx", &pgd))
		return 2;

	devmem = open("/dev/mem", O_RDONLY);
	if (-1 == devmem) {
		printf("unable to open /dev/mem\n");
		return 4;
	}

	dump_pmd(devmem, pgd + 0x1000, 0x00000000);
	dump_pmd(devmem, pgd + 0x2000, 0x40000000);
	dump_pmd(devmem, pgd + 0x3000, 0x80000000);
	dump_pmd(devmem, pgd + 0x4000, 0xc0000000);

	close(devmem);

	return 0;
}
