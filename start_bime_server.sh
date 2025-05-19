# Get the route of shell script
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")

# Restart Apache2 server
sudo service apache2 restart

# 定義 tmux 會話名稱
SESSION_NAME_NGROK="bime_ngrok_service"

tmux new-session -d -s $SESSION_NAME_NGROK

tmux send-keys -t $SESSION_NAME_NGROK "ngrok http --url=blessed-dogfish-morally.ngrok-free.app 80" C-m