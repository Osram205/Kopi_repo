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
            # CORRECCIÓN CLAVE: Enviamos como formulario (data=) usando 'username' y 'password'
            # para acoplarnos al cambio de OAuth2PasswordRequestForm en FastAPI
            respuesta = requests.post(f"{FASTAPI_URL}/auth/login", data={
                "username": correo,
                "password": password
            })
            if respuesta.status_code == 200:
                session["admin_token"] = respuesta.json().get("access_token")
                return redirect(url_for("dashboard"))
            else:
                flash("Credenciales inválidas o no tienes privilegios de administrador.", "danger")
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
    
    # CORRECCIÓN: Si el token expiró (401) o no es admin (403), limpiamos sesión y expulsamos al login
    if respuesta.status_code in [401, 403]:
        session.clear()
        flash("Tu sesión ha expirado o no tienes autorización. Inicia sesión de nuevo.", "danger")
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
    
    if respuesta.status_code in [401, 403]:
        session.clear()
        flash("Sesión expirada. Por favor vuelve a ingresar.", "danger")
        return redirect(url_for("login"))
        
    alumnos_pendientes = respuesta.json() if respuesta.status_code == 200 else []
    return render_template("usuarios.html", alumnos=alumnos_pendientes)

# --- 3. PANEL DE DIRECTORIO GENERAL (CON FILTRADO) ---
@app.route("/directorio")
def directorio_usuarios():
    if "admin_token" not in session:
        return redirect(url_for("login"))

    estatus_filtro = request.args.get("estatus", "")
    headers = {"Authorization": f"Bearer {session['admin_token']}"}
    
    url = f"{FASTAPI_URL}/admin/usuarios/directorio"
    if estatus_filtro:
        url += f"?estatus={estatus_filtro}"
        
    respuesta = requests.get(url, headers=headers)
    
    if respuesta.status_code in [401, 403]:
        session.clear()
        flash("Sesión expirada. Por favor vuelve a ingresar.", "danger")
        return redirect(url_for("login"))
        
    usuarios_procesados = respuesta.json() if respuesta.status_code == 200 else []
    return render_template("directorio.html", alumnos=usuarios_procesados, estatus_actual=estatus_filtro)

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
        flash("Error al procesar la solicitud o sesión expirada.", "danger")
    return redirect(url_for("gestion_usuarios"))

@app.route("/revocar/<int:usuario_id>", methods=["POST"])
def revocar_alumno_conductor(usuario_id):
    if "admin_token" not in session:
        return redirect(url_for("login"))

    headers = {"Authorization": f"Bearer {session['admin_token']}"}
    try:
        respuesta = requests.put(f"{FASTAPI_URL}/admin/usuarios/{usuario_id}/revocar", headers=headers)
        if respuesta.status_code == 200:
            flash("Permisos de conducción revocados. El alumno ha vuelto al rol de pasajero.", "info")
        else:
            flash("Error al procesar la revocación o sesión expirada.", "danger")
    except requests.exceptions.ConnectionError:
        flash("Error de comunicación con el backend.", "danger")
        
    return redirect(url_for("directorio_usuarios"))

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
    return redirect(request.referrer or url_for("gestion_usuarios"))

@app.route("/logout")
def logout():
    session.clear()
    return redirect(url_for("login"))

if __name__ == "__main__":
    app.run(port=5050, debug=True)