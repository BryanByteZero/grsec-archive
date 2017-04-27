#include <idc.idc>
static blah()
{
	auto addr,temp;
	addr = SelStart();
	if (addr == BADADDR)
		return;
	for(;addr<SelEnd(); addr++) {
		temp=Byte(addr)-1;
		PatchByte(addr,temp^Byte(addr-1));
	}
}
