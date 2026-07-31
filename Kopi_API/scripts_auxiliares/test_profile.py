
import requests
import base64

url = "http://127.0.0.1:8000/usuarios/perfil"

# Since we cant easily grab a token, lets just mock the login to get one
login_res = requests.post("http://127.0.0.1:8000/auth/login", data={"username": "4426763239", "password": "password"})
print("Login:", login_res.status_code)
if login_res.status_code == 200:
    token = login_res.json()["access_token"]
    headers = {"Authorization": f"Bearer {token}"}
    
    # Update profile
    files = {"foto_perfil": ("test.jpg", b"fake_image_data", "image/jpeg")}
    res = requests.post(url, headers=headers, data={"telefono": "5556667778"}, files=files)
    print("Update Profile:", res.status_code, res.text)
    
    # Get profile
    get_res = requests.get(url, headers=headers)
    print("Get Profile:", get_res.status_code, get_res.text)

