# coding:utf-8 Copy Right Atelier UEDA🐸 © 2016 -
import sys
import os
import re
import subprocess

#作成する timelapse file name
cfn = os.path.basename(os.path.abspath("."))  # expect as like "20160923"
pfn = os.path.basename(os.path.abspath("..")) # expect as like "video0"
tl_file_name = os.path.basename(os.path.abspath("..")) + "." + cfn[0:4] + "." + cfn[4:6] + "." + cfn[6:8] + ".mp4"
print tl_file_name
#sys.exit()

# make ORIGINAL NAME file
ls_sh = 'ls > names.txt'
p = subprocess.call(ls_sh, stdout=subprocess.PIPE, shell=True)

# make timelapse folder
os.mkdir("timelapse")

# rename pictures as serial number, then move to timelapse folder
a = 1
#指定する画像フォルダ
files = sorted(os.listdir('.'))
for file in files:
    jpg = re.compile("jpeg")
    if jpg.search(file):
        print file
        os.rename(file, "timelapse/image%06d.jpg" %(a))
        a+=1
    else:
        pass

#ffmpeg
#ffmpeg_sh = 'ffmpeg -f image2 -r 30 -i timelapse/image%04d.jpg -r 30 -an -vcodec mpeg4 -pix_fmt yuv420p your_output.mp4'
ffmpeg_sh = 'ffmpeg -f image2 -r 30 -i timelapse/image%06d.jpg -r 30 -an -vcodec mpeg4 -pix_fmt yuv420p ' + tl_file_name
p = subprocess.call(ffmpeg_sh, stdout=subprocess.PIPE, shell=True)
