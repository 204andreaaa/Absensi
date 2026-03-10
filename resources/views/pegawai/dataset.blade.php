@extends('layouts.pegawai')

@section('content')
    <div class="section-header">
        <h1>Dataset Wajah Saya</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Perekaman Dataset</h4>
        </div>

        <div class="card-body text-center">
            @if($datasetCount >= 30)
                <div class="alert alert-success mb-0">
                    Dataset wajah sudah lengkap.<br>
                    Anda sudah dapat melakukan absensi.
                </div>
            @else
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="mb-4 p-3 rounded" style="background: #f6f9fc;">
                            <div class="text-muted small text-uppercase font-weight-bold mb-2">
                                Progress Dataset
                            </div>
                            <h4 id="datasetCounter" class="font-weight-bold mb-3">
                                Dataset: {{ $datasetCount }} / 30
                            </h4>
                            <div class="progress" style="height: 10px; border-radius: 999px;">
                                <div
                                    id="datasetProgress"
                                    class="progress-bar bg-success"
                                    role="progressbar"
                                    style="width: {{ ($datasetCount / 30) * 100 }}%;"
                                ></div>
                            </div>
                        </div>

                        <div class="camera-wrapper">
                            <video
                                id="video"
                                width="420"
                                height="320"
                                autoplay
                                muted
                                playsinline
                                class="camera-video"
                            ></video>

                            <canvas
                                id="overlay"
                                class="camera-overlay"
                            ></canvas>
                        </div>

                        <div class="mt-4">
                            <button onclick="startCamera()" class="btn btn-primary btn-block">
                                Mulai Kamera
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    @if($datasetCount < 30)
        <script src="{{ asset('js/face-api.min.js') }}"></script>

        <script>
            const video = document.getElementById('video');
            const canvas = document.getElementById('overlay');
            const datasetCounter = document.getElementById('datasetCounter');
            const datasetProgress = document.getElementById('datasetProgress');

            let datasetCount = {{ $datasetCount }};
            const maxDataset = 30;
            let captureInterval = null;
            let isSaving = false;

            async function loadModels() {
                await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition');
            }

            async function startCamera() {
                await loadModels();

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });

                video.srcObject = stream;
            }

            function stopCamera() {
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach((track) => track.stop());
                }

                if (captureInterval) {
                    clearInterval(captureInterval);
                }
            }

            function updateProgress() {
                datasetCounter.innerText = `Dataset: ${datasetCount} / ${maxDataset}`;
                datasetProgress.style.width = `${(datasetCount / maxDataset) * 100}%`;
            }

            video.addEventListener('play', () => {
                const displaySize = {
                    width: video.width,
                    height: video.height
                };

                canvas.width = video.width;
                canvas.height = video.height;

                faceapi.matchDimensions(canvas, displaySize);

                captureInterval = setInterval(async () => {
                    if (datasetCount >= maxDataset) {
                        stopCamera();

                        Swal.fire({
                            icon: 'success',
                            title: 'Dataset Berhasil',
                            text: 'Wajah berhasil direkam.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = "{{ route('pegawai.dashboard') }}";
                        });

                        return;
                    }

                    const detections = await faceapi
                        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceDescriptors();

                    const resized = faceapi.resizeResults(detections, displaySize);
                    const ctx = canvas.getContext('2d');

                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    faceapi.draw.drawDetections(canvas, resized);

                    if (detections.length > 0 && !isSaving) {
                        isSaving = true;

                        const descriptor = detections[0].descriptor;
                        await saveDataset(descriptor);

                        datasetCount++;
                        updateProgress();

                        setTimeout(() => {
                            isSaving = false;
                        }, 1200);
                    }
                }, 1500);
            });

            async function saveDataset(descriptor) {
                try {
                    await fetch("{{ route('pegawai.dataset.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            descriptor: Array.from(descriptor)
                        })
                    });
                } catch (error) {
                    console.error(error);
                }
            }
        </script>
    @endif

    <style>
        .camera-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 420px;
            border-radius: 22px;
            overflow: hidden;
            background: #081120;
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.18);
        }

        .camera-video {
            width: 100%;
            height: auto;
            display: block;
        }

        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
@endpush
