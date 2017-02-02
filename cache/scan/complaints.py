# -*- coding: utf-8 -*-

#sudo apt-get install python-software-properties software-properties-common
#sudo add-apt-repository ppa:gezakovacs/pdfocr
#sudo apt-get install 
#sudo apt-get install unoconv pdftohtml html2text tesseract-ocr cuneiform python-dateutil ocrfeeder gocr antiword

# sudo apt-get install curl python-scipy python-matplotlib python-tables imagemagick python-opencv python-bs4
#git clone https://github.com/tmbdev/ocropy.git
#wget 
#sudo python setup.py install


#from __future__ import absolute_import, division, print_function, unicode_literals

import sys
reload(sys)
sys.setdefaultencoding('utf8')

import os, re, subprocess, requests, json, unicodedata, string
from subprocess import Popen, PIPE
from dateutil import parser

from datetime import date
import dateutil.parser as dateparser
import time, HTMLParser

htmlparser = HTMLParser.HTMLParser()

import download
#from db import sqlite
from db import mysql

brokendates = {"29 February 2011": "28 February 2011", "7June 2011": "7 June 2011", "12 September 2012 (Revised 14 December 2012)": "12 September 2012", "21 Februrary 2013": "21 February 2013", "0 June 2015": "1 June 2015", "17June 2015": "17 June 2015", "30 November 2102": "30 November 2012", "5 June 2103": "5 June 2013", "5 July 2103": "5 July 2013", "7 August 2103": "7 August 2013", "24 Ju.ne 1991": "24 June 1991", "20 Ju.ne 1994": "20 June 1994", "9 Ju.ne 1997": "9 June 1997", "9August 2005": "9 August 2005", "27^ August 2013": "27 August 2013", "11 June 2014`": "11 June 2014", "1^ September 2014": "1 September 2014", "2^ June 2015": "2 June 2015"}

brokentext = {"Billboard A billboard": "Billboard\n\nA billboard", "Cgmﬁlaint": "Complaint", "Camplainant": "Complainant", "sement~": "Advertisement:", "Cmmpleaint": "Complaint", "Complainan't ~": "Complainant:", " Kiwi Bonds A newspaper": " Kiwi Bonds\n\nA newspaper", "Aﬁvertisement:": "Advertisement:", "Complaint: D ., H a Payne": "Complainant: D. H. Payne", "Complaint: W.Mn Easther": "Complainant: W. M. Easther", "Complaint:		 C. Turner, GOAL": "Complainant: C. Turner, GOAL", "Complaint:		 T. Quayle, FOA": "Complainant: T. Quayle, FOA", "CompI			 B. Langford, Health p,ction": "Complainant: B. Langford, Health Action", "Complaint:	 Dr ToO En't'tcott": "Complainant: Dr T. O. Enttcott", "Advertise:ment ~ lsonlls Whisky": "Advertisement: Wilson's Whisky", "Complaint:		 M. E. Jackson": "Complainant: M. E. Jackson", "Ccmplaint: J. A. Judson Advertigement: Wilsan’s Whisky": "Complainant: J. A. Judson Advertisement: Wilson’s Whisky", "Complainant~	 G.Blake.				Advertisment.: IIFarewell Fat with LiposucJcion": "Complainant: G.Blake Advertisement: ""Farewell Fat with Liposuction", "Complaint:	 A. Hume": "Complainant: A. Hume"}

brokendecision = {"’": "'", "Chai r": "Chair", "' s": "'s", "Chairm an": "Chairman", "Rul i ng": "Ruling", "Ruli ng": "Ruling", "Ruling ": "Ruling: ", "Decision ": "Decision: ", "Chairmans": "Chairman's", "Chairpersons": "Chairperson's", "Upehld": "Upheld", "Ugh": "Uph", "Settled, Media Error": "Settled - Media Error", "Settled- ": "Settled - ", "No Ground ": "No Grounds ", "No Upheld": "Not Upheld", "–": "-"}

others = {"one other": "Other", "another": "Other", "duplicate": "Other", "others": "Others", "duplicates": "Others", "other students": "Others", "class": "Others"}

#debug = True

#controlCharRegex = re.compile('[%s]' % re.escape(''.join(map(unichr, range(0,32) + range(127,160)))))

