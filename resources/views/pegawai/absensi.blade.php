@extends('layouts.pegawai')

@section('content')
    <div class="section-header">
        <h1>Absensi Wajah</h1>
    </div>

    @if($datasetCount < $minDataset)
        <div class="alert alert-warning">
            Dataset wajah Anda belum lengkap. Silakan daftar dan lengkapi dataset terlebih dahulu sebelum melakukan absensi.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Kamera Absensi</h4>
        </div>

        <div class="card-body">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="p-2 rounded-pill" style="background: #f6f9fc;">
                        <div class="row no-gutters">
                            <div class="col-6 pr-1">
                                <button class="btn btn-success btn-block" onclick="pilihMode('masuk')">
                                    Absen Masuk
                                </button>
                            </div>
                            <div class="col-6 pl-1">
                                <button class="btn btn-danger btn-block" onclick="pilihMode('keluar')">
                                    Absen Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="position-relative d-inline-block border rounded overflow-hidden bg-dark shadow-sm">
                        <video
                            id="video"
                            width="420"
                            height="320"
                            autoplay
                            muted
                            playsinline
                            class="d-block"
                            style="max-width: 100%; height: auto;"
                        ></video>

                        <canvas
                            id="overlay"
                            class="position-absolute"
                            style="top: 0; left: 0; pointer-events: none;"
                        ></canvas>
                    </div>

                    <div class="mt-4">
                        <h5 id="result" class="font-weight-bold mb-2">
                            Silakan pilih mode absensi
                        </h5>

                        <p id="gestureInfo" class="text-muted mb-0">
                            Pilih absen masuk atau keluar terlebih dahulu
                        </p>
                    </div>

                    <div class="mt-4 p-3 rounded" style="background: #f6f9fc;">
                        <div class="text-muted small text-uppercase font-weight-bold mb-2">
                            Alur Verifikasi
                        </div>
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 10px;">
                            <span class="badge badge-light px-3 py-2">1. Hadap Kiri</span>
                            <span class="badge badge-light px-3 py-2">2. Hadap Kanan</span>
                            <span class="badge badge-light px-3 py-2">3. Buka Mulut</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/face-api.min.js') }}"></script>

    <script>
        const video = document.getElementById('video');
        const overlay = document.getElementById('overlay');
        const resultText = document.getElementById('result');
        const gestureText = document.getElementById('gestureInfo');

        let detectionInterval = null;
        let absenDone = false;
        let gestureStep = 0;
        let absensiMode = null;
        let isStarting = false;
        const minimumConfidence = 60;

        const gestures = [
            'Hadap Kiri',
            'Hadap Kanan',
            'Buka Mulut'
        ];

        function pilihMode(mode) {
            if (isStarting) {
                return;
            }

            if ({{ $datasetCount }} < {{ $minDataset }}) {
                resultText.innerText = 'Dataset wajah belum lengkap';
                gestureText.innerText = 'Silakan lengkapi dataset terlebih dahulu di menu Dataset Wajah';
                showErrorAlert('Dataset wajah belum lengkap. Silakan daftar dataset terlebih dahulu.');
                return;
            }

            absensiMode = mode;
            absenDone = false;
            gestureStep = 0;

            resultText.innerText = `Mode Absensi: ${mode.toUpperCase()}`;
            gestureText.innerText = 'Memuat kamera...';

            start();
        }

        function updateGestureText() {
            gestureText.innerText = `Silakan lakukan gesture: ${gestures[gestureStep]}`;
        }

        function getFriendlyCameraError(error) {
            if (!window.isSecureContext) {
                return 'Kamera HP butuh HTTPS atau localhost. Jika dibuka dari IP jaringan, aktifkan HTTPS.';
            }

            if (error.name === 'NotAllowedError') {
                return 'Izin kamera ditolak. Izinkan akses kamera di browser HP Anda.';
            }

            if (error.name === 'NotFoundError') {
                return 'Kamera depan tidak ditemukan di perangkat ini.';
            }

            if (error.name === 'NotReadableError') {
                return 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi kamera lalu coba lagi.';
            }

            return error.message || 'Periksa dataset wajah atau izin kamera Anda';
        }

        function stopCamera() {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach((track) => {
                    track.stop();
                });
                video.srcObject = null;
            }

            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }
        }

        function showSuccessAlert(message) {
            Swal.fire({
                icon: 'success',
                title: 'Absensi Berhasil',
                text: message || `Absensi ${absensiMode} berhasil`,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('pegawai.dashboard') }}";
            });
        }

        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Absensi Gagal',
                text: message,
                confirmButtonText: 'OK'
            });
        }

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

            if (diff > 12) return 'kanan';
            if (diff < -12) return 'kiri';

            return 'tengah';
        }

        function mouthOpen(landmarks) {
            const mouth = landmarks.getMouth();
            const top = mouth[13].y;
            const bottom = mouth[19].y;

            return bottom - top > 15;
        }

        function getMatchConfidence(match) {
            return Math.max(
                0,
                Math.min(100, Math.round((1 - match.distance) * 100))
            );
        }

        function isMatchValid(match) {
            if (match.label === 'unknown') {
                return false;
            }

            return getMatchConfidence(match) >= minimumConfidence;
        }

        function formatMatchLabel(match) {
            if (match.label === 'unknown') {
                return 'Unknown';
            }

            const confidence = getMatchConfidence(match);

            return `${match.label} (${confidence}%)`;
        }

        function beginFaceDetection(faceMatcher) {
            const displaySize = {
                width: video.clientWidth || video.width,
                height: video.clientHeight || video.height
            };

            overlay.width = displaySize.width;
            overlay.height = displaySize.height;
            overlay.style.width = `${displaySize.width}px`;
            overlay.style.height = `${displaySize.height}px`;

            faceapi.matchDimensions(overlay, displaySize);
            updateGestureText();

            if (detectionInterval) {
                clearInterval(detectionInterval);
            }

            detectionInterval = setInterval(async () => {
                const detections = await faceapi
                    .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptors();

                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                const context = overlay.getContext('2d');

                context.clearRect(0, 0, overlay.width, overlay.height);

                if (!resizedDetections.length) {
                    resultText.innerText = 'Wajah belum terdeteksi';
                    return;
                }

                resizedDetections.forEach((detection) => {
                    const match = faceMatcher.findBestMatch(detection.descriptor);
                    const matchIsValid = isMatchValid(match);
                    const label = matchIsValid
                        ? formatMatchLabel(match)
                        : `Unknown (${getMatchConfidence(match)}%)`;
                    const drawBox = new faceapi.draw.DrawBox(detection.detection.box, {
                        label
                    });

                    drawBox.draw(overlay);
                    resultText.innerText = `Terdeteksi: ${label}`;

                    const landmarks = detection.landmarks;

                    if (!matchIsValid) {
                        gestureStep = 0;
                        gestureText.innerText = `Wajah tidak dikenali atau confidence di bawah ${minimumConfidence}%`;
                        return;
                    }

                    if (gestureStep === 0) {
                        if (detectHeadDirection(landmarks) === 'kiri') {
                            gestureStep++;
                            updateGestureText();
                        }
                    } else if (gestureStep === 1) {
                        if (detectHeadDirection(landmarks) === 'kanan') {
                            gestureStep++;
                            updateGestureText();
                        }
                    } else if (gestureStep === 2) {
                        if (mouthOpen(landmarks) && !absenDone) {
                            absenDone = true;
                            gestureStep++;
                            stopCamera();

                            absenPegawai()
                                .then((message) => {
                                    resultText.innerText = 'Absensi Berhasil';
                                    gestureText.innerText = 'Data absensi berhasil disimpan';
                                    showSuccessAlert(message);
                                })
                                .catch((error) => {
                                    absenDone = false;
                                    gestureStep = 0;
                                    resultText.innerText = 'Absensi gagal disimpan';
                                    gestureText.innerText = error.message;
                                    showErrorAlert(error.message);
                                });
                        }
                    }
                });
            }, 250);
        }

        async function loadModels() {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition');
        }

        async function startCamera() {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user'
                },
                audio: false
            });

            video.srcObject = stream;
            video.setAttribute('playsinline', 'true');
            video.muted = true;

            try {
                await video.play();
            } catch (error) {
                console.log(error);
            }
        }

        async function loadDataset() {
            const response = await fetch('/pegawai/dataset/load');
            const data = await response.json();
            const groupedDescriptors = {};

            data.forEach((item) => {
                if (!groupedDescriptors[item.label]) {
                    groupedDescriptors[item.label] = [];
                }

                groupedDescriptors[item.label].push(new Float32Array(item.descriptor));
            });

            return Object.keys(groupedDescriptors).map((label) => (
                new faceapi.LabeledFaceDescriptors(label, groupedDescriptors[label])
            ));
        }

        async function absenPegawai() {
            const response = await fetch("{{ route('pegawai.absensi.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    mode: absensiMode
                })
            });

            const result = await response.json();

            if (!result.status) {
                throw new Error(result.message || 'Absensi gagal disimpan');
            }

            return result.message || `Absensi ${absensiMode} berhasil`;
        }

        async function start() {
            if (!absensiMode) {
                return;
            }

            isStarting = true;
            stopCamera();

            try {
                resultText.innerText = 'Memuat model...';
                await loadModels();

                resultText.innerText = 'Mengaktifkan kamera...';
                await startCamera();

                resultText.innerText = 'Memuat dataset...';
                const labeledDescriptors = await loadDataset();

                if (!labeledDescriptors.length) {
                    stopCamera();
                    resultText.innerText = 'Dataset wajah belum tersedia';
                    gestureText.innerText = 'Silakan rekam dataset wajah terlebih dahulu';
                    return;
                }

                const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);

                if (video.readyState >= 2) {
                    beginFaceDetection(faceMatcher);
                    return;
                }

                video.addEventListener('loadeddata', () => {
                    beginFaceDetection(faceMatcher);
                }, { once: true });
            } catch (error) {
                console.error(error);
                stopCamera();
                resultText.innerText = 'Gagal memulai absensi';
                gestureText.innerText = getFriendlyCameraError(error);
            } finally {
                isStarting = false;
            }
        }
    </script>
@endpush
