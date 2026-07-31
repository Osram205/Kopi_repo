import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from app.core.config import settings

class EmailService:
    @staticmethod
    def enviar_otp(destinatario: str, otp: str):
        if not settings.SMTP_USER or not settings.SMTP_PASSWORD:
            print("SMTP no configurado. No se pudo enviar OTP.")
            return False

        remitente = settings.SMTP_USER

        msg = MIMEMultipart()
        msg['From'] = f"{settings.SMTP_FROM_NAME} <{remitente}>"
        msg['To'] = destinatario
        msg['Subject'] = "KOPI - Código de Seguridad para Recuperación"

        # Plantilla HTML del correo
        cuerpo_html = f"""
        <html>
            <body style="font-family: Arial, sans-serif; text-align: center; padding: 20px; color: #333;">
                <h2 style="color: #0d6efd;">KOPI - Movilidad Segura</h2>
                <p>Has solicitado restablecer el acceso a tu cuenta.</p>
                <p>Tu código de seguridad de 6 dígitos es:</p>
                <h1 style="letter-spacing: 5px; color: #222; background: #f8f9fa; padding: 15px; border-radius: 8px; display: inline-block; border: 1px solid #dee2e6;">
                    {otp}
                </h1>
                <p style="font-size: 13px; color: #777; margin-top: 20px;">
                    Este código es confidencial. Si no solicitaste este cambio, por favor ignora este correo.
                </p>
            </body>
        </html>
        """
        
        msg.attach(MIMEText(cuerpo_html, 'html'))

        try:
            server = smtplib.SMTP(settings.SMTP_HOST, settings.SMTP_PORT)
            server.starttls() # Encriptación de seguridad TLS
            server.login(remitente, settings.SMTP_PASSWORD)
            server.sendmail(remitente, destinatario, msg.as_string())
            server.quit()
            return True
        except Exception as e:
            print(f"Error interno al enviar el correo a {destinatario}: {e}")
            return False
