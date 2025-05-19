import requests

rasa_server_url = "http://25.3.211.17:5005/webhooks/rest/webhook"

def send_message_to_rasa(message, sender="user"):
    payload = {
        "sender": sender,
        "message": message
    }
    response = requests.post(rasa_server_url, json=payload)
    return response.json()

if __name__ == "__main__":
    user_message = "What's the weather in Taipei?"
    user_message = "Hello!"
    response = send_message_to_rasa(user_message)
    print(response)
