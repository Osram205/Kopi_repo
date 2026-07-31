
import requests

url = "http://127.0.0.1:8000/usuarios/perfil"
token = open("test_token.txt").read().strip() if open("test_token.txt").read().strip() else ""
headers = {"Authorization": f"Bearer {token}"}

# Test 1: Only phone
res1 = requests.post(url, headers=headers, data={"telefono": "1112223334"})
print("Test 1 (Form):", res1.status_code, res1.text)

# Test 2: Phone + File
files = {"foto_perfil": ("test.jpg", b"fake_image_data", "image/jpeg")}
res2 = requests.post(url, headers=headers, data={"telefono": "5556667778"}, files=files)
print("Test 2 (Multipart):", res2.status_code, res2.text)


