

from shazamio import Shazam 
import asyncio
from scipy.io.wavfile import write
import sounddevice as sd
import wavio as wv
import sys
import json
file = sys.argv[1]

async def  run():
    shazam = Shazam()
    out =  await shazam.recognize_song(file)
    print(json.dumps(out))


loop = asyncio.get_event_loop()
loop.run_until_complete(run())