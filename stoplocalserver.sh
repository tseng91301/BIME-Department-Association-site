SESSION_NAME2="bime_linebot_local_python"
SESSION_NAME_RASA_CHAT="bime_linebot_local_rasa_chat"
SESSION_NAME_RASA_ACTION="bime_linebot_local_rasa_action"

sudo service apache2 stop

tmux kill-session -t $SESSION_NAME2
tmux kill-session -t $SESSION_NAME_RASA_CHAT
tmux kill-session -t $SESSION_NAME_RASA_ACTION
