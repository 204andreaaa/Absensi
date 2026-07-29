@extends('layouts.admin')

@section('content')

<div class="section-header">
    <h1>Testing Liveness Detection</h1>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Pendeteksi Gerak Wajah Secara Live</h4>
            </div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <button class="btn btn-primary btn-lg" onclick="startLivenessTest()" id="btnStart">
                        <i class="fas fa-play"></i> Mulai Liveness Test
                    </button>
                    <button class="btn btn-danger btn-lg" onclick="stopLivenessTest()" id="btnStop" style="display:none;">
                        <i class="fas fa-stop"></i> Hentikan Tes
                    </button>
                </div>

                <div class="row">
                    <!-- CAMERA VIEW -->
                    <div class="col-lg-7 text-center">
                        <div class="position-relative d-inline-block border rounded overflow-hidden bg-dark mb-3 w-100">
                            <video
                                id="video"
                                width="640"
                                height="480"
                                autoplay
                                muted
                                playsinline
                                class="d-block w-100"
                                style="transform: scaleX(-1);"
                            ></video>

                            <canvas
                                id="overlay"
                                class="position-absolute"
                                style="top:0; left:0; pointer-events:none;"
                            ></canvas>
                        </div>
                        
                        <div id="status-container" class="alert alert-secondary mt-2">
                            <h5 id="result" class="font-weight-bold mb-0">
                                Klik tombol Mulai untuk memulai tes.
                            </h5>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <button class="btn btn-warning btn-lg font-weight-bold" onclick="captureLiveness()" id="btnCapture" style="display:none; width:100%;">
                                <i class="fas fa-camera"></i> Capture & Tampilkan Perhitungan
                            </button>
                            <button class="btn btn-success btn-lg font-weight-bold" onclick="resumeLiveness()" id="btnResume" style="display:none; width:100%;">
                                <i class="fas fa-play"></i> Lanjutkan Kamera
                            </button>
                        </div>
                    </div>
                    
                    <!-- LIVENESS INSTRUCTIONS -->
                    <div class="col-lg-5">
                        <div class="card border-primary mb-3 shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-camera"></i> Mode Dokumentasi</h5>
                                <div class="btn-group btn-group-sm w-100 mb-3" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="setManualChallenge(0)">Buka Mulut</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setManualChallenge(1)">Tengok Kiri</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setManualChallenge(2)">Tengok Kanan</button>
                                </div>
                                
                                <h5 class="text-muted mt-2">Instruksi Saat Ini:</h5>
                                <h3 id="current-instruction" class="text-dark my-3">-</h3>
                            </div>
                        </div>

                        <div class="bg-dark text-light p-3 rounded shadow-sm">
                            <h6 class="text-warning border-bottom pb-2 mb-3"><i class="fas fa-cogs"></i> Debugging Metrik (Real-time)</h6>
                            <ul class="list-unstyled mb-0" style="font-family: monospace; font-size: 14px;">
                                <li class="mb-2">MAR (Mouth Aspect Ratio): <span id="debug-mouth" class="text-info font-weight-bold">0.00</span></li>
                                <li class="mb-2">Delta X (Menoleh): <span id="debug-turn" class="text-info font-weight-bold">0.00</span> px</li>
                            </ul>
                            <small class="text-muted mt-3 d-block border-top pt-2 mt-2">
                                * Buka Mulut: MAR > 0.50<br>
                                * Menoleh Kiri: ΔX < -15<br>
                                * Menoleh Kanan: ΔX > 15
                            </small>
                        </div>

                        <!-- HASIL CAPTURE -->
                        <div id="capture-result" class="card border-success mt-3 shadow-sm" style="display:none;">
                            <div class="card-body">
                                <h5 class="text-success font-weight-bold border-bottom pb-2"><i class="fas fa-check-circle"></i> Hasil Perhitungan Liveness</h5>
                                <div id="capture-details" class="mt-3" style="font-family: monospace; font-size: 15px;"></div>
                            </div>
                        </div>
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
const statusContainer = document.getElementById('status-container');
const btnStart = document.getElementById('btnStart');
const btnStop = document.getElementById('btnStop');

