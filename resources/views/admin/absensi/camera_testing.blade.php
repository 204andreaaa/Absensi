@extends('layouts.admin')

@section('content')

<div class="section-header">
    <h1>Testing Kamera & Face Landmarks</h1>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">Kamera Preview (Landmark Dots)</h4>
    </div>

    <div class="card-body">
        <div class="text-center mb-4">
            <button class="btn btn-primary" onclick="startCameraTest()" id="btnStart">
                Mulai Kamera
            </button>
            <button class="btn btn-danger" onclick="stopCameraTest()" id="btnStop" style="display:none;">
                Hentikan Kamera
            </button>
        </div>

        <div class="row mb-4">
            <!-- Feed 1: Deteksi Bounding Box -->
            <div class="col-lg-4 text-center">
                <h6 class="font-weight-bold text-info">1. Bounding Box (Box Biru)</h6>
                <div class="position-relative d-inline-block border rounded overflow-hidden bg-dark w-100">
                    <video id="video" autoplay muted playsinline class="d-block w-100" style="transform: scaleX(-1);"></video>
                    <canvas id="overlay" class="position-absolute" style="top:0; left:0; pointer-events:none;"></canvas>
                </div>
            </div>
            
            <!-- Feed 2: Ekstraksi Landmark -->
            <div class="col-lg-4 text-center">
                <h6 class="font-weight-bold text-success">2. Box Biru + Landmark</h6>
                <div class="position-relative d-inline-block border rounded overflow-hidden bg-dark w-100">
                    <video id="video2" autoplay muted playsinline class="d-block w-100" style="transform: scaleX(-1);"></video>
                    <canvas id="overlay2" class="position-absolute" style="top:0; left:0; pointer-events:none;"></canvas>
                </div>
            </div>

            <!-- Feed 3: JSON -->
            <div class="col-lg-4">
                <h6 class="font-weight-bold text-primary text-center">3. Hasil Descriptor JSON</h6>
                <div class="bg-dark text-light p-3 rounded text-left" style="height: calc(100% - 25px); max-height: 300px; overflow-y: auto;">
                    <pre id="jsonViewer" class="text-light m-0" style="font-size: 11px;">Belum ada data...</pre>
                </div>
            </div>
            
            <div class="col-12 mt-3 text-center">
                <h5 id="result" class="font-weight-bold mb-2">Klik tombol Mulai Kamera untuk melihat preview.</h5>
                <p id="info" class="text-muted mb-0">Harap tunggu hingga model selesai dimuat.</p>
            </div>
        </div>
        
        <!-- VISUALISASI PROSES -->
        <hr class="mt-5 mb-4 border-secondary">
        <h5 class="text-center mb-3 font-weight-bold">Visualisasi Proses Setelah Capture</h5>
        
        <div class="text-center mb-4">
            <button class="btn btn-warning btn-lg font-weight-bold text-dark shadow-sm" onclick="captureAndProcess()" id="btnCapture" style="display:none;">
                <i class="fas fa-camera"></i> Ambil Gambar (Capture)
            </button>
        </div>

        <div class="row text-center align-items-center">
            <!-- Step 1 -->
            <div class="col-md-2 p-1">
                <div class="card bg-dark border-secondary mb-0">
                    <div class="card-body p-2">
                        <h6 class="text-muted small">1. Bounding Box</h6>
                        <div id="step1-container" style="height: 100px; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                            <span class="text-muted small">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 p-0">
                <i class="fas fa-arrow-right text-secondary"></i>
            </div>
            <!-- Step 2 -->
            <div class="col-md-2 p-1">
                <div class="card bg-dark border-secondary mb-0">
                    <div class="card-body p-2">
                        <h6 class="text-muted small">2. Matrix RGB</h6>
                        <div id="step2-container" style="height: 100px; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                            <span class="text-muted small">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 p-0">
                <i class="fas fa-arrow-right text-secondary"></i>
            </div>
            <!-- Step 3 -->
            <div class="col-md-2 p-1">
                <div class="card bg-dark border-secondary mb-0">
                    <div class="card-body p-2">
                        <h6 class="text-muted small">3. 68 Landmark</h6>
                        <div id="step3-container" style="height: 100px; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                            <span class="text-muted small">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 p-0">
                <i class="fas fa-arrow-right text-secondary"></i>
            </div>
            <!-- Step 4 -->
            <div class="col-md-3 p-1">
                <div class="card bg-dark border-secondary mb-0">
                    <div class="card-body p-2">
                        <h6 class="text-muted small">4. Model ResNet (128 Angka)</h6>
                        <div id="step4-container" style="height: 100px; display: flex; flex-direction: column; justify-content: center; align-items: center; width:100%;">
                            <canvas id="embedding-chart" width="200" height="90" style="display:none; width:100%; height:100%;"></canvas>
                            <div id="step4-placeholder">
                                <i class="fas fa-microchip fa-2x text-primary mb-2"></i>
                                <div class="badge badge-success d-block">Data JSON (Atas)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- VISUALISASI CNN -->
        <hr class="mt-5 mb-4 border-secondary">
        <h5 class="text-center mb-3 font-weight-bold">Di Dalam "Otak" AI (Convolutional Neural Network)</h5>
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <h6 class="text-info">1. Matrix Piksel (Input)</h6>
                <div id="cnn-step1" style="height:140px; border: 1px dashed #555; border-radius: 8px; display:flex; justify-content:center; align-items:center; overflow:hidden;">
                    <span class="text-muted small">-</span>
                </div>
                <p class="small text-muted mt-2">Gambar wajah dipecah menjadi matriks kotak-kotak piksel warna.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-info">2. Feature Maps (Saringan)</h6>
                <div id="cnn-step2" style="height:140px; border: 1px dashed #555; border-radius: 8px; display:flex; justify-content:center; align-items:center; overflow:hidden; position:relative;">
                    <span class="text-muted small">-</span>
                </div>
                <p class="small text-muted mt-2">Piksel disaring berkali-kali mencari pola lekukan, hidung, dan mata.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-info">3. Flattening (Pemadatan)</h6>
                <div id="cnn-step3" style="height:140px; border: 1px dashed #555; border-radius: 8px; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                     <span class="text-muted small">-</span>
                </div>
                <p class="small text-muted mt-2">Kotak-kotak fitur dilebur paksa menjadi 1 baris (128 Desimal).</p>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>

