#-*- coding:utf-8 -*-
import os
def system(cmd):    
    return os.popen(cmd).read()
system("nw\\nw.exe source")   