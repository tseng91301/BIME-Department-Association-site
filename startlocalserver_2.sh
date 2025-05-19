#!/bin/bash

# Get the route of shell script
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")

# 定義 tmux 會話名稱
SESSION_NAME_PHP="bime_linebot_local_php"
SESSION_NAME_FORWARDER="bime_linebot_local_python"



tmux new-session -d -s $SESSION_NAME_PHP
tmux new-session -d -s $SESSION_NAME_FORWARDER


tmux send-keys -t $SESSION_NAME_PHP "php -t $SCRIPT_DIR/local_server -S 0.0.0.0:6734" C-m
tmux send-keys -t $SESSION_NAME_FORWARDER "python3 $SCRIPT_DIR/forward_service.py" C-m