<script>
const video = document.getElementById('video');
const video2 = document.getElementById('video2');
const overlay = document.getElementById('overlay');
const overlay2 = document.getElementById('overlay2');
const resultText = document.getElementById('result');
const infoText = document.getElementById('info');
const btnStart = document.getElementById('btnStart');
const btnStop = document.getElementById('btnStop');
const btnCapture = document.getElementById('btnCapture');

let detectionInterval = null;
let modelsLoaded = false;

// Custom function to draw bounding box and optionally landmarks, adapted for mirrored video
function drawBoundingBoxMirrored(canvas, detections, displaySize, withLandmarks = false) {
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    detections.forEach(detection => {
        const box = detection.detection.box;
        // Mirror X for bounding box
        const mirroredX = displaySize.width - (box.x + box.width);
        
        // Draw Blue Box
        ctx.strokeStyle = '#00d2ff'; // Cyan/Blue
        ctx.lineWidth = 2;
        ctx.strokeRect(mirroredX, box.y, box.width, box.height);
        
        // Draw Text Label
        ctx.fillStyle = '#00d2ff';
        ctx.font = '12px Arial';
        ctx.fillText('Wajah', mirroredX, box.y - 5);
        
        if (withLandmarks && detection.landmarks) {
            ctx.fillStyle = '#00ff00';
            const positions = detection.landmarks.positions;
            
            positions.forEach((point, index) => {
                const mx = displaySize.width - point.x;
                
                // Draw Dot
                ctx.beginPath();
                ctx.arc(mx, point.y, 2, 0, 2 * Math.PI);
                ctx.fill();
                
                // Draw Index
                ctx.font = '10px Arial';
                ctx.fillStyle = '#ffffff';
                ctx.fillText(index, mx + 4, point.y - 2);
                ctx.fillStyle = '#00ff00';
            });
        }
    });
}

async function loadModels() {
    if (modelsLoaded) return;
    infoText.innerText = "Memuat model deteksi wajah...";
    
    await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
    await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
    await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition');
    
    modelsLoaded = true;
    infoText.innerText = "Model berhasil dimuat.";
}

