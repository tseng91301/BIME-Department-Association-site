import requests
import json
import time
import sys
import os
import base64
from urllib.parse import quote

DEVELOP_MODE = True
# Getting current path
# 獲取當前文件的相對路徑
current_file_path = sys.argv[0]
# 獲取當前文件的絕對路徑
absolute_file_path = os.path.abspath(current_file_path)
# 獲取當前文件所在的目錄
current_directory = os.path.dirname(absolute_file_path)

SECRET_KEY = str(open(f"{current_directory}/admin/key_chain/transfer_secret_key", 'r').read())

LOCAL_HTTP_SERVER = 'http://127.0.0.1:6734'
LOCAL_MSG_HANDLE_SERVER = "http://localhost:5005/webhooks/rest/webhook"

NGROK_SERVER = "https://blessed-dogfish-morally.ngrok-free.app"

print("Forward service STARTED!")

while True:
    
    try:
        if DEVELOP_MODE:
            print("Server callback start read")
        # time.sleep(0.1)
        req1 = requests.get("https://ntu-bime-linebot.onrender.com/server_callback", headers={**requests.utils.default_headers(), 'TRANSFER-SECRET-KEY': SECRET_KEY})
        if DEVELOP_MODE:
            print("Server callback reading...")
        # req1 = requests.get("http://192.168.1.100:5000/server_callback", headers={**requests.utils.default_headers(), 'TRANSFER-SECRET-KEY': SECRET_KEY})
        res1 = req1.json()
        id = res1['id']
        if id == 'None':
            continue
        
        if DEVELOP_MODE:
            print("Got req")
        if res1['type'] == 'http_request':
            # print("Got request")
            path = res1['path']
            data_r = {**res1['body'], **res1['args']}
            if DEVELOP_MODE:
                print("Posting to local server...")
            if res1['method'] == 'POST':
                req2 = requests.post(f"{LOCAL_HTTP_SERVER}{path}", data=data_r, headers=res1['headers'],)
                pass
            elif res1['method'] == 'GET':
                req2 = requests.get(f"{LOCAL_HTTP_SERVER}{path}", params=data_r, headers=res1['headers'])
                pass
            if DEVELOP_MODE:
                print("Get local server response")
            response_dict = {
                'id': id,
                'headers': dict(req2.headers),
                # 'body': req2.json(),
                'status_code': req2.status_code
            }

            # Convert all body content into base64-encode and record the raw length
            raw_length = len(req2.content)
            response_dict.update({'body': base64.b64encode(req2.content).decode('utf-8')})
            response_dict['headers']['Raw-Length'] = raw_length
            # response_dict['headers']['Content-Length'] = int(response_dict['headers']['Content-Length'])
        elif res1['type'] == 'text_msg_handle':
            msg = str(res1['data'])
            need_ai = 1

            # Handle default message
            if(msg == "contact with us"):
                need_ai = 0
                res_text = "此功能目前正在積極開發中，待完成後即可享受優質的一對一客服體驗！\n　目前暫時以其他平台私訊聯絡為主"
            elif(msg == "more"):
                need_ai = 0
                res_text = "抱歉！目前尚未有更多功能，若上線會第一時間通知您！"
            elif(msg == "help"):
                need_ai = 0
                res_text = "目前Line Bot主要是作為訊息通知的媒介，因此其他操作尚未完備，若有開發新功能，將會第一時間通知您~\n點擊下面的連結觀看說明文件(hand pointing down)\nhttps://hackmd.io/@tseng91301/B1CPTsJcR"
                
            msg_url = quote(msg)
            payload = {
                "sender": "user",
                "message": msg
            }
            response = requests.post(LOCAL_MSG_HANDLE_SERVER, json=payload)
            try:
                if(need_ai):
                    res_text = response.json()[0]["text"]
                    res_url = quote(res_text)
                    response_dict = {
                        'id': id,
                        'reply': f"{res_text}\n\n不滿意這個訊息的輸出？點擊以下連結幫我優化回覆內容\nhttps://ntu-bime-linebot.onrender.com/suggestion/chat_information.php?trig={msg_url}&echo="
                    }
                else:
                    response_dict = {
                        'id': id,
                        'reply': f"{res_text}"
                    }
            except:
                response_dict = {
                    'id': id,
                    'reply': f"抱歉，目前無法回復此訊息： {msg}\n 如果想要讓機器人回覆此訊息，可以點擊以下連結提供建議，我們將會不定期更新我們的AI！\nhttps://ntu-bime-linebot.onrender.com/suggestion/chat_information.php?trig={msg_url}"
                }
            print(response_dict)
            
    except Exception as e:
        print(f"Error: {str(e)}")
        response_dict = {
            'headers': None,
            'body': {"status": "Error", "detail": str(e)},
            'status_code': 500,
        }
    

    try:
        if DEVELOP_MODE:
            print("Starting response to remote server")
        response_dict.update({'id': id})
        response_str = json.dumps(response_dict)
        req3 = requests.post("https://ntu-bime-linebot.onrender.com/server_callback", headers={'Content-Type': 'application/json', 'TRANSFER-SECRET-KEY': SECRET_KEY}, data = response_str)
        # req3 = requests.post("http://192.168.1.100:5000/server_callback", headers={'Content-Type': 'application/json', 'TRANSFER-SECRET-KEY': SECRET_KEY}, data = response_str)
        if(req3.status_code == 200):
            if DEVELOP_MODE:
                print("Message forward successfully")
    except Exception as e:
        print(f"encountered error while sending: {str(e)}")

    
