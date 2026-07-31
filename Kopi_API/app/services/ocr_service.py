import pytesseract
import cv2
import numpy as np
import re
from thefuzz import fuzz
from typing import Tuple

class OCRService:
    @staticmethod
    def _preprocess_image(imagen_bytes: bytes) -> np.ndarray:
        """
        Preprocesa la imagen usando OpenCV para mejorar los resultados de Tesseract.
        1. Convierte bytes a array de numpy.
        2. Convierte a escala de grises.
        3. Aplica threshold adaptativo (binarización) para resaltar el texto y eliminar reflejos/ruido.
        """
        # Convertir bytes a imagen OpenCV
        nparr = np.frombuffer(imagen_bytes, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        
        # 1. Escala de grises
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        
        # 2. Aumentar contraste y Binarizar (Adaptive Thresholding)
        # Esto elimina las sombras y los reflejos del holograma creando un fondo blanco puro
        procesada = cv2.adaptiveThreshold(
            gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 11, 2
        )
        
        return procesada

    @staticmethod
    def validar_credencial(imagen_frente_bytes: bytes, imagen_trasera_bytes: bytes, matricula: str, nombre: str) -> bool:
        """
        Lee el texto de ambos lados de la credencial procesada con OpenCV.
        Usa validación difusa (thefuzz) y Regex para encontrar la información con alta tolerancia a errores.
        """
        try:
            # Preprocesamiento con OpenCV (Lentes para Tesseract)
            img_frente_cv = OCRService._preprocess_image(imagen_frente_bytes)
            img_trasera_cv = OCRService._preprocess_image(imagen_trasera_bytes)
            
            # OCR Extraer texto
            texto_frente = pytesseract.image_to_string(img_frente_cv, lang='spa+eng').upper()
            texto_trasera = pytesseract.image_to_string(img_trasera_cv, lang='spa+eng').upper()
            texto_extraido = (texto_frente + " " + texto_trasera).replace('\n', ' ')
            
            print(f"[OCR] Texto extraido: {texto_extraido}")

            # 1. Validación de Institución (Fuzzy Matching)
            # Tesseract suele fallar leyendo el logo. Validamos si hay al menos un 80% de coincidencia parcial
            target_univ = "UNIVERSIDAD POLITECNICA DE QUERETARO"
            target_upq = "UPQ"
            
            score_univ = fuzz.partial_ratio(target_univ, texto_extraido)
            score_upq = fuzz.partial_ratio(target_upq, texto_extraido)
            
            es_upq = score_univ >= 80 or score_upq >= 90
            print(f"[OCR] UPQ Score: {score_univ} (univ), {score_upq} (upq) -> Valido: {es_upq}")

            # 2. Validación de Matrícula (Regex + Exact)
            # Buscamos cualquier bloque de 9 números seguidos
            coincide_matricula = False
            if matricula.upper() in texto_extraido:
                coincide_matricula = True
            else:
                # Buscar con regex cualquier 9 digitos
                patron_matricula = re.search(r'\d{9}', texto_extraido)
                if patron_matricula:
                    print(f"[OCR] Matricula detectada por regex: {patron_matricula.group()}")
                    # Asumimos que si hay 9 digitos en una credencial UPQ, es válida (o se compara con la dada)
                    # Comparamos la matrícula extraida con la proporcionada por el usuario (permitiendo un margen de error tipográfico del OCR)
                    matricula_leida = patron_matricula.group()
                    if fuzz.ratio(matricula.upper(), matricula_leida) >= 80:
                        coincide_matricula = True

            print(f"[OCR] Matricula coincide: {coincide_matricula}")

            # 3. Validación de Nombre (Fuzzy Matching)
            # Solo buscamos que el primer nombre o apellidos coincidan fuertemente
            primer_nombre = nombre.split(" ")[0].upper()
            score_nombre = fuzz.partial_ratio(primer_nombre, texto_extraido)
            coincide_nombre = score_nombre >= 85
            
            print(f"[OCR] Nombre Score ({primer_nombre}): {score_nombre} -> Valido: {coincide_nombre}")

            return es_upq and coincide_matricula and coincide_nombre

        except Exception as e:
            print(f"Error en OCR Credencial: {e}")
            return False

    @staticmethod
    def validar_licencia(imagen_bytes: bytes, nombre: str) -> bool:
        """
        Lee la licencia de conducir con OpenCV y Fuzzy Matching.
        """
        try:
            img_cv = OCRService._preprocess_image(imagen_bytes)
            texto_extraido = pytesseract.image_to_string(img_cv, lang='spa+eng').upper()
            texto_extraido = texto_extraido.replace('\n', ' ')
            
            # Buscar palabras clave con fuzzy
            score_lic = fuzz.partial_ratio("LICENCIA DE CONDUCIR", texto_extraido)
            score_auto = fuzz.partial_ratio("AUTOMOVILISTA", texto_extraido)
            score_chofer = fuzz.partial_ratio("CHOFER", texto_extraido)
            
            es_licencia = score_lic >= 80 or score_auto >= 80 or score_chofer >= 80
            
            primer_nombre = nombre.split(" ")[0].upper()
            score_nombre = fuzz.partial_ratio(primer_nombre, texto_extraido)
            coincide_nombre = score_nombre >= 85
            
            return es_licencia and coincide_nombre

        except Exception as e:
            print(f"Error en OCR Licencia: {e}")
            return False
