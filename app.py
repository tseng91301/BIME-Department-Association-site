from flask import Flask, request, abort, jsonify, make_response, render_template_string
from linebot import LineBotApi, WebhookHandler
from linebot.exceptions import InvalidSignatureError
from linebot.models import *
import os

import requests
from collections import deque
import threading
import json
# from urllib.parse import parse_qs
import base64

from session.generate import generate_id

app = Flask(__name__)

line_bot_api = LineBotApi(os.environ['CHANNEL_ACCESS_TOKEN'])
handler = WebhookHandler(os.environ['CHANNEL_SECRET'])
# line_bot_api = LineBotApi("os.environ['CHANNEL_ACCESS_TOKEN']")
# handler = WebhookHandler("os.environ['CHANNEL_SECRET']")

TRANSFER_SECRET_KEY = open("admin/key_chain/transfer_secret_key", 'r').read()

@app.route("/callback", methods=['POST'])
def callback():
    signature = request.headers['X-Line-Signature']
    body = request.get_data(as_text=True)
    app.logger.info("Request body: " + body)
    try:
        handler.handle(body, signature)
    except InvalidSignatureError:
        abort(400)
    return 'OK'

LINE_CHANNEL_ID = os.environ['LINE_LOGIN_ID']
LINE_CHANNEL_SECRET = os.environ['LINE_LOGIN_SECRET']
# LINE_CHANNEL_ID = ""
# LINE_CHANNEL_SECRET = ""
REDIRECT_URI = 'https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line'
@app.route('/login_callback', methods=['GET'])
def login_callback():
    code = request.args.get('code')
    state = request.args.get('state')
    print(code)
    if(state != "fsaafjwri20ttga0hwpjisg0t5"):
        return "bad request"
    token_url = 'https://api.line.me/oauth2/v2.1/token'
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    data = {
        'grant_type': 'authorization_code',
        'code': code,
        'redirect_uri': REDIRECT_URI,
        'client_id': LINE_CHANNEL_ID,
        'client_secret': LINE_CHANNEL_SECRET
    }
    response = requests.post(token_url, headers=headers, data=data)
    token_data = response.json()

    # 獲取用戶資訊
    access_token = token_data['access_token']
    user_info_url = 'https://api.line.me/v2/profile'
    headers = {
        'Authorization': f'Bearer {access_token}'
    }
    user_info_response = requests.get(user_info_url, headers=headers)
    user_info = user_info_response.json()

    return render_template_string('''
        <!DOCTYPE html>
        <html lang="zh-tw">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login Redirect</title>
        </head>
        <body>
        <form id="myForm" action="/profile/entrance/index.php" method="post">
            {% for key, value in user_info.items() %}
            <input type="hidden" name="{{ key }}" value="{{ value }}">
            {% endfor %}
            <!-- input type="hidden" name="redirect_path" value="/profile" -->
            <input type="submit" value="若無反應請按此鈕跳轉">
        </form>
        </body>
        <script type="text/javascript">
            document.getElementById('myForm').submit();
        </script>
    ''', user_info=user_info)

@app.route('/login_form')
def login_form():
    return(open("login/index.html", "r", encoding="utf-8").read())

request_queue = deque()
request_events = dict()
response_dict = dict()

@handler.add(MessageEvent, message=TextMessage)
def handle_message(event):
    msg_type = event.message.type
    if(msg_type != "text"):
        line_bot_api.reply_message(event.reply_token, "抱歉！目前 Line Bot 不支援此格式的訊息，敬請期待未來開發的功能")
        return
    message = str(event.message.text)
    id = generate_id()
    request_events.update({id: threading.Event()})
    request_queue.appendleft({'id': id, 'type': 'text_msg_handle', 'data': message})
    print("Line API >> Get message "+message)
    request_events[id].wait()
    request_events[id].clear()
    del request_events[id]
    try:
        print("Line API >> Send message "+response_dict[id]['reply'])
        line_bot_api.reply_message(event.reply_token, TextSendMessage(text=response_dict[id]['reply']))
    except Exception as e:
        msg_out = f"Error while replying: {str(e)}"
        line_bot_api.reply_message(event.reply_token, TextSendMessage(text=msg_out))
    del response_dict[id]
    pass

@app.route('/server_callback', methods=['POST', 'GET'])
def server_callback():
    try:
        # print(str(request.headers))
        if(request.headers['TRANSFER-SECRET-KEY'] != TRANSFER_SECRET_KEY):
            response = make_response("Bad request", 400)
            return response
    except:
        response = make_response("Bad request", 400)
        return response
    if(request.method == 'GET'):
        # print("Get server_callback")
        try:
            return_req = request_queue.pop()
            return jsonify(return_req)
        except:
            return jsonify({'id': "None"})
    elif(request.method == 'POST'):
        # print("Post server_callback")
        try:
            body = request.get_json()
            # print(str(body))
            id = body['id']
            response_dict[id] = body
            request_events[id].set()
        except Exception as e:
            print(f"Error: {e}")
            pass
        return "task set"
    pass

@app.route('/<path:path>', methods = ['POST', 'GET'])
def all_path(path):
    id = generate_id()
    request_events.update({id: threading.Event()})
    is_raw = 0
    if request.content_type == 'application/x-www-form-urlencoded':
        body = request.form.to_dict()
        pass
    elif request.is_json:
        body = request.get_json()
    else:
        is_raw = 1
        body = {'raw_data': request.get_data(as_text=True)}
    args = dict(request.args)
    redirect_path = "/"+path
    request_queue.appendleft({'id': id, 'type': 'http_request', 'is_raw': is_raw, 'method': request.method, 'headers': dict(request.headers), 'args':args, 'body': body, 'path': redirect_path})
    request_events[id].wait()
    request_events[id].clear()
    del request_events[id]

    try:
        response_dict[id]['headers']['Content-Length'] = int(response_dict[id]['headers']['Raw-Length'])
    except:
        response_dict[id]['headers'].update({'Content-Length': int(response_dict[id]['headers']['Raw-Length'])})
    print("Response body: ", base64.b64decode(response_dict[id]['body']))
    response = make_response(base64.b64decode(response_dict[id]['body']), int(response_dict[id]['status_code']))
    response.headers["Content-Type"] = "text/html; charset=UTF-8"
        
    
    response.headers = response_dict[id]['headers']
    # response.headers['Content-type'] = 'text/html'
    del response_dict[id]
    return response

if __name__ == "__main__":
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)