device=$1
month=$2
echo device: $device
echo month: $month

sudo mv ../160613/gal4_server/uploads/00000000c9c51a68/${device}/2018${month}* uploads/00000000c9c51a68/${device}
