from api_env import CHANNEL_ACCESS_TOKEN, CHANNEL_SECRET
import requests
import json

pick_name = "main_function"
pick_inf = "panel_property/function_v1.0.2.json"
pick_pic = "panel_img/function_v1.0.2.jpg"
default = True

headers = {"Authorization":f"Bearer {CHANNEL_ACCESS_TOKEN}","Content-Type":"application/json"}
body = json.loads(open(pick_inf, "r", encoding='utf-8').read())
req = requests.request('POST', 'https://api.line.me/v2/bot/richmenu', 
                       headers=headers,data=json.dumps(body).encode('utf-8'))
# print(req.text)
res1 = json.loads(req.text)
pick_id = res1["richMenuId"]
print(f"Pick ID: {pick_id}")
res1["aliasId"] = pick_name

# Add picture to the rich menu
headers4 = {
    "Authorization":f"Bearer {CHANNEL_ACCESS_TOKEN}",
    "Content-Type":"image/jpeg",
}
with open(pick_pic, "rb") as file:
    res4 = requests.post(f"https://api-data.line.me/v2/bot/richmenu/{pick_id}/content",
                         headers=headers4,
                         data=file)
    
# Set as default menu if necessary
if(default):
    headers5 = {
        "Authorization": f"Bearer {CHANNEL_ACCESS_TOKEN}"
    }
    req5 = requests.request('POST', f"https://api.line.me/v2/bot/user/all/richmenu/{pick_id}",
                            headers=headers5)
    if(req5.status_code == 200):
        print("Successfully set the panel to the default")