async function startCameraTest() {
    try {
        btnStart.disabled = true;
        
        await loadModels();
        
        infoText.innerText = "Mengaktifkan kamera...";
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video2.srcObject = stream;
        
        btnStart.style.display = 'none';
        btnStop.style.display = 'inline-block';
        btnCapture.style.display = 'inline-block';
        btnStart.disabled = false;
        
        if (video.readyState >= 2) {
            beginDetection();
        } else {
            video.addEventListener('loadeddata', () => {
                beginDetection();
            }, { once: true });
        }
    } catch (error) {
        console.error(error);
        infoText.innerText = "Gagal memulai kamera: " + error.message;
        btnStart.disabled = false;
    }
}

function stopCameraTest() {
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        video2.srcObject = null;
    }
    
    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }
    
    const ctx = overlay.getContext('2d');
    ctx.clearRect(0, 0, overlay.width, overlay.height);
    const ctx2 = overlay2.getContext('2d');
    ctx2.clearRect(0, 0, overlay2.width, overlay2.height);
    
    btnStart.style.display = 'inline-block';
    btnStop.style.display = 'none';
    btnCapture.style.display = 'none';
    infoText.innerText = "Kamera dihentikan.";
    resultText.innerText = "Klik tombol Mulai Kamera untuk melihat preview.";
    document.getElementById('jsonViewer').innerText = "Belum ada data...";
    
    document.getElementById('step1-container').innerHTML = '<span class="text-muted small">-</span>';
    document.getElementById('step2-container').innerHTML = '<span class="text-muted small">-</span>';
    document.getElementById('step3-container').innerHTML = '<span class="text-muted small">-</span>';
    
    document.getElementById('cnn-step1').innerHTML = '<span class="text-muted small">-</span>';
    document.getElementById('cnn-step2').innerHTML = '<span class="text-muted small">-</span>';
    document.getElementById('cnn-step3').innerHTML = '<span class="text-muted small">-</span>';
    
    document.getElementById('embedding-chart').style.display = 'none';
    document.getElementById('step4-placeholder').style.display = 'block';
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
    
    overlay2.width = displaySize.width;
    overlay2.height = displaySize.height;
    overlay2.style.width = displaySize.width + "px";
    overlay2.style.height = displaySize.height + "px";
    faceapi.matchDimensions(overlay2, displaySize);
    
    infoText.innerText = "Mendeteksi landmark...";
    
    if (detectionInterval) {
        clearInterval(detectionInterval);
    }
    
    detectionInterval = setInterval(async () => {
        if (!video.srcObject) return; // Stop if video is closed
        
        const detections = await faceapi
            .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks();
            
        const resizedDetections = faceapi.resizeResults(detections, displaySize);
        
        if (resizedDetections.length > 0) {
            resultText.innerText = "Wajah Terdeteksi. Silakan klik Capture.";
        } else {
            resultText.innerText = "Wajah belum terdeteksi";
        }
        
        // Draw the points (Live Preview)
        drawBoundingBoxMirrored(overlay, resizedDetections, displaySize, false); // Box only
        drawBoundingBoxMirrored(overlay2, resizedDetections, displaySize, true); // Box + Landmarks
        
    }, 100);
}

