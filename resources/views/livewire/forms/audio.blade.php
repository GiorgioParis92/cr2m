<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
    @if(auth()->user()->id==1)
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Audio Recorder</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    
        <h2>Audio Recorder</h2>
        <button id="startRecord">🎤 Start Recording</button>
        <button id="stopRecord" disabled>⏹️ Stop Recording</button>
        <button id="saveAudio" disabled>💾 Save Audio</button>
        <a id="downloadLink" style="display: none;">⬇️ Download Audio</a>
        <audio id="audioPlayback" controls style="display: none;"></audio>
    
        <script>
            let mediaRecorder;
            let audioChunks = [];
            let audioBlob;
    
            document.getElementById("startRecord").addEventListener("click", async function () {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.ondataavailable = event => {
                        audioChunks.push(event.data);
                    };
    
                    mediaRecorder.onstop = () => {
                        audioBlob = new Blob(audioChunks, { type: "audio/wav" });
                        const audioUrl = URL.createObjectURL(audioBlob);
    
                        document.getElementById("downloadLink").href = audioUrl;
                        document.getElementById("downloadLink").download = "recorded-audio.wav";
                        document.getElementById("downloadLink").style.display = "block";
    
                        document.getElementById("audioPlayback").src = audioUrl;
                        document.getElementById("audioPlayback").style.display = "block";
                        document.getElementById("saveAudio").disabled = false;
    
                        audioChunks = []; // Reset chunks
                    };
    
                    mediaRecorder.start();
                    document.getElementById("startRecord").disabled = true;
                    document.getElementById("stopRecord").disabled = false;
                } catch (error) {
                    console.error("Error accessing microphone:", error);
                    alert("Could not access microphone. Please allow microphone access.");
                }
            });
    
            document.getElementById("stopRecord").addEventListener("click", function () {
                if (mediaRecorder && mediaRecorder.state === "recording") {
                    mediaRecorder.stop();
                    document.getElementById("startRecord").disabled = false;
                    document.getElementById("stopRecord").disabled = true;
                }
            });
    
            document.getElementById("saveAudio").addEventListener("click", async function () {
                if (!audioBlob) {
                    alert("No audio recorded!");
                    return;
                }
    
                const formData = new FormData();
                formData.append("audio", audioBlob, "audio.wav");
    
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    
                try {
                    const response = await fetch("{{ route('audio.store') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: formData
                    });
    
                    const data = await response.json();
                    if (response.ok) {
                        alert("Audio saved successfully!");
                        console.log("Saved file path:", data.file_path);
                    } else {
                        alert("Error saving audio: " + data.message);
                    }
                } catch (error) {
                    console.error("Error saving audio:", error);
                    alert("Failed to save audio.");
                }
            });
    
        </script>
    
 
    @endif
</div>