// Liveness Elements
const currentInstructionEl = document.getElementById('current-instruction');
const livenessProgressEl = document.getElementById('liveness-progress');
const challengeStatusEl = document.getElementById('challenge-status');
const debugMouthEl = document.getElementById('debug-mouth');
const debugTurnEl = document.getElementById('debug-turn');

let detectionInterval = null;
let modelsLoaded = false;

// Liveness State
const challenges = [
    { type: 'mouth', text: 'Buka Mulut Anda', check: checkMouthOpen },
    { type: 'turn_left', text: 'Tengok ke Kiri', check: checkTurnLeft },
    { type: 'turn_right', text: 'Tengok ke Kanan', check: checkTurnRight }
];
let activeChallenges = challenges; // Pakai semua
let currentChallengeIndex = 0;
let isLivenessPassed = false;
let latestMar = 0;
let latestDeltaX = 0;
let latestM1X = 0, latestM2X = 0, latestHX = 0, latestCenterX = 0;

function setManualChallenge(index) {
    currentChallengeIndex = index;
    isLivenessPassed = false;
    updateChallengeUI();
}

// Math helpers
function getDistance(pt1, pt2) {
    return Math.sqrt(Math.pow(pt1.x - pt2.x, 2) + Math.pow(pt1.y - pt2.y, 2));
}

// Logic Liveness
function checkMouthOpen(landmarks) {
    const positions = landmarks.positions;
    // Karena kamera di-mirror, posisi "Kiri" di layar sebenarnya adalah "Kanan" pada landmark asli face-api.
    // Indeks face-api (Observer's perspective):
    // 60: Kiri luar (Visual Kanan)
    // 64: Kanan luar (Visual Kiri)
    // 61: Atas kiri (Visual Kanan)
    // 63: Atas kanan (Visual Kiri)
    // 67: Bawah kiri (Visual Kanan)
    // 65: Bawah kanan (Visual Kiri)
    
    const p1 = positions[64]; // Sudut kiri mulut
    const p2 = positions[60]; // Sudut kanan mulut
    const p3 = positions[63]; // Bibir atas (kiri-tengah)
    const p4 = positions[65]; // Bibir bawah (kiri-tengah)
    const p5 = positions[61]; // Bibir atas (kanan-tengah)
    const p6 = positions[67]; // Bibir bawah (kanan-tengah)

    const distHorizontal = getDistance(p1, p2); // ||P1 - P2||
    const distVertical1 = getDistance(p3, p4);  // ||P3 - P4||
    const distVertical2 = getDistance(p5, p6);  // ||P5 - P6||
    
    // Rumus MAR = (||P3 - P4|| + ||P5 - P6||) / (2 * ||P1 - P2||)
    const mar = distHorizontal > 0 ? (distVertical1 + distVertical2) / (2 * distHorizontal) : 0;
    
    latestMar = mar;
    debugMouthEl.innerText = mar.toFixed(3);
    
    return mar > 0.50; // Threshold MAR > 0.5 sesuai dokumen
}

function checkTurnLeft(landmarks) {
    const positions = landmarks.positions;
    const getCentroidX = (pts) => {
        let sumX = 0; pts.forEach(p => sumX += p.x); return sumX / pts.length;
    };
    const eye1X = getCentroidX(positions.slice(36, 42)); // Observer left
    const eye2X = getCentroidX(positions.slice(42, 48)); // Observer right
    const noseX = positions[30].x;
    
    // Pada canvas yang di-mirror, Delta X berbalik arah
    const deltaX = - (noseX - ((eye1X + eye2X) / 2));
    
    latestDeltaX = deltaX;
    debugTurnEl.innerText = deltaX.toFixed(2);
    
    // Threshold menoleh kiri -> ΔX < 0
    return deltaX < -15.0; 
}

function checkTurnRight(landmarks) {
    const positions = landmarks.positions;
    const getCentroidX = (pts) => {
        let sumX = 0; pts.forEach(p => sumX += p.x); return sumX / pts.length;
    };
    const eye1X = getCentroidX(positions.slice(36, 42)); // Observer left
    const eye2X = getCentroidX(positions.slice(42, 48)); // Observer right
    const noseX = positions[30].x;
    
    // Pada canvas yang di-mirror, Delta X berbalik arah
    const deltaX = - (noseX - ((eye1X + eye2X) / 2));
    
    latestDeltaX = deltaX;
    debugTurnEl.innerText = deltaX.toFixed(2);
    
    // Threshold menoleh kanan -> ΔX > 0
    return deltaX > 15.0; 
}

