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


with open("isrc-spotify.json") as file:
    data = json.load(file)
print(len(data))
i = 1
for song in data:
    if not song["isrc"] == "USCH38500019":
        print(i)
        query = "UPDATE songs SET mbid = %s WHERE isrc = %s"
        values = (song["mbid"], song["isrc"])
        cursor.execute(query, values)
        conn.commit()
        i += 1

cursor.close()
conn.close()
