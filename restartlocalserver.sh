# Get the route of shell script
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")

$SCRIPT_DIR/stoplocalserver.sh
$SCRIPT_DIR/startlocalserver.sh
