#!/usr/bin/env python

# Written by meev0, available at:
# http://en.wikibooks.org/wiki/Grsecurity/How_to_Contribute
# original version cached here for security reasons
#

import sys
import codecs
import os
import kconfiglib
 
def get_sysctl_opts(kern_src_dir):
    """
    Returns a dictionary of sysctl variables related to grsecurity.
 
    Parameters:
    kern_src_dir    (string) Absolute path to a kernel source directory
                    (e.g. /usr/src/linux-3.2.48.)
 
    The key of each item is the name of a kernel configuration symbol (e.g.
    GRKERNSEC_SYMLINKOWN) and the value is a list of sysctl variable names
    (e.g. [enforce_symlinksifowner, symlinkown_gid]).
    """
    file_name = os.path.join(kern_src_dir, "grsecurity", "grsec_sysctl.c")
    options = {}
    with open(file_name, 'r') as f:
        cur_symbol = None
        for line in f:
            if line.startswith("#ifdef"):
                start = line.find("GRKERNSEC")
                cur_symbol = line[start:].strip()
                options[cur_symbol] = []
            elif line.startswith("#if defined("):
                start = line.find("GRKERNSEC")
                end = line.find(")")
                cur_symbol = line[start:end].strip()
                options[cur_symbol] = []
            elif line.startswith("#endif"):
                if cur_symbol in options:
                    options[cur_symbol].sort()
                cur_symbol = None
 
            if cur_symbol is not None and line.find(".procname") >= 0:
                a = line.find('"') + 1 
                b = line.rfind('"')
                sysctl_var = line[a:b].strip()
                options[cur_symbol].append(sysctl_var)
 
    return options
 
 
def get_categorized_sysctl_opts(sysctl_opts):
    """
    Returns a list of lists or tuples.
 
    Parameters:
    sysctl_opts     (dict) Dictionary of sysctl variables. This would be the
                    return value of get_sysctl_opts().
 
    Each index in the returned list specifies a category. The categories are
    [0] Audit/logging variables
    [1] Chroot variables
    [2] Network variables
    [3] Misc. variables
 
    The categorization is based on the name of the sysctl variable, and is
    not entirely accurate. But it's "good enough."
 
    Each category consists of a list of tuples. The first element of a tuple
    is the name of a kernel configuration symbol (e.g. GRKERNSEC_SYMLINKOWN)
    and the second element is a sysctl variable that is related to that
    symbol. If a symbol is related to more than one sysctl variable, the list
    will contain multiple tuples that have the same first element.
    """
    audit_opts = []
    chroot_opts = []
    net_opts = []
    other_opts = []
 
    for item in sysctl_opts.iteritems():
        for opt in item[1]:
            if opt.find("audit") >= 0 or opt.find("log") >= 0:
                audit_opts.append((opt,item[0]))
            elif opt.find("chroot") >= 0:
                chroot_opts.append((opt,item[0]))
            elif opt.find("socket") >= 0 or opt.find("ip") >= 0:
                net_opts.append((opt,item[0]))
            else:
                other_opts.append((opt,item[0]))
 
    audit_opts.sort()
    chroot_opts.sort()
    net_opts.sort()
    other_opts.sort()
    all_opts = [audit_opts, chroot_opts, net_opts, other_opts]
 
    return all_opts
 
 
def write_wikified_sysctl_opts(kern_src_dir, out_file_name):
    """
    Writes a categorized list of sysctl variables wrapped in MediaWiki links
    construct.
 
    Parameters:
    kern_src_dir    (string) Absolute path to a kernel source directory
                    (e.g. /usr/src/linux-3.2.48.)
 
    out_file_name   (string) Path and file name or just the file name of the
                    output file.
 
    The links point to anchors on the Grsecurity and PaX Configuration Options
    page. The links are written in alphabetical order by the sysctl variable.
    """
    sysctl_opts = get_sysctl_opts(kern_src_dir)
    categorized_opts = get_categorized_sysctl_opts(sysctl_opts)
    kconfig_path = os.path.join(kern_src_dir, "security", "Kconfig")
    conf = kconfiglib.Config(kconfig_path, kern_src_dir)
    link_fmt = "* [[Grsecurity/Appendix/Grsecurity_and_PaX"\
        "_Configuration_Options#{0}|{1}]]\n"
 
    with codecs.open(out_file_name, 'w', "utf-8") as f:
        f.write("| style=\"border:1px solid gray;\"|\n")
        for opt in categorized_opts[0]:
            symbol = conf.get_symbol(opt[1])
            prompt = get_prompt(symbol)
            f.write(link_fmt.format(prompt, opt[0]))
 
        f.write("| style=\"border:1px solid gray;\"|\n")
        for opt in categorized_opts[1]:
            symbol = conf.get_symbol(opt[1])
            prompt = get_prompt(symbol)
            f.write(link_fmt.format(prompt, opt[0]))
 
        f.write("| style=\"border:1px solid gray;\"|\n")
        for opt in categorized_opts[2]:
            symbol = conf.get_symbol(opt[1])
            prompt = get_prompt(symbol)
            f.write(link_fmt.format(prompt, opt[0]))
 
        f.write("| style=\"border:1px solid gray;\"|\n")
        for opt in categorized_opts[3]:
            symbol = conf.get_symbol(opt[1])
            prompt = get_prompt(symbol)
            f.write(link_fmt.format(prompt, opt[0]))
 
 