async function loadModels() {
    if (modelsLoaded) return;
    resultText.innerText = "Memuat model AI...";
    
    await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
    await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
    
    modelsLoaded = true;
    resultText.innerText = "Model dimuat. Mengaktifkan kamera...";
}

function generateChallenges() {
    activeChallenges = challenges;
    currentChallengeIndex = 0;
    isLivenessPassed = false;
    updateChallengeUI();
}

function updateChallengeUI() {
    const currentChallenge = activeChallenges[currentChallengeIndex];
    currentInstructionEl.innerText = currentChallenge.text;
    currentInstructionEl.className = 'text-primary my-4 font-weight-bold';
    
    statusContainer.classList.remove('alert-success', 'alert-secondary');
    statusContainer.classList.add('alert-info');
    resultText.innerText = "Silakan ikuti instruksi!";
}

async function startLivenessTest() {
    try {
        btnStart.disabled = true;
        
        await loadModels();
        
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        
        btnStart.style.display = 'none';
        btnStop.style.display = 'inline-block';
        document.getElementById('btnCapture').style.display = 'inline-block';
        document.getElementById('btnResume').style.display = 'none';
        document.getElementById('capture-result').style.display = 'none';
        btnStart.disabled = false;
        
        statusContainer.classList.remove('alert-secondary', 'alert-success');
        statusContainer.classList.add('alert-info');
        generateChallenges();
        
        if (video.readyState >= 2) {
            beginDetection();
        } else {
            video.addEventListener('loadeddata', () => {
                beginDetection();
            }, { once: true });
        }
    } catch (error) {
        console.error(error);
        resultText.innerText = "Gagal memulai kamera: " + error.message;
        btnStart.disabled = false;
    }
}

function stopLivenessTest() {
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }
    
    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }
    
    const ctx = overlay.getContext('2d');
    ctx.clearRect(0, 0, overlay.width, overlay.height);
    
    btnStart.style.display = 'inline-block';
    btnStop.style.display = 'none';
    document.getElementById('btnCapture').style.display = 'none';
    document.getElementById('btnResume').style.display = 'none';
    document.getElementById('capture-result').style.display = 'none';
    
    resultText.innerText = "Kamera dihentikan. Tes dibatalkan.";
    currentInstructionEl.innerText = "-";
    currentInstructionEl.className = "text-dark my-4";
    livenessProgressEl.style.width = '0%';
    livenessProgressEl.innerText = '0%';
    challengeStatusEl.innerText = "Tantangan: 0 / 2 Selesai";
    debugMouthEl.innerText = "0.00";
    debugTurnEl.innerText = "0.00";
    
    statusContainer.classList.remove('alert-success', 'alert-info');
    statusContainer.classList.add('alert-secondary');
}

