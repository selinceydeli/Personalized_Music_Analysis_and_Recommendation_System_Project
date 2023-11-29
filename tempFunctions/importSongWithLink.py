from datetime import datetime
import mysql.connector
import argparse
from urllib.parse import urlparse
import requests
import json

client_id = '0871147f112a492b88fc9fea2d3bf5e7'
client_secret = 'de4f9a66d7714e75bda922d3959e1a3d'
access_token = ""

# Connect to the MySQL server
connection = mysql.connector.connect(
    host='142.93.108.221',
    user='selin',
    password='music-tailor',
    database='music_tailor'
)

# Create a cursor object to interact with the database
cursor = connection.cursor()
notes = {
    0: 'C',
    1: 'C#',
    2: 'D',
    3: 'D#',
    4: 'E',
    5: 'F',
    6: 'F#',
    7: 'G',
    8: 'G#',
    9: 'A',
    10: 'A#',
    11: 'B'
}


def getToken(id, secret):
    auth_url = 'https://accounts.spotify.com/api/token'
    data = {
        'grant_type': 'client_credentials',
        'client_id': id,
        'client_secret': secret,
    }
    auth_response = requests.post(auth_url, data=data)
    return auth_response.json().get('access_token')


def getAlbum(albumID):
    base_url = 'https://api.spotify.com/v1/'
    headers = {
        'Authorization': 'Bearer {}'.format(access_token)
    }
    featured_playlists_endpoint = f'albums/{albumID}'
    featured_playlists_url = ''.join([base_url, featured_playlists_endpoint])
    response = requests.get(featured_playlists_url, headers=headers)

    albumResponse = response.json()
    albumResponse["artist_id"] = albumResponse["artists"][0]["id"]
    del albumResponse["artists"]

    del albumResponse["available_markets"]
    del albumResponse["release_date_precision"]
    del albumResponse["uri"]
    del albumResponse["href"]
    albumResponse["album_id"] = albumResponse["id"]
    del albumResponse["id"]
    del albumResponse["external_urls"]
    albumResponse["upc"] = albumResponse["external_ids"]["upc"]
    del albumResponse["external_ids"]

    albumResponse["copyrights"] = albumResponse["copyrights"][0]["text"]
    albumResponse["image"] = albumResponse["images"][0]["url"]
    del albumResponse["images"]
    del albumResponse["tracks"]["href"]
    del albumResponse["tracks"]["limit"]
    del albumResponse["tracks"]["next"]
    del albumResponse["tracks"]["offset"]
    del albumResponse["tracks"]["previous"]
    songSet = set()
    artistSet = set()
    tmp = 0
    for i in range(len(albumResponse["tracks"]["items"])):
        del albumResponse["tracks"]["items"][i]["available_markets"]
        del albumResponse["tracks"]["items"][i]["disc_number"]
        del albumResponse["tracks"]["items"][i]["external_urls"]
        del albumResponse["tracks"]["items"][i]["href"]
        del albumResponse["tracks"]["items"][i]["name"]
        del albumResponse["tracks"]["items"][i]["is_local"]
        del albumResponse["tracks"]["items"][i]["type"]
        del albumResponse["tracks"]["items"][i]["uri"]
        del albumResponse["tracks"]["items"][i]["preview_url"]
        del albumResponse["tracks"]["items"][i]["explicit"]
        del albumResponse["tracks"]["items"][i]["duration_ms"]
        ctr = albumResponse["release_date"].count('-')
        for _ in range(2-ctr):
            albumResponse["release_date"] = albumResponse["release_date"] + "-01"
        songSet.add(albumResponse["tracks"]["items"][i]["id"])
        for j in range(len(albumResponse["tracks"]["items"][i]["artists"])):
            artistSet.add(albumResponse["tracks"]
                          ["items"][i]["artists"][j]["id"])

        if tmp < albumResponse["tracks"]["items"][i]["track_number"]:
            tmp = albumResponse["tracks"]["items"][i]["track_number"]
        else:
            tmp += 1
            albumResponse["tracks"]["items"][i]["track_number"] = tmp
        del albumResponse["tracks"]["items"][i]["artists"]
    with open(f"{albumID}_album.json", "w") as outfile:
        json.dump(albumResponse, outfile, indent=4)

    songQuery = "ids="
    for song in songSet:
        songQuery += song + ","
    songQuery = songQuery[:-1]
    featured_playlists_endpoint = f'tracks/?{songQuery}'
    featured_playlists_url = ''.join([base_url, featured_playlists_endpoint])
    response = requests.get(featured_playlists_url, headers=headers)
    songResponse = response.json()
    songResponse = songResponse["tracks"]
    for i in range(len(songResponse)):
        del songResponse[i]['available_markets']
        del songResponse[i]['disc_number']
        del songResponse[i]['is_local']
        del songResponse[i]['href']
        del songResponse[i]['type']
        del songResponse[i]['track_number']
        songResponse[i]['song_id'] = songResponse[i]['id']
        songResponse[i]['isrc'] = songResponse[i]['external_ids']['isrc']
        del songResponse[i]['external_ids']
        del songResponse[i]['id']
        del songResponse[i]['external_urls']
        del songResponse[i]['uri']
        songResponse[i]['album_id'] = songResponse[i]['album']["id"]
        del songResponse[i]['album']
        songResponse[i]['artists_ids'] = [
            id["id"] for id in songResponse[i]['artists']]
        del songResponse[i]['artists']

    featured_playlists_endpoint = f'audio-features/?{songQuery}'
    featured_playlists_url = ''.join([base_url, featured_playlists_endpoint])
    response = requests.get(featured_playlists_url, headers=headers)
    songFeatureResponse = response.json()
    songFeatureResponse = songFeatureResponse["audio_features"]
    songDict = dict()
    for i in range(len(songFeatureResponse)):
        del songFeatureResponse[i]["type"]
        del songFeatureResponse[i]["uri"]
        del songFeatureResponse[i]["track_href"]
        del songFeatureResponse[i]["analysis_url"]
        del songFeatureResponse[i]["duration_ms"]
        if songFeatureResponse[i]["mode"] == 0:
            songFeatureResponse[i]["mode"] = "Minor"
        else:
            songFeatureResponse[i]["mode"] = "Major"
        songFeatureResponse[i]["key"] = notes[songFeatureResponse[i]["key"]]
        songDict[songFeatureResponse[i]["id"]] = songFeatureResponse[i]
        del songDict[songFeatureResponse[i]["id"]]["id"]
    for i in range(len(songResponse)):
        songResponse[i].update(songDict[songResponse[i]["song_id"]])
        if songResponse[i]["explicit"] == False:
            songResponse[i]["explicit"] = 0
        else:
            songResponse[i]["explicit"] = 1
    with open(f"{albumID}_songs.json", "w") as outfile:
        json.dump(songResponse, outfile, indent=4)

    artistQuery = "ids="
    for artist in artistSet:
        artistQuery += artist + ","
    artistQuery = artistQuery[:-1]
    featured_playlists_endpoint = f'artists/?{artistQuery}'
    featured_playlists_url = ''.join([base_url, featured_playlists_endpoint])
    response = requests.get(featured_playlists_url, headers=headers)
    artistResponse = response.json()
    artistResponse = artistResponse["artists"]
    for i in range(len(artistResponse)):
        del artistResponse[i]["external_urls"]
        artistResponse[i]["followers"] = artistResponse[i]["followers"]["total"]
        del artistResponse[i]["href"]
        if len(artistResponse[i]["images"]) > 0:
            artistResponse[i]["image"] = artistResponse[i]["images"][0]["url"]
        else:
            artistResponse[i]["image"] = None
        del artistResponse[i]["images"]
        del artistResponse[i]["type"]
        del artistResponse[i]["uri"]
        artistResponse[i]["artist_id"] = artistResponse[i]["id"]
        del artistResponse[i]["id"]

    with open(f"{albumID}_artists.json", "w") as outfile:
        json.dump(artistResponse, outfile, indent=4)
    for i in range(len(artistResponse)):
        insertPerformer = "INSERT INTO performers (`artist_id`,`name`,`genre`,`popularity`,`image_url`,`created_at`) VALUES (%s, %s, %s, %s, %s, %s)"
        datestr = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        data = (artistResponse[i]["artist_id"], artistResponse[i]["name"],
                json.dumps(artistResponse[i]["genres"]), artistResponse[i]["popularity"], artistResponse[i]["image"], datestr)
        try:
            cursor.execute(insertPerformer, data)
            connection.commit()
        except Exception as e:
            print("Ekleyemedim")
            pass
    insertAlbum = "INSERT INTO albums (`name`, `image_url`, `album_id`, `copyright`, `label`,  `artist_id`,  `album_type`,  `popularity`,  `total_tracks`,  `release_date`,`created_at`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
    datestr = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    data = (albumResponse["name"], albumResponse["image"], albumResponse["album_id"], albumResponse["copyrights"], albumResponse["label"], albumResponse
            ["artist_id"], albumResponse["album_type"], albumResponse["popularity"], albumResponse["total_tracks"], albumResponse["release_date"], datestr)
    try:
        cursor.execute(insertAlbum, data)
        connection.commit()
    except Exception as e:
        print("Ekleyemedim2")
        pass
    for i in range(len(songResponse)):
        insertSongs = "INSERT INTO songs (`name`,`song_id`,`album_id`,`explicit`,`duration`,`key`,`tempo`,`performers`,`isrc`,`lyrics`,`system_entry_date`,  `mode`,`danceability`,`energy`,`loudness`,`speechiness`,`instrumentalness`,`liveness`,`valence`,`time_signature`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
        datestr = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        data = (songResponse[i]["name"],
                songResponse[i]["song_id"],
                songResponse[i]["album_id"],
                songResponse[i]["explicit"],  songResponse[i]["duration_ms"],  songResponse[i]["key"],  songResponse[i]["tempo"],  json.dumps(songResponse[i]["artists_ids"]),  songResponse[i]["isrc"],  "None", datestr, songResponse[i]["mode"],  songResponse[i]["danceability"],  songResponse[i]["energy"],  songResponse[i]["loudness"],  songResponse[i]["speechiness"],  songResponse[i]["instrumentalness"],  songResponse[i]["liveness"],  songResponse[i]["valence"],  songResponse[i]["time_signature"])
        try:
            cursor.execute(insertSongs, data)
            connection.commit()
        except Exception as e:
            print("Ekleyemedim3")
            print(e)
    cursor.close()
    connection.close()
    return


def parseSpotifyLink(url):
    # Parse the URL
    parsed_url = urlparse(url)

    songQuery = parsed_url.path[7:]
    base_url = 'https://api.spotify.com/v1/'
    headers = {
        'Authorization': 'Bearer {}'.format(access_token)
    }
    featured_playlists_endpoint = f'tracks/{songQuery}'

    featured_playlists_url = ''.join([base_url, featured_playlists_endpoint])
    response = requests.get(featured_playlists_url, headers=headers)
    response = response.json()
    return response["album"]["id"]


parser = argparse.ArgumentParser()
parser.add_argument("spotifyLink", type=str)
url = parser.parse_args().spotifyLink
access_token = getToken(client_id, client_secret)
albumID = parseSpotifyLink(url)
getAlbum(albumID)
