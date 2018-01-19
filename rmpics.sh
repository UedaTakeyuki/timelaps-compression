device=$1
month=$2
echo device: $device
echo month: $month

thismonth=uploads/00000000c9c51a68/${device}/2018${month}

sudo rm -rf ${thismonth}00/timelapse
for i in 01 02 03 04 05 06 07 08 09 {10..31} ;do
  sudo rm -rf ${thismonth}${i}
done
ls uploads/00000000c9c51a68/${device}
