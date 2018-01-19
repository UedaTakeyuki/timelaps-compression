device=$1
month=$2
echo device: $device
echo month: $month

thismonth=uploads/00000000c9c51a68/${device}/2018${month}

sudo mkdir ${thismonth}00
for i in 01 02 03 04 05 06 07 08 09 {10..31} ;do
  sudo cp ${thismonth}${i}/*.jpeg ${thismonth}00
done
sudo ls ${thismonth}00 | wc -l 
sudo cp a.py ${thismonth}00
sudo cp b.py ${thismonth}00
