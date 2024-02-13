#!/usr/bin/python3
import re
# docx to txt converter
import docx2txt
# language processor
import nltk
# pdf to text converter
from pdfminer.high_level import extract_text
import ssl
# language processor
import spacy
# covert csv data to list
import csv
import sys

# you may read the database from a csv file or some other database
SKILLS_DB = [
  'machine learning',
  'data science',
  'python',
  'word',
  'excel',
  'English',
  'php',
  'drupal',
  'html',
  'css',
  'javascript',
  'jquery',
  'react',
  'angular',
  'jira',
]

RESERVED_WORDS = [
  'school',
  'college',
  'univers',
  'academy',
  'faculty',
  'institute',
  'faculdades',
  'Schola',
  'schule',
  'lise',
  'lyceum',
  'lycee',
  'polytechnic',
  'kolej',
  'ünivers',
  'okul',
]

def extract_text_from_pdf(pdf_path):
  return extract_text(pdf_path)

nltk.download('stopwords')
nltk.download('punkt')
nltk.download('averaged_perceptron_tagger')
nltk.download('maxent_ne_chunker')
nltk.download('words')

def extract_text_from_docx(docx_path):
  txt = docx2txt.process(docx_path)
  if txt:
    return txt.replace('\t', ' ')
  return None

def extract_names(txt):
  person_names = []

  for sent in nltk.sent_tokenize(txt):
    for chunk in nltk.ne_chunk(nltk.pos_tag(nltk.word_tokenize(sent))):
      if hasattr(chunk, 'label') and chunk.label() == 'PERSON':
        person_names.append(
          ' '.join(chunk_leave[0] for chunk_leave in chunk.leaves())
        )

  return person_names

def extract_phone_number(txt):
  # Regex for phone number
  PHONE_REG = re.compile(r'[\+\(]?[1-9][0-9 .\-\(\)]{8,}[0-9]')
  phone = re.findall(PHONE_REG, txt)
  if phone:
    number = ''.join(phone[0])
    if txt.find(number) >= 0 and len(number) <= 16:
      return number
  return None

def extract_email_address(txt):
  # Regex for email address
  EMAIL_REG = re.compile(r'[A-Za-z0-9\.\-+_]+@[a-z0-9\.\-+_]+\.[a-z]+')
  return re.findall(EMAIL_REG, txt)

def extract_skills(txt):
  stop_words = set(nltk.corpus.stopwords.words('english'))
  word_tokens = nltk.tokenize.word_tokenize(text)

  #remove stop words
  filtered_tokens = [w for w in word_tokens if w not in stop_words]

  #remove punctuation
  filtered_tokens = [w for w in word_tokens if w.isalpha()]

  # generate bigrams and trigrams (such as artificial intelligence)
  bigrams_trigrams = list(map(' '.join, nltk.everygrams(filtered_tokens, 2, 3)))

  # we create a set to keep the results in.
  found_skills = set()

  # we search for each token in our skills database
  for token in filtered_tokens:
    if token.lower() in SKILLS_DB:
      found_skills.add(token.lower())

  # we search for each bigram and trigram in our skills database
  for ngram in bigrams_trigrams:
    if ngram.lower() in SKILLS_DB:
      found_skills.add(ngram.lower())

  return found_skills

def extract_education(txt):
  organizations = []
 
  # first get all the organization names using nltk
  for sent in nltk.sent_tokenize(txt):
    for chunk in nltk.ne_chunk(nltk.pos_tag(nltk.word_tokenize(sent))):
      if hasattr(chunk, 'label') and chunk.label() == 'ORGANIZATION':
        organizations.append(' '.join(c[0] for c in chunk.leaves()))
 
  # we search for each bigram and trigram for reserved words
  # (college, university etc...)
  education = set()
  for org in organizations:
    for word in RESERVED_WORDS:
      if org.lower().find(word) >= 0:
        education.add(org)
 
  return education

def extract_years_of_experience(txt):
  EXP_REG = re.compile(r'(\d+)\+?\s*(year|yr)[s]*\s*(of\s*)?(experience|exp)?')
  all_experiences = re.findall(EXP_REG, txt)
  total_experience_years = sum(int(match[0]) for match in all_experiences)
  return total_experience_years

if __name__ == '__main__':
  if sys.argv[1:][1] == 'docx':
    text = extract_text_from_docx(sys.argv[1:][0])
  elif sys.argv[1:][1] == 'pdf':
    text = extract_text_from_pdf(sys.argv[1:][0])
  else:
    print("Incorrect file format")
  
  names = extract_names(text)
  phone_number = extract_phone_number(text)
  email_address = extract_email_address(text)
  skills = extract_skills(text)
  education = extract_education(text)
  experience = extract_years_of_experience(text)

  if names:
    print(names[0])
    print(names[1])
  if phone_number:
    print(phone_number)
  if email_address:
    print(email_address[0])
  print(skills)
  print(education)
  print(experience)

  