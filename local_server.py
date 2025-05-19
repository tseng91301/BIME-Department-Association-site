from flask import Flask, request, abort, jsonify, make_response
import os
import json

app = Flask(__name__)

@app.route('/client_callback', methods = ['POST', 'GET'])
def client_callback():
    response_dict = {
        "message": "Local server received message, Happy :)"
    }
    response = make_response(json.dumps(response_dict), 200)
    return response

port = int(os.environ.get('PORT', 6734))
app.run(host='0.0.0.0', port=port)