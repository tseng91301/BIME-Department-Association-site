import requests
import json

from api_env import CHANNEL_ACCESS_TOKEN, CHANNEL_SECRET

import requests

DELETE_MENUS = ["richmenu-c8cb74f2adf72aacd3056c3f7ceedcd6"]

# 你的 Channel Access Token
access_token = CHANNEL_ACCESS_TOKEN

# 获取所有Rich Menu
headers = {
    'Authorization': f'Bearer {access_token}'
}

# 删除每个Rich Menu
for rich_menu_id in DELETE_MENUS:
    delete_response = requests.delete(f'https://api.line.me/v2/bot/richmenu/{rich_menu_id}', headers=headers)
    if delete_response.status_code == 200:
        print(f'Successfully deleted rich menu: {rich_menu_id}')
    else:
        print(f'Failed to delete rich menu: {rich_menu_id}, Status code: {delete_response.status_code}')