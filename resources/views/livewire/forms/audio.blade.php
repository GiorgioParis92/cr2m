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
            // Wrap everything in an IIFE so each instance has its own scope
            (function() {
                let mediaRecorder_{{ $uniqueId }};
                let audioChunks_{{ $uniqueId }} = [];
                let audioBlob_{{ $uniqueId }};

                // Grab all needed elements by ID, using the uniqueId
                const startBtn = document.getElementById("startRecord-{{ $uniqueId }}");
                const stopBtn = document.getElementById("stopRecord-{{ $uniqueId }}");
                const analyseBtn = document.getElementById("AnalyseAudio-{{ $uniqueId }}");
                const downloadLink = document.getElementById("analyse_audio-{{ $uniqueId }}");
                const audioPlayback = document.getElementById("audioPlayback-{{ $uniqueId }}");
                const hiddenValueInput = document.getElementById("value-{{ $uniqueId }}");

                startBtn.addEventListener("click", async function() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            audio: true
                        });

                        mediaRecorder_{{ $uniqueId }} = new MediaRecorder(stream);
                        mediaRecorder_{{ $uniqueId }}.ondataavailable = event => {
                            audioChunks_{{ $uniqueId }}.push(event.data);
                        };

                        mediaRecorder_{{ $uniqueId }}.onstop = async () => {
                            audioBlob_{{ $uniqueId }} = new Blob(
                            audioChunks_{{ $uniqueId }}, {
                                type: "audio/wav"
                            });
                            const audioUrl = URL.createObjectURL(audioBlob_{{ $uniqueId }});

                            // Display the recorded audio immediately
                            audioPlayback.src = '../..' + audioUrl;
                            audioPlayback.style.display = "block";
                            // Reset chunks
                            audioChunks_{{ $uniqueId }} = [];

                            // Automatically save the audio after stopping
                            await saveAudio(audioBlob_{{ $uniqueId }});
                        };

                        mediaRecorder_{{ $uniqueId }}.start();
                        startBtn.disabled = true;
                        stopBtn.disabled = false;
                        analyseBtn.style.display = "none";

                    } catch (error) {
                        console.error("Error accessing microphone:", error);
                        alert("Could not access microphone. Please allow microphone access.");
                    }
                });

                stopBtn.addEventListener("click", function() {
                    if (mediaRecorder_{{ $uniqueId }} && mediaRecorder_{{ $uniqueId }}.state ===
                        "recording") {
                        mediaRecorder_{{ $uniqueId }}.stop();
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
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

                analyseBtn.addEventListener("click", async function() {
                    try {
                        const response = await fetch("{{ route('audio.analyse') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content")
                            },
                            body: JSON.stringify({
                                value: hiddenValueInput.value,
                                dossier_id: {{ $dossier_id }},
                                form_id: {{ $form_id }},
                                name: '{{ $conf['name'] ?? '' }}',
                                api_link: '{{ $api_link ?? '' }}'
                            })
                        });

                        if (!response.ok) {
                            throw new Error("Server error during transcription request.");
                        }

                        const data = await response.json();
                        console.log("Transcribed text: ", data.transcription);

                        if (data.oceer_result && data.oceer_result.results) {
                            const {
                                results
                            } = data.oceer_result;

                            // Boucle sur les clés/valeurs dans results
                            for (const [key, resultItem] of Object.entries(results)) {
                                console.log(`\n=== Résultat pour : ${key} ===`);
                                console.log('Value :', resultItem.value);
                                console.log('Score :', resultItem.score);
                                console.log('ID    :', resultItem.id);

                                if (resultItem.value) {
                                    const inputToFill = document.querySelector(
                                    `input[name='${resultItem.id}']`);
                                    if (inputToFill) {
                                        inputToFill.value = resultItem.value;
                                    }

                                    fillFormField({
                                        id: resultItem.id,
                                        value: resultItem.value
                                    });
                                }

                                if (Array.isArray(resultItem.metadata)) {
                                    resultItem.metadata.forEach((meta, index) => {
                                        console.log(`Metadata #${index} :`, meta);
                                    });
                                }
                            }
                        } else {
                            console.warn('Aucun résultat dans oceer_result.');
                        }
                    } catch (error) {
                        console.error(error);
                        alert("Failed to transcribe audio.");
                    }
                });



                function fillFormField(resultItem) {
                    // Vérification des données d'entrée 
                    if (!resultItem?.id || resultItem.value === undefined || resultItem.value === null) {
                        return; // Early return si aucune donnée exploitable
                    }

                    // Recherche du champ dans le DOM en utilisant l'attribut name
                    const field = document.querySelector(`[name='${resultItem.id}']`);
                    if (!field) {
                        return; // Early return si le champ n'existe pas
                    }

                    // Détermination du type d'élément
                    const tagName = field.tagName.toLowerCase();
                    const fieldType = field.getAttribute('type')?.toLowerCase() || '';

                    switch (tagName) {
                        case 'input': {

                            if (fieldType === 'radio' || fieldType === 'checkbox') {
                                // Pour radio ou checkbox, il peut y en avoir plusieurs avec le même name
                                const allInputs = document.querySelectorAll(
                                    `input[type='${fieldType}'][name='${resultItem.id}']`
                                );

                                allInputs.forEach((input) => {
                                    // Si la valeur est un tableau, on suppose plusieurs checkbox
                                    if (Array.isArray(resultItem.value)) {
                                        input.checked = resultItem.value.includes(input.value);
                                    } else {
                                        // Si la valeur est 0, on décoche le checkbox
                                        if (fieldType === 'checkbox' && resultItem.value == 0) {
                                            input.checked = false;
                                        } else {
                                            input.checked = (input.value === String(resultItem.value));
                                        }
                                    }

                                    // Ajout d’une classe pour marquer la mise à jour
                                    input.classList.add('oceer_focus');
                                });
                            } else {
                                // Cas général (text, email, number, etc.)
                                field.value = resultItem.value;
                                field.classList.add('oceer_focus');
                            }
                            break;
                        }
                        case 'select': {
                            field.value = resultItem.value;
                            field.classList.add('oceer_focus');
                            break;
                        }
                        case 'textarea': {
                            field.value = resultItem.value;
                            field.classList.add('oceer_focus');
                            break;
                        }
                        default:
                            // Gérer éventuellement d'autres types d'éléments
                            break;
                    }
                }


            })(); // End of IIFE
        </script>
    @endif
</div>
