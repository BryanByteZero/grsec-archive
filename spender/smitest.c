#include <sys/io.h>

int main(void)
{
        iopl(3);

        asm volatile (
        ".intel_syntax noprefix\n"
        ".code32\n"
        "mov eax, 0\n"
        "mov edx, 0xb2\n"
        "out dx, al\n"
        ".att_syntax noprefix\n"
        );

        return 0;
}