function drawBoundingBoxMirrored(canvas, detections, displaySize, activeChallenge = null) {
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    detections.forEach(detection => {
        const box = detection.detection.box;
        const mirroredX = displaySize.width - (box.x + box.width);
        
        // Draw Box. Green if passed, Blue if testing
        ctx.strokeStyle = isLivenessPassed ? '#28a745' : '#00d2ff';
        ctx.lineWidth = 3;
        ctx.strokeRect(mirroredX, box.y, box.width, box.height);
        
        if(detection.landmarks) {
            const positions = detection.landmarks.positions;
            
            if (activeChallenge && activeChallenge.type === 'mouth') {
                // Custom MAR Visualization (Sesuai referensi dokumen)
                const getMirrored = (pt) => ({ x: displaySize.width - pt.x, y: pt.y });
                const p1 = getMirrored(positions[64]); // Kiri
                const p2 = getMirrored(positions[60]); // Kanan
                const p3 = getMirrored(positions[63]); // Atas Kiri
                const p4 = getMirrored(positions[65]); // Bawah Kiri
                const p5 = getMirrored(positions[61]); // Atas Kanan
                const p6 = getMirrored(positions[67]); // Bawah Kanan
                
                // Draw Horizontal Line (Green)
                ctx.beginPath();
                ctx.strokeStyle = '#00ff00';
                ctx.lineWidth = 2;
                ctx.moveTo(p1.x, p1.y);
                ctx.lineTo(p2.x, p2.y);
                ctx.stroke();
                
                // Draw Vertical Lines (Orange)
                ctx.beginPath();
                ctx.strokeStyle = '#ff8800';
                ctx.lineWidth = 2;
                ctx.moveTo(p3.x, p3.y);
                ctx.lineTo(p4.x, p4.y);
                ctx.stroke();
                
                ctx.beginPath();
                ctx.moveTo(p5.x, p5.y);
                ctx.lineTo(p6.x, p6.y);
                ctx.stroke();
                
                // Draw Points and Labels with very large offsets to avoid overlap
                const drawPoint = (pt, label, offsetX, offsetY) => {
                    const roundedX = Math.round(pt.x);
                    const roundedY = Math.round(pt.y);
                    const textLabel = `${label} (${roundedX}, ${roundedY})`;
                    
                    // Dot
                    ctx.fillStyle = '#ffe600'; // Yellow
                    ctx.beginPath();
                    ctx.arc(pt.x, pt.y, 4, 0, 2 * Math.PI);
                    ctx.fill();
                    
                    const boxX = pt.x + offsetX;
                    const boxY = pt.y + offsetY;
                    
                    // Draw a long, clear line from dot to label
                    ctx.beginPath();
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 1.5;
                    ctx.moveTo(pt.x, pt.y);
                    // Determine which side of the box to connect the line to
                    const connectX = offsetX < 0 ? boxX + 68 : boxX;
                    ctx.lineTo(connectX, boxY);
                    ctx.stroke();

                    // Text Box Background
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
                    ctx.fillRect(boxX, boxY - 10, 68, 16);
                    
                    // Text
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 10px Arial';
                    ctx.fillText(textLabel, boxX + 3, boxY + 2);
                };
                
                // Offset sangat besar agar tidak numpuk sama sekali
                drawPoint(p1, 'p1', -130, -10);  // Jauh ke kiri
                drawPoint(p2, 'p2', 60, -10);    // Jauh ke kanan
                drawPoint(p3, 'p3', -110, -50);  // Jauh ke atas kiri
                drawPoint(p5, 'p5', 40, -50);    // Jauh ke atas kanan
                drawPoint(p4, 'p4', -110, 50);   // Jauh ke bawah kiri
                drawPoint(p6, 'p6', 40, 50);     // Jauh ke bawah kanan

            } else if (activeChallenge && (activeChallenge.type === 'turn_left' || activeChallenge.type === 'turn_right')) {
                // Custom Delta X Visualization (Tengok Kiri / Kanan)
                const getMirrored = (pt) => ({ x: displaySize.width - pt.x, y: pt.y });
                const getCentroid = (pts) => {
                    let sumX = 0, sumY = 0;
                    pts.forEach(p => { sumX += p.x; sumY += p.y; });
                    return getMirrored({ x: sumX / pts.length, y: sumY / pts.length });
                };
                
                const m2 = getCentroid(positions.slice(36, 42)); // Mata Kanan (Visual), aslinya Observer Left
                const m1 = getCentroid(positions.slice(42, 48)); // Mata Kiri (Visual), aslinya Observer Right
                const h = getMirrored(positions[30]); // Hidung
                
                const centerX = (m1.x + m2.x) / 2;
                const deltaX = h.x - centerX;
                
                // Simpan untuk ditampilkan di hasil capture
                latestM1X = m1.x;
                latestM2X = m2.x;
                latestHX = h.x;
                latestCenterX = centerX;
                
                // Draw Garis Horizontal Mata (Biru putus-putus)
                ctx.beginPath();
                ctx.setLineDash([5, 5]);
                ctx.strokeStyle = '#007bff';
                ctx.lineWidth = 2;
                ctx.moveTo(m1.x, m1.y);
                ctx.lineTo(m2.x, m2.y);
                ctx.stroke();
                
                // Draw Garis Tengah Mata (dihapus sesuai permintaan)
                
                // Draw Garis Hidung (Kuning putus-putus)
                ctx.beginPath();
                ctx.strokeStyle = '#ffc107';
                ctx.moveTo(h.x, h.y - 40);
                ctx.lineTo(h.x, h.y + 40);
                ctx.stroke();
                
                ctx.setLineDash([]); // Reset dash for dots and text
                
                // Draw Points and Labels for Delta X
                const drawTurnPoint = (pt, label, color, offsetX, offsetY) => {
                    const roundedX = Math.round(pt.x);
                    const textLabel = `${label} (X: ${roundedX})`;
                    
                    ctx.fillStyle = color;
                    ctx.beginPath();
                    ctx.arc(pt.x, pt.y, 5, 0, 2 * Math.PI);
                    ctx.fill();
                    
                    const boxX = pt.x + offsetX;
                    const boxY = pt.y + offsetY;
                    
                    ctx.beginPath();
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 1;
                    ctx.moveTo(pt.x, pt.y);
                    const connectX = offsetX < 0 ? boxX + 80 : boxX;
                    ctx.lineTo(connectX, boxY);
                    ctx.stroke();

                    ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
                    ctx.fillRect(boxX, boxY - 10, 68, 16);
                    
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 10px Arial';
                    ctx.fillText(textLabel, boxX + 3, boxY + 2);
                };
                
                drawTurnPoint(m1, 'M1', '#ff0000', -120, -20); // Mata Kiri
                drawTurnPoint(m2, 'M2', '#ff0000', 40, -20);   // Mata Kanan
                drawTurnPoint(h, 'H', '#ffe600', 40, 20);      // Hidung
                
                // Tampilkan Nilai Delta X di bawah
                ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
                ctx.fillRect(centerX - 60, h.y + 60, 120, 25);
                ctx.fillStyle = deltaX > 15 ? '#ffc107' : (deltaX < -15 ? '#28a745' : '#ffffff');
                ctx.font = 'bold 14px Arial';
                ctx.fillText(`ΔX = ${deltaX.toFixed(1)}`, centerX - 45, h.y + 78);

            } else {
                // Draw Default Landmarks
                ctx.fillStyle = isLivenessPassed ? '#28a745' : '#00ff00';
                positions.forEach((point) => {
                    const mx = displaySize.width - point.x;
                    ctx.beginPath();
                    ctx.arc(mx, point.y, 1.5, 0, 2 * Math.PI);
                    ctx.fill();
                });
            }
        }
    });
}

