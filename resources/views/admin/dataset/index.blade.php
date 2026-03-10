@extends('layouts.admin')

@section('content')

<div class="section-header">
    <h1>Dataset Wajah</h1>
</div>

<div class="card">

    <div class="card-body text-center">

        <div class="camera-wrapper">

            <video
                id="video"
                width="420"
                height="320"
                autoplay
                muted
                class="camera-video"
            ></video>

            <canvas
                id="overlay"
                class="camera-overlay"
            ></canvas>

        </div>

        <div class="mt-3">
            <h5 id="datasetCounter">
                Dataset : 0 / 30
            </h5>
        </div>

        <div class="mt-3">

            <button
                onclick="startCamera()"
                class="btn btn-primary"
            >
                Mulai Kamera
            </button>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/face-api.min.js') }}"></script>

<script>

const video  = document.getElementById('video')
const canvas = document.getElementById('overlay')

let datasetCount = 0
const maxDataset = 30



/* ===============================
   LOAD FACE API MODEL
=============================== */

async function loadModels(){

    await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector')

    await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68')

    await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition')

    console.log("MODEL LOADED")

}



/* ===============================
   START CAMERA
=============================== */

async function startCamera(){

    await loadModels()

    const stream = await navigator.mediaDevices.getUserMedia({
        video : true
    })

    video.srcObject = stream

}



/* ===============================
   FACE DETECTION
=============================== */

video.addEventListener('play', () => {

    const displaySize = {

        width  : video.width,
        height : video.height

    }

    canvas.width  = video.width
    canvas.height = video.height

    faceapi.matchDimensions(canvas, displaySize)



    setInterval(async () => {

        if(datasetCount >= maxDataset) return

        const detections = await faceapi
            .detectAllFaces(
                video,
                new faceapi.TinyFaceDetectorOptions()
            )
            .withFaceLandmarks()
            .withFaceDescriptors()

        const resized = faceapi.resizeResults(detections, displaySize)

        const ctx = canvas.getContext('2d')
        ctx.clearRect(0,0,canvas.width,canvas.height)

        faceapi.draw.drawDetections(canvas,resized)

        if(detections.length > 0){

            const descriptor = detections[0].descriptor

            await saveDataset(descriptor)

            datasetCount++

            document.getElementById('datasetCounter').innerText =
            `Dataset : ${datasetCount} / ${maxDataset}`

        }

    },1500)

})



/* ===============================
   SAVE DATASET TO DATABASE
=============================== */

async function saveDataset(descriptor){

    try{

        const response = await fetch("{{ route('admin.dataset.store') }}",{

            method : "POST",

            headers : {

                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content

            },

            body : JSON.stringify({

                pegawai_id : 2,
                descriptor : Array.from(descriptor)

            })

        })


        const text = await response.text()

        console.log("SERVER RESPONSE:",text)

        const result = JSON.parse(text)

        console.log("DATASET SAVED:",result)

    }

    catch(error){

        console.error("SAVE ERROR:",error)

    }

}

</script>



<style>

.camera-wrapper{

    position:relative;
    display:inline-block;

}

.camera-video{

    border-radius:10px;

}

.camera-overlay{

    position:absolute;
    top:0;
    left:0;

}

</style>

@endpush