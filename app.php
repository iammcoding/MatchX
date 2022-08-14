<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Matchx</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  </head>
  <body class="bg-warning">
    <div class="container main_area main_ar">
       <div class="d-flex">

     <div class="container">
     <h2 class="text-center text-bold">MatchX</h2>

<center> <div class="container mt-5">
<button class="btn btn-outline match_btn rounded-circle shadow-md shadow"><img width="250" style="filter: brightness(19.5);" height="250" src="https://i.pinimg.com/originals/6e/77/6d/6e776da81e522b2fa8b3d00b8d7f69de.png"/></button>
</div></center>
</div>

<div class="container song" style="padding: 20px;display:none">

 <a href="#" class="text-center text-white mb-4 showm" style=" display:none ; text-decoration: none;" id="match_btn">Match Again</a>

<div class="d-flex gap-2 song_ar">
<img src="" id="img" width="200" class="rounded mb-3">
 
<div>
<p><span  style="font-weight:bold">Artists:</span> <span id="artists">-</span></p>
<p><span  style="font-weight:bold">Title:</span> <span id="title">-</span></p>
<p><span  style="font-weight:bold">Year:</span> <span id="year">-</span></p>
</div>
 
</div>
<div id="lyrics" style="font-size:20px;    overflow-y: scroll;
    height: 600px;">

</div>

</div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

    <div style="display:none">
		<h2>Audio record and playback</h2>
    <p>
       <label for="audioSource">Audio input source: </label>
       <select id="audioSource"></select>
    </p>
		<p>
			<button id=startRecord>start</button>
			<button id=stopRecord disabled>stop</button>
		</p>	
		<p>
            <input type="hidden" id="token" value="yCNtAwI6xp8M6oaEzN656eugmd7Nt3ssp5gyRBA0"/>
			<audio id=recordedAudio></audio>
			<a id=audioDownload></a>
		</p>
</div>

<script>

const audioInputSelect = document.querySelector('select#audioSource');
const selectors = [audioInputSelect];
var query;

function go(){
    $(".song").hide();
    $(".match_btn").addClass("request-loader")
  
    $("#startRecord").click()
 


setTimeout(()=>{
    $("#stopRecord").click()
   

},7000)
}
function gotDevices(deviceInfos) {
  // Handles being called several times to update labels. Preserve values.
  const values = selectors.map(select => select.value);
  selectors.forEach(select => {
    while (select.firstChild) {
      select.removeChild(select.firstChild);
    }
  });
  for (let i = 0; i !== deviceInfos.length; ++i) {
    const deviceInfo = deviceInfos[i];
    const option = document.createElement('option');
    option.value = deviceInfo.deviceId;
    if (deviceInfo.kind === 'audioinput') {
      option.text = deviceInfo.label || `microphone ${audioInputSelect.length + 1}`;
      audioInputSelect.appendChild(option);
    }
  }
  selectors.forEach((select, selectorIndex) => {
    if (Array.prototype.slice.call(select.childNodes).some(n => n.value === values[selectorIndex])) {
      select.value = values[selectorIndex];
    }
  });
}

function gotStream(stream) {
  window.stream = stream; // make stream available to console

  rec = new MediaRecorder(stream);
  rec.ondataavailable = e => {
    audioChunks.push(e.data);
    if (rec.state == "inactive") {
      let blob = new Blob(audioChunks, {
        type: 'audio/x-mpeg-3'
      });
      


      var fd = new FormData(); 
      fd.append('file',  blob); 
      $.ajax({
          type: 'POST',
          url: '/match.php',
          data: fd,
          processData: false,
          contentType: false
      }).done(function(data) {
          // alert(data)
        var data = JSON.parse(data);
         if(data && Object.keys(data.matches).length > 0){
          $(".song").animate({width:'toggle'},350);
            $("#title").html(data.track.title)

            $("#artists").html(data.track.subtitle)
            $("#lyrics").html("")
            data.track.sections[1].text.map((res)=>{
                $("#lyrics").append(`<p>${res}</p>`)
            })
            data.track.sections[0].metadata.map((i,k)=>{
                if(i.title == "Released"){

                    $("#year").html(i.text)
                    return;
                }
            }) 
            
            // $("#img").atrr("src","https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif?20151024034921")
            $("#img").attr("src",data.track.images.coverarthq)
           
         }else{
            go()
         }
      

      }).fail(()=>{
   
      });
      recordedAudio.src = URL.createObjectURL(blob);
      recordedAudio.controls = false;
      recordedAudio.autoplay = false;
      audioDownload.href = recordedAudio.src;
      audioDownload.download = 'mp3';
      audioDownload.innerHTML = 'download';
    }
  }
}

