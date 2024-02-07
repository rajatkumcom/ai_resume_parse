#!/usr/bin/python3
import sys
from pdfminer.high_level import extract_text

def extract_text_from_pdf(pdf_path):
  return extract_text(pdf_path)

if __name__ == '__main__':
  print('Hi')
    # print(extract_text_from_pdf(sys.argv[1:][0]))
  