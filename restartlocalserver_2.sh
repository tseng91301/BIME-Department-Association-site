# Get the route of shell script
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")

$SCRIPT_DIR/stoplocalserver_2.sh
$SCRIPT_DIR/startlocalserver_2.sh
