import random
import string

def generate_id(length = 20):
    # 定義可用字符集：小寫字母、大寫字母和數字
    characters = string.ascii_letters + string.digits
    # 隨機選擇字符並組合成指定長度的字符串
    random_string = ''.join(random.choice(characters) for _ in range(length))
    return random_string