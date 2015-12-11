# -*- coding: utf-8 -*-

from scan import complaints
from scan import download

#download.getOldDecisions()
complaints = complaints.complaints(quick = True)
#import profile
#profile.run('complaints.getAll()')
#profile.run('complaints.getOthers()')
complaints.getAll()
#complaints.getOthers()