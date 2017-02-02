# -*- coding: utf-8 -*-

import os, re, json, sys
from datetime import datetime, date
from collections import OrderedDict
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
  #self.connection.autocommit(True)
  self.cursor = self.connection.cursor(MySQLdb.cursors.DictCursor)
  #self.year = date.today().year
  self.year = 2015
  self.tables = ["complainants", "decisions", "products", "products_sub", "media", "media_sub", "codes", "codes_clauses", "advertisers", "advertisers_details", "complainants_translations", "sources", "complaints", "complaints_docs", "complaints_appeals", "complaints_complainants", "complaints_media", "complaints_clauses", "complaints_advertisers"] # , "badges"
  self.json = OrderedDict()
  self.json["sources"] = ["name", "url"]
  self.json["products"] = ["id", "name"]
  self.json["products_sub"] = ["id", "products_id", "name"]
  self.json["decisions"] = ["id", "name", "success"]
  self.json["decisions_sub"] = ["decisions_id", "name", "arbiter", "ruling"]
  self.json["media"] = ["id", "name"]
  self.json["media_sub"] = ["id", "media_id", "source", "name"]
  self.json["codes"] = ["id", "id_old", "name", "url"]
  self.json["codes_clauses"] = ["id", "id_old", "codes_id", "name", "name_old"]
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
  self.cursor.execute("CREATE TABLE sources (name VARCHAR(8) PRIMARY KEY, url VARCHAR(64))")
  self.cursor.execute("CREATE TABLE decisions (id SMALLINT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE, success BOOLEAN)")
  self.cursor.execute("CREATE TABLE decisions_sub (id SMALLINT AUTO_INCREMENT PRIMARY KEY, decisions_id SMALLINT, name VARCHAR(128) NOT NULL UNIQUE, arbiter VARCHAR(8), ruling VARCHAR(64), FOREIGN KEY (decisions_id) REFERENCES decisions(id))")
  self.cursor.execute("CREATE TABLE products (id SMALLINT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE)")
  self.cursor.execute("CREATE TABLE products_sub (id SMALLINT PRIMARY KEY, products_id SMALLINT, name VARCHAR(128) NOT NULL UNIQUE, FOREIGN KEY (products_id) REFERENCES products(id))")
  self.cursor.execute("CREATE TABLE media (id SMALLINT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE)")
  self.cursor.execute("CREATE TABLE media_sub (id SMALLINT, media_id SMALLINT, source VARCHAR(8), name VARCHAR(128) NOT NULL, PRIMARY KEY (id, source), FOREIGN KEY (media_id) REFERENCES media(id), FOREIGN KEY (source) REFERENCES sources(name))")
  self.cursor.execute("CREATE TABLE codes (id SMALLINT PRIMARY KEY, id_old SMALLINT, name VARCHAR(128) NOT NULL UNIQUE, url VARCHAR(128))")
  self.cursor.execute("CREATE TABLE codes_clauses (id SMALLINT UNIQUE, id_old SMALLINT UNIQUE, codes_id SMALLINT NOT NULL, name VARCHAR(128), name_old VARCHAR(128), FOREIGN KEY (codes_id) REFERENCES codes(id))") # , PRIMARY KEY (id, id_old, codes_id)
  self.cursor.execute("CREATE TABLE complainants (id SMALLINT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(128) NOT NULL UNIQUE)")
  self.cursor.execute("CREATE TABLE advertisers (id SMALLINT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(128) NOT NULL, advertisers_id SMALLINT, UNIQUE (name, advertisers_id), FOREIGN KEY (advertisers_id) REFERENCES advertisers(id))")
  #self.cursor.execute("CREATE TABLE advertisers_details (advertisers_id SMALLINT PRIMARY KEY, url VARCHAR(256), number INT UNIQUE, FOREIGN KEY (advertisers_id) REFERENCES advertisers(id))")
  self.cursor.execute("CREATE TABLE complainants_translations (name VARCHAR(128), complainants_id SMALLINT, advertisers_id SMALLINT, UNIQUE (name, complainants_id, advertisers_id), FOREIGN KEY (complainants_id) REFERENCES complainants(id), FOREIGN KEY (advertisers_id) REFERENCES advertisers(id))")
  #self.cursor.execute("CREATE TABLE complaints (id CHAR(5), source VARCHAR(8), year YEAR(4) NOT NULL, meetingdate DATE, complainant VARCHAR(128), advertiser VARCHAR(128), advert VARCHAR(128), products_id SMALLINT, decisions_id SMALLINT, docwords MEDIUMINT, PRIMARY KEY (id, source), FOREIGN KEY (products_id) REFERENCES products(id), FOREIGN KEY (decisions_id) REFERENCES decisions(id))")
  self.cursor.execute("CREATE TABLE complaints (id CHAR(5), source VARCHAR(8), year YEAR(4) NOT NULL, meeting DATE, released DATE, complainants VARCHAR(128), advertisers VARCHAR(128), advertisement VARCHAR(128), products VARCHAR(64), media VARCHAR(64), clauses VARCHAR(1024), decisions VARCHAR(256), url VARCHAR(128), cache VARCHAR(128), appeal CHAR(5), PRIMARY KEY (id, source), FOREIGN KEY (source) REFERENCES sources(name))")
  self.cursor.execute("CREATE TABLE docs (complaints_id CHAR(5), complaints_source VARCHAR(8), size INT, words INT, type VARCHAR(4), url VARCHAR(128), cache VARCHAR(128), html VARCHAR(128), text VARCHAR(128), contents MEDIUMTEXT, PRIMARY KEY (complaints_id, complaints_source), FOREIGN KEY (complaints_id, complaints_source) REFERENCES complaints(id, source))")
  self.cursor.execute("CREATE TABLE appeals (id CHAR(5) PRIMARY KEY, complaints_id CHAR(5) NOT NULL UNIQUE, success BOOLEAN NOT NULL, decisions VARCHAR(32), FOREIGN KEY (complaints_id) REFERENCES complaints(id))")
  self.cursor.execute("CREATE TABLE complaints_complainants (complaints_id CHAR(5) NOT NULL, complainants_id SMALLINT, advertisers_id SMALLINT, complainants_id_advertisers_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, complainants_id_advertisers_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (complainants_id) REFERENCES complainants(id), FOREIGN KEY (advertisers_id) REFERENCES advertisers(id))")
  self.cursor.execute("CREATE TABLE complaints_products (complaints_id CHAR(5) NOT NULL, products_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, products_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (products_id) REFERENCES products(id))")
  self.cursor.execute("CREATE TABLE complaints_products_sub (complaints_id CHAR(5) NOT NULL, products_sub_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, products_sub_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (products_sub_id) REFERENCES products_sub(id))")
  self.cursor.execute("CREATE TABLE complaints_media (complaints_id CHAR(5) NOT NULL, media_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, media_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (media_id) REFERENCES media(id))")
  self.cursor.execute("CREATE TABLE complaints_media_sub (complaints_id CHAR(5) NOT NULL, media_sub_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, media_sub_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (media_sub_id) REFERENCES media_sub(id))")
  self.cursor.execute("CREATE TABLE complaints_decisions (complaints_id CHAR(5) NOT NULL, decisions_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, decisions_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (decisions_id) REFERENCES decisions(id))")
  self.cursor.execute("CREATE TABLE complaints_decisions_sub (complaints_id CHAR(5) NOT NULL, decisions_sub_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, decisions_sub_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (decisions_sub_id) REFERENCES decisions_sub(id))")
  self.cursor.execute("CREATE TABLE complaints_codes (complaints_id CHAR(5) NOT NULL, codes_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, codes_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (codes_id) REFERENCES codes(id))")
  self.cursor.execute("CREATE TABLE complaints_codes_clauses (complaints_id CHAR(5) NOT NULL, codes_clauses_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, codes_clauses_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (codes_clauses_id) REFERENCES codes_clauses(id))")
  self.cursor.execute("CREATE TABLE complaints_advertisers (complaints_id CHAR(5) NOT NULL, advertisers_id SMALLINT NOT NULL, PRIMARY KEY (complaints_id, advertisers_id), FOREIGN KEY (complaints_id) REFERENCES complaints(id), FOREIGN KEY (advertisers_id) REFERENCES advertisers(id))")
  #self.cursor.execute("CREATE TABLE badges (name VARCHAR(16) PRIMARY KEY, description VARCHAR(128) NOT NULL, category VARCHAR(16) NOT NULL, value SMALLINT, image VARCHAR(128) NOT NULL, points SMALLINT NOT NULL)")
  self.__fill()

 def __fill(self):
  for table, columns in self.json.iteritems():
   self.insertMany(table, columns, json.loads(download.loadResource(os.path.join("lookup", table + ".json"))))
  self.loadAdvertisers()
  self.loadComplainants()
  self.commit()
  
 def __empty(self):
  self.cursor.execute("SET foreign_key_checks = 0")
  for table in self.tables:
   self.cursor.execute("EMPTY TABLE " + table)
  self.cursor.execute("SET foreign_key_checks = 1")

 def commit(self):
  self.connection.commit()

 def insertDict(self, table, values):
  #print values
  self.cursor.execute("INSERT IGNORE INTO " + table + " (" + (", ").join(values.keys()) + ") VALUES (" + ", ".join(['%s'] * len(values)) + ")", values.values())
 
 def insertMany(self, table, columns, values):
  self.cursor.executemany("INSERT IGNORE INTO " + table + " (" + (", ").join(columns) + ") VALUES (" + ", ".join(['%s'] * len(columns)) + ")", values)
  self.commit()

 def insertAdvertiser(self, advertiser):
  if advertiser != None and advertiser != "":
   self.cursor.execute("INSERT INTO advertisers (name) SELECT * FROM (SELECT %s) AS temp WHERE NOT EXISTS (SELECT name FROM advertisers WHERE name = %s)", [advertiser, advertiser])
   self.cursor.execute("UPDATE advertisers SET advertisers_id = LAST_INSERT_ID() WHERE id = LAST_INSERT_ID() AND advertisers_id IS NULL")

 def getIndices(self, query):
  self.cursor.execute(query)
  indices = {}
  for index in self.cursor.fetchall():
   indices[index["name"]] = index["id"]
  return indices

 def getList(self, folder, filename):
  return download.loadResource(os.path.join(folder, filename)).decode('unicode_escape').splitlines()

 def loadAdvertisers(self):
  advertiserInsert = "INSERT IGNORE INTO advertisers (name, advertisers_id) VALUES (%s, (SELECT id FROM (SELECT * FROM advertisers) AS advertiserstemp WHERE name = %s))"
  for advertisers in self.getList('lookup', 'advertisers_altnames.txt'):
   advertisers = advertisers.split("|")
   self.insertAdvertiser(advertisers[0])
   for advertisername in advertisers[1:]:
    self.cursor.execute(advertiserInsert, [advertisername, advertisers[0]])
  for advertisers in self.getList('lookup', 'advertisers_multiple.txt'):
   advertisers = advertisers.split("|")
   for advertisername in advertisers[1:]:
    self.insertAdvertiser(advertisername)
    self.cursor.execute(advertiserInsert, [advertisers[0], advertisername])

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
     self.insertAdvertiser(complainant[1])
    else:
     complainant.append(None)
     #complainant[1] = ""
    self.cursor.execute("INSERT INTO complainants_translations (name, complainants_id, advertisers_id) VALUES (%s, (SELECT id FROM complainants WHERE name = %s), (SELECT advertisers_id FROM advertisers WHERE name = %s))", [complainants[0], complainant[0], complainant[1]])
  for advertiser in self.getList('lookup', 'complainants_advertisers.txt'):
   self.insertAdvertiser(advertiser)

 def insertComplainants(self, id, complainants):
  for complainant in complainants:
   self.cursor.execute("INSERT IGNORE INTO complaints_complainants (complaints_id, complainants_id, advertisers_id, complainants_id_advertisers_id) SELECT %s, complainants_id, advertisers_id, (complainants_id + advertisers_id) FROM complainants_translations WHERE name = %s", [id, complainant])
   if self.cursor.rowcount == 0:
    self.cursor.execute("INSERT IGNORE INTO complaints_complainants (complaints_id, advertisers_id, complainants_id_advertisers_id) SELECT %s, advertisers_id, advertisers_id FROM advertisers WHERE name = %s", [id, complainant])
    if self.cursor.rowcount == 0:
     self.cursor.execute("INSERT IGNORE INTO complainants (name) VALUES (%s)", [complainant])
     self.cursor.execute("INSERT IGNORE INTO complaints_complainants (complaints_id, complainants_id, complainants_id_advertisers_id) SELECT %s, id, id FROM complainants WHERE name = %s", [id, complainant])
  #for complainant in complainants:
  # self.cursor.execute("INSERT IGNORE INTO complainants (name) VALUES (%s)", [complainant])
  # self.cursor.execute("INSERT IGNORE INTO complaints_complainants (complaints_id, complainants_id, complainants_id_advertisers_id) SELECT %s, id, id FROM complainants WHERE name = %s", [id, complainant])

 def loadBadges(self):
  for badge in self.getList('lookup', 'badges.txt'):
   badge = badge.split("|")
   #self.cursor.execute("INSERT INTO badges (name, description, category, value, image, points) VALUES (%s, %s, ");
	 
 def insertComplaint(self, complaint):
  print complaint["details"];