function beginDetection() {
    const displaySize = {
        width: video.clientWidth || video.width,
        height: video.clientHeight || video.height
    };
    
    overlay.width = displaySize.width;
    overlay.height = displaySize.height;
    overlay.style.width = displaySize.width + "px";
    overlay.style.height = displaySize.height + "px";
    
    faceapi.matchDimensions(overlay, displaySize);
    
    resultText.innerText = "Mendeteksi wajah...";
    
    if (detectionInterval) {
        clearInterval(detectionInterval);
    }
    
    let successHoldFrames = 0;
    const REQUIRED_HOLD_FRAMES = 15; // 15 frames * 150ms = ~2.25 detik
    
    detectionInterval = setInterval(async () => {
        if (!video.srcObject) return;
        
        const detections = await faceapi
            .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks();
            
        const resizedDetections = faceapi.resizeResults(detections, displaySize);
        
        if (resizedDetections.length > 0) {
            
            // Logika Liveness Challenge
            if (!isLivenessPassed && currentChallengeIndex < activeChallenges.length) {
                const currentChallenge = activeChallenges[currentChallengeIndex];
                const passed = currentChallenge.check(resizedDetections[0].landmarks);
                
                if (passed) {
                    resultText.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Sesuai Instruksi! (Tahan untuk screenshot)</span>`;
                    // Dihapus auto-advance agar user bisa screenshot sepuasnya
                } else {
                    resultText.innerText = "Wajah terdeteksi. Silakan ikuti instruksi di layar!";
                }
            }
        } else {
            resultText.innerText = "Wajah tidak terdeteksi. Pastikan wajah berada di tengah kamera.";
            debugMouthEl.innerText = "0.00";
            debugTurnEl.innerText = "0.00";
        }
        
        const currentChallenge = (!isLivenessPassed && currentChallengeIndex < activeChallenges.length) 
            ? activeChallenges[currentChallengeIndex] 
            : null;
            
        drawBoundingBoxMirrored(overlay, resizedDetections, displaySize, currentChallenge);
        
    }, 150); // Frame rate
}

function captureLiveness() {
    video.pause();
    if(detectionInterval) clearInterval(detectionInterval);
    
    document.getElementById('btnCapture').style.display = 'none';
    document.getElementById('btnResume').style.display = 'inline-block';
    
    const resultDiv = document.getElementById('capture-result');
    const detailsDiv = document.getElementById('capture-details');
    resultDiv.style.display = 'block';
    
    const activeChallenge = activeChallenges[currentChallengeIndex];
    let html = '';
    
    if(activeChallenge.type === 'mouth') {
        html += `<p class="mb-1"><b>Mode:</b> Deteksi Buka Mulut</p>`;
        html += `<p class="mb-1"><b>Nilai MAR:</b> ${latestMar.toFixed(3)}</p>`;
        html += `<p class="mb-1"><b>Ambang Batas:</b> > 0.50</p>`;
        
        if(latestMar > 0.50) {
            html += `<div class="alert alert-success mt-3 py-2 px-3 mb-0"><b>STATUS: VALID</b><br>Sistem mendeteksi mulut terbuka.</div>`;
        } else {
            html += `<div class="alert alert-danger mt-3 py-2 px-3 mb-0"><b>STATUS: TIDAK VALID</b><br>Mulut belum terbuka lebar.</div>`;
        }
    } else {
        html += `<p class="mb-1"><b>Mode:</b> Deteksi Menoleh (ΔX)</p>`;
        
        html += `<div class="p-2 bg-light border rounded mb-3 mt-2" style="font-size: 14px;">`;
        html += `  <b class="text-primary">Detail Koordinat X (Real-time):</b><br>`;
        html += `  &bull; Hidung (X_hidung) = <b>${Math.round(latestHX)}</b><br>`;
        html += `  &bull; Mata Kiri (X_mata_kiri) = ${Math.round(latestM1X)}<br>`;
        html += `  &bull; Mata Kanan (X_mata_kanan) = ${Math.round(latestM2X)}<br>`;
        html += `  &bull; Titik Tengah Mata = (${Math.round(latestM1X)} + ${Math.round(latestM2X)}) / 2 = <b>${Math.round(latestCenterX)}</b><br>`;
        html += `</div>`;
        
        html += `<p class="mb-1"><b>Perhitungan Rumus ΔX:</b><br>`;
        html += `ΔX = X_hidung - Titik_Tengah<br>`;
        html += `ΔX = ${Math.round(latestHX)} - ${Math.round(latestCenterX)} = <b>${latestDeltaX.toFixed(2)} px</b></p>`;
        
        if(latestDeltaX > 15) {
            html += `<div class="alert alert-success mt-3 py-2 px-3 mb-0"><b>STATUS: VALID (Menoleh Kanan)</b><br>Karena X_hidung (${Math.round(latestHX)}) lebih besar dari Titik Tengah (${Math.round(latestCenterX)}), maka kepala mengarah ke kanan.</div>`;
        } else if (latestDeltaX < -15) {
            html += `<div class="alert alert-success mt-3 py-2 px-3 mb-0"><b>STATUS: VALID (Menoleh Kiri)</b><br>Karena X_hidung (${Math.round(latestHX)}) lebih kecil dari Titik Tengah (${Math.round(latestCenterX)}), maka kepala mengarah ke kiri.</div>`;
        } else {
            html += `<div class="alert alert-danger mt-3 py-2 px-3 mb-0"><b>STATUS: TIDAK VALID</b><br>Posisi wajah masih netral / menghadap lurus (Titik Hidung dan Titik Tengah nyaris sejajar).</div>`;
        }
    }
    detailsDiv.innerHTML = html;
}

function resumeLiveness() {
    video.play();
    document.getElementById('btnCapture').style.display = 'inline-block';
    document.getElementById('btnResume').style.display = 'none';
    document.getElementById('capture-result').style.display = 'none';
    beginDetection();
}
</script>
@endpush