class complaints:
 def __init__(self, refresh = False, quick = False):
  self.currentYear = date.today().year
  self.db = mysql()
  #self.db = mysql(True)
  self.refresh = refresh
  self.quick = quick
  #self.complaint['details'] = complaint(self.refresh, self.quick)
  self.sources = {'old': 'old.asa.co.nz', 'www': 'www.asa.co.nz', 'nzlii': 'www.nzlii.org'}
  self.source = ''

 def getAll(self):
  print "Scraping from: old.asa.co.nz"
  self.getOld()
  print "Scraping from: www.asa.co.nz"
  self.getWWW()
  print "Scraping from: www.nzlii.org"
  self.getNZLII()
 
 def cleanNZLII(self, text):
  return " ".join(htmlparser.unescape(text).replace("\n", " ").replace("<br>", "").replace("Chai rm an’ s Rul i ng", "Chairman's Ruling").split()).strip()

 def newComplaint(self):
  self.complaint = {'details': {}, 'doc': {}, 'complainants': [], 'advertisers': [], 'codes_clauses': [], 'media': [], 'media_sub': [], 'products': [], 'products_sub': [], 'decisions': [], 'decisions_sub': []}
 
 def getNZLII(self):
  for year in range(1991, self.currentYear + 1):
   complaintspage = download.getFile("http://www.nzlii.org/nz/cases/NZASA/" + str(year) + "/", download.getFilename("cache", "nzlii", year, "pages", "complaints"), year == self.currentYear)
   if complaintspage:
    complaints = re.finditer(ur'<a href="\.\.\/' + str(year) + '\/(?P<id>[0-9]{1,5})\.html" class="make-database">(?P<advertisement>.*?) \[(?P<year>[1-2][0-9]{3})\] NZASA (?P<id2>[0-9]{1,5}) \((?P<date>.*?)\)<\/a>', complaintspage, flags=re.DOTALL) # 
    for complaintmatch in complaints:
     complaint = complaintmatch.groupdict()
     self.newComplaint()
     self.complaint['details']['id'] = str(year)[2:] + complaint["id"].zfill(3)
     self.complaint['details']['source'] = 'nzlii'
     self.complaint['details']['year'] = year
     self.complaint['details']['meeting'] = parser.parse(complaint["date"])
     self.complaint['details']['advertisement'] = complaint["advertisement"].replace(" Chairman's Ruling","").rstrip('- ')
     self.complaint['details']['url'] = "http://www.nzlii.org/nz/cases/NZASA/" + str(year) + "/" + complaint["id"] + '.html'
     self.complaint['doc']['type'] = "pdf" if year < 2000 else "html"
     self.complaint['doc']['url'] = "http://www.nzlii.org/nz/cases/NZASA/" + str(year) + "/" + complaint["id"] + "." + self.complaint['doc']['type']
     self.complaint['doc']["cache"] = download.getFilename("cache", self.complaint['details']['source'], year, "docs", complaint["id"], self.complaint['doc']['type'])
     self.processComplaint()
    self.db.commit()

 def getWWW(self):
  for year in range(2015, self.currentYear + 1):
   for category, code in self.db.getIndices("SELECT name, id FROM products").iteritems():
    self.getWWWJSON(year, "products", "product", category, code)
   for category, code in self.db.getIndices("SELECT name, id FROM media UNION SELECT name, id FROM media_sub WHERE source = 'www'").iteritems():
    self.getWWWJSON(year, "media", "media", category, code)
   for category, code in self.db.getIndices("SELECT name, id FROM decisions").iteritems():
    self.getWWWJSON(year, "decisions", "ruling", category, code)

 def getWWWJSON(self, year, categorycolumn, categorytype, category, code):
  complaints = download.getFile("http://www.asa.co.nz/backend/documentsearch.php?year=" + str(year) + "&" + categorytype + "=" + str(code), download.getFilename("cache", "www", str(year), categorycolumn, str(code) + " - " + category, 'json'), year == self.currentYear)
  if complaints:
   for complaint in json.loads(complaints):
    year = int(complaint["date"][:4])
    self.newComplaint()
    self.complaint['doc']['type'] = "pdf"
    if complaint["filename"]:
     self.complaint['details']['id'] = complaint["filename"][:5]
    else:
     self.complaint['details']['id'] = complaint["title"][10:12] + complaint["title"][13:16]
    if self.complaint['details']['id'][:1] != "D":
     self.complaint['details']['source'] = "www"
     self.complaint['details']['year'] = year
     self.complaint['details']['advertisers'] = complaint["title"][17:]
     self.complaint['advertisers'] = [self.complaint['details']['advertisers']]
     if ", " in complaint["title"]:
      self.complaint['details']['media'] = complaint["title"].replace("–", ",").replace("-", ",").split(', ')[-1]
      self.complaint['media'] = self.complaint['details']['media'].split(" and ")
      self.complaint['details']['advertisers'] = complaint["title"][17:-(len(self.complaint['details']['media']) + 2)]
      self.complaint['advertisers'] = [self.complaint['details']['advertisers']]
     try:
      self.complaint['details']['complainants'] = re.match(ur"Complainant: ([A-Z]\. [^\s,]+)", complaint["summary"], flags=re.DOTALL).group(1)
      self.complaint['complainants'] = self.splitComplainants(self.complaint['details']['complainants'])
     except: pass
     self.complaint['details'][categorycolumn] = category
     #if "decisions" in self.complaint['details']: self.complaint['details']["decisions"] = ""
     self.complaint['details']['released'] = parser.parse(complaint["date"])
     try: self.complaint['details']['meeting'] = parser.parse(re.search(ur"(?:Ruling Date|Date of Ruling):([^\n]*)", complaint["summary"], flags=re.DOTALL).group(1))
     except: pass
     #self.complaint['details']['decisions'] = complaint["summary"].split(': ')[-1].strip(". ")
     try:
      self.complaint['details']['decisions'] = re.search(ur"Outcome:(.*)", complaint["summary"], flags=re.DOTALL).group(1).strip(". ")
      self.complaint['decisions'] = [self.fixDecision(self.complaint['details']['decisions'])]
     except: pass
     self.complaint[categorycolumn] = [category]
     self.complaint['doc']['url'] = complaint['url'].replace("//backend/documents/", "/backend/documents/")
     self.complaint['doc']["cache"] = download.getFilename("cache", self.complaint['details']['source'], year, "docs", self.complaint['details']["id"], self.complaint['doc']['type'])
     self.processComplaint()
   self.db.commit()

 def getOld(self):
  for year in range(2006, 2016):
   self.db.cursor.execute("SELECT * FROM products_sub")
   for product in self.db.cursor.fetchall():
    content = download.getFile("http://old.asa.co.nz/srch_product.php", download.getFilename("cache", "old", year, "products", product["name"]), year == self.currentYear, {'product_id': product["id"], 'year': year})
    if content:
     complaints = re.finditer(ur"<tr align='left' valign='top'><td><b><p><a href='display.php\?ascb_number=(?P<id>[0-9]{5})'>[0-1][0-9]/[0-9]{3}(?:.*?)</b> - (?P<advertisement>.*?)</a></p></td><td><p>(?P<decisions>.*?)</p></td><td><a href='(?P<doc_url>.*?)'[^>]*><p>Full Decision</p></a></td></tr>", content, flags=re.DOTALL)
     for complaintmatch in complaints:
      complaint = complaintmatch.groupdict()
      self.newComplaint()
      self.complaint['details'].update(complaintmatch.groupdict())
      self.complaint['details']["year"] = year
      self.complaint['details']['url'] = "http://old.asa.co.nz/display.php?ascb_number=" + complaint["id"]
      self.complaint['details']["products"] = product["name"]
      self.complaint["products"] = [product["name"]]
      self.complaint["decisions"] = [self.fixDecision(self.complaint["details"]["decisions"])]
      self.complaint['doc']['url'] = self.complaint['details']["doc_url"]
      del self.complaint['details']["doc_url"]
      if self.complaint['doc']['url'][:4] != "http": self.complaint['doc']['url'] = "http://old.asa.co.nz/" + self.complaint['doc']['url']
      #self.complaint['details']["advertisement"] = complaint["advertisement"]
      #print self.complaint['details']
      self.getOldComplaint()
    self.db.commit()
   #for id in self.db.getMissing(year):
   # self.complaint['details'] = dict()
   # self.complaint['details']["id"] = id
   # self.complaint['details']['year'] = year
   # self.complaint['details']['url'] = "http://old.asa.co.nz/display.php?ascb_number=" + complaint["id"]
   # self.complaint['doc']['url'] = "http://old.asa.co.nz/decision_file.php?ascbnumber=" + self.complaint['details']["id"]
   # self.getOldComplaint()

 def getOldComplaint(self):
  self.complaint['details']['source'] = "old"
  self.complaint['doc']['type'] = "doc" if self.complaint['details']["year"] >= 2007 else "rtf"
  self.complaint['doc']["cache"] = download.getFilename("cache", self.complaint['details']['source'], self.complaint['details']["year"], "docs", self.complaint['details']["id"], self.complaint['doc']['type'])
  if self.complaint['details']['url']:
   self.complaint['details']["cache"] = download.getFilename("cache", self.complaint['details']['source'], self.complaint['details']["year"], "pages", self.complaint['details']["id"])
   page = download.getFile(self.complaint['details']['url'], self.complaint['details']["cache"])
   if page:
    data = re.search(ur"<h1>[0-1][0-9]/[0-9]{3} - (?P<advertisement>.*?)</h1>(?:.*?)<p><b>(?P<appeal>.*?)</b></p>(?:.*?)<p><b>Media</b><br>(?P<media>.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(?P<clauses>.*?)(?:<br>)*</p>(?:.*?)<p><b>(?P<decisions>.*?)</b></p>(?:.*?)<p>\(<a href='(?P<doc_url>.*?)'[^>]*>Full Decision</a>\)</p>", page, flags=re.DOTALL)
    self.complaint['details'].update(data.groupdict())
    clauses = filter(None, self.complaint['details']["clauses"].split("<br>"))
    for clause in clauses:
     self.complaint["codes_clauses"].append(clause.split(", ", 1))
    self.complaint['doc']['url'] = self.complaint['details']["doc_url"]
    del self.complaint['details']["doc_url"]
    self.complaint["decisions"] = [self.fixDecision(self.complaint["details"]["decisions"])]
    if self.complaint['doc']['url'][:4] != "http": self.complaint['doc']['url'] = "http://old.asa.co.nz/" + self.complaint['doc']['url']
    for clause in filter(None, self.complaint['details']["clauses"].split("<br>")):
     self.complaint["codes_clauses"].append(clause.split(', ', 1))
    self.complaint["media"] = filter(None, list(set(self.complaint['details']["media"].split("<br>"))))
    if self.complaint['details']["appeal"]:
    # self.complaint['details']["appealsuccess"] = None
    # if "Allowed" in self.complaint['details']["appeal"]:
    #  self.complaint['details']["appealsuccess"] = 1
    # elif "Declined" in self.complaint['details']["appeal"] or "Dismissed" in self.complaint['details']["appeal"]:
    #  self.complaint['details']["appealsuccess"] = 0
     self.complaint['details']["appeal"] = self.complaint['details']["appeal"][8:10] + self.complaint['details']["appeal"][11:14]
    #else:
    # del self.complaint['details']["appeal"]
    self.processComplaint()

 def processComplaint(self):
  self.convertDoc()
  self.processDoc()
  if 'complainants' in self.complaint['details']:
   self.complaint['complainants'] = self.splitComplainants(self.complaint['details']['complainants'])
  self.db.insertComplaint(self.complaint)
  #sys.exit()

 def splitComplainants(self, complainants):
  complainants = complainants.strip(" .")
  for find, replacement in others.iteritems():
   if complainants.lower().endswith(find):
    complainants = replacement + "|" + complainants[:-len(find)].strip()
  for find in (" and", ",", "&"):
   if complainants.lower().endswith(find):
    complainants = complainants[:-len(find)].strip()
  complainants = complainants.replace(", ", "|").replace("/", "|").replace(". & ", "#").replace(" & ", "|").replace("#", ". & ").replace(" and ", "|").replace(".", ". ").replace("  ", " ")
  complainants = complainants.split("|")
  complainants[:] = [re.sub(r'([A-Z]) ([A-Z][a-z]+)', r'\1. \2', complainant) for complainant in complainants]
  return [value for value in complainants if value not in ["[A. Complainant]", "A. Complainant", "A Complainant", "A Person", "A. Person", "An Individual"]]

 def cleanScrape(self, scrape):
  #return re.sub(' +', ' ', text)
  #return ' '.join(text.split())
  return dict([(k, ' '.join(v.split())) for k,v in scrape.items()])
 
 def cleanText(self, text):
  text = re.sub(' +', ' ', re.sub('(\n){2,}', '\r\r', htmlparser.unescape(text).replace('\r\n', '\n').replace('\r', '\n')).replace('\n', ' ').replace('\r', '\n').replace('    ', '\t'))
  return '\n'.join([t.strip() for t in text.splitlines()]).strip()

 def words(self, text):
  return len(text.split())
  
 def getText(self, format = "txt", altocr = False):
  #command = []
  shell = False
  filename = download.getAbsolutePath(self.complaint['doc']["cache"])
  if self.complaint['details']['source'] == 'nzlii':
   if self.complaint['doc']['type'] == 'pdf' and format == 'html':
    return False
   elif self.complaint['doc']['type'] == 'html' and format == 'txt':
    filename = download.getAbsolutePath(self.complaint['doc']["html"]) # Get the cleaned version of the file, rather than the original page
  if self.complaint['doc']['type'] == 'pdf':
   if format == 'txt':
    if self.complaint['details']['source'] == 'nzlii' and not altocr:
     #command = ["convert", "-geometry", "3000x3000", "-density", "300x300", "-quality", "100", "-monochrome", "-append", filename, "png:-", "|", "tesseract", "-l", "eng", "stdin", "stdout"]
     #command = "convert -geometry 3000x3000 -density 300x300 -quality 100 -monochrome -append " + filename + " png:- | tesseract -l eng stdin stdout"
     #command = "convert -geometry 1500x1500 -density 150x150 -quality 100 -monochrome -append " + filename + " png:- | tesseract -l eng stdin stdout"
     command = "convert -geometry 1000x1000 -density 100x100 -quality 100 -monochrome -append " + filename + " png:- | tesseract -l eng stdin stdout"
     shell = True
    else:
     command = ["pdftotext", "-layout", "-eol", "unix", "-enc", "UTF-8", "-nopgbrk", filename, "-"]
   elif format == 'html':
    command = ["pdftohtml", "-noframes", "-enc", "UTF-8", "-stdout", filename]
  elif self.complaint['doc']['type'] == 'doc':
   if format == 'txt':
    command = ["antiword", "-w", "99999", filename]
   elif format == 'html':
    command = ["unoconv", "-f", "html", "-e", "FilterOptions=UTF8", "--stdout", filename]
  elif self.complaint['doc']['type'] == 'rtf':
   if format == 'txt':
    command = ["unrtf", "--text", filename]
   elif format == 'html':
    command = ["unrtf", "--html", filename]
  elif self.complaint['doc']['type'] == 'html':
   if format == 'txt':
    #command = ["html2text", "-nobs", "-ascii", "-utf8", "-width", "99999", filename]
	command = ["w3m", "-dump", "-o", "display_charset=UTF-8", filename] # , "-no-graph"
   elif format == 'html':
    command = ["cat", filename]
  #print command
  try:
   text = subprocess.check_output(command, shell = shell)
  except subprocess.CalledProcessError as e:
   #print "Conversion failed"
   print e.output
   return
  if self.complaint['doc']['type'] == 'rtf' and format == 'txt':
   #text = controlCharRegex.sub('', text.split("-----------------")[1])
   text = filter(lambda x: x in set(string.printable), text.split("-----------------")[1])
  elif self.complaint['doc']['type'] == 'html' and self.complaint['details']['source'] == 'nzlii':
   #text = text.replace("Chai rm an’ s Rul i ng", "Chairman's Ruling").replace("Chair’ s", "Chair's").replace("Chai r’ s", "Chair's")
   if format == 'html':
    try: text = re.search('<\/P>(.*?)<!--sino noindex-->', text, flags=re.DOTALL).group(1)
    except: return False
   elif format == 'txt':
    for char in ['┐', '┤', '┘']: text = text.replace(char, '\n')
    for char in ['─', '┌', '┬', '│', '├', '┼', '└', '┴']: text = text.replace(char, '')
    text = text.replace("_", " ")
  elif self.complaint['doc']['type'] == 'doc' and format == "txt":
   text = text.replace('|\n', '\n\n').replace("|", "")
  elif format == "html":
   text = re.search('<body(?:.*?)>(.*?)</body>'                              , text, flags=re.DOTALL).group(1)
  return self.cleanText(text)

 def getPaths(self):
  paths = dict()
  for type in ["cache", "html", "text"]:
   paths[type] = download.getAbsolutePath(self.complaint['doc'][type])
  return paths

 def convertDoc(self):
  self.complaint['doc']["text"] = download.getFilename("output", self.complaint['details']['source'], self.complaint['details']["year"], "text", self.complaint['details']["id"], "txt")
  self.complaint['doc']["html"] = download.getFilename("output", self.complaint['details']['source'], self.complaint['details']["year"], "html", self.complaint['details']["id"])
  paths = self.getPaths()
  if not os.path.exists(paths["cache"]):
   download.getFile(self.complaint['doc']['url'], self.complaint['doc']["cache"], returnfile = False)
  if os.path.exists(paths["cache"]):
   if not os.path.exists(paths["html"]):
    download.saveContents(paths["html"], self.getText("html"))
   if not os.path.exists(paths["text"]):
    self.complaint['doc']['contents'] = self.getText()
    download.saveContents(paths["text"], self.complaint['doc']['contents'])
   else:
    self.complaint['doc']['contents'] = download.loadResource(self.complaint['doc']["text"])
   textalt = download.getAbsolutePath(download.getFilename("output", self.complaint['details']['source'], self.complaint['details']["year"], "text-alt", self.complaint['details']["id"], "txt"))
   if self.complaint['details']['source'] == 'nzlii' and self.complaint['doc']['type'] == 'pdf' and not os.path.exists(textalt):
	download.saveContents(textalt, self.getText(altocr = True))
   if self.complaint['doc']['contents']:
    self.complaint['doc']["size"] = len(self.complaint['doc']['contents'])
    self.complaint['doc']["words"] = len(self.complaint['doc']['contents'].split())

 def getDate(self, date):
  return str(dateparser.parse(date))[:10]
 
 def fixDate(self, date):
  if date in brokendates: return self.getDate(brokendates[date])
  return self.getDate(date)

 def fixText(self, text):
  for t, r in brokentext.iteritems():
   text = text.replace(t, r)
  return text

 def fixDecision(self, text):
  for t, r in brokendecision.iteritems():
   text = text.replace(t, r)
  return text.strip().strip(".")

 def splitMulti(self):
  #for separator in [", ", " and ", " & "]
  pass

 def processDoc(self):
  if 'contents' in self.complaint['doc'] and self.complaint['doc']['contents']:
   #print self.complaint['doc']['contents'][:512]
   if self.complaint['details']['source'] == "www":
    self.processWWW()
   elif self.complaint['details']['source'] == "old": # and self.complaint['doc']['type'] == 'doc':
    self.processWord()
   elif self.complaint['details']['source'] == "nzlii":
    if self.complaint['doc']['type'] == "pdf":
     self.processNZLII()
     if not "complainants" in self.complaint['details']:
      print "\n\n"
      self.complaint['doc']['contents'] = download.loadResource(download.getAbsolutePath(download.getFilename("output", self.complaint['details']['source'], self.complaint['details']["year"], "text-alt", self.complaint['details']["id"], "txt")))
      if self.complaint['doc']['contents']:
       self.processNZLII()
     #if not "complainants" in self.complaint['details']:
      #if raw_input('Exit?').lower().startswith("y"): sys.exit()
    else:
     self.processWord()
     self.complaint['details']['decisions'] = self.complaint['doc']['contents'].splitlines()[-1]
     self.complaint['decisions'] = [self.fixDecision(self.complaint['details']['decisions'])]

 def processWord(self):
  result = re.search(ur"(?P<meeting>[0-9]{0,2}(?:st|nd|rd|th){0,1}[ ]*(?:January|February|March|April|May|may|June|July|August|September|October|November|December)[ ]*20[0-9]{2})(?:.*?)(?:Complainants|Complainant/Applicant|Complainant)(?::){0,1}(?P<complainants>.*?)(?:Advertisement/Item|Advertisements|Advertisement|Parade|Matter|Advertiser / Applicant|Item|Advertiser|Image|Material|Promotion|Editorial|Name|Magazine cover|Publisher|Editorial|Editoral|Content|Complaint)(?:.*?)(?::){0,1}(?P<advertisers>.*?)(?:\n\n|Limited Appellant:|Applicant:|\nComplaint:)", self.complaint['doc']['contents'][:512], flags=re.DOTALL)
  if result:
   scrape = self.cleanScrape(result.groupdict())
   #print scrape["meeting"]
   scrape['meeting'] = self.fixDate(scrape['meeting'])
   self.complaint['details'].update(scrape)
   self.complaint['complainants'] = self.splitComplainants(self.complaint['details']['complainants'])
   self.complaint['advertisers'] = [self.complaint['details']['advertisers']]
   #print scrape
  else:
   self.processWWW()

 def processWWW(self):
  result = re.search(ur"(?:COMPLAINANTS|COMPLAINANT)(?P<complainants>.*?)(?:ADVERTISERS|ADVERTISER|SOURCE)(?P<advertisers>.*?)(?:ADVERTISEMENT|ITEM)(?P<advertisement>.*?)(?:DATE OF MEETING|DATE)(?P<meeting>.*?)(?:\[){0,1}(?:OUTCOME)(?P<decisions>.*?)(?:\n)", self.complaint['doc']['contents'][:512], flags=re.DOTALL)
  if not result: result = re.search(ur"(?:COMPLAINANTS|COMPLAINANT)(?P<complainants>.*?)(?:ADVERTISER|SOURCE|ADVERTISEMENT|MEDIA)(?P<advertisers>.*?)(?:DATE OF MEETING|DATE)(?:.*?)(?P<meeting>.*?)(?:OUTCOME)(?P<decisions>.*?)(?:\n)", self.complaint['doc']['contents'][:512], flags=re.DOTALL)
  if result:
   scrape = self.cleanScrape(result.groupdict())
   #print scrape["meeting"]
   scrape['meeting'] = self.fixDate(scrape['meeting'])
   self.complaint['details'].update(scrape)
   self.complaint['decisions'] = [self.fixDecision(self.complaint['details']['decisions'])]
   self.complaint['advertisers'] = [self.complaint['details']['advertisers']]
   #print scrape
  else:
   print self.complaint['doc']['contents'][:512]

 def processNZLII(self):
  text = self.fixText(self.complaint['doc']['contents'][:512])
  result = re.search(r"(?:Ccmplainant|Ccmpleinant|Comgglainant |Camplainan‘t|Complainan't|Applicant|Complainan-t|Complainant~|Complaina.nt|€omplainant|Campiainant|Ccmplsinant|Complainaizt|Camplainam|Compieinam|Complamam|Cemplainant|Compiainamt|Appellant|Compla:Ll10ant|Com.plainant|'l,.Olrnp1.a:m:ant|Complaim:mt|Compiainam;|Ccmpﬁainam|Complainam|Complaimm|Complainants|Complainant|complainant|Complaint:)(?::){0,1}(?P<complainants>.*?)(?:Advertisements|Advertisement|Advertisment|Advertisemen't ~|Advertising|Complaint|Application|Advertisemeat;|Advartisement|Advertisemem|Advsﬁisement|Advez’gisemem|Aoyertlsemerlt|Atlvertisement|Advextisement|Adveliisement|i\\\.dvertisement|Advertisemam|Adveiiisemeni|Adve:liisement|Adveltisement|Advertisenient|Advertisement;|AdVerﬁsemenf)(?:.*?)(?::){0,1}(?P<advertisers>.*?)(?:\n\n|Complaint:|Eornnlnint:)", text, flags=re.DOTALL)
  if result:
   scrape = self.cleanScrape(result.groupdict())
   self.complaint['details'].update(scrape)
   try:
    self.complaint['details']['decisions'] = re.search(r'(?:Decision:|The complaint was therefore )(?P<decisions>.*?)(?:$|\n)', self.complaint['doc']['contents'], flags=re.DOTALL).group('decisions').strip()
    self.complaint['decisions'] = [self.fixDecision(self.complaint['details']['decisions'])]
   except: print "No decision"
   self.complaint['complainants'] = self.splitComplainants(self.complaint['details']['complainants'])	 
   self.complaint['advertisers'] = [self.complaint['details']['advertisers']]
   #print scrape
  else:
   print text