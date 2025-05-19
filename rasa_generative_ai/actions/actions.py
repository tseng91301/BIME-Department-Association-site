# This files contains your custom actions which can be used to run
# custom Python code.
#
# See this guide on how to implement these action:
# https://rasa.com/docs/rasa/custom-actions


# This is a simple example for a custom action which utters "Hello World!"

# from typing import Any, Text, Dict, List
#
# from rasa_sdk import Action, Tracker
# from rasa_sdk.executor import CollectingDispatcher
#
#
# class ActionHelloWorld(Action):
#
#     def name(self) -> Text:
#         return "action_hello_world"
#
#     def run(self, dispatcher: CollectingDispatcher,
#             tracker: Tracker,
#             domain: Dict[Text, Any]) -> List[Dict[Text, Any]]:
#
#         dispatcher.utter_message(text="Hello World!")
#
#         return []

from typing import Any, Text, Dict, List
from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
import requests

class ActionGetWeather(Action):

    def name(self) -> Text:
        return "action_get_weather"

    def run(self, dispatcher: CollectingDispatcher,
            tracker: Tracker,
            domain: Dict[Text, Any]) -> List[Dict[Text, Any]]:
        
        # 从tracker中提取实体
        location = tracker.get_slot('location')
        date = tracker.get_slot('date')

        if not location:
            location = "Taipei"

        if not date:
            date = "today"

        # 调用天气API获取天气信息
        print(location)
        print(date)
        api_key = "3a392efea7c04a888b3152040232108"
        url = f"http://api.weatherapi.com/v1/current.json?key={api_key}&q={location}"

        response = requests.get(url)
        weather = response.json()

        # 创建响应
        message = f"The current weather in {location} is {weather['current']['condition']['text']} with a temperature of {weather['current']['temp_c']}°C."

        dispatcher.utter_message(text=message)

        return []

