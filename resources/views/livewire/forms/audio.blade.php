<div  class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
    @if(auth()->user()->id==1)
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Audio Recorder</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    {{ $value }}
    <input type="text"    name="{{ $conf['name'] }}"  class="form-control "  wire:model.debounce.500ms="value"  placeholder="">

        <h2>Audio Recorder</h2>
        <div class="d-flex gap-2 mb-3">
            <button id="startRecord" class="btn btn-primary">
                <i class="bi bi-mic-fill"></i> Démarrer l'enregistrement
            </button>
            <button id="stopRecord" class="btn btn-danger" disabled>
                <i class="bi bi-stop-fill"></i> Stop
            </button>
            <button id="saveAudio" class="btn btn-success" disabled>
                <i class="bi bi-save-fill"></i> Enregistrer
            </button>
        </div>
        <div>
            <a id="downloadLink" class="btn btn-secondary" style="display: none;">
                <i class="bi bi-download"></i> Download Audio
            </a>
        </div>
        <div class="mt-3" wire:poll>
         
            @if (!empty($value))
                <audio id="audioPlayback" controls class="w-100" src="{{ ($value) }}"></audio>
            @else
                <audio id="audioPlayback" controls style="display: none;" class="w-100"></audio>
            @endif
        </div>
        
    
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
    
                        // document.getElementById("downloadLink").href = audioUrl;
                        // document.getElementById("downloadLink").download = "recorded-audio.wav";
                        // document.getElementById("downloadLink").style.display = "block";
    
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
                formData.append("name",  "{{ $conf['name'] ?? '' }}");
                formData.append("dossier_id",  "{{ $dossier_id ?? '' }}");
                formData.append("form_id",  "{{ $form_id ?? ''}}");
    
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
                        // alert("Audio saved successfully!");
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
