@php
    // Generate a unique identifier for this specific recorder instance
    $uniqueId = uniqid('recorder_');
@endphp


<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
    @if (auth()->user()->id == 1)


        {{-- Use the uniqueId in the input's ID --}}
        <input id="value-{{ $uniqueId }}" type="hidden" name="{{ $conf['name'] }}" class="form-control"
            wire:model.debounce.500ms="value">

        <div class="">
            <button id="startRecord-{{ $uniqueId }}" class="btn btn-primary">
                <i class="bi bi-mic-fill"></i> Démarrer l'enregistrement
            </button>
            <button id="stopRecord-{{ $uniqueId }}" class="btn btn-danger" disabled>
                <i class="bi bi-stop-fill"></i> Stop
            </button>
            <button id="AnalyseAudio-{{ $uniqueId }}" class="btn btn-success"
                style="display:{{ $value ? 'block' : 'none' }}">
                <i class="bi bi-save-fill"></i> Analyser l'audio
            </button>

            @if ($pdf)
                <button data-img-src="{{ asset('storage/' . $pdf) }}" class="btn btn-success pdfmodal">
                    <i class="bi bi-save-fill"></i> PDF
                </button>
            @endif

        </div>

        <div>
            <a id="analyse_audio-{{ $uniqueId }}" class="btn btn-secondary" style="display: none;">
                <i class="bi bi-download"></i> Download Audio
            </a>
        </div>

        <div class="mt-3">
            @if (!empty($value))
                <audio id="audioPlayback-{{ $uniqueId }}" controls class="w-100"
                    src="../../storage/{{ $value }}"></audio>
            @else
                <audio id="audioPlayback-{{ $uniqueId }}" controls style="display: none;" class="w-100"></audio>
            @endif
        </div>
        <style>
            .oceer_focus {
                color: #495057;
                background-color: #fff;
                border-color: #a1ff51;
                outline: 0;
                box-shadow: 0 0 0 2px #9cff8b;
            }
        </style>

<script>
    (function recordAudioIIFE() {
      'use strict';
  
      // ********************************************************************
      // *************************   VARIABLES   *****************************
      // ********************************************************************
  
      let mediaRecorder;
      let audioChunks = [];
      let audioBlob;
  
      const startBtn = document.getElementById("startRecord-{{ $uniqueId }}");
      const stopBtn = document.getElementById("stopRecord-{{ $uniqueId }}");
      const analyseBtn = document.getElementById("AnalyseAudio-{{ $uniqueId }}");
      const downloadLink = document.getElementById("analyse_audio-{{ $uniqueId }}");
      const audioPlayback = document.getElementById("audioPlayback-{{ $uniqueId }}");
      const hiddenValueInput = document.getElementById("value-{{ $uniqueId }}");
  
      // ********************************************************************
      // *************************   FUNCTIONS   *****************************
      // ********************************************************************
  
      /**
       * Request microphone permission and start recording.
       */
      async function startRecording() {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
          
          // Force a well-supported MIME type
          const options = { mimeType: 'audio/webm; codecs=opus' };
          mediaRecorder = new MediaRecorder(stream, options);
  
          // When data is available, push it into our chunks array
          mediaRecorder.ondataavailable = (event) => {
            audioChunks.push(event.data);
          };
  
          // When recording stops, create the final Blob and handle it
          mediaRecorder.onstop = handleRecordingStop;
  
          // Start the recorder
          mediaRecorder.start();
  
          // Update UI
          startBtn.disabled = true;
          stopBtn.disabled = false;
          analyseBtn.style.display = "none";
        } catch (error) {
          console.error("Error accessing microphone:", error);
          alert("Could not access microphone. Please allow microphone access.");
        }
      }
  
      /**
       * Stop recording if active.
       */
      function stopRecording() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
          mediaRecorder.stop();
          startBtn.disabled = false;
          stopBtn.disabled = true;
        }
      }
  
      /**
       * Handle operations once the recording has stopped.
       */
      async function handleRecordingStop() {
        // Build a Blob from the captured chunks
        audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
        audioChunks = []; // Reset for next recording
  
        // Create a local playback URL
        const audioUrl = URL.createObjectURL(audioBlob);
        audioPlayback.src = audioUrl;
        audioPlayback.style.display = "block";
  
        // Automatically upload the audio
        await saveAudio(audioBlob);
      }
  
      /**
       * Send the recorded audio blob to the server.
       * @param {Blob} audioBlob
       */
      async function saveAudio(audioBlob) {
        if (!audioBlob) {
          alert("No audio recorded!");
          return;
        }
  
        const formData = new FormData();
        formData.append("audio", audioBlob, "audio.webm");
        formData.append("name", "{{ $conf['name'] ?? '' }}");
        formData.append("dossier_id", "{{ $dossier_id ?? '' }}");
        formData.append("form_id", "{{ $form_id ?? '' }}");
        formData.append("api_link", "{{ $api_link ?? '' }}");
  
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
  
            // Update audioPlayback to use the saved file
            // IMPORTANT: adapt this path as needed if your endpoint returns a raw file path
            audioPlayback.src = '.././../storage/' + data.file_path;
            hiddenValueInput.value = data.file_path;
  
            // Show 'Analyser l'audio' button
            analyseBtn.style.display = "block";
          } else {
            alert("Error saving audio: " + data.message);
          }
        } catch (error) {
          console.error("Error saving audio:", error);
          alert("Failed to save audio.");
        }
      }
  
      /**
       * Request transcription from the server and fill form fields if data returns.
       */
      async function analyseAudio() {
        try {
          const response = await fetch("{{ route('audio.analyse') }}", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({
              value: hiddenValueInput.value,
              dossier_id: {{ $dossier_id }},
              form_id: {{ $form_id }},
              name: "{{ $conf['name'] ?? '' }}",
              api_link: "{{ $api_link ?? '' }}"
            })
          });
  
          if (!response.ok) {
            throw new Error("Server error during transcription request.");
          }
  
          const data = await response.json();
          console.log("Transcribed text: ", data.transcription);
  
          if (data.oceer_result && data.oceer_result.results) {
            processOceerResults(data.oceer_result.results);
          } else {
            console.warn("Aucun résultat dans oceer_result.");
          }
        } catch (error) {
          console.error(error);
          alert("Failed to transcribe audio.");
        }
      }
  
      /**
       * Process the results from the server and fill matching form fields.
       * @param {Object} results
       */
      function processOceerResults(results) {
        for (const [key, resultItem] of Object.entries(results)) {
          console.log(`\n=== Résultat pour : ${key} ===`);
          console.log('Value :', resultItem.value);
          console.log('Score :', resultItem.score);
          console.log('ID    :', resultItem.id);
  
          if (resultItem.value) {
            fillFormField({ id: resultItem.id, value: resultItem.value });
          }
  
          if (Array.isArray(resultItem.metadata)) {
            resultItem.metadata.forEach((meta, index) => {
              console.log(`Metadata #${index} :`, meta);
            });
          }
        }
      }
  
      /**
       * Fill a form field given an ID and a value.
       * @param {Object} resultItem - Object with an 'id' and 'value'.
       */
      function fillFormField(resultItem) {
        if (!resultItem?.id || resultItem.value === undefined || resultItem.value === null) {
          return;
        }
  
        const field = document.querySelector(`[name='${resultItem.id}']`);
        if (!field) {
          return;
        }
  
        const tagName = field.tagName.toLowerCase();
        const fieldType = (field.getAttribute('type') || '').toLowerCase();
  
        switch (tagName) {
          case 'input':
            if (fieldType === 'radio' || fieldType === 'checkbox') {
              fillCheckedFields(fieldType, resultItem);
            } else {
              fillSimpleField(field, resultItem.value);
            }
            break;
          case 'select':
          case 'textarea':
            fillSimpleField(field, resultItem.value);
            break;
          default:
            // Handle other element types as needed
            break;
        }
      }
  
      /**
       * Fill a simple field (e.g., text, number, email) with a given value.
       * @param {HTMLInputElement|HTMLSelectElement|HTMLTextAreaElement} field
       * @param {string|number} value
       */
      function fillSimpleField(field, value) {
        field.value = value;
        field.classList.add('oceer_focus');
      }
  
      /**
       * Fill radio or checkbox fields (multiple inputs with the same name).
       * @param {string} fieldType
       * @param {Object} resultItem
       */
      function fillCheckedFields(fieldType, resultItem) {
        const allInputs = document.querySelectorAll(
          `input[type='${fieldType}'][name='${resultItem.id}']`
        );
  
        allInputs.forEach((input) => {
          if (Array.isArray(resultItem.value)) {
            // If value is an array, we assume multiple checkboxes
            input.checked = resultItem.value.includes(input.value);
          } else {
            // For a single numeric or string value
            if (fieldType === 'checkbox' && resultItem.value == 0) {
              // e.g., set it unchecked for "0"
              input.checked = false;
            } else {
              input.checked = (input.value === String(resultItem.value));
            }
          }
          input.classList.add('oceer_focus');
        });
      }
  
      // ********************************************************************
      // **************************   EVENTS   *******************************
      // ********************************************************************
  
      // Start recording
      startBtn.addEventListener("click", startRecording);
  
      // Stop recording
      stopBtn.addEventListener("click", stopRecording);
  
      // Analyse recorded audio
      analyseBtn.addEventListener("click", analyseAudio);
  
    })(); // End of IIFE
  </script>
  
    @endif
</div>
