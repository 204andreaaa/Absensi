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
            @if($datasetCount >= $minDataset)
                <div class="alert alert-success mb-0">
                    Dataset wajah sudah lengkap.<br>
                    Anda sudah dapat melakukan absensi.
                </div>
            @else
                <div class="alert alert-warning text-left">
                    Dataset wajah Anda belum lengkap atau telah dihapus admin.
                    Silakan lengkapi minimal {{ $minDataset }} data wajah terlebih dahulu agar bisa mengakses semua fitur.
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="mb-4 p-3 rounded text-left" style="background: #eef6ff; border: 1px solid #d6e7ff;">
                            <div class="text-muted small text-uppercase font-weight-bold mb-2">
                                Arahan Pengambilan
                            </div>
                            <h5 id="instructionTitle" class="font-weight-bold mb-2">
                                Dekatkan wajah ke kamera
                            </h5>
                            <p id="instructionText" class="mb-0 text-muted">
                                Pastikan hanya satu wajah terlihat dan pencahayaan cukup terang.
                            </p>
                        </div>

                        <div class="mb-4 p-3 rounded" style="background: #f6f9fc;">
                            <div class="text-muted small text-uppercase font-weight-bold mb-2">
                                Progress Dataset
                            </div>
                            <h4 id="datasetCounter" class="font-weight-bold mb-3">
                                Dataset: {{ $datasetCount }} / {{ $minDataset }}
                            </h4>
                            <div class="progress" style="height: 10px; border-radius: 999px;">
                                <div
                                    id="datasetProgress"
                                    class="progress-bar bg-success"
                                    role="progressbar"
                                    style="width: {{ ($datasetCount / $minDataset) * 100 }}%;"
                                ></div>
                            </div>
                        </div>

                        <div class="camera-wrapper">
                            <video
                                id="video"
                                width="640"
                                height="480"
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
    @if($datasetCount < $minDataset)
        <script src="{{ asset('js/face-api.min.js') }}"></script>

        <script>
            const video = document.getElementById('video');
            const canvas = document.getElementById('overlay');
            const datasetCounter = document.getElementById('datasetCounter');
            const datasetProgress = document.getElementById('datasetProgress');
            const instructionTitle = document.getElementById('instructionTitle');
            const instructionText = document.getElementById('instructionText');

            let datasetCount = {{ $datasetCount }};
            const maxDataset = {{ $minDataset }};
            let captureInterval = null;
            let isSaving = false;
            const savedDescriptors = [];
            const minimumDetectionScore = 0.68;
            const minimumFaceSizeRatio = 0.24;
            const maximumFaceSizeRatio = 0.9;
            const minimumDescriptorDistance = 0.16;
            const centerToleranceRatio = 0.34;
            const captureSteps = [
                { pose: 'depan', title: 'Dekatkan wajah ke kamera', text: 'Posisikan wajah lurus ke depan dan isi area tengah kamera.' },
                { pose: 'depan', title: 'Tetap lihat depan', text: 'Jaga wajah tetap stabil, jangan terlalu jauh dari kamera.' },
                { pose: 'depan', title: 'Lihat depan dengan cahaya cukup', text: 'Pastikan wajah terang dan tidak tertutup bayangan.' },
                { pose: 'depan', title: 'Pertahankan wajah depan', text: 'Jangan ada wajah lain masuk ke dalam frame.' },
                { pose: 'depan', title: 'Satu lagi posisi depan', text: 'Tetap netral, jangan menunduk atau mendongak.' },
                { pose: 'kiri', title: 'Tengok kiri', text: 'Putar kepala sedikit ke kiri, jangan terlalu ekstrem.' },
                { pose: 'kiri', title: 'Pertahankan ke kiri', text: 'Pastikan mata dan hidung masih terlihat jelas.' },
                { pose: 'kiri', title: 'Sedikit kiri lagi', text: 'Jaga wajah tetap dekat ke kamera saat menoleh kiri.' },
                { pose: 'kiri', title: 'Tahan pose kiri', text: 'Cahaya tetap harus cukup terang pada wajah.' },
                { pose: 'kiri', title: 'Terakhir pose kiri', text: 'Pastikan wajah tidak blur saat tertangkap kamera.' },
                { pose: 'kanan', title: 'Tengok kanan', text: 'Putar kepala sedikit ke kanan, tetap santai.' },
                { pose: 'kanan', title: 'Pertahankan ke kanan', text: 'Usahakan wajah masih berada di tengah frame.' },
                { pose: 'kanan', title: 'Sedikit kanan lagi', text: 'Jangan terlalu menunduk saat menoleh ke kanan.' },
                { pose: 'kanan', title: 'Tahan pose kanan', text: 'Biarkan sistem menyimpan beberapa variasi sudut kanan.' },
                { pose: 'kanan', title: 'Terakhir pose kanan', text: 'Setelah tersimpan, dataset akan selesai otomatis.' }
            ];

            function getCenterPoint(points) {
                const total = points.reduce((accumulator, point) => {
                    accumulator.x += point.x;
                    accumulator.y += point.y;

                    return accumulator;
                }, { x: 0, y: 0 });

                return {
                    x: total.x / points.length,
                    y: total.y / points.length
                };
            }

            function detectHeadDirection(landmarks) {
                const nose = landmarks.getNose();
                const leftEyeCenter = getCenterPoint(landmarks.getLeftEye());
                const rightEyeCenter = getCenterPoint(landmarks.getRightEye());
                const eyeCenterX = (leftEyeCenter.x + rightEyeCenter.x) / 2;
                const noseTipX = nose[3].x;
                const diff = noseTipX - eyeCenterX;

                if (diff > 12) return 'kiri';
                if (diff < -12) return 'kanan';

                return 'depan';
            }

            function updateInstruction() {
                const currentStep = captureSteps[Math.min(datasetCount, captureSteps.length - 1)];

                instructionTitle.innerText = currentStep.title;
                instructionText.innerText = currentStep.text;
            }

            function getFaceSizeLimits(displaySize) {
                const baseSize = Math.min(displaySize.width, displaySize.height);

                return {
                    minimum: baseSize * minimumFaceSizeRatio,
                    maximum: baseSize * maximumFaceSizeRatio
                };
            }

            function isFaceCloseEnough(box, displaySize) {
                return box.width >= getFaceSizeLimits(displaySize).minimum && box.height >= getFaceSizeLimits(displaySize).minimum;
            }

            function isFaceTooClose(box, displaySize) {
                return box.width > getFaceSizeLimits(displaySize).maximum || box.height > getFaceSizeLimits(displaySize).maximum;
            }

            function isFaceCentered(box, displaySize) {
                const faceCenterX = box.x + (box.width / 2);
                const faceCenterY = box.y + (box.height / 2);
                const centerX = displaySize.width / 2;
                const centerY = displaySize.height / 2;
                const toleranceX = displaySize.width * centerToleranceRatio;
                const toleranceY = displaySize.height * centerToleranceRatio;

                return Math.abs(faceCenterX - centerX) <= toleranceX && Math.abs(faceCenterY - centerY) <= toleranceY;
            }

            function descriptorDistance(firstDescriptor, secondDescriptor) {
                let total = 0;

                for (let index = 0; index < firstDescriptor.length; index++) {
                    const diff = firstDescriptor[index] - secondDescriptor[index];
                    total += diff * diff;
                }

                return Math.sqrt(total);
            }

            function isDescriptorTooSimilar(descriptor) {
                return savedDescriptors.some((savedDescriptor) => {
                    return descriptorDistance(savedDescriptor, descriptor) < minimumDescriptorDistance;
                });
            }

            function mirrorBox(box, displayWidth) {
                return {
                    x: displayWidth - box.x - box.width,
                    y: box.y,
                    width: box.width,
                    height: box.height
                };
            }

            async function loadModels() {
                await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition');
            }

            async function startCamera() {
                await loadModels();

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                });

                video.srcObject = stream;
                video.setAttribute('playsinline', 'true');
                video.muted = true;
                try { await video.play(); } catch (error) { console.log(error); }
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
                updateInstruction();
            }

            video.addEventListener('play', () => {
                const displaySize = {
                    width: video.width,
                    height: video.height
                };

                canvas.width = video.width;
                canvas.height = video.height;

                faceapi.matchDimensions(canvas, displaySize);
                updateInstruction();

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
                    resized.forEach((detection) => {
                        new faceapi.draw.DrawBox(mirrorBox(detection.detection.box, displaySize.width)).draw(canvas);
                    });

                    if (detections.length === 1 && !isSaving) {
                        const currentStep = captureSteps[Math.min(datasetCount, captureSteps.length - 1)];
                        const detection = detections[0];
                        const resizedDetection = resized[0];
                        const headDirection = detectHeadDirection(detection.landmarks);
                        const faceBox = resizedDetection.detection.box;
                        const detectionScore = detection.detection.score || 0;

                        if (!isFaceCloseEnough(faceBox, displaySize)) {
                            instructionTitle.innerText = 'Dekatkan wajah ke kamera';
                            instructionText.innerText = 'Wajah masih terlalu jauh. Dekatkan sedikit lalu tahan posisi.';
                            return;
                        }

                        if (isFaceTooClose(faceBox, displaySize)) {
                            instructionTitle.innerText = 'Mundur sedikit';
                            instructionText.innerText = 'Wajah terlalu dekat. Beri jarak sedikit agar seluruh wajah terbaca jelas.';
                            return;
                        }

                        if (!isFaceCentered(faceBox, displaySize)) {
                            instructionTitle.innerText = 'Tengah dulu';
                            instructionText.innerText = 'Posisikan wajah di tengah frame sebelum dataset disimpan.';
                            return;
                        }

                        if (detectionScore < minimumDetectionScore) {
                            instructionTitle.innerText = 'Perjelas wajah';
                            instructionText.innerText = 'Deteksi wajah belum cukup yakin. Cari cahaya lebih terang dan tahan posisi.';
                            return;
                        }

                        if (headDirection !== currentStep.pose) {
                            if (currentStep.pose === 'depan') {
                                instructionTitle.innerText = 'Lihat lurus ke depan';
                            } else if (currentStep.pose === 'kiri') {
                                instructionTitle.innerText = 'Arahkan wajah ke kiri';
                            } else {
                                instructionTitle.innerText = 'Arahkan wajah ke kanan';
                            }

                            instructionText.innerText = currentStep.text;
                            return;
                        }

                        isSaving = true;

                        const descriptor = detection.descriptor;

                        if (isDescriptorTooSimilar(descriptor)) {
                            instructionTitle.innerText = 'Variasi kurang';
                            instructionText.innerText = 'Pose terlalu mirip dengan data sebelumnya. Ubah sedikit sudut wajah lalu tahan.';
                            setTimeout(() => {
                                isSaving = false;
                            }, 900);
                            return;
                        }

                        await saveDataset(descriptor);
                        savedDescriptors.push(new Float32Array(descriptor));

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

    @if($forceDatasetRegistration || $datasetCount < $minDataset)
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Daftarkan Dataset Dulu',
                text: 'Dataset wajah Anda belum lengkap atau telah dihapus admin. Lengkapi dataset terlebih dahulu sebelum mengakses fitur lain.',
                confirmButtonText: 'Mengerti',
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        </script>
    @endif

    <style>
        .camera-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 640px;
            aspect-ratio: 4 / 3;
            border-radius: 22px;
            overflow: hidden;
            background: #081120;
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.18);
        }

        .camera-video {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
@endpush