function handleError(error) {
  console.log('navigator.MediaDevices.getUserMedia error: ', error.message, error.name);
}

function start() {
  // Second call to getUserMedia() with changed device may cause error, so we need to release stream before changing device
  if (window.stream) {
    stream.getAudioTracks()[0].stop();
  }

  const audioSource = audioInputSelect.value;

  const constraints = {
    audio: {
      deviceId: audioSource ? {
        exact: audioSource
      } : undefined
    }
  };

  navigator.mediaDevices.getUserMedia(constraints).then(gotStream).catch(handleError);
}

audioInputSelect.onchange = start;

startRecord.onclick = e => {
  startRecord.disabled = true;
  stopRecord.disabled = false;
  audioChunks = [];
  rec.start();
}
stopRecord.onclick = e => {
  startRecord.disabled = false;
  stopRecord.disabled = true;
  rec.stop();
}

$(document).ready(()=>{
        $(".match_btn,#match_btn").click(()=>{
            go()
        })  
});
navigator.mediaDevices.enumerateDevices()
  .then(gotDevices)
  .then(start)
  .catch(handleError);
  $(document).ajaxStart(function() {
    //   $("button").attr("disabled",true)
      $(".match_btn").addClass("request-loader")
});
$(document).ajaxComplete(function() { 
    $("button").attr("disabled",false)
    $(".match_btn").removeClass("request-loader")
});
</script>
</body>
</html>
<style>

html {
  height: 100%;
}
body {
  min-height: 100%;
}

.main_area{
    display:flex;
    flex-direction:column;
    justify-content:center;
    padding:100px;
}
 
.match_btn{ 
}

.match_btn:hover{
    border:0px  !important;
}

.match_btn{
    border:0px  !important;

    animation: breathing 2s ease-out infinite normal;
}
 

@keyframes breathing {
  0% {
    -webkit-transform: scale(0.9);
    -ms-transform: scale(0.9);
    transform: scale(0.9);
  }

  50% {
    -webkit-transform: scale(1);
    -ms-transform: scale(1);
    transform: scale(1);
  }

  100% {
    -webkit-transform: scale(0.9);
    -ms-transform: scale(0.9);
    transform: scale(0.9);
  }
}



 
.request-loader::after {
  opacity: 0;
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  position: absolute;
 
  right: 0;
  bottom: 0;
  content: "";
  height: 100%;
  width: 100%;
  border: 8px solid rgba(0, 0, 0, 0.2);
  border-radius: 100%;
  -webkit-animation-name: ripple;
          animation-name: ripple;
  -webkit-animation-duration: 3s;
          animation-duration: 3s;
  -webkit-animation-delay: 0s;
          animation-delay: 0s;
  -webkit-animation-iteration-count: infinite;
          animation-iteration-count: infinite;
  -webkit-animation-timing-function: cubic-bezier(0.65, 0, 0.34, 1);
          animation-timing-function: cubic-bezier(0.65, 0, 0.34, 1);
  z-index: -1;
}
 

@-webkit-keyframes ripple {
  from {
    opacity: 1;
    transform: scale3d(0.75, 0.75, 1);
  }
  to {
    opacity: 0;
    transform: scale3d(1.5, 1.5, 1);
  }
}

@keyframes ripple {
  from {
    opacity: 1;
    transform: scale3d(0.75, 0.75, 1);
  }
  to {
    opacity: 0;
    transform: scale3d(1.5, 1.5, 1);
  }
}

@media (max-width:480px){
  .main_area {
  
    padding-top: 100px  !important;
    padding:0;
}

.song_ar{
  flex-direction: column;
}

.main_ar >div{
  flex-direction: column-reverse;
}

 
.showm{
  display:block !important;
}
#img{
  width: 100%;
}
}



</style>
