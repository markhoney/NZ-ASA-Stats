# -*- coding: utf-8 -*-

import os, re, subprocess, requests, json
from datetime import date
import time, HTMLParser

parser = HTMLParser.HTMLParser()

import download
#from db import sqlite
from db import mysql

#antiword = os.path.join(os.getcwd(), "scan", "antiword", "antiword.exe")
#unrtf    = os.path.join(os.getcwd(), "scan", "unrtf", "bin", "unrtf.exe")
antiword = os.path.join("C:\\", "scan", "antiword", "antiword.exe")
unrtf    = os.path.join("C:\\", "scan", "unrtf", "bin", "unrtf.exe")
if os.name == 'posix':
 antiword = 'antiword'
 unrtf    = 'unrtf'
 unoconv  = 'unoconv'
 pdftotext = 'pdftotext'
 pdftohtml = 'pdftohtml'
 
debug = True

class complaints:
 def __init__(self, refresh = False, quick = False):
  self.currentYear = date.today().year
  self.db = mysql()
  #self.db = mysql(True)
  self.refresh = refresh
  self.quick = quick
  #self.complaint = complaint(self.refresh, self.quick)
  self.sources = {'old': 'old.asa.co.nz', 'www': 'www.asa.co.nz', 'nzlii': 'www.nzlii.org'}
  self.source = ''
  self.complaint = dict()

 def getAll(self):
  self.getOld()
  self.getWWW()
  self.getNZLII()
 
 def getNZLII(self):
  self.source = 'nzlii'
  #for year in range(2000, self.currentYear + 1):
  for year in range(1991, self.currentYear + 1):
   complaintspage = download.getFile("http://www.nzlii.org/nz/cases/NZASA/" + str(year) + "/", download.getFilename(self.source, year, "pages", "complaints"))
   if complaintspage:
    complaints = re.finditer(ur'<a href="\.\.\/' + str(year) + '\/(?P<id>[0-9]{1,5})\.html" class="make-database">(?P<title>.*?) \[(?P<year>[1-2][0-9]{3})\] NZASA (?P<id2>[0-9]{1,5}) \((?P<date>.*?)\)<\/a>', complaintspage, flags=re.DOTALL) # 
    for complaintmatch in complaints:
     complaint = complaintmatch.groupdict()
     self.complaint = {'id': str(year)[2:] + complaint['id'].zfill(3), 'year': year, 'title': complaint['title'].replace(" Chairman's Ruling","")}
     self.complaint['url_page'] = "http://www.nzlii.org/nz/cases/NZASA/" + str(year) + "/" + complaint['id'] + '.html'
     self.complaint['source'] = self.source
     if year < 2000:
      doctype = ".pdf"
     else:
      doctype = ".html"
     self.complaint['url_doc'] = "http://www.nzlii.org/nz/cases/NZASA/" + str(year) + "/" + complaint["id"] + doctype
     self.complaint['cache_doc'] = download.getFilename(self.source, year, "docs", complaint["id"], doctype)
     complaintpage = download.getFile(self.complaint['url_doc'], self.complaint['cache_doc'])
     self.convertDoc()
     print self.complaint
     #return

 def getWWW(self):
  self.source = 'www'
  for year in range(2015, self.currentYear + 1):
   for product, code in {'Advocacy': 1, 'Alcohol': 2, 'Apparel': 3, 'Electronic and ICT': 4, 'Entertainment': 5, 'Finance': 6, 'Food and Beverage': 7, 'Health and Beauty': 8, 'Household Goods': 9, 'Services': 10, 'Telecommunications': 11, 'Vehicles / Transportation': 12, 'Other': 13}.iteritems():
    complaints = download.getFile("http://www.asa.co.nz/backend/documentsearch.php?year=" + str(year) + "&product=" + str(code), download.getFilename(self.source, str(year), "pages", product, '.json'), year == self.currentYear)
    if complaints:
     for complaint in json.loads(complaints):
      year = int(complaint["date"][:4])
      media = complaint['title'].split(', ')[-1]
      self.complaint = {'id': complaint['filename'][:5], 'year': year, 'product': product, 'meeting': complaint['date'], 'media': media, 'title': complaint['title'][17:-(len(media)+2)], 'decision': complaint['summary'].split(': ')[-1]}
      self.complaint['source'] = self.source
      self.complaint['url_doc'] = complaint['url'].replace("//", "/")
      self.complaint['cache_doc'] = download.getFilename(self.source, year, "docs", complaint['filename'], "")
      download.getFile(self.complaint['url_doc'], self.complaint['cache_doc'])
      self.convertDoc()
      #self.processDoc()
      #self.db.insertComplaint(self.complaint)
      ##command2 = [unoconv, "-f", "html", "-o", os.path.join(os.getcwd(), htmlfilename), os.path.join(os.getcwd(), docfilename)]
      print self.complaint
      #return
 
 def getOld(self):
  self.source = 'old'
  for year in range(2006, 2015):
   self.db.cursor.execute("SELECT * FROM products")
   for product in self.db.cursor.fetchall():
    content = download.getFile("http://old.asa.co.nz/srch_product.php", download.getFilename(self.source, year, "product", product["name"]), year == self.currentYear, {'product_id': product["id"], 'year': year})
    if content:
     complaints = re.finditer(ur"<tr align='left' valign='top'><td><b><p><a href='display.php\?ascb_number=(?P<id>[0-9]{5})'>[0-1][0-9]/[0-9]{3}(?:.*?)</b> - (?P<title>.*?)</a></p></td><td><p>(?P<decision>.*?)</p></td><td><a href='(?P<url_doc>.*?)'><p>Full Decision</p></a></td></tr>", content, flags=re.DOTALL)
     for complaintmatch in complaints:
      complaint = complaintmatch.groupdict()
      self.complaint = complaintmatch.groupdict()
      self.complaint['source'] = self.source
      self.complaint['url_page'] = "http://old.asa.co.nz/display.php?ascb_number=" + complaint['id']
      self.complaint["year"] = year
      self.complaint["url_doc"] = "http://old.asa.co.nz/" + complaint["url_doc"]
      self.complaint["product"] = product["name"]
      self.getOldComplaint()
  for id in self.db.getMissing(self.currentYear):
   self.complaint = {'id': id, 'year': year, 'product': None}
   self.getOldComplaint()

 def getOldComplaint(self):
  if "id" in self.complaint and self.complaint["id"]:
  #self.complaint["year"] = int("20" + self.complaint["id"][0:2])
   docType = self.complaint["year"] >= 2007 and "doc" or "rtf"
   #download.getFile("/".join(("http://203.152.114.11/decisions", self.complaint["id"][0:2], self.complaint["id"] + "." + docType)), docfilename, returnfile = False)
   self.complaint['url_doc'] = "http://old.asa.co.nz/decision_file.php?ascbnumber=" + self.complaint["id"]
   self.complaint['cache_doc'] = download.getFilename(self.source, self.complaint["year"], "docs", self.complaint["id"], "." + docType)
   download.getFile(self.complaint['url_doc'], self.complaint['cache_doc'], returnfile = False)
   #if docType == "doc":
   # self.__getPage(download.getFile("http://old.asa.co.nz/display.php?ascb_number=" + self.complaint["id"], download.getFilename(self.source, self.complaint["year"], 'pages', self.complaint["id"]), self.refresh))
   self.convertDoc()
   #self.processDoc()
   print self.complaint
   #return
  else:
   print "No ID"
  if "decision" in self.complaint and self.complaint["decision"] != "" and "url_doc" in self.complaint:
   pass
   #self.db.insertComplaint(self.complaint)

 def __getPage(self, contents):
  if contents:
   # <h1>[0-1][0-9]/[0-9]{3} - (.*?)</h1>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p><b>Media</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p>\(<a href='(.*?)'>Full Decision</a>\)</p>
   # data = re.search(ur"<h1>[0-1][0-9]/[0-9]{3} - (?P<title>.*?)</h1>(?:.*?)<p><b>(?P<appeal>.*?)</b></p>(?:.*?)<p><b>Media</b><br>(?P<media>.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(?P<clauses>.*?)(?:<br>)*</p>(?:.*?)<p><b>(?P<decision>.*?)</b></p>(?:.*?)<p>\(<a href='(?P<docurl>.*?)' target='_blank'>Full Decision</a>\)</p>", contents, flags=re.DOTALL)
   # <h1>[0-1][0-9]/[0-9]{3} - (.*?)</h1>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p><b>Media</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p>\(<a href='(.*?)'>Full Decision</a>\)</p>
   data = re.search(ur"<h1>[0-1][0-9]/[0-9]{3} - (?P<title>.*?)</h1>(?:.*?)<p><b>(?P<appeal>.*?)</b></p>(?:.*?)<p><b>Media</b><br>(?P<media>.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(?P<clauses>.*?)(?:<br>)*</p>(?:.*?)<p><b>(?P<decision>.*?)</b></p>(?:.*?)<p>\(<a href='(?P<docurl>.*?)'>Full Decision</a>\)</p>", contents, flags=re.DOTALL)
   self.update(data.groupdict())
   #del self.complaint["docurl"]
   clauses = filter(None, self.complaint["clauses"].split("<br>"))
   self.complaint["clauses"] = list()
   for clause in clauses:
    self.complaint["clauses"].append(clause.split(", "))
    # ("SELECT id FROM clauses WHERE code_id = (SELECT id FROM codes WHERE name = %s) AND name = %s", clause)
   self.complaint["media"] = filter(None, list(set(self.complaint["media"].split("<br>"))))
   if self.complaint["appeal"]:
    self.complaint["appealsuccess"] = None
    if "Allowed" in self.complaint["appeal"]:
     self.complaint["appealsuccess"] = 1
    elif "Declined" in self.complaint["appeal"] or "Dismissed" in self.complaint["appeal"]:
     self.complaint["appealsuccess"] = 0
    self.complaint["appeal"] = self.complaint["appeal"][8:10] + self.complaint["appeal"][11:14]
   else:
    del self.complaint["appeal"]
   return True
  return False  
 
 def cleanNZLII(self, text):
  return " ".join(parser.unescape(text).replace("\n", " ").replace("<br>", "").replace("Chai rm an’ s Rul i ng", "Chairman's Ruling").split())

 def convertDoc(self):
  self.complaint["doc"] = dict()
  docType = os.path.splitext(self.complaint['cache_doc'])[1]
  self.complaint["cache_text"] = download.getFilename(self.source, self.complaint["year"], "text", self.complaint["id"], ".txt")
  self.complaint["cache_html"] = download.getFilename(self.source, self.complaint["year"], "html", self.complaint["id"], ".html")
  localtext = download.getAbsolutePath(self.complaint["cache_text"])
  localhtml = download.getAbsolutePath(self.complaint["cache_html"])
  self.complaint["doc"]["date"], self.complaint["doc"]["size"] = download.getFileDetails(self.complaint['cache_doc'])
  localdoc = download.getAbsolutePath(self.complaint['cache_doc'])
  if os.path.exists(localdoc):
   if os.path.exists(localhtml) and (self.complaint["doc"]["date"] == os.path.getmtime(localhtml) or self.quick):
    self.complaint["html"] = download.loadResource(localhtml)
   else:
    if (docType == '.html'):
     self.complaint["doc"]["html"] = cleanNZLII(re.search('<!--make_database header end-->(.*?)<!--sino noindex-->', download.loadResource(self.complaint["cache_doc"]), flags=re.DOTALL).group(1))
    else:
     if (docType == '.pdf'):
      #command = [pdftohtml, "-noframes", "-nomerge", localdoc, localhtml] # -stdout
      command = [pdftohtml, "-noframes", "-nomerge", "-stdout", localdoc] 
     else:
      #command = [unoconv, "-f", "html", "-o", localhtml, localdoc] # --stdout
      command = [unoconv, "-f", "html", "--stdout", localdoc]
     print "Saving: " + localhtml
     try:
      html = subprocess.check_output(command)
     except:
      print "Could not convert " + localdoc
     else:
      self.complaint["doc"]["html"] = parser.unescape(re.search('<body(?:.*?)>(.*?)</body>', html.decode("UTF-8"), flags=re.DOTALL).group(1))
    if "html" in self.complaint["doc"]:
     download.saveResourcePost(localhtml, self.complaint["doc"]["html"])
   if os.path.exists(localtext) and self.complaint["doc"]["date"] == os.path.getmtime(localtext):
    self.complaint["doc"]['text'] = download.loadResource(self.complaint["cache_text"])
   else:
    if docType == ".doc":
     command = [antiword, "-w", "0", "-m", "8859-1.txt", localdoc]
    elif docType == ".rtf":
     command = [unrtf, "--text", localdoc]
    elif docType == ".pdf":
     command = [pdftotext, "-layout", localdoc, '-']
    elif docType == ".html":
     command = ['w3m', '-dump', "-cols", "99999", "-I", "iso-8859-1", "-O", "UTF-8", "-s", localhtml]
    try:
     self.complaint["doc"]["text"] = subprocess.check_output(command).decode('unicode_escape')
    except Exception, e:
     print "Failed to convert doc " + localdoc + ": " + str(e)
    else:
     if docType == ".rtf":
      self.complaint["doc"]["text"] = self.complaint["doc"]["text"].split("-----------------", 1)[1]
     download.saveResourcePost(localtext, self.complaint["doc"]["text"], (time.time(), self.complaint["doc"]["date"]))
  if not "doc" in self.complaint: # and self.complaint["id"] != "10283":
   print "No Doc"

 def processDoc(self):
  if "text" in self.complaint["doc"] and self.complaint["doc"]["text"]:
   self.complaint["doc"]["words"] = len(self.complaint["doc"]["text"].split())
   # \|(?:COMPLAINANTS|COMPLAINANT)(.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(?:.*?)\|(.*?)\|(?:.*?)(?:DATE|DATE OF MEETING)(?:.*?)\|(.*?)\|
   result = re.search(ur"\|(?:COMPLAINANTS|COMPLAINANT)(?P<complainants>.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(?:.*?)\|(?P<companies>.*?)\|(?:.*?)(?:DATE|DATE OF MEETING)(?:.*?)\|(?P<meeting>.*?)\|", self.complaint["doc"]["text"], flags=re.DOTALL)
   if result:
    self.complaint["complainants"] = " ".join(result.group('complainants').replace("|", "").strip().split())
    if result.group(2):
     self.complaint["companies"] = " ".join(result.group('companies').replace("|", "").strip().split())
    try:
     self.complaint["meeting"] = time.strftime('%Y-%m-%d', time.strptime(result.group('meeting').replace("|", "").strip(), "%d %B %Y"))
     #if self.complaint["meeting"] > :
     # self.complaint["meeting"] = self.complaint["meeting"] - 90 years
    except:
     try:
      # \|(?:COMPLAINANTS|COMPLAINANT)(.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(.*?)\|(?:ADVERTISEMENT|ITEM|DATE OF MEETING)
	  result = re.search(ur"\|(?:COMPLAINANTS|COMPLAINANT)(?P<complainants>.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(?P<companies>.*?)\|(?:ADVERTISEMENT|ITEM|DATE OF MEETING)", self.complaint["doc"], flags=re.DOTALL)
     except:
      pass
   else:
    # ([0-9]{1,2}(?:st|nd|rd|th){0,1}[ ]*(January|February|March|April|May|may|June|July|August|September|October|November|December)[ ]*20[0-9]{2})(?:.*?)(?:Complainants|Complainant/Applicant|Complainant)(?::){0,1}(.*?)(?:Advertisement/Item|Advertisements|Advertisement|Parade|Matter|Advertiser / Applicant|Item|Advertiser|Image|Material|Promotion|Editorial|Name|Magazine cover|Publisher|Editorial|Editoral|Content|Complaint)(?:.*?)(?::){0,1}(.*?)(?:\n\n|Limited Appellant:|Applicant:|\nComplaint:)
    result = re.search(ur"(?P<meeting>[0-9]{1,2}(?:st|nd|rd|th){0,1}[ ]*(January|February|March|April|May|may|June|July|August|September|October|November|December)[ ]*20[0-9]{2})(?:.*?)(?:Complainants|Complainant/Applicant|Complainant)(?::){0,1}(?P<complainants>.*?)(?:Advertisement/Item|Advertisements|Advertisement|Parade|Matter|Advertiser / Applicant|Item|Advertiser|Image|Material|Promotion|Editorial|Name|Magazine cover|Publisher|Editorial|Editoral|Content|Complaint)(?:.*?)(?::){0,1}(?P<companies>.*?)(?:\n\n|Limited Appellant:|Applicant:|\nComplaint:)", self.complaint["doc"]["text"], flags=re.DOTALL)
    if result:
     try:
      self.complaint["meeting"] = time.strftime('%Y-%m-%d', time.strptime(result.group('meeting'), "%d %B %Y"))
     except:
      pass
     self.complaint["complainants"] = " ".join(result.group('complainants').strip().split())
     if result.group(2):
      self.complaint["companies"] = " ".join(result.group('companies').strip().split())
    else:
     "No result"
   if self.complaint["complainants"]:
    if re.match(ur"[A-Z] [A-Z][a-z]+$", self.complaint["complainants"]):
     self.complaint["complainants"] = ". ".join(self.complaint["complainants"].split())
   if not "decision" in self.complaint:
    #result = re.search(ur"(Acting |Deputy |)(Decision|Chairman's Ruling):(.*?)\n", self.complaint["doc"], flags=re.DOTALL)
    result = re.search(ur"(Acting |)(Chairman('|)s Ru(li|Ii|lii|lil)ng|Decision|Deputy Chairman|(Panel's |)Ruling|Complaint Adjourned): (.*?)\n", self.complaint["doc"], flags=re.DOTALL)
    self.complaint["title"] = None
    self.complaint["product"] = None
    if result:
     self.complaint["decision"] = " ".join(result.group(0).strip(" .").split()).replace("'", "").replace("( ", "(").replace("Up Up", "Up")
    else:
     self.complaint["decision"] = None
  else:
   print "No doc to process"
   self.complaint["doc"] = None