#  if complaint and "doc" in complaint and "date" in complaint["doc"] and complaint["doc"]["date"]:
#   complaint["doc"]["date"] = datetime.fromtimestamp(complaint["doc"]["date"])
  complaint["doc"]["complaints_id"] = complaint["details"]["id"]
  complaint["doc"]["complaints_source"] = complaint["details"]["source"]
  for field in complaint["details"]:
   if isinstance(complaint["details"][field], str) or isinstance(complaint["details"][field], unicode):
    complaint["details"][field] = complaint["details"][field].strip()
   if complaint["details"][field] == "":
    complaint["details"][field] = None
  self.insertDict("complaints", complaint["details"])
  self.insertDict("docs", complaint["doc"])
  for code, clause in complaint["codes_clauses"]:
   self.cursor.execute("INSERT IGNORE INTO complaints_codes (codes_id, complaints_id) VALUES ((SELECT id FROM codes WHERE name = %s), %s)", (code, complaint["details"]["id"]))
   self.cursor.execute("INSERT IGNORE INTO complaints_codes_clauses (codes_clauses_id, complaints_id) VALUES ((SELECT id FROM codes_clauses WHERE codes_id = (SELECT id FROM codes WHERE name = %s) AND name = %s), %s)", (code, clause, complaint["details"]["id"]))
  for medium in complaint["media"]:
   self.cursor.execute("INSERT IGNORE INTO complaints_media (media_id, complaints_id) VALUES ((SELECT id FROM media WHERE name = %s), %s)", [medium, complaint["details"]["id"]])
   self.cursor.execute("INSERT IGNORE INTO complaints_media_sub (media_sub_id, complaints_id) VALUES ((SELECT id FROM media_sub WHERE source = %s AND name = %s), %s)", [complaint["details"]["source"], medium, complaint["details"]["id"]])
   self.cursor.execute("INSERT IGNORE INTO complaints_media (media_id, complaints_id) VALUES ((SELECT media_id FROM media_sub WHERE source = %s AND name = %s), %s)", [complaint["details"]["source"], medium, complaint["details"]["id"]])
  for decision in complaint["decisions"]:
   decision = decision.strip()
   self.cursor.execute("INSERT IGNORE INTO complaints_decisions (decisions_id, complaints_id) VALUES ((SELECT id FROM decisions WHERE name = %s), %s)", [decision, complaint["details"]["id"]])
   self.cursor.execute("INSERT IGNORE INTO complaints_decisions_sub (decisions_sub_id, complaints_id) VALUES ((SELECT id FROM decisions_sub WHERE name = %s), %s)", [decision, complaint["details"]["id"]])
   self.cursor.execute("INSERT IGNORE INTO complaints_decisions (decisions_id, complaints_id) VALUES ((SELECT decisions_id FROM decisions_sub WHERE name = %s), %s)", [decision, complaint["details"]["id"]])
  for product in complaint["products"]:
   product = product.strip()
   self.cursor.execute("INSERT IGNORE INTO complaints_products (products_id, complaints_id) VALUES ((SELECT id FROM products WHERE name = %s), %s)", [product, complaint["details"]["id"]])
   self.cursor.execute("INSERT IGNORE INTO complaints_products_sub (products_sub_id, complaints_id) VALUES ((SELECT id FROM products_sub WHERE name = %s), %s)", [product, complaint["details"]["id"]])
   self.cursor.execute("INSERT IGNORE INTO complaints_products (products_id, complaints_id) VALUES ((SELECT products_id FROM products_sub WHERE name = %s), %s)", [product, complaint["details"]["id"]])
  for advertiser in complaint["advertisers"]:
   advertiser = advertiser.strip()
   self.insertAdvertiser(advertiser)
   self.cursor.execute("INSERT IGNORE INTO complaints_advertisers (complaints_id, advertisers_id) SELECT %s, advertisers_id FROM advertisers WHERE id IN (SELECT advertisers_id FROM advertisers WHERE name = %s)", [complaint["details"]["id"], advertiser])
  if complaint["complainants"]:
   self.insertComplainants(complaint["details"]["id"], complaint["complainants"])
