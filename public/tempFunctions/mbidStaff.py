import json
import mysql.connector

conn = mysql.connector.connect(
    host='176.217.245.160',
    user='default',
    password='1aaf5a1fe37d97e8468ac9a7b1b1473ef8be50ff2bdf251a54c910fe0f36be6b',
    database='music_tailor',
    port=8000
)
cursor = conn.cursor()

with open("new.json") as file:
    data = json.load(file)
print(len(data))
j = 1
for song in data:
    tmp = song["staff"]
    for i in range(len(tmp)):
        tmp[i]["link"] = "https://musicbrainz.org/artist/"+tmp[i]["id"]
    print(j)
    query = "UPDATE songs SET staff = %s WHERE mbid = %s"
    values = (json.dumps(tmp), song["mbid"])
    cursor.execute(query, values)
    conn.commit()
    j += 1

cursor.close()
conn.close()
