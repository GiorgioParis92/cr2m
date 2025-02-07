<div  class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
    @if(auth()->user()->id==1)
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Audio Recorder</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    {{ $value }}
    <input id="value" type="text"    name="{{ $conf['name'] }}"  class="form-control "  wire:model.debounce.500ms="value"  placeholder="">

        <h2>Audio Recorder</h2>
        <div class="d-flex gap-2 mb-3">
            <button id="startRecord" class="btn btn-primary">
                <i class="bi bi-mic-fill"></i> Démarrer l'enregistrement
            </button>
            <button id="stopRecord" class="btn btn-danger" disabled>
                <i class="bi bi-stop-fill"></i> Stop
            </button>
            <button id="AnalyseAudio" class="btn btn-success" disabled style="display:{{$value ? 'block' : 'none'}}">
                <i class="bi bi-save-fill"></i> Analyser l'audio
            </button>
        </div>
        <div>
            <a id="analyse_audio" class="btn btn-secondary" style="display: none;">
                <i class="bi bi-download"></i> Download Audio
            </a>
        </div>
        <div class="mt-3" >
         
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
        
                    mediaRecorder.onstop = async () => {
                        audioBlob = new Blob(audioChunks, { type: "audio/wav" });
                        const audioUrl = URL.createObjectURL(audioBlob);
        
                        // ✅ Display the recorded audio immediately
                        document.getElementById("audioPlayback").src = audioUrl;
                        document.getElementById("audioPlayback").style.display = "block";
        
                        audioChunks = []; // Reset chunks
        
                        // ✅ Automatically save the audio after stopping the recording
                        await saveAudio(audioBlob);
                    };
        
                    mediaRecorder.start();
                    document.getElementById("startRecord").disabled = true;
                    document.getElementById("stopRecord").disabled = false;
                    $('#AnalyseAudio').hide();
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
        
            async function saveAudio(audioBlob) {
                if (!audioBlob) {
                    alert("No audio recorded!");
                    return;
                }
        
                const formData = new FormData();
                formData.append("audio", audioBlob, "audio.wav");
                formData.append("name", "{{ $conf['name'] ?? '' }}");
                formData.append("dossier_id", "{{ $dossier_id ?? '' }}");
                formData.append("form_id", "{{ $form_id ?? '' }}");
        
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
                        console.log("Saved file path:", data.file_path);
        
                        // ✅ Update audioPlayback to use the saved file from the server
                        document.getElementById("audioPlayback").src = data.file_path;
                        $('#value').val(data.file_path);
                        $('#AnalyseAudio').show();
                    } else {
                        alert("Error saving audio: " + data.message);
                    }
                } catch (error) {
                    console.error("Error saving audio:", error);
                    alert("Failed to save audio.");
                }
            }



            document.getElementById("AnalyseAudio").addEventListener("click", async function () {
        try {
            const response = await fetch("{{ route('audio.analyse') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({
                    // any additional data you want to send
                    // for example, an "audio_id" if you have a DB reference
                    audio: $('#value')
                })
            });

            if (!response.ok) {
                throw new Error("Server error during transcription request.");
            }

            const data = await response.json();
            // data will contain the transcription if everything goes well
            console.log("Transcribed text: ", data.transcription);

            alert(`Texte transcrit : ${data.transcription}`);
        } catch (error) {
            console.error(error);
            alert("Failed to transcribe audio.");
        }
    });
        </script>
        
    
 
    @endif
</div>