#  if "appealsuccess" in complaint:
#   self.cursor.execute("INSERT IGNORE INTO appeals (id, complaints_id, success) VALUES (%s, %s, %s)", [complaint["appeal"], complaint["id"], complaint["appealsuccess"]])
  #self.commit()
  #sys.exit()

 def getIncomplete(self):
  self.cursor.execute("SELECT complaint.id FROM complaints LEFT JOIN decisions ON complaint.decisions_id = decisions.id WHERE decisions.success IS NULL")

 def getAllMissing(self, currentYear):
  missingids = list()
  self.cursor.execute("SELECT year, MAX(id) AS id FROM complaints WHERE source = 'old' GROUP BY year ORDER BY year ASC")
  for max in self.cursor.fetchall():
   ids = list()
   self.cursor.execute("SELECT id FROM complaints WHERE year = %s", [max["year"]])
   for complaint in self.cursor.fetchall():
    ids.append(int(complaint["id"]))
   for possibleid in range(int(max["id"][0:2] + "001"), int(max["id"]) + 5):
    if possibleid not in ids:
     missingids.append(str(possibleid).zfill(5))
  if "year" in max and int(max["year"]) < currentYear:
   firstid = int(str(currentYear)[2:4] + "001")
   for possibleid in range(firstid, firstid + 20):
    missingids.append(str(possibleid).zfill(5))
  return missingids
  
 def getMissing(self, year):
  ids = list()
  missingids = list()
  self.cursor.execute("SELECT id FROM complaints WHERE year = %s", [year])
  for complaint in self.cursor.fetchall():
   ids.append(int(complaint["id"]))
  for possibleid in range(int(str(year)[2:4] + "001"), int(max(ids)) + 5):
   if possibleid not in ids:
    missingids.append(str(possibleid).zfill(5))
  return missingids