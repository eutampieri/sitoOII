#!encoding: utf-8
from sys import argv
from os import remove,system,makedirs,rmdir
from urllib import quote,urlretrieve
from lxml import html
from subprocess import call
import requests,json
from uuid import uuid4 as u
task=open(argv[1]).read().replace('/dl','')
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
makedirs(cartella)
tsk={"action":"get","name":task}
s=requests.post("https://cms.di.unipi.it/api/task",data=json.dumps(tsk),headers={"Content-Type":"application/json"})
tsk=json.loads(s.text)
st=tsk["statements"]["it"]
url="https://cms.di.unipi.it/api/files/"+st+"/"+task+".pdf"
urlretrieve(url,cartella+"/"+task+".pdf")
sendFile(user,cartella+"/"+task+".pdf")
remove(cartella+"/"+task+".pdf")
remove(argv[1])
rmdir(cartella)
