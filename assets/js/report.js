// Form Validation and Progress Bar
const form = document.getElementById('crimeReportForm');
const progressBar = document.querySelector('.progress-bar');
const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

// Validate each input on blur
inputs.forEach(input => {
    input.addEventListener('blur', () => validateInput(input));
});

function validateInput(input) {
    const formGroup = input.closest('.mb-3');
    const feedback = formGroup.querySelector('.feedback');
    
    if (!input.value.trim()) {
        setInvalid(input, feedback, 'This field is required');
        return false;
    } else {
        setValid(input, feedback);
        return true;
    }
}

function setInvalid(input, feedback, message) {
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
    feedback.textContent = message;
    feedback.classList.add('text-danger');
}

function setValid(input, feedback) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    feedback.textContent = '';
}

// Update progress bar
function updateProgress() {
    const totalFields = inputs.length;
    const completedFields = Array.from(inputs).filter(input => input.value.trim()).length;
    const progress = (completedFields / totalFields) * 100;
    progressBar.style.width = `${progress}%`;
}

// Listen for form changes
form.addEventListener('change', updateProgress);

// Form submission
form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isValid = true;
    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });
    
    if (isValid) {
        progressBar.style.width = '100%';
        setTimeout(() => {
            this.submit();
        }, 500);
    }
});


// Initialize map
function initMap() {
    map = L.map('mapPreview').setView([12.1700, 6.6600], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    marker = L.marker([12.1700, 6.6600]).addTo(map);
}

// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', initMap);

// Form validation
inputs.forEach(input => {
    input.addEventListener('blur', function() {
        validateInput(this);
    });
});

// Location detection
document.getElementById('getLocation').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Update map
            map.setView([lat, lng], 15);
            marker.setLatLng([lat, lng]);
            
            // Reverse geocoding
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('location').value = data.display_name;
                });
        });
    }
});

// File upload handling
const dropZone = document.getElementById('dropZone');
const fileInput = dropZone.querySelector('input[type="file"]');
const filePreview = document.getElementById('filePreview');

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    handleFiles(e.dataTransfer.files);
});

fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
});
// File upload handling functions
function handleFiles(files) {
    const maxSize = 100 * 1024 * 1024; // 100MB
    filePreview.innerHTML = '';
    
    Array.from(files).forEach(file => {
        if (file.size > maxSize) {
            showAlert('File ' + file.name + ' is too large. Maximum size is 100MB');
            return;
        }
        
        const reader = new FileReader();
        const previewItem = createPreviewItem(file);
        
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                const img = previewItem.querySelector('img');
                img.src = e.target.result;
            }
        }
        
        if (file.type.startsWith('image/')) {
            reader.readAsDataURL(file);
        }
        
        filePreview.appendChild(previewItem);
    });
}

function createPreviewItem(file) {
    const div = document.createElement('div');
    div.className = 'preview-item';
    
    const content = `
        <img src="${getFileIcon(file)}" alt="${file.name}">
        <div class="file-info">
            <span>${file.name}</span>
            <small>${formatFileSize(file.size)}</small>
        </div>
        <button type="button" class="remove-file">&times;</button>
    `;
    
    div.innerHTML = content;
    
    div.querySelector('.remove-file').addEventListener('click', function() {
        div.remove();
    });
    
    return div;
}

function getFileIcon(file) {
    if (file.type.startsWith('image/')) {
        return 'assets/images/image-icon.png';
    } else if (file.type.startsWith('video/')) {
        return 'assets/images/video-icon.png';
    } else if (file.type.startsWith('audio/')) {
        return 'assets/images/audio-icon.png';
    }
    return 'assets/images/document-icon.png';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Voice Recording Implementation
let mediaRecorder;
let audioChunks = [];

const startRecordingBtn = document.getElementById('startRecording');
const stopRecordingBtn = document.getElementById('stopRecording');
const audioPlayback = document.getElementById('audioPlayback');
const waveform = document.getElementById('waveform');
const waveformCtx = waveform.getContext('2d');

startRecordingBtn.addEventListener('click', function() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.start();
            
            startRecordingBtn.classList.add('d-none');
            stopRecordingBtn.classList.remove('d-none');
            
            // Visualize audio
            visualizeAudio(stream);
            
            mediaRecorder.addEventListener("dataavailable", event => {
                audioChunks.push(event.data);
            });
            
            mediaRecorder.addEventListener("stop", () => {
                const audioBlob = new Blob(audioChunks);
                const audioUrl = URL.createObjectURL(audioBlob);
                audioPlayback.src = audioUrl;
                audioPlayback.classList.remove('d-none');
            });
        });
});

stopRecordingBtn.addEventListener('click', function() {
    mediaRecorder.stop();
    startRecordingBtn.classList.remove('d-none');
    stopRecordingBtn.classList.add('d-none');
});

function visualizeAudio(stream) {
    const audioContext = new AudioContext();
    const source = audioContext.createMediaStreamSource(stream);
    const analyser = audioContext.createAnalyser();
    
    source.connect(analyser);
    analyser.fftSize = 256;
    
    const bufferLength = analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);
    
    function draw() {
        const WIDTH = waveform.width;
        const HEIGHT = waveform.height;
        
        requestAnimationFrame(draw);
        
        analyser.getByteFrequencyData(dataArray);
        
        waveformCtx.fillStyle = '#ffffff';
        waveformCtx.fillRect(0, 0, WIDTH, HEIGHT);
        
        const barWidth = (WIDTH / bufferLength) * 2.5;
        let barHeight;
        let x = 0;
        
        for(let i = 0; i < bufferLength; i++) {
            barHeight = dataArray[i]/2;
            
            waveformCtx.fillStyle = `rgb(0, 123, 255)`;
            waveformCtx.fillRect(x, HEIGHT-barHeight/2, barWidth, barHeight);
            
            x += barWidth + 1;
        }
    }
    
    draw();
}


// Progress bar update
form.addEventListener('submit', function(e) {
    e.preventDefault();
    const progressBar = document.querySelector('.progress-bar');
    let progress = 0;
    
    const interval = setInterval(() => {
        progress += 10;
        progressBar.style.width = progress + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
            form.submit();
        }
    }, 200);
});
// When stopping recording
stopRecordingBtn.addEventListener('click', function() {
    mediaRecorder.stop();
    startRecordingBtn.classList.remove('d-none');
    stopRecordingBtn.classList.add('d-none');
    
    // Create hidden input for audio data
    const audioInput = document.createElement('input');
    audioInput.type = 'hidden';
    audioInput.name = 'audio_data';
    audioInput.value = audioPlayback.src;
    form.appendChild(audioInput);
});
