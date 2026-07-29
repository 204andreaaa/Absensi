@extends('layouts.admin')

@section('content')

<div class="section-header">
    <h1>Absensi Wajah</h1>
</div>

<div class="card shadow-sm">

<div class="card-header">
<h4 class="mb-0">Kamera Absensi</h4>
</div>

<div class="card-body">

<div class="text-center mb-4">

<button class="btn btn-success mr-2" onclick="pilihMode('masuk')">
Absen Masuk
</button>

<button class="btn btn-danger mr-2" onclick="pilihMode('keluar')">
Absen Keluar
</button>

<button class="btn btn-warning" onclick="resetAbsensiHariIni()">
Reset Absensi Hari Ini (Testing)
</button>

</div>

<div class="row justify-content-center">
<div class="col-lg-8 text-center">

<div class="position-relative d-inline-block border rounded overflow-hidden bg-dark">

<video
id="video"
width="420"
height="320"
autoplay
muted
playsinline
class="d-block"
style="max-width: 100%; height: auto; transform: scaleX(-1);"

> </video>

<canvas
id="overlay"
class="position-absolute"
style="top:0; left:0; pointer-events:none;"

> </canvas>

</div>

<div class="mt-4">

<h5 id="result" class="font-weight-bold mb-2">
Silakan pilih mode absensi
</h5>

<p id="gestureInfo" class="text-muted mb-0">
Pilih absen masuk atau keluar terlebih dahulu
</p>

</div>

<div
id="successPanel"
class="alert alert-success mt-4 mb-0 d-none"
role="alert"
>

<h5 class="alert-heading mb-2">Absensi Berhasil</h5>

<p id="successMessage" class="mb-0"></p>

</div>

</div>
</div>

</div>
</div>
@endsection

@push('scripts')

<script src="{{ asset('js/face-api.min.js') }}"></script>

<script>

const video = document.getElementById('video')
const overlay = document.getElementById('overlay')
const resultText = document.getElementById('result')
const gestureText = document.getElementById('gestureInfo')
const successPanel = document.getElementById('successPanel')
const successMessage = document.getElementById('successMessage')

let detectionInterval = null
let absenDone = false
let gestureStep = 0
let absensiMode = null
const faceMatchThreshold = 0.80

const gestures = [
'Hadap Kiri',
'Hadap Kanan',
'Buka Mulut'
]

function pilihMode(mode){

absensiMode = mode

resultText.innerText = "Mode Absensi : " + mode.toUpperCase()
gestureText.innerText = "Memuat kamera..."

start()

}

function updateGestureText(){

gestureText.innerText = "Silakan lakukan gesture: " + gestures[gestureStep]

}

function showSuccessState(nama){

resultText.innerText = "Absensi Berhasil"

gestureText.innerText = "Verifikasi gesture selesai"

successMessage.innerText = nama + " berhasil melakukan absensi " + absensiMode + "."

successPanel.classList.remove('d-none')

}

function stopCamera(){

if(video.srcObject){

video.srcObject.getTracks().forEach(track=>{
track.stop()
})

}

if(detectionInterval){

clearInterval(detectionInterval)

}

}

function showSuccessAlert(nama){

Swal.fire({
icon:'success',
title:'Absensi Berhasil',
text:nama + ' berhasil melakukan absensi ' + absensiMode,
confirmButtonText:'OK'
})

}

function getCenterPoint(points){

const total = points.reduce((accumulator, point)=>{

accumulator.x += point.x
accumulator.y += point.y

return accumulator

},{x:0,y:0})

return{
x: total.x / points.length,
y: total.y / points.length
}

}

function detectHeadDirection(landmarks){

const nose = landmarks.getNose()

const leftEyeCenter = getCenterPoint(landmarks.getLeftEye())

const rightEyeCenter = getCenterPoint(landmarks.getRightEye())

const eyeCenterX = (leftEyeCenter.x + rightEyeCenter.x) / 2

const noseTipX = nose[3].x

const diff = noseTipX - eyeCenterX

if(diff > 12) return 'kiri'

if(diff < -12) return 'kanan'

return 'tengah'

}

function mouthOpen(landmarks){

const mouth = landmarks.getMouth()

const top = mouth[13].y
const bottom = mouth[19].y

return bottom - top > 15

}

function formatMatchLabel(match){

if(match.label === 'unknown'){
return 'Unknown'
}

const normalizedDistance = Math.max(
0,
Math.min(1.5, match.distance / faceMatchThreshold)
)

const confidence = Math.max(
0,
Math.min(100, Math.round(100 - (normalizedDistance * 20)))
)

return match.label + " (" + confidence + "%)"

}

function isMatchValid(match){

return match.label !== 'unknown' && match.distance <= faceMatchThreshold

}

function mirrorBox(box, displayWidth){

return {
x: displayWidth - box.x - box.width,
y: box.y,
width: box.width,
height: box.height
}

}

