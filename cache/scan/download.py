# -*- coding: utf-8 -*-

import os, urllib, urllib2, time

debug = True

downloaddelay = 5

def getFilename(year, type, value, extension = ".html"):
 return os.path.join(os.path.abspath(os.path.join(os.getcwd(), '../..')), "files", str(year), type, validFilename(value + extension))

def validFilename(filename):
 for char in '\/:*?"<>|':
  filename = filename.replace(char, "")
 return filename

def createFolder(folderName):
 try:
  os.makedirs(folderName)
 except:
  pass

def getJSON(url):
 import requests
 return requests.get(url).json()

def saveResourcePost(filepathname, contents, times = False):
 if contents:
  createFolder(os.path.dirname(filepathname))
  if debug: print "Saving: " + filepathname
  import codecs
  with codecs.open(filepathname, 'wb', encoding = 'utf-8') as f:
   f.write(contents)
   f.close()
   if times:
    os.utime(filepathname, times)
   return True

def saveResource(url, filepathname, times = False):
 if url:
  createFolder(os.path.dirname(filepathname))
  if debug: print "Saving: " + filepathname
  import codecs
  urllib.URLopener().retrieve(url, filepathname)
  #try:
  # urllib.urlretrieve(url, filepathname)
  if times:
   os.utime(filepathname, times)
  if os.path.getsize(filepathname) > 2200:
   return True
  print "Too small: " + url
  os.remove(filepathname)
  return False
 else:
  print "No URL"

def loadResource(filepathname):
 if debug: print "Loading: " + filepathname
 if os.path.exists(filepathname):
  with open(filepathname, 'rb') as f:
   return f.read()

def getFile(url, filepathname, refresh = False, post = False, cache = True, returnfile = True):
 timeout = 5
 if cache and not refresh and os.path.exists(filepathname):
  if returnfile:
   return loadResource(filepathname)
  return True
 time.sleep(downloaddelay)
 try:
  if post:
   doc = urllib2.urlopen(urllib2.Request(url, urllib.urlencode(post)), timeout = timeout)
  else:
   doc = urllib2.urlopen(url, timeout = timeout)
 except:
  print "Could not connect to " + url
  print post
  return False
 if not cache:
  if returnfile:
   return doc.read()
  return True
 filedate = False
 filesize = False
 mTime = False
 newversion = False
 meta = doc.info()
 if len(meta.getheaders("Last-Modified")) > 0:
  mTime = int(time.mktime(time.strptime(meta.getheaders("Last-Modified")[0], '%a, %d %b %Y %H:%M:%S %Z')))
  filedate = (time.time(), mTime)
 if len(meta.getheaders("Content-Length")) > 0:
  filesize = int(meta.getheaders("Content-Length")[0])
 if os.path.exists(filepathname):
  if mTime and mTime != int(os.path.getmtime(filepathname)):
   newversion = True
  if filesize and filesize != os.path.getsize(filepathname):
    newversion = True
 else:
  newversion = True
 if newversion:
  if cache:
   #saveResource(filepathname, contents, filedate)
   if post:
    time.sleep(downloaddelay)
    saved = saveResourcePost(filepathname, doc.read(), filedate)
   else:
    time.sleep(downloaddelay)
    saved = saveResource(url, filepathname, filedate)
   if not saved:
    return False
 if returnfile:
  return loadResource(filepathname)
 return True

def getFileDetails(filepathname):
 if os.path.exists(filepathname):
  return os.path.getmtime(filepathname), os.path.getsize(filepathname)
 return None, None