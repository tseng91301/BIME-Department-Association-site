# Get the route of shell script
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")

# 定義 tmux 會話名稱
SESSION_NAME_MOBILE_FORWARDER="mobile_forwarder"

tmux new-session -d -s $SESSION_NAME_MOBILE_FORWARDER
tmux send-keys -t $SESSION_NAME_MOBILE_FORWARDER "ngrok start ssh"  C-m

tmux attach -t $SESSION_NAME_MOBILE_FORWARDER