function beginFaceDetection(faceMatcher){

const displaySize = {
width: video.clientWidth || video.width,
height: video.clientHeight || video.height
}

overlay.width = displaySize.width
overlay.height = displaySize.height

overlay.style.width = displaySize.width + "px"
overlay.style.height = displaySize.height + "px"

faceapi.matchDimensions(overlay,displaySize)

updateGestureText()

if(detectionInterval){
clearInterval(detectionInterval)
}

detectionInterval = setInterval(async()=>{

const detections = await faceapi
.detectAllFaces(video,new faceapi.TinyFaceDetectorOptions())
.withFaceLandmarks()
.withFaceDescriptors()

const resizedDetections = faceapi.resizeResults(detections,displaySize)

const context = overlay.getContext('2d')

context.clearRect(0,0,overlay.width,overlay.height)

if(!resizedDetections.length){

resultText.innerText = "Wajah belum terdeteksi"

return

}

resizedDetections.forEach((detection)=>{

const match = faceMatcher.findBestMatch(detection.descriptor)

const label = formatMatchLabel(match)
const matchIsValid = isMatchValid(match)

const drawBox = new faceapi.draw.DrawBox(mirrorBox(detection.detection.box, displaySize.width),{
label
})

drawBox.draw(overlay)

resultText.innerText = "Terdeteksi : " + label

const landmarks = detection.landmarks

if(!matchIsValid){

gestureStep = 0
gestureText.innerText = "Wajah tidak dikenali. Dekatkan wajah, cari cahaya yang cukup, atau ulangi perekaman dataset."

return

}

if(gestureStep === 0){

if(detectHeadDirection(landmarks)==='kiri'){

gestureStep++
updateGestureText()

}

}

else if(gestureStep === 1){

if(detectHeadDirection(landmarks)==='kanan'){

gestureStep++
updateGestureText()

}

}

else if(gestureStep === 2){

if(mouthOpen(landmarks) && !absenDone){

absenDone = true
gestureStep++

const idMatch = match.label.match(/ID:\s*(\d+)/)

const labelParts = match.label.split('|')

const namaPegawai = labelParts[0].trim()

showSuccessState(namaPegawai)

if(idMatch){

const pegawaiId = idMatch[1]

absenPegawai(pegawaiId)

}

stopCamera()

showSuccessAlert(namaPegawai)

}

}

})

},250)

}

async function loadModels(){

await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector')

await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68')

await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition')

}

async function startCamera(){

const stream = await navigator.mediaDevices.getUserMedia({
video:true
})

video.srcObject = stream

}

async function loadDataset(){

const response = await fetch('/admin/dataset/load')

const data = await response.json()

const groupedDescriptors = {}

data.forEach((item)=>{

if(!groupedDescriptors[item.pegawai_id]){

groupedDescriptors[item.pegawai_id] = {
label:item.pegawai_nama || "Pegawai #" + item.pegawai_id,
descriptors:[]
}

}

groupedDescriptors[item.pegawai_id].descriptors.push(
new Float32Array(item.descriptor)
)

})

return Object.keys(groupedDescriptors).map((id)=>{

const item = groupedDescriptors[id]

return new faceapi.LabeledFaceDescriptors(
item.label + " | ID: " + id,
item.descriptors
)

})

}

async function absenPegawai(id){

try{

const response = await fetch("{{ route('admin.absensi.store') }}",{

method:'POST',

headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content
},

body:JSON.stringify({
pegawai_id:id,
mode:absensiMode
})

})

const result = await response.json()

if(result.status){

successMessage.innerText += " Data absensi berhasil disimpan."

}else{

resultText.innerText = result.message || "Absensi gagal disimpan"

}

}catch(error){

console.log(error)

resultText.innerText = "Terjadi kesalahan saat menyimpan absensi"

}

}

async function resetAbsensiHariIni(){
    if(!confirm("Yakin ingin menghapus seluruh data absensi hari ini untuk uji coba?")){
        return;
    }

    try{
        const response = await fetch("{{ route('admin.absensi.reset-hari-ini') }}",{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content
            }
        });
        
        const result = await response.json();
        
        if(result.status){
            Swal.fire({
                icon:'success',
                title:'Berhasil',
                text:result.message,
                confirmButtonText:'OK'
            }).then(() => {
                location.reload();
            });
        }else{
            Swal.fire('Error', 'Gagal reset absensi', 'error');
        }
    }catch(error){
        console.error(error);
        Swal.fire('Error', 'Terjadi kesalahan saat mereset absensi', 'error');
    }
}

async function start(){

try{

resultText.innerText = "Memuat model deteksi wajah..."

await loadModels()

resultText.innerText = "Mengaktifkan kamera..."

await startCamera()

resultText.innerText = "Memuat dataset wajah..."

const labeledDescriptors = await loadDataset()

if(!labeledDescriptors.length){

resultText.innerText = "Dataset wajah belum tersedia"

return

}

const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors,faceMatchThreshold)

if(video.readyState >= 2){

beginFaceDetection(faceMatcher)

return

}

video.addEventListener('loadeddata',()=>{

beginFaceDetection(faceMatcher)

},{once:true})

}catch(error){

console.error(error)

resultText.innerText = "Gagal memulai absensi wajah"

}

}

</script>

@endpush
