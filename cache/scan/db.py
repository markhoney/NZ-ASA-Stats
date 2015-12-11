# -*- coding: utf-8 -*-

import os, re
from datetime import datetime, date
import download

class sqlite:
 def __init__(self):
  import sqlite3
  self.dbFile = "complaints.sqlite"
  self.open()

 def open(self):
  dbexists = os.path.exists(self.dbFile)
  self.conn = sqlite3.connect(self.dbFile, isolation_level = None)
  self.conn.row_factory = sqlite3.Row
  self.cursor = self.conn.cursor()
  if not dbexists:
   self.__create()

class mysql:
 def __init__(self, drop = False, empty = False):
  #CREATE DATABASE ASA;
  import MySQLdb
  import config
  self.connection = MySQLdb.connect(host = config.host, user = config.user, passwd = config.passwd, db = config.name, charset = "utf8", use_unicode = True)
  self.connection.autocommit(True)
  self.cursor = self.connection.cursor(MySQLdb.cursors.DictCursor)
  self.year = date.today().year
  self.tables = ["complainants", "decisions", "products", "media", "codes", "clauses", "companies", "companiesdetails", "complainants_translations", "complaints", "docs", "appeals", "complaints_complainants", "complaints_media", "complaints_clauses", "complaints_companies"] # , "badges"
  #self.views = ["complaints"]
  self.cursor.execute("SHOW TABLES LIKE 'complaints'")
  if drop or not self.cursor.fetchone():
   self.__create()
  elif empty:
   self.__empty()
   self.__fill()
   
 def close(self):
  self.connection.close()

 def __drop(self):
  self.cursor.execute("SET foreign_key_checks = 0")
  for table in self.tables:
   self.cursor.execute("DROP TABLE IF EXISTS " + table)
  #for view in self.views:
  # self.cursor.execute("DROP VIEW IF EXISTS " + view)
  self.cursor.execute("SET foreign_key_checks = 1")

 def __create(self):
  self.__drop()
  self.cursor.execute("ALTER DATABASE CHARACTER SET utf8")
  #self.cursor.execute("CREATE TABLE settings (name VARCHAR(32) PRIMARY KEY, value VARCHAR(128) NOT NULL)")
  self.cursor.execute("CREATE TABLE complainants (id SMALLINT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE)")
  self.cursor.execute("CREATE TABLE decisions (id SMALLINT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE, arbiter VARCHAR(8), ruling VARCHAR(64), success BOOLEAN)")
  self.cursor.execute("CREATE TABLE products (id SMALLINT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE)")
  self.cursor.execute("CREATE TABLE media (id SMALLINT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE)")
  self.cursor.execute("CREATE TABLE codes (id SMALLINT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE, url VARCHAR(128))")
  self.cursor.execute("CREATE TABLE clauses (id SMALLINT PRIMARY KEY, codes_id SMALLINT NOT NULL, name VARCHAR(128) NOT NULL, UNIQUE (codes_id, name), FOREIGN KEY (codes_id) REFERENCES codes(id))")
  self.cursor.execute("CREATE TABLE companies (id SMALLINT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(128) NOT NULL, companies_id SMALLINT, UNIQUE (name, companies_id), FOREIGN KEY (companies_id) REFERENCES companies(id))")
  #self.cursor.execute("CREATE TABLE companies_details (companies_id SMALLINT PRIMARY KEY, url VARCHAR(256), number INT UNIQUE, FOREIGN KEY (companies_id) REFERENCES companies(id))")
  self.cursor.execute("CREATE TABLE complainants_translations (name VARCHAR(128), complainants_id SMALLINT, companies_id SMALLINT, UNIQUE (name, complainants_id, companies_id), FOREIGN KEY (complainants_id) REFERENCES complainants(id), FOREIGN KEY (companies_id) REFERENCES companies(id))")
  self.cursor.execute("CREATE TABLE complaints (id CHAR(5) PRIMARY KEY, year YEAR(4) NOT NULL, meetingdate DATE, complainant VARCHAR(128), company VARCHAR(128), advert VARCHAR(128), products_id SMALLINT, decisions_id SMALLINT, docwords MEDIUMINT, FOREIGN KEY (products_id) REFERENCES products(id), FOREIGN KEY (decisions_id) REFERENCES decisions(id))")
  self.cursor.execute("CREATE TABLE docs (complaints_id CHAR(5) PRIMARY KEY, date TIMESTAMP, size INT, contents MEDIUMTEXT, FOREIGN KEY (complaints_id) REFERENCES complaints(id))")
  self.cursor.execute("CREATE TABLE appeals (id CHAR(5) PRIMARY KEY, complaints_id CHAR(5) NOT NULL UNIQUE, success BOOLEAN NOT NULL, decision VARCHAR(32), FOREIGN KEY (complaints_id) REFERENCES complaints(id))")
  self.cursor.execute("CREATE TABLE complaints_complainants (complaints_id CHAR(5) NOT NULL, complainants_id SMALLINT, companies_id SMALLINT, complainants_id_companies_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, complainants_id_companies_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (complainants_id) REFERENCES complainants(id), FOREIGN KEY (companies_id) REFERENCES companies(id))")
  self.cursor.execute("CREATE TABLE complaints_media (complaints_id CHAR(5) NOT NULL, media_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, media_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (media_id) REFERENCES media(id))")
  self.cursor.execute("CREATE TABLE complaints_clauses (complaints_id CHAR(5) NOT NULL, clauses_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, clauses_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (clauses_id) REFERENCES clauses(id))")
  self.cursor.execute("CREATE TABLE complaints_companies (complaints_id CHAR(5) NOT NULL, companies_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, companies_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (companies_id) REFERENCES companies(id))")
  #self.cursor.execute("CREATE TABLE badges (name VARCHAR(16) PRIMARY KEY, description VARCHAR(128) NOT NULL, category VARCHAR(16) NOT NULL, value SMALLINT, image VARCHAR(128) NOT NULL, points SMALLINT NOT NULL)")
  self.__fill()

 def __fill(self):
  decisions = list()
  decisions.append(("Chairmans Ruling: Complaint Resolved", "Chairman", "Resolved", 1))
  decisions.append(("Chairmans Ruling: Complaint Settled", "Chairman", "Settled", 1))
  decisions.append(("Chairmans Ruling: Complaint Withdrawn", "Chairman", "Withdrawn", None))
  decisions.append(("Chairmans Ruling: No Grounds To Proceed", "Chairman", "No Grounds To Proceed", 0))
  decisions.append(("Chairmans Ruling: No Jurisdiction", "Chairman", "No Jurisdiction", 0))
  decisions.append(("Chairmans Ruling: Settled - Advertiser Error", "Chairman", "Settled - Advertiser Error", 1))
  decisions.append(("Chairmans Ruling: Settled - Media Error", "Chairman", "Settled - Media Error", 1))
  decisions.append(("Chairmans Ruling: Settled - Retailer Error", "Chairman", "Settled - Retailer Error", 1))
  decisions.append(("Decision: Complaint No Determination", "Board", "No Determination", None))
  decisions.append(("Decision: Complaint Not Upheld", "Board", "Not Upheld", 0))
  decisions.append(("Decision: Complaint Resolved", "Board", "Resolved", 1))
  decisions.append(("Decision: Complaint Settled", "Board", "Settled", 1))
  decisions.append(("Decision: Complaint Upheld", "Board", "Upheld", 1))
  decisions.append(("Decision: No Grounds To Proceed", "Board", "No Grounds To Proceed", 0))
  decisions.append(("Decision: No Jurisdiction", "Board", "No Jurisdiction", 0))
  decisions.append(("Decision: Complaint Adjourned (Sine Die)", "Board", "Adjourned (Sine Die)", None))
  decisions.append(("See notes", None, "See Notes", None))
  decisions.append(("Pre-ASCB", None, "Pending", None))
  decisions.append(("Pre-Accepted", None, "Pending", None))
  decisions.append(("Chairmans Ruling: Complaint Not Accepted", "Chairman", "Not Accepted", 0))
  decisions.append(("Chairmans Ruling: Complaints Not Accepted", "Chairman", "Not Accepted", 0))
  decisions.append(("Chairman's Ruling: Complaint Settled", "Chairman", "Settled", 1))
  decisions.append(("Ruling: Complaint Settled", "Chairman", "Settled", 1))
  decisions.append(("Decision: Upheld (in part)", "Board", "Upheld (in part)", 1))
  decisions.append(("Decision: Complaint Upheld (in part)", "Board", "Upheld (in part)", 1))
  decisions.append(("Decision: Complaint Upheld In Part", "Board", "Upheld (in part)", 1))
  decisions.append(("Decision: Complaint Upheld in part and Settled in part", "Board", "Upheld (in part) and Settled (in part)", 1))
  decisions.append(("Decision: Complaint Upheld (in part) Settled (in part)", "Board", "Upheld (in part) and Settled (in part)", 1))
  decisions.append(("Decision: Complaint Settled (in part) Not Upheld (in part) Upheld (in part)", "Board", "Upheld (in part), Settled (in part) and Not Upheld (in part)", 1))
  decisions.append(("Decision: Complaint Settled (in part)", "Board", "Settled (in part)", 1))
  decisions.append(("Decision: Complaint Not Accepted", "Board", "Not Accepted", 0))
  decisions.append(("Decision: Complaint Settled (in part) / Not Upheld (in part)", "Board", "Settled (in part) and Not Upheld (in part)", 1))
  decisions.append(("Panel's Ruling: Complaint Settled", "Board", "Settled", 1))
  #Pre-Accepted
  #Pre-ASCB
  #Withdrawn
  #Upheld
  #Settled
  #Resolved
  #Not Upheld
  #Adjourned
  #No Determination
  #No Grounds To Proceed
  #No Jurisdiction
  #
  #
  #Decision: Complaint: 01/29 & 01/31 - Settled
  #Decision: Complaint 01/81 Upheld
  #Decision: Complaint: Not Upheld
  self.cursor.executemany("INSERT INTO decisions (name, arbiter, ruling, success) VALUES (%s, %s, %s, %s);", decisions)
  self.cursor.executemany("INSERT INTO products (id, name) VALUES (%s, %s);", re.findall(ur'<OPTION value="([0-9]{1,2})">(.*?)</OPTION>', download.getFile("http://old.asa.co.nz/search_product.php", download.getFilename(self.year, 'categories', "search_product")), flags=re.DOTALL))
  self.cursor.executemany("INSERT INTO media (id, name) VALUES (%s, %s);", re.findall(ur'<OPTION value="([0-9]{1,2})">(.*?)</OPTION>', download.getFile("http://old.asa.co.nz/search_media.php", download.getFilename(self.year, 'categories', "search_media")), flags=re.DOTALL))
  codes = re.findall(ur'<OPTION value="([0-9]{1,2})">(.*?)</OPTION>', download.getFile("http://old.asa.co.nz/search_code.php", download.getFilename(self.year, 'categories', "search_code")), flags=re.DOTALL)
  self.cursor.executemany("INSERT INTO codes (id, name) VALUES (%s, %s);", codes)
  self.cursor.executemany("UPDATE codes SET url = %s WHERE name = %s", [("http://old.asa.co.nz/code_ethics.php", "Code of Ethics"), ("http://old.asa.co.nz/code_financial.php", "Code for Financial Advertising"), ("http://old.asa.co.nz/code_people.php", "Code for People in Advertising"), ("http://old.asa.co.nz/code_weight.php", "Code for Advertising of Weight Management"), ("http://old.asa.co.nz/code_gaming.php", "Code for Gaming and Gambling"), ("http://old.asa.co.nz/code_vehicles.php", "Code for Advertising Vehicles"), ("http://old.asa.co.nz/code_therapeutic_services.php", "Therapeutic Services Advertising Code"), ("http://old.asa.co.nz/code_therapeutic_products.php", "Therapeutic Products Advertising Code"), ("http://old.asa.co.nz/code_children.php", "Code for Advertising to Children"), ("http://old.asa.co.nz/code_children_food.php", "Children's Code for Advertising Food"), ("http://old.asa.co.nz/code_food.php", "Code for Advertising Food"), ("http://old.asa.co.nz/code_promo_advert_liquor.php", "Code for Advertising and Promotion of Alcohol"), ("http://old.asa.co.nz/code_comparative.php", "Code for Comparative Advertising"), ("http://old.asa.co.nz/code_environmental.php", "Code for Environmental Claims")])
  for code in codes:
   for clause in re.findall(ur'<OPTION value="([0-9]{1,3})">(.*?)</OPTION>', download.getFile("http://old.asa.co.nz/srch_code.php", download.getFilename(self.year, 'categories', code[1]), post = {"code_id": code[0], "year": self.year}), flags=re.DOTALL):
    self.cursor.execute("INSERT INTO clauses (id, name, codes_id) VALUES (%s, %s, %s);", clause + (code[0],))
  self.loadCompanies()
  self.loadComplainants()
  
 def __empty(self):
  self.cursor.execute("SET foreign_key_checks = 0")
  for table in self.tables:
   self.cursor.execute("EMPTY TABLE " + table)
  self.cursor.execute("SET foreign_key_checks = 1")

 def insertCompany(self, company):
  if company != None and company != "":
   self.cursor.execute("INSERT INTO companies (name) SELECT * FROM (SELECT %s) AS temp WHERE NOT EXISTS (SELECT name FROM companies WHERE name = %s)", [company, company])
   self.cursor.execute("UPDATE companies SET companies_id = LAST_INSERT_ID() WHERE id = LAST_INSERT_ID() AND companies_id IS NULL")

 def getList(self, folder, filename):
  return download.loadResource(os.path.join(folder, filename)).decode('unicode_escape').splitlines()

 def loadCompanies(self):
  companyInsert = "INSERT IGNORE INTO companies (name, companies_id) VALUES (%s, (SELECT id FROM (SELECT * FROM companies) AS companiestemp WHERE name = %s))"
  for companies in self.getList('lookup', 'companies_altnames.txt'):
   companies = companies.split("|")
   self.insertCompany(companies[0])
   for companyname in companies[1:]:
    self.cursor.execute(companyInsert, [companyname, companies[0]])
  for companies in self.getList('lookup', 'companies_multiple.txt'):
   companies = companies.split("|")
   for companyname in companies[1:]:
    self.insertCompany(companyname)
    self.cursor.execute(companyInsert, [companies[0], companyname])

 def loadComplainants(self):
  for complainants in self.getList('lookup', 'complainants_translations.txt'):
   complainants = complainants.split("|")
   for complainant in complainants[1:]:
    complainant = complainant.split("#")
    if complainant[0] == "":
     complainant[0] = None
    if complainant[0]:
     self.cursor.execute("INSERT IGNORE INTO complainants (name) VALUES (%s)", [complainant[0]])
    if len(complainant) == 2:
     self.insertCompany(complainant[1])
    else:
     complainant.append(None)
     #complainant[1] = ""
    self.cursor.execute("INSERT INTO complainants_translations (name, complainants_id, companies_id) VALUES (%s, (SELECT id FROM complainants WHERE name = %s), (SELECT companies_id FROM companies WHERE name = %s))", [complainants[0], complainant[0], complainant[1]])
  for company in self.getList('lookup', 'complainants_companies.txt'):
   self.insertCompany(company)

 def splitComplainants(self, complainants):
  complainants = complainants.strip(" .")
  for find, replacement in {"one other": "Other", "another": "Other", "duplicate": "Other", "others": "Others", "duplicates": "Others", "other students": "Others", "class": "Others"}.iteritems():
   if complainants.lower().endswith(find):
    complainants = replacement + "|" + complainants[:-len(find)].strip()
  for find in (" and", ",", "&"):
   if complainants.lower().endswith(find):
    complainants = complainants[:-len(find)].strip()
  complainants = complainants.replace(", ", "|").replace("/", "|").replace(". & ", "#").replace(" & ", "|").replace("#", ". & ").replace(" and ", "|").replace(".", ". ").replace("  ", " ")
  complainants = complainants.split("|")
  #for complainant in complainants:
   #"Unable to be Identified"
  # if complainant in ["[A. Complainant]", "A. Complainant", "A Complainant", "A Person", "A. Person", "An Individual"]:
  #  pass
  #return complainants
  return [value for value in complainants if value not in ["[A. Complainant]", "A. Complainant", "A Complainant", "A Person", "A. Person", "An Individual"]]

 def insertComplainants(self, id, complainants):
  self.cursor.execute("INSERT IGNORE INTO complaints_complainants (complaints_id, complainants_id, companies_id, complainants_id_companies_id) SELECT %s, complainants_id, companies_id, (complainants_id + companies_id) FROM complainants_translations WHERE name = %s", [id, complainants])
  if self.cursor.rowcount == 0:
   self.cursor.execute("INSERT IGNORE INTO complaints_complainants (complaints_id, companies_id, complainants_id_companies_id) SELECT %s, companies_id, companies_id FROM companies WHERE name = %s", [id, complainants])
   if self.cursor.rowcount == 0:
    for complainant in self.splitComplainants(complainants):
     self.cursor.execute("INSERT IGNORE INTO complainants (name) VALUES (%s)", complainant)
     self.cursor.execute("INSERT IGNORE INTO complaints_complainants (complaints_id, complainants_id, complainants_id_companies_id) SELECT %s, id, id FROM complainants WHERE name = %s", [id, complainant])

 def loadBadges(self):
  for badge in self.getList('lookup', 'badges.txt'):
   badge = badge.split("|")
   self.cursor.execute("INSERT INTO badges (name, description, category, value, image, points) VALUES (%s, %s, ");
	 
 def insertComplaint(self, complaint):
  if complaint["docdate"]:
   complaint["docdate"] = datetime.fromtimestamp(complaint["docdate"])
  for field in complaint:
   if complaint[field] == "":
    complaint[field] = None
  self.cursor.execute("INSERT IGNORE INTO complaints (id, year, meetingdate, complainant, company, advert, products_id, decisions_id, docwords) VALUES (%s, %s, %s, %s, %s, %s, (SELECT id FROM products WHERE name = %s), (SELECT id FROM decisions WHERE name = %s), %s)", [complaint["id"], complaint["year"], complaint["meetingdate"], complaint["complainants"], complaint["companies"], complaint["advert"], complaint["product"], complaint["decision"], complaint["docwords"]])
  self.cursor.execute("INSERT IGNORE INTO docs (complaints_id, date, size, contents) VALUES (%s, %s, %s, %s)", [complaint["id"], complaint["docdate"], complaint["docsize"], complaint["doc"]])
  if "clauses" in complaint and complaint["clauses"]:
   for clause in complaint["clauses"]:
    clause.append(complaint["id"])
    self.cursor.execute("INSERT IGNORE INTO complaints_clauses (clauses_id, complaints_id) VALUES ((SELECT id FROM clauses WHERE codes_id = (SELECT id FROM codes WHERE name = %s) AND name = %s), %s)", clause)
  if "media" in complaint and complaint["media"]:
   for medium in complaint["media"]:
    self.cursor.execute("INSERT IGNORE INTO complaints_media (media_id, complaints_id) VALUES ((SELECT id FROM media WHERE name = %s), %s)", [medium, complaint["id"]])
  if "companies" in complaint and complaint["companies"]:
   self.insertCompany(complaint["companies"])
  self.cursor.execute("INSERT IGNORE INTO complaints_companies (complaints_id, companies_id) SELECT %s, companies_id FROM companies WHERE id IN (SELECT companies_id FROM companies WHERE name = %s)", [complaint["id"], complaint["companies"]])
  if "complainants" in complaint and complaint["complainants"]:
   self.insertComplainants(complaint["id"], complaint["complainants"])
  if "appealsuccess" in complaint:
   self.cursor.execute("INSERT IGNORE INTO appeals (id, complaints_id, success) VALUES (%s, %s, %s)", [complaint["appeal"], complaint["id"], complaint["appealsuccess"]])

 def getIncomplete(self):
  self.cursor.execute("SELECT complaint.id FROM complaints LEFT JOIN decisions ON complaint.decisions_id = decisions.id WHERE decisions.success IS NULL")

 def getMissing(self, currentYear):
  missingids = list()
  self.cursor.execute("SELECT year, MAX(id) AS id FROM complaints GROUP BY year ORDER BY year ASC")
  for max in self.cursor.fetchall():
   ids = list()
   self.cursor.execute("SELECT id FROM complaints WHERE year = %s", [max["year"]])
   for id in self.cursor.fetchall():
    ids.append(int(id["id"]))
   for possibleid in range(int(max["id"][0:2] + "001"), int(max["id"]) + 5):
    if possibleid not in ids:
     missingids.append(str(possibleid).zfill(5))
  if "year" in max and int(max["year"]) < currentYear:
   firstid = int(str(currentYear)[2:4] + "001")
   for possibleid in range(firstid, firstid + 20):
    missingids.append(str(possibleid).zfill(5))
  return missingids