def get_prompt(item):
    """ 
    Return the first prompt text of the specified symbol or choice.
 
    Parameters:
    item        (Kconfiglib.Symbol or Kconfiglib.Choice) The object whose
                prompt is to be returned.
 
    Kconfiglib does not expose the prompt(s) of a Symbol or Choice. They have
    to be extracted from the string representation of a Symbol object. This
    function only returns the first prompt. There may be more prompts and
    they may or may not be identical.
    """
    s = str(item)
    prefix = "Prompts:"
    i = s.find(prefix)
    prompt = None
    if i >= 0:
        a = s.find('"', i + len(prefix)) + 1
        b = s.find('"', a)
        prompt = s[a:b]
 
    return prompt
 
 
def write_wikified_item(f, sysctl_opts, item, level):
    """
    Write the given Kconfiglib.Item in the specified file.
 
    Parameters:
    f               (file) Output file object open for writing.
 
    sysctl_opts     (dict) Dictionary of sysctl variables. This would be the
                    return value of get_sysctl_opts().
 
    item            (Kconfiglib.Item) Item that should be formatted.
 
    level           (integer) Heading level (number of '=' characters before
                    and after a heading). Items are hierarchial and this
                    function will increment the level when it calls itself
                    recursively to process child Items.
 
    This function wraps certain properties of item and related content from
    sysctl_opts in MediaWiki markup and writes the output to the specified
    file object.
 
    If item is a Menu, it is formatted as a heading and it's child Items
    are iterated and formatted.
 
    If item is a Choice, its prompt text is formatted as a heading and
    help text as preformatted text.
 
    If item is a Symbol, its prompt text is formatted as a heading, name as
    teletype text, help text as preformatted text and all related sysctl
    options as intended lines below a sort of heading that's regular
    unformatted text.
    """
    heading_fmt = "\n{0}{1}{0}\n"
    symbol_fmt = "<tt>{0}</tt><br/>\n"
    help_fmt = "<pre>{0}</pre>\n"
    sysctl_vars_heading = "Related sysctl variables:<br/>\n"
    sysctl_var_fmt = ":<tt>kernel.grsecurity.{0}</tt>\n"
 
    if item.is_menu():
        if level > 1:
            f.write(heading_fmt.format("=" * level, item.get_title()))
        for subitem in item.get_items():
            write_wikified_item(f, sysctl_opts, subitem, level + 1)
 
    if item.is_choice():
        f.write(heading_fmt.format("=" * level, get_prompt(item)))
        help_text = item.get_help()
        if help_text is not None and len(help_text) > 0:
            f.write(help_fmt.format(help_text.strip()))
        for subitem in item.get_items():
            write_wikified_item(f, sysctl_opts, subitem, level + 1)
 
    if item.is_symbol():
        name = item.get_name().strip()
        prompt = get_prompt(item)
        help_text = item.get_help()
 
        if prompt is not None and len(prompt) >= 3:
            f.write(heading_fmt.format("=" * level, prompt))
            f.write(symbol_fmt.format(name))
 
            if name in sysctl_opts:
                opt_list = sysctl_opts[name]
                if opt_list is not None and len(opt_list) > 0:
                    f.write(sysctl_vars_heading)
                    for opt in sysctl_opts[name]:
                        f.write(sysctl_var_fmt.format(opt))
 
            if help_text is not None and len(help_text) > 0:
                f.write(help_fmt.format(help_text.strip()))
 
 
def write_wikified_kconfig(kern_src_dir, out_file_name):
    """
    Create a MediaWiki-formatted version of the Kconfig file found in
    the kern_src_dir/security/ directory and write the output to the
    given file.
 
    Parameters:
    kern_src_dir    (string) Absolute path to a kernel source directory
                    (e.g. /usr/src/linux-3.2.48.)
 
    out_file_name   (string) Path and file name or just the file name of the
                    output file.
    """
    security_kconfig = os.path.join(kern_src_dir, "security", "Kconfig")
    conf = kconfiglib.Config(security_kconfig, kern_src_dir)
    if conf is not None:
        for menu in conf.get_menus():
            if menu.get_title() == "Grsecurity":
                with codecs.open(out_file_name, 'w', "utf-8") as f:
                    sysctl_opts = get_sysctl_opts(kern_src_dir)
                    write_wikified_item(f, sysctl_opts, menu, 1)
                break
 
# kern_src_dir is expected to be an absolute path to the kernel
# source directory (e.g. /usr/src/linux-3.2.48).
kern_src_dir = sys.argv[1]
if kern_src_dir is not None and len(kern_src_dir) > 0:
    write_wikified_kconfig(kern_src_dir,
        "Grsecurity_and_PaX_Configuration_Options.wiki")
    write_wikified_sysctl_opts(kern_src_dir, "Sysctl_Options.wiki")
