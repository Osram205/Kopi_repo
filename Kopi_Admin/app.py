# Kopi_Admin/app.py

from flask import Flask, render_template, request, redirect, url_for, session, flash
import requests

app = Flask(__name__)
app.secret_key = "super_secreta_clave_flask_admin"
FASTAPI_URL = "http://127.0.0.1:8000"

@app.route("/", methods=["GET", "POST"])
def login():
    if "admin_token" in session:
        return redirect(url_for("dashboard"))
    if request.method == "POST":
        correo = request.form.get("correo")
        password = request.form.get("password")
        try:
            respuesta = requests.post(f"{FASTAPI_URL}/auth/login", json={
                "correo_institucional": correo,
                "contrasena": password
            })
            if respuesta.status_code == 200:
                session["admin_token"] = respuesta.json().get("access_token")
                return redirect(url_for("dashboard"))
            else:
                flash("Credenciales inválidas.", "danger")
        except requests.exceptions.ConnectionError:
            flash("Error de conexión con FastAPI.", "danger")
    return render_template("login.html")

# --- 1. CUADRO DE MANDO PRINCIPAL (SOLO MÉTRICAS Y GRÁFICAS) ---
@app.route("/dashboard")
def dashboard():
    if "admin_token" not in session:
        return redirect(url_for("login"))

    headers = {"Authorization": f"Bearer {session['admin_token']}"}
    respuesta = requests.get(f"{FASTAPI_URL}/admin/metricas", headers=headers)
    
    if respuesta.status_code == 403:
        session.clear()
        flash("Acceso denegado.", "danger")
        return redirect(url_for("login"))
        
    metricas = respuesta.json() if respuesta.status_code == 200 else {}
    return render_template("dashboard.html", metricas=metricas)

# --- 2. PANEL DE OPERACIONES (APROBACIONES Y SUSPENSIONES) ---
@app.route("/usuarios")
def gestion_usuarios():
    if "admin_token" not in session:
        return redirect(url_for("login"))

    headers = {"Authorization": f"Bearer {session['admin_token']}"}
    respuesta = requests.get(f"{FASTAPI_URL}/admin/verificaciones/pendientes", headers=headers)
    
    if respuesta.status_code == 403:
        session.clear()
        return redirect(url_for("login"))
        
    alumnos_pendientes = respuesta.json() if respuesta.status_code == 200 else []
    return render_template("usuarios.html", alumnos=alumnos_pendientes)

@app.route("/evaluar/<int:usuario_id>", methods=["POST"])
def evaluar_alumno(usuario_id):
    if "admin_token" not in session:
        return redirect(url_for("login"))

    accion = request.form.get("accion")
    headers = {"Authorization": f"Bearer {session['admin_token']}"}
    respuesta = requests.put(f"{FASTAPI_URL}/admin/usuarios/{usuario_id}/verificacion?accion={accion}", headers=headers)
    
    if respuesta.status_code == 200:
        flash(f"Usuario {accion} con éxito.", "success")
    else:
        flash("Error al procesar la solicitud.", "danger")
    return redirect(url_for("gestion_usuarios")) # Redirección corregida

@app.route("/suspender/<int:usuario_id>", methods=["POST"])
def suspender_usuario(usuario_id):
    if "admin_token" not in session:
        return redirect(url_for("login"))

    headers = {"Authorization": f"Bearer {session['admin_token']}"}
    respuesta = requests.delete(f"{FASTAPI_URL}/admin/usuarios/{usuario_id}/suspender", headers=headers)
    
    if respuesta.status_code == 200:
        flash("Usuario suspendido permanentemente.", "warning")
    else:
        flash("Error al suspender al usuario.", "danger")
    return redirect(url_for("gestion_usuarios")) # Redirección corregida

@app.route("/logout")
def logout():
    session.clear()
    return redirect(url_for("login"))

if __name__ == "__main__":
    app.run(port=5050, debug=True)