#!/bin/bash
python3 /home/projet/global.py
raspivid -t 999999 --vflip --hflip -o - -w 512 -h 512 -fps 15 | nc 192.168.1.123 5001

#On Windows
#C:\Users\anton\Downloads\netcat\nc.exe -L -p 5001 | C:\Users\anton\Downloads\netcat\mplayer\mplayer.exe -vo direct3d -fps 24 -cache 512 -