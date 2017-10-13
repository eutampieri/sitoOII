#!encoding: utf-8
from sys import argv
from os import remove,system,makedirs,rmdir
from urllib import quote,urlretrieve
from lxml import html
from subprocess import call
import requests,json
from uuid import uuid4 as u
task=open(argv[1]).read()
user=argv[2]
cartella=user+"-"+str(u())
def sendMsg(to,msg):
	token=""
	url="https://api.telegram.org/bot"+token+"/sendMessage?chat_id="+to+"&text="+quote(msg)
	m=requests.get(url)
def sendFile(to,path):
	token=""
	print 'curl -F "chat_id='+to+'" -F document=@'+path+' https://api.telegram.org/bot'+token+'/sendDocument'
	system('curl -F "chat_id='+to+'" -F document=@'+path+' https://api.telegram.org/bot'+token+'/sendDocument')
	#remove(vid+ext)search={"action":"list","search":task,"first":0,"last":10}
search={"action":"list","search":task,"first":0,"last":10}
s=requests.post("https://cms.di.unipi.it/api/task",data=json.dumps(search),headers={"Content-Type":"application/json"})
t=json.loads(s.text)
if t["num"]==0:
	sendMsg(user,"Nessun problema trovato...")
elif t["num"]==1:
	makedirs(cartella)
	t=t["tasks"][0]["name"]
	tsk={"action":"get","name":t}
	s=requests.post("https://cms.di.unipi.it/api/task",data=json.dumps(tsk),headers={"Content-Type":"application/json"})
	tsk=json.loads(s.text)
	st=tsk["statements"]["it"]
	url="https://cms.di.unipi.it/api/files/"+st+"/"+t+".pdf"
	urlretrieve(url,cartella+"/"+t+".pdf")
	sendFile(user,cartella+"/"+t+".pdf")
	remove(cartella+"/"+t+".pdf")
	rmdir(cartella)
else:
	msg="Sono stati trovati più problemi:\n"
	for p in t["tasks"]:
		msg=msg+"- /dl"+str(p["name"])+" "+str(p["title"])+"\n"
	sendMsg(user,msg)
remove(argv[1])
