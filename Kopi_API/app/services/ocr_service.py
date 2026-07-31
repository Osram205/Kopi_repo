import pytesseract
from PIL import Image
import io

class OCRService:
    @staticmethod
    def validar_credencial(imagen_frente_bytes: bytes, imagen_trasera_bytes: bytes, matricula: str, nombre: str) -> bool:
        """
        Lee el texto de ambos lados de la credencial y valida que pertenezca a la UPQ y al alumno.
        """
        try:
            image_frente = Image.open(io.BytesIO(imagen_frente_bytes))
            image_trasera = Image.open(io.BytesIO(imagen_trasera_bytes))
            
            texto_frente = pytesseract.image_to_string(image_frente).lower()
            texto_trasera = pytesseract.image_to_string(image_trasera).lower()
            
            texto_extraido = texto_frente + " " + texto_trasera
            
            # Palabras clave obligatorias para validar que es de la UPQ
            es_upq = "politécnica" in texto_extraido or "upq" in texto_extraido or "querétaro" in texto_extraido
            
            # Validar que contenga la matrícula
            coincide_matricula = matricula.lower() in texto_extraido
            
            # Validar que contenga al menos una parte del nombre
            primer_nombre = nombre.split(" ")[0].lower()
            coincide_nombre = primer_nombre in texto_extraido

            # Consideramos válido si es de la UPQ y coincide la matrícula y el nombre
            return es_upq and coincide_matricula and coincide_nombre
        except Exception as e:
            print(f"Error en OCR Credencial: {e}")
            return False

    @staticmethod
    def validar_licencia(imagen_bytes: bytes, nombre: str) -> bool:
        """
        Lee la licencia de conducir y verifica que el nombre coincida.
        """
        try:
            image = Image.open(io.BytesIO(imagen_bytes))
            texto_extraido = pytesseract.image_to_string(image).lower()
            
            es_licencia = "licencia" in texto_extraido or "conducir" in texto_extraido or "chofer" in texto_extraido or "automovilista" in texto_extraido
            
            primer_nombre = nombre.split(" ")[0].lower()
            coincide_nombre = primer_nombre in texto_extraido
            
            return es_licencia and coincide_nombre
        except Exception as e:
            print(f"Error en OCR Licencia: {e}")
            return False
