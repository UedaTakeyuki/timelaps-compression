# coding:utf-8 Copy Right Atelier UEDA🐸 © 2016 -

import argparse
import os
import re
import subprocess

parser = argparse.ArgumentParser(description='timelapse decomposition')
parser.add_argument('mpgFileName', help='Input file path of timelapse movie file')
args = parser.parse_args()

# names.txt ファイルを読んで元のファイル名の array を作成
org_files = []
with open("names.txt", "r") as f:
  for line in f:
    org_files.append(line[:-1]) # 末尾文字を削除
#print files

# timelapse フォルダがなければをつくり、timelapse 動画を連番の写真に展開する
if not os.path.exists("timelapse"):
	os.mkdir("timelapse")
ffmpeg_sh = 'ffmpeg -i ' + args.mpgFileName + ' -ss 0 -r 30 -f image2 timelapse/image%04d.jpg'
p = subprocess.call(ffmpeg_sh, stdout=subprocess.PIPE, shell=True)

# 連番ファイル名を元のファイル名に変更
a=0
files = os.listdir('timelapse')
for file in files:
    jpg = re.compile("jpg")
    if jpg.search(file):
        print file
        print org_files[a]
        os.rename("timelapse/"+file, "timelapse/"+org_files[a])
        a+=1
    else:
        pass
