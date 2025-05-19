#!/bin/bash

# Get the route of shell script
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")

# 定義 tmux 會話名稱
SESSION_NAME_FORWARDER="bime_linebot_local_python"
SESSION_NAME_RASA_CHAT="bime_linebot_local_rasa_chat"
SESSION_NAME_RASA_ACTION="bime_linebot_local_rasa_action"

sudo service apache2 restart

tmux new-session -d -s $SESSION_NAME_FORWARDER
tmux new-session -d -s $SESSION_NAME_RASA_CHAT
tmux new-session -d -s $SESSION_NAME_RASA_ACTION


tmux send-keys -t $SESSION_NAME_FORWARDER "python3 $SCRIPT_DIR/forward_service.py" C-m
tmux send-keys -t $SESSION_NAME_RASA_CHAT "cd /home/bime/bime_line_bot/rasa_generative_ai && source /home/bime/bime_line_bot/rasa_generative_ai/venv/bin/activate && rasa run --enable-api" C-m
tmux send-keys -t $SESSION_NAME_RASA_ACTION "cd /home/bime/bime_line_bot/rasa_generative_ai && source /home/bime/bime_line_bot/rasa_generative_ai/venv/bin/activate && rasa run actions" C-m