// FUNGSI UNTUK CAPTURE DAN PROSES STEP-BY-STEP
async function captureAndProcess() {
    if (!video.srcObject) return;
    
    btnCapture.disabled = true;
    btnCapture.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    // Detect on current frame with descriptors
    const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptors();
        
    const displaySize = {
        width: video.clientWidth || video.width,
        height: video.clientHeight || video.height
    };
    
    const resizedDetections = faceapi.resizeResults(detections, displaySize);
    
    if (resizedDetections.length === 0) {
        alert("Wajah tidak terdeteksi saat difoto! Pastikan wajah terlihat jelas.");
        btnCapture.disabled = false;
        btnCapture.innerHTML = '<i class="fas fa-camera"></i> Ambil Gambar (Capture)';
        return;
    }
    
    resultText.innerText = "Capture Berhasil! Visualisasi proses ditampilkan di bawah.";
    
    // Show JSON (128 array)
    const descriptorArray = Array.from(resizedDetections[0].descriptor);
    document.getElementById('jsonViewer').innerText = JSON.stringify(descriptorArray, null, 2);
    
    // VISUALIZE 128 DIMENSIONS AS BAR CHART
    const chartCanvas = document.getElementById('embedding-chart');
    const placeholder = document.getElementById('step4-placeholder');
    placeholder.style.display = 'none';
    chartCanvas.style.display = 'block';
    
    const cctx = chartCanvas.getContext('2d');
    cctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
    
    // Normalisasi array untuk digambar (mencari nilai min dan max)
    const minVal = Math.min(...descriptorArray);
    const maxVal = Math.max(...descriptorArray);
    const range = maxVal - minVal || 1;
    const barWidth = chartCanvas.width / descriptorArray.length;
    
    descriptorArray.forEach((val, i) => {
        const normalized = (val - minVal) / range;
        const barHeight = Math.max(1, normalized * chartCanvas.height); // minimal 1px
        const x = i * barWidth;
        const y = chartCanvas.height - barHeight;
        
        // Warna batang: biru muda untuk positif, merah muda untuk negatif
        cctx.fillStyle = val > 0 ? '#00d2ff' : '#ff3366';
        cctx.fillRect(x, y, barWidth - 0.2, barHeight);
    });
    
    // VISUALIZATION PROCESS (Steps)
    try {
        const extractedFaces = await faceapi.extractFaces(video, detections.map(d => d.detection));
        
        if(extractedFaces.length > 0) {
            const faceCanvas = extractedFaces[0];
            
            const applyStyle = (el) => {
                el.style.transform = 'scaleX(-1)'; // Mirror for display
                el.style.maxHeight = '90px';
                el.style.width = 'auto';
                el.style.borderRadius = '8px';
            };
            
            // Step 1: Crop Bounding Box
            const step1Canvas = document.createElement('canvas');
            step1Canvas.width = faceCanvas.width;
            step1Canvas.height = faceCanvas.height;
            step1Canvas.getContext('2d').drawImage(faceCanvas, 0, 0);
            applyStyle(step1Canvas);
            
            document.getElementById('step1-container').innerHTML = '';
            document.getElementById('step1-container').appendChild(step1Canvas);
            
            // Step 2: Matrix RGB (Split R, G, B channels)
            const step2Div = document.createElement('div');
            step2Div.style.display = 'flex';
            step2Div.style.justifyContent = 'center';
            step2Div.style.alignItems = 'center';
            step2Div.style.width = '100%';
            
            const channels = ['#ff0000', '#00ff00', '#0000ff'];
            channels.forEach(color => {
                const c = document.createElement('canvas');
                c.width = faceCanvas.width; c.height = faceCanvas.height;
                const cctx = c.getContext('2d');
                cctx.drawImage(faceCanvas, 0, 0);
                cctx.fillStyle = color;
                cctx.globalCompositeOperation = 'multiply';
                cctx.fillRect(0,0, c.width, c.height);
                
                c.style.transform = 'scaleX(-1)';
                c.style.maxHeight = '80px';
                c.style.width = 'auto';
                c.style.borderRadius = '4px';
                c.style.margin = '0 2px';
                c.style.border = '1px solid #444';
                step2Div.appendChild(c);
            });
            document.getElementById('step2-container').innerHTML = '';
            document.getElementById('step2-container').appendChild(step2Div);
            
            // Step 3: 68 Landmarks
            const step3Canvas = document.createElement('canvas');
            step3Canvas.width = faceCanvas.width;
            step3Canvas.height = faceCanvas.height;
            const ctx3 = step3Canvas.getContext('2d');
            ctx3.drawImage(faceCanvas, 0, 0);
            
            const box = detections[0].detection.box;
            const landmarks = detections[0].landmarks.positions;
            ctx3.fillStyle = '#00ff00';
            
            landmarks.forEach((p) => {
                const x = p.x - box.x;
                const y = p.y - box.y;
                ctx3.beginPath();
                ctx3.arc(x, y, 2, 0, 2 * Math.PI);
                ctx3.fill();
            });
            applyStyle(step3Canvas);
            
            document.getElementById('step3-container').innerHTML = '';
            document.getElementById('step3-container').appendChild(step3Canvas);
            
            // --- CNN VISUALIZATION LOGIC ---
            
            // 1. Matrix Piksel (Pixelated Effect)
            const cnnStep1 = document.getElementById('cnn-step1');
            cnnStep1.innerHTML = '';
            const pixelCanvas = document.createElement('canvas');
            pixelCanvas.width = faceCanvas.width;
            pixelCanvas.height = faceCanvas.height;
            const pctx = pixelCanvas.getContext('2d');
            pctx.imageSmoothingEnabled = false;
            pctx.drawImage(faceCanvas, 0, 0, faceCanvas.width * 0.1, faceCanvas.height * 0.1);
            pctx.drawImage(pixelCanvas, 0, 0, faceCanvas.width * 0.1, faceCanvas.height * 0.1, 0, 0, faceCanvas.width, faceCanvas.height);
            
            pctx.strokeStyle = 'rgba(255,255,255,0.3)';
            pctx.lineWidth = 1;
            const step = 8;
            for(let x=0; x<faceCanvas.width; x+=step) { pctx.beginPath(); pctx.moveTo(x,0); pctx.lineTo(x, faceCanvas.height); pctx.stroke(); }
            for(let y=0; y<faceCanvas.height; y+=step) { pctx.beginPath(); pctx.moveTo(0,y); pctx.lineTo(faceCanvas.width, y); pctx.stroke(); }
            
            applyStyle(pixelCanvas);
            pixelCanvas.style.maxHeight = '110px';
            cnnStep1.appendChild(pixelCanvas);
            
            // 2. Feature Maps (Simulated Filter Layers)
            const cnnStep2 = document.getElementById('cnn-step2');
            cnnStep2.innerHTML = '';
            const filters = [
                'contrast(200%) grayscale(100%) invert(100%)', // edge-like
                'sepia(100%) hue-rotate(180deg) blur(2px)',    // blob-like
                'contrast(150%) hue-rotate(90deg) blur(1px)'   // texture-like
            ];
            filters.forEach((filterStr, i) => {
                const fCanvas = document.createElement('canvas');
                fCanvas.width = faceCanvas.width;
                fCanvas.height = faceCanvas.height;
                const fctx = fCanvas.getContext('2d');
                fctx.filter = filterStr;
                fctx.drawImage(faceCanvas, 0, 0);
                
                fCanvas.style.transform = 'scaleX(-1)';
                fCanvas.style.position = 'absolute';
                fCanvas.style.maxHeight = '90px';
                fCanvas.style.width = 'auto';
                fCanvas.style.borderRadius = '8px';
                fCanvas.style.opacity = '0.7';
                fCanvas.style.left = `calc(50% - 40px + ${i*15}px)`; 
                fCanvas.style.top = `${20 + i*10}px`;
                fCanvas.style.border = '1px solid #00d2ff';
                
                cnnStep2.appendChild(fCanvas);
            });
            
            // 3. Flattening (128 array compressed)
            const cnnStep3 = document.getElementById('cnn-step3');
            cnnStep3.innerHTML = '';
            
            const flattenIcon = document.createElement('div');
            flattenIcon.innerHTML = '<i class="fas fa-compress-arrows-alt text-warning fa-2x mb-2"></i>';
            cnnStep3.appendChild(flattenIcon);
            
            const flatCanvas = document.createElement('canvas');
            flatCanvas.width = 256;
            flatCanvas.height = 40;
            const flatCtx = flatCanvas.getContext('2d');
            flatCtx.clearRect(0,0, flatCanvas.width, flatCanvas.height);
            const bWidth2 = flatCanvas.width / descriptorArray.length;
            descriptorArray.forEach((val, i) => {
                const normalized = (val - minVal) / range;
                const bHeight = Math.max(1, normalized * flatCanvas.height); 
                flatCtx.fillStyle = '#00ff00';
                flatCtx.fillRect(i * bWidth2, flatCanvas.height - bHeight, bWidth2 - 0.5, bHeight);
            });
            flatCanvas.style.width = '90%';
            flatCanvas.style.height = '40px';
            flatCanvas.style.border = '1px solid #555';
            flatCanvas.style.borderRadius = '4px';
            cnnStep3.appendChild(flatCanvas);
        }
    } catch(e) {
        console.error("Extraction error", e);
    }
    
    btnCapture.disabled = false;
    btnCapture.innerHTML = '<i class="fas fa-camera"></i> Ambil Gambar (Capture)';
}
</script>
@endpush
