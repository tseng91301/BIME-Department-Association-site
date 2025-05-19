from PyPDF2 import PdfReader, PdfWriter
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
from io import BytesIO

import sys
import base64
import json
import os

# 創建包含文字浮水印的 PDF
def create_text_watermark(watermark_text):
    packet = BytesIO()
    c = canvas.Canvas(packet, pagesize=letter)
    width, height = letter

    # 設定浮水印文字的樣式
    c.setFont("Helvetica", 40)
    c.setFillColorRGB(0.5, 0.5, 0.5, alpha=0.3)  # 浮水印顏色和透明度
    c.rotate(45)  # 旋轉浮水印文字
    c.drawString(150, 500, watermark_text)  # 在頁面上繪製文字
    c.save()
    
    packet.seek(0)
    return PdfReader(packet)

# 將浮水印應用到 PDF 文件
def add_watermark(input_pdf_path, watermark_text):
    # 讀取原始 PDF 文件
    original_pdf = PdfReader(input_pdf_path)
    # 生成文字浮水印 PDF
    watermark_pdf = create_text_watermark(watermark_text)
    watermark_page = watermark_pdf.pages[0]

    # 創建 PdfWriter 來寫入新的 PDF
    pdf_writer = PdfWriter()

    # 對每一頁添加浮水印
    for page_num in range(len(original_pdf.pages)):
        page = original_pdf.pages[page_num]
        # 將浮水印頁疊加到每一頁
        page.merge_page(watermark_page)
        pdf_writer.add_page(page)

    output_stream =  BytesIO()
    pdf_writer.write(output_stream)
    output_stream.seek(0) # 將指針移回流的開頭
    return output_stream.getvalue()

args = sys.argv
try:
    file_path = args[1]
    water_mark_str = args[2]
    if file_path[0] == "/":
        file_path = file_path[1:]
    file_path = os.path.join("/data", file_path)
    file_path = os.path.abspath(file_path)
    print(file_path)
    if file_path.split("/")[1] != "data":
        valid = False
    else:
        valid = True
    if valid:
        if file_path.split(".")[-1] == "pdf":
            return_val = base64.b64encode(add_watermark(file_path, water_mark_str))
        else:
            return_val = base64.b64encode(open(file_path, "rb").read())
        ret = {"code": 0, "response": "success", "data": str(return_val)}
        print(json.dumps(ret))
    else:
        ret = {"code": 2, "response": "This is illeagal", "data": ""}
        print(json.dumps(ret))
except Exception as e:
    ret = {"code": 1, "response": str(e), "data": ""}
    print(json.dumps(ret))