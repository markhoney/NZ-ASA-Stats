# -*- coding: utf-8 -*-

import os, re, subprocess, requests
from datetime import date
import time

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
 
debug = True

class complaints:
 def __init__(self, refresh = False, quick = False):
  self.firstYear = 2008
  #self.firstYear = 2015
  self.currentYear = date.today().year
  self.db = mysql()
  #self.db = mysql(True)
  self.refresh = refresh
  self.quick = quick
  self.complaint = complaint(self.refresh, self.quick)

 def refreshYear(self, year):
  if self.quick:
   return False
  if year > self.currentYear - 2:
   return True
  return self.refresh

 def getIncomplete(self):
  pass

 def getAll(self):
  #self.getOld()
  self.getNew()
  self.getOthers()  
 
 def getOld(self):
  for year in range(2000, 2007):
   self.complaint.refresh = self.refreshYear(year)
   missing = 0
   id = 0
   while missing < 3:
    id += 1
    self.complaint.complaint = {"id": str(year)[2:4] + str(id).zfill(3), "year": year}
    self.complaint.get()
    if self.complaint.complaint and "decision" in self.complaint.complaint and self.complaint.complaint["decision"] != "":
     self.db.insertComplaint(self.complaint.complaint)
     missing = 0
    else:
     missing += 1

 def getNZLII(self):
  for year in range(1991, 1999):
   self.complaint.refresh = self.refreshYear(year)
   missing = 0
   id = 0
   while missing < 3:
    id += 1
    self.complaint.complaint = {"id": str(year)[2:4] + str(id).zfill(3), "year": year}
    self.complaint.get()
    if self.complaint.complaint and "decision" in self.complaint.complaint and self.complaint.complaint["decision"] != "":
     self.db.insertComplaint(self.complaint.complaint)
     missing = 0
    else:
     missing += 1

 def getNew(self):
  for year in range(self.firstYear, self.currentYear + 1):
   self.getYear(year)

 def getYear(self, year):
  self.db.cursor.execute("SELECT * FROM products")
  for value in self.db.cursor.fetchall():
   self.getComplaints(year, value["id"], value["name"])

 def getComplaints(self, year, productid, product):
  content = download.getFile("http://old.asa.co.nz/srch_product.php", download.getFilename(year, "product", product), self.refreshYear(year), {'product_id': productid, 'year': year})
  if content:
   complaints = re.finditer(ur"<tr align='left' valign='top'><td><b><p><a href='display.php\?ascb_number=(?P<id>[0-9]{5})'>[0-1][0-9]/[0-9]{3}(?:.*?)</b> - (?P<advert>.*?)</a></p></td><td><p>(?P<decision>.*?)</p></td><td><a href='(?P<docurl>.*?)'><p>Full Decision</p></a></td></tr>", content, flags=re.DOTALL)
   for complaintmatch in complaints:
    self.complaint.complaint = complaintmatch.groupdict()
    self.complaint.complaint["product"] = product
    self.getComplaint(self.refreshYear(year))

 def getNewComplaints():
  # "id":"33","title":"Complaint 15\/001 Better Wellington, Radio","summary":"Complainant: F. Smith was offended by the way the council was represented in the advertisement. The relevant provisions were Basic Principle 4 and Rules 5 and 11 of the Code of Ethics.\r\n\r\nRuling Date: 16 January 2015\r\nOutcome: No Grounds to Proceed","date":"2015-07-06","filename":"15001.pdf","url":"http:\/\/www.asa.co.nz\/\/backend\/documents\/2015\/07\/06\/15001.pdf"
  for complaint in download.getJSON("http://www.asa.co.nz/backend/documentsearch.php"):
   self.complaint.complaint = {'id': complaint['filename'][:5], 'product': None, 'meetingdate': complaint['date'], 'title': complaint['title'][17:], 'media': complaint['title'].split(', ')[-1], 'decision': complaint['summary'].split(': ')[-1], 'doc': complaint['url']}

 def getComplaint(self, refresh):
  self.complaint.refresh = refresh
  self.complaint.get()
  if "decision" in self.complaint.complaint and self.complaint.complaint["decision"] != "" and "doc" in self.complaint.complaint:
   self.db.insertComplaint(self.complaint.complaint)

 def getOthers(self):
  for id in self.db.getMissing(self.currentYear):
   self.complaint.complaint = {'id': id, 'product': None}
   self.getComplaint(not self.quick)

 def stats(self):
  # Company Page
  # Complainant Page
  # Year Page
  # Media Page
  # Top 10 complainants
  print self.db.cursor.execute("SELECT COUNT(*) AS complaints, complainant FROM complaints WHERE complainant IS NOT NULL AND complainant != '[A. Complainant]' GROUP BY complainant ORDER BY COUNT(*) DESC LIMIT 10")
  # Top 10 successful complainants
  print self.db.cursor.execute("SELECT COUNT(*) AS complaints, complainant FROM complaints WHERE complainant IS NOT NULL AND complainant != '[A. Complainant]' AND success = 1 GROUP BY complainant ORDER BY COUNT(*) DESC LIMIT 10")
  # Most complained about 10 companies
  print self.db.cursor.execute("SELECT COUNT(*) AS complaints, corp FROM complaints WHERE corp IS NOT NULL GROUP BY corp ORDER BY COUNT(*) DESC LIMIT 10")
  # Worst 10 companies
  print self.db.cursor.execute("SELECT COUNT(*) AS complaints, corp FROM complaints WHERE corp IS NOT NULL AND success = 1 GROUP BY corp ORDER BY COUNT(*) DESC LIMIT 10")
  # Successful complaints per year
  print self.db.cursor.execute("SELECT COUNT(*) AS complaints, year FROM complaints WHERE success = 1 GROUP BY year ORDER BY year ASC")
  

class complaint:
 def __init__(self, refresh = False, quick = False):
  self.refresh = refresh
  self.quick = quick
  self.complaint = dict()
  
 def __getPage(self, contents):
  if contents:
  # <h1>[0-1][0-9]/[0-9]{3} - (.*?)</h1>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p><b>Media</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p>\(<a href='(.*?)'>Full Decision</a>\)</p>
  #   data = re.search(ur"<h1>[0-1][0-9]/[0-9]{3} - (?P<advert>.*?)</h1>(?:.*?)<p><b>(?P<appeal>.*?)</b></p>(?:.*?)<p><b>Media</b><br>(?P<media>.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(?P<clauses>.*?)(?:<br>)*</p>(?:.*?)<p><b>(?P<decision>.*?)</b></p>(?:.*?)<p>\(<a href='(?P<docurl>.*?)' target='_blank'>Full Decision</a>\)</p>", contents, flags=re.DOTALL)
   # <h1>[0-1][0-9]/[0-9]{3} - (.*?)</h1>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p><b>Media</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(.*?)(?:<br>)*</p>(?:.*?)<p><b>(.*?)</b></p>(?:.*?)<p>\(<a href='(.*?)'>Full Decision</a>\)</p>
   data = re.search(ur"<h1>[0-1][0-9]/[0-9]{3} - (?P<advert>.*?)</h1>(?:.*?)<p><b>(?P<appeal>.*?)</b></p>(?:.*?)<p><b>Media</b><br>(?P<media>.*?)(?:<br>)*</p>(?:.*?)<p><b>Codes</b><br>(?P<clauses>.*?)(?:<br>)*</p>(?:.*?)<p><b>(?P<decision>.*?)</b></p>(?:.*?)<p>\(<a href='(?P<docurl>.*?)'>Full Decision</a>\)</p>", contents, flags=re.DOTALL)
   self.complaint.update(data.groupdict())
   del self.complaint["docurl"]
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

 def docType(self, year):
  if year >= 2007:
   return "doc"
  return "rtf"

 def get(self):
  if "id" in self.complaint and self.complaint["id"]:
   self.complaint["year"] = int("20" + self.complaint["id"][0:2])
   docType = self.docType(self.complaint["year"])
   docfilename = download.getFilename(self.complaint["year"], "docs", self.complaint["id"], "." + docType)
   textfilename = download.getFilename(self.complaint["year"], "text", self.complaint["id"], ".txt")
   htmlfilename = download.getFilename(self.complaint["year"], "html", self.complaint["id"], ".html")
   #download.getFile("/".join(("http://203.152.114.11/decisions", self.complaint["id"][0:2], self.complaint["id"] + "." + docType)), docfilename, self.refresh, returnfile = False)
   download.getFile("http://old.asa.co.nz/decision_file.php?ascbnumber=" + self.complaint["id"], docfilename, self.refresh, returnfile = False)
   if docType == "doc":
    self.__getPage(download.getFile("http://old.asa.co.nz/display.php?ascb_number=" + self.complaint["id"], download.getFilename(self.complaint["year"], 'pages', self.complaint["id"]), self.refresh))
   for field in ["docdate", "docsize", "docwords", "complainants", "companies", "meetingdate"]:
    self.complaint[field] = None
   self.complaint["docdate"], self.complaint["docsize"] = download.getFileDetails(docfilename)
   if os.path.exists(docfilename):
    if os.path.exists(textfilename) and (self.complaint["docdate"] == os.path.getmtime(textfilename) or self.quick):
     self.complaint["doc"] = download.loadResource(textfilename)
    else:    
     if docType == "doc":
      command = [antiword, "-w", "0", "-m", "8859-1.txt"]
     else:
      command = [unrtf, "--text"]
     command.append(os.path.join(os.getcwd(), docfilename))
     try:
      self.complaint["doc"] = subprocess.check_output(command).decode('unicode_escape')
     except Exception, e:
      print "Failed to convert doc " + os.path.join(os.getcwd(), docfilename) + ": " + str(e)
     else:
      if docType == "rtf":
       self.complaint["doc"] = self.complaint["doc"].split("-----------------", 1)[1]
      download.saveResourcePost(textfilename, self.complaint["doc"], (time.time(), self.complaint["docdate"]))
    if os.path.exists(htmlfilename) and (self.complaint["docdate"] == os.path.getmtime(htmlfilename) or self.quick):
     self.complaint["html"] = download.loadResource(htmlfilename)
    else:    
     command2 = [unoconv, "-f", "html", "-o", os.path.join(os.getcwd(), htmlfilename), os.path.join(os.getcwd(), docfilename)]
     print "Saving: " + htmlfilename
     subprocess.check_output(command2)
    self.complaint["html"] = re.search('<body(?:.*?)>(.*?)</body>', download.loadResource(htmlfilename), flags=re.DOTALL).group(1)
   if "doc" in self.complaint and self.complaint["id"] != "10283":
    self.processDoc(docfilename)
   else:
    print "No Doc"
  else:
   print "No ID"

 def processDoc(self, docfilename):
  if "doc" in self.complaint and self.complaint["doc"]:
   self.complaint["docwords"] = len(self.complaint["doc"].split())
   # \|(?:COMPLAINANTS|COMPLAINANT)(.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(?:.*?)\|(.*?)\|(?:.*?)(?:DATE|DATE OF MEETING)(?:.*?)\|(.*?)\|
   result = re.search(ur"\|(?:COMPLAINANTS|COMPLAINANT)(?P<complainants>.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(?:.*?)\|(?P<companies>.*?)\|(?:.*?)(?:DATE|DATE OF MEETING)(?:.*?)\|(?P<meetingdate>.*?)\|", self.complaint["doc"], flags=re.DOTALL)
   if result:
    self.complaint["complainants"] = " ".join(result.group('complainants').replace("|", "").strip().split())
    if result.group(2):
     self.complaint["companies"] = " ".join(result.group('companies').replace("|", "").strip().split())
    try:
     self.complaint["meetingdate"] = time.strftime('%Y-%m-%d', time.strptime(result.group('meetingdate').replace("|", "").strip(), "%d %B %Y"))
     #if self.complaint["meetingdate"] > : 
     # self.complaint["meetingdate"] = self.complaint["meetingdate"] - 90 years
    except:
     try:
      # \|(?:COMPLAINANTS|COMPLAINANT)(.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(.*?)\|(?:ADVERTISEMENT|ITEM|DATE OF MEETING)
	  result = re.search(ur"\|(?:COMPLAINANTS|COMPLAINANT)(?P<complainants>.*?)\|(?:ADVERTISER|SOURCE|ADVERTISEMENT)(?P<companies>.*?)\|(?:ADVERTISEMENT|ITEM|DATE OF MEETING)", self.complaint["doc"], flags=re.DOTALL)
     except:
      pass
   else:
    # ([0-9]{1,2}(?:st|nd|rd|th){0,1}[ ]*(January|February|March|April|May|may|June|July|August|September|October|November|December)[ ]*20[0-9]{2})(?:.*?)(?:Complainants|Complainant/Applicant|Complainant)(?::){0,1}(.*?)(?:Advertisement/Item|Advertisements|Advertisement|Parade|Matter|Advertiser / Applicant|Item|Advertiser|Image|Material|Promotion|Editorial|Name|Magazine cover|Publisher|Editorial|Editoral|Content|Complaint)(?:.*?)(?::){0,1}(.*?)(?:\n\n|Limited Appellant:|Applicant:|\nComplaint:)
    result = re.search(ur"(?P<meetingdate>[0-9]{1,2}(?:st|nd|rd|th){0,1}[ ]*(January|February|March|April|May|may|June|July|August|September|October|November|December)[ ]*20[0-9]{2})(?:.*?)(?:Complainants|Complainant/Applicant|Complainant)(?::){0,1}(?P<complainants>.*?)(?:Advertisement/Item|Advertisements|Advertisement|Parade|Matter|Advertiser / Applicant|Item|Advertiser|Image|Material|Promotion|Editorial|Name|Magazine cover|Publisher|Editorial|Editoral|Content|Complaint)(?:.*?)(?::){0,1}(?P<companies>.*?)(?:\n\n|Limited Appellant:|Applicant:|\nComplaint:)", self.complaint["doc"], flags=re.DOTALL)
    if result:
     try:
      self.complaint["meetingdate"] = time.strftime('%Y-%m-%d', time.strptime(result.group('meetingdate'), "%d %B %Y"))
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
    self.complaint["advert"] = None
    self.complaint["product"] = None
    if result:
     self.complaint["decision"] = " ".join(result.group(0).strip(" .").split()).replace("'", "").replace("( ", "(").replace("Up Up", "Up")
    else:
     self.complaint["decision"] = None
  else:
   print "No doc to process"
   self.complaint["doc